<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithStyles
{
    public function collection()
    {
        return Asset::all();
    }

    // Data dimulai dari baris ke-5 (seperti pada template)
    public function startCell(): string
    {
        return 'A5';
    }

    public function headings(): array
    {
        return [
            'Asset ID',
            'Asset Category Name',
            'Asset Name',
            'Product Code',
            'Branch',
            'Location',
            'Asset Account',
            'Asset Depreciation Account',
            'Asset Expense Account',
            'Depreciation Length',
            'Depreciation Occurence',
            'Starting Value',
            'Current Value',
            'Registration Date',
            'Start Depreciation Date',
            'Status'
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->asset_code,
            $asset->category,
            $asset->asset_name,
            '', // Product Code
            '', // Branch
            $asset->location,
            '', // Asset Account
            '', // Asset Depreciation Account
            '', // Asset Expense Account
            '', // Depreciation Length
            '', // Depreciation Occurence
            '', // Starting Value
            '', // Current Value
            $asset->created_at ? $asset->created_at->format('Y-m-d') : '',
            '', // Start Depreciation Date
            $asset->condition == 'Baik' ? 'Active' : $asset->condition
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Menyusun Header Laporan di bagian atas
        $sheet->setCellValue('A1', 'Maison Feerie');
        $sheet->setCellValue('A3', 'Generated');
        $sheet->setCellValue('B3', now()->format('d-m-Y H:i:s'));

        // Font bold untuk header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A5:P5')->getFont()->setBold(true);

        return [];
    }
}