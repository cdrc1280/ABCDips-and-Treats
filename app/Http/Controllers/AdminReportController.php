<?php

namespace App\Http\Controllers;

use App\Exports\GenericReportExport;
use App\Services\ReportExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AdminReportController extends Controller
{
    protected ReportExportService $reportService;

    public function __construct(ReportExportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Download report in PDF, Excel (.xlsx/.csv), or Word (.doc) format.
     */
    public function download(string $type, string $format)
    {
        $startDate = request('start_date');
        $endDate = request('end_date');
        
        // Validate date range if provided
        if ($startDate || $endDate) {
            $validator = \Illuminate\Support\Facades\Validator::make(
                ['start_date' => $startDate, 'end_date' => $endDate],
                [
                    'start_date' => ['nullable', 'date'],
                    'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
                ]
            );
            if ($validator->fails()) {
                return response()->json(['message' => 'Invalid date range. End date must be greater than or equal to start date.', 'errors' => $validator->errors()], 422);
            }
        }
        
        // Convert to Carbon for date filtering
        $start = $startDate ? \Carbon\Carbon::parse($startDate)->startOfDay() : null;
        $end = $endDate ? \Carbon\Carbon::parse($endDate)->endOfDay() : null;
        
        $reportData = $this->reportService->getReportData($type, $start, $end);
        
        // Check if no data returned
        if (empty($reportData['rows'])) {
            $dateMsg = ($start && $end) ? " for the selected date range ({$startDate} to {$endDate})" : '';
            return response()->json(['message' => "No data found{$dateMsg}. Please select a different date range."], 422);
        }
        
        $dateLabel = $start ? "_{$startDate}_to_{$endDate}" : '';
        $fileName = 'ABCDips_' . ucfirst($type) . '_Report' . $dateLabel . '_' . date('Ymd_His');

        switch (strtolower($format)) {
            case 'pdf':
                $pdf = Pdf::loadView('reports.executive_report', ['report' => $reportData]);
                $pdf->setPaper('a4', 'landscape');
                return $pdf->download($fileName . '.pdf');

            case 'excel':
            case 'xlsx':
            case 'csv':
                try {
                    return Excel::download(new GenericReportExport($reportData), $fileName . '.xlsx');
                } catch (\Throwable $e) {
                    $headers = [
                        'Content-Type' => 'text/csv; charset=UTF-8',
                        'Content-Disposition' => 'attachment; filename="' . $fileName . '.csv"',
                    ];
                    $callback = function () use ($reportData) {
                        $file = fopen('php://output', 'w');
                        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
                        fputcsv($file, $reportData['headings'] ?? []);
                        foreach ($reportData['rows'] as $row) {
                            fputcsv($file, array_values($row));
                        }
                        fclose($file);
                    };
                    return response()->stream($callback, 200, $headers);
                }

            case 'word':
            case 'doc':
                $html = view('reports.executive_report', ['report' => $reportData])->render();
                return response($html, 200, [
                    'Content-Type' => 'application/msword',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '.doc"',
                ]);

            default:
                abort(400, 'Unsupported export format.');
        }
    }
}
