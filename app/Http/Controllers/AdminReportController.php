<?php

namespace App\Http\Controllers;

use App\Exports\GenericReportExport;
use App\Services\ReportExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

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
        $reportData = $this->reportService->getReportData($type);
        $fileName = 'ABCDips_' . ucfirst($type) . '_Report_' . date('Ymd_His');

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
                    // Fallback to direct CSV stream if Excel driver has an issue
                    $headers = [
                        'Content-Type' => 'text/csv; charset=UTF-8',
                        'Content-Disposition' => 'attachment; filename="' . $fileName . '.csv"',
                    ];
                    $callback = function () use ($reportData) {
                        $file = fopen('php://output', 'w');
                        // UTF-8 BOM for Excel compatibility
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
