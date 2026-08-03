<?php

namespace App\Imports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Throwable;

class AssetImport implements ToModel, WithHeadingRow, SkipsEmptyRows, SkipsOnError
{
    /**
     * Menentukan baris tempat Header/Judul Kolom berada di file Excel.
     * Sesuai dengan format export Excel Maison Feerie, header berada di baris ke-5.
     */
    public function headingRow(): int
    {
        return 5; 
    }

    public function model(array $row)
    {
        // Bersihkan key array dari spasi / karakter tersembunyi
        $cleanRow = [];
        foreach ($row as $key => $value) {
            $cleanKey = trim(strtolower((string)$key));
            $cleanRow[$cleanKey] = is_string($value) ? trim($value) : $value;
        }

        // 1. Tangkap Kode Aset / Asset ID
        $assetCode = $cleanRow['asset_id'] 
            ?? $cleanRow['asset_code'] 
            ?? $cleanRow['kode_aset'] 
            ?? $cleanRow['kode_asset'] 
            ?? $cleanRow['kode'] 
            ?? null;

        // 2. Tangkap Nama Aset
        $assetName = $cleanRow['asset_name'] 
            ?? $cleanRow['nama_aset'] 
            ?? $cleanRow['nama_asset'] 
            ?? $cleanRow['nama_barang'] 
            ?? $cleanRow['nama']
            ?? null;

        // Jika baris kosong, tidak ada nama aset dan kode aset, lewati
        if (!$assetName && !$assetCode) {
            return null;
        }

        // Fallback jika asset_code di Excel kosong
        if (!$assetCode) {
            $assetCode = 'MF-HOTNG-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }

        // 3. Ambil Kategori
        $category = $cleanRow['asset_category_name'] 
            ?? $cleanRow['category'] 
            ?? $cleanRow['kategori'] 
            ?? 'Lain-lain';

        // 4. Ambil branch_code
        $branchCode = $cleanRow['branch'] 
            ?? $cleanRow['branch_code'] 
            ?? $cleanRow['kode_cabang'] 
            ?? null;

        if (!$branchCode) {
            $parts = explode('-', $assetCode);
            $branchCode = (isset($parts[1]) && !empty($parts[1])) ? $parts[1] : 'HOTNG';
        }

        // 5. Tangkap Tanggal Registrasi jika tersedia
        $registrationDate = null;
        if (!empty($cleanRow['registration_date'])) {
            try {
                $registrationDate = date('Y-m-d', strtotime($cleanRow['registration_date']));
            } catch (Throwable $e) {
                $registrationDate = null;
            }
        }

        return new Asset([
            'asset_code'         => $assetCode,
            'asset_name'         => $assetName ?? 'Tanpa Nama',
            'category'           => $category,
            'description'        => $cleanRow['description'] ?? $cleanRow['keterangan'] ?? 'Imported from Excel',
            'branch_code'        => $branchCode,
            'branch_name'        => $cleanRow['branch_name'] ?? $cleanRow['nama_cabang'] ?? $branchCode,
            'product_code'       => $cleanRow['product_code'] ?? null,
            'location'           => $cleanRow['location'] ?? null,
            'registration_date'  => $registrationDate,
            'status'             => $cleanRow['status'] ?? 'Aktif',
        ]);
    }

    public function onError(Throwable $e)
    {
        // Mengabaikan baris error agar tidak menghentikan import baris lainnya
    }
}