<?php

namespace App\Imports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssetImport implements ToModel, WithHeadingRow
{
    // Mengindikasikan baris ke-5 sebagai header kolom
    public function headingRow(): int
    {
        return 5;
    }

    public function model(array $row)
    {
        // Lewati jika kolom Asset ID / Asset Name kosong
        if (empty($row['asset_id']) || empty($row['asset_name'])) {
            return null;
        }

        // Cek jika data sudah ada berdasarkan asset_code, lakukan update/create
        return Asset::updateOrCreate(
            ['asset_code' => $row['asset_id']],
            [
                'asset_name' => $row['asset_name'],
                'category'   => $row['asset_category_name'] ?? 'IT',
                'location'   => $row['location'] ?? 'Bintaro',
                'condition'  => ($row['status'] ?? 'Active') == 'Active' ? 'Baik' : 'Rusak',
                'description'=> 'Imported from Excel',
            ]
        );
    }
}