<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenericReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    protected array $reportData;

    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;
    }

    public function title(): string
    {
        return substr($this->reportData['title'] ?? 'Report', 0, 31);
    }

    public function headings(): array
    {
        return $this->reportData['headings'] ?? [];
    }

    /** @return Collection<int, mixed> */
    public function collection(): Collection
    {
        return new Collection($this->reportData['rows'] ?? []);
    }

    /** @return array<int, array<string, mixed>> */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '5C3A22'],
                ],
            ],
        ];
    }
}
