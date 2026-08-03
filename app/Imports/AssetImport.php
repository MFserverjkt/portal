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
     * Tentukan baris tempat Header/Judul Kolom berada di Excel.
     * Jika di file Excel kamu baris 1 adalah judul, baris 2 kosong, dan header baru di baris 3,
     * ubah return 1 menjadi return 3 (atau sesuaikan nomor barisnya).
     */
    public function headingRow(): int
    {
        return 1; 
    }

    public function model(array $row)
    {
        // Bersihkan key array dari spasi / karakter tersembunyi
        $cleanRow = [];
        foreach ($row as $key => $value) {
            $cleanKey = trim(strtolower((string)$key));
            $cleanRow[$cleanKey] = is_string($value) ? trim($value) : $value;
        }

        // 1. Tangkap Kode Aset (berbagai kemungkinan penulisan header Excel)
        $assetCode = $cleanRow['asset_code'] 
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

        // Jika baris kosong atau nama aset tidak ada, lewati
        if (!$assetName && !$assetCode) {
            return null;
        }

        // Fallback jika asset_code di Excel kosong
        if (!$assetCode) {
            $assetCode = 'MF-HOTNG-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }

        // Ambil branch_code
        $branchCode = $cleanRow['branch_code'] ?? $cleanRow['kode_cabang'] ?? null;
        if (!$branchCode) {
            $parts = explode('-', $assetCode);
            $branchCode = (isset($parts[1]) && !empty($parts[1])) ? $parts[1] : 'HOTNG';
        }

        return new Asset([
            'asset_code'  => $assetCode,
            'asset_name'  => $assetName ?? 'Tanpa Nama',
            'category'    => $cleanRow['category'] ?? $cleanRow['kategori'] ?? 'Lain-lain',
            'description' => $cleanRow['description'] ?? $cleanRow['keterangan'] ?? 'Imported from Excel',
            'branch_code' => $branchCode,
            'branch_name' => $cleanRow['branch_name'] ?? $cleanRow['nama_cabang'] ?? 'HEAD OFFICE TANGERANG',
        ]);
    }

    public function onError(Throwable $e)
    {
        // Mengabaikan baris error agar tidak menghentikan import baris lainnya
    }
}