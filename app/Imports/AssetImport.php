<?php

namespace App\Imports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Throwable;

class AssetImport implements ToModel, WithHeadingRow, SkipsEmptyRows, SkipsOnError
{
    /**
     * Header kolom di file Excel Asset_Data berada di Baris ke-6 (Row 6).
     */
    public function headingRow(): int
    {
        return 6; 
    }

    public function model(array $row)
    {
        // 1. Normalisasi key array (ubah spasi/karakter khusus menjadi underscore & lowercase)
        $cleanRow = [];
        foreach ($row as $key => $value) {
            $cleanKey = strtolower(trim((string)$key));
            $cleanKey = str_replace([' ', '-'], '_', $cleanKey);
            $cleanRow[$cleanKey] = is_string($value) ? trim($value) : $value;
        }

        // 2. Ambil Asset ID / Kode Aset
        $assetCode = $cleanRow['asset_id'] 
            ?? $cleanRow['asset_code'] 
            ?? $cleanRow['kode_aset'] 
            ?? $cleanRow['kode_asset'] 
            ?? null;

        // 3. Ambil Nama Aset
        $assetName = $cleanRow['asset_name'] 
            ?? $cleanRow['nama_aset'] 
            ?? $cleanRow['nama_asset'] 
            ?? null;

        // Jika baris kosong/header terulang, lewati
        if (!$assetCode && !$assetName) {
            return null;
        }

        // 4. Ambil Raw Branch & Map ke Kode/Nama Cabang Resmi
        $rawBranch = $cleanRow['branch'] 
            ?? $cleanRow['branch_name'] 
            ?? $cleanRow['branch_code'] 
            ?? '';

        $branchInfo = $this->mapBranchInfo($rawBranch, $assetCode);

        // 5. Format Tanggal Registrasi
        $registrationDate = null;
        $rawDate = $cleanRow['registration_date'] ?? $cleanRow['register_date'] ?? null;

        if (!empty($rawDate)) {
            try {
                if (is_numeric($rawDate)) {
                    $registrationDate = ExcelDate::excelToDateTimeObject($rawDate)->format('Y-m-d');
                } else {
                    $registrationDate = date('Y-m-d', strtotime($rawDate));
                }
            } catch (Throwable $e) {
                $registrationDate = date('Y-m-d');
            }
        }

        // 6. Buat / Update Data Aset
        return new Asset([
            'asset_code'        => $assetCode,
            'asset_name'        => $assetName ?? 'Tanpa Nama',
            'category'          => $cleanRow['asset_category_name'] ?? $cleanRow['category'] ?? 'Lain-lain',
            'description'       => $cleanRow['description'] ?? 'Imported from Excel',
            'branch_code'       => $branchInfo['code'],
            'branch_name'       => $branchInfo['name'],
            'product_code'      => $cleanRow['product_code'] ?? null,
            'location'          => $cleanRow['location'] ?? $branchInfo['name'],
            'register_date'     => $registrationDate ?? date('Y-m-d'),
            'registration_date' => $registrationDate ?? date('Y-m-d'),
            'status'            => $cleanRow['status'] ?? 'Bagus / Normal',
        ]);
    }

    /**
     * Helper Pemetaan Nama Cabang Excel ke Kode & Nama Resmi Sistem
     */
    private function mapBranchInfo(string $rawBranch, string $assetCode = ''): array
    {
        $branches = [
            'bintaro'         => ['code' => 'MFBX',  'name' => 'MAISON FEERIE BINTARO EXCHANGE'],
            'graha'           => ['code' => 'MFHGF', 'name' => 'MAISON FEERIE HOKKY FRUIT GRAHA FAMILY SBY'],
            'darmo'           => ['code' => 'MFHDH', 'name' => 'MAISON FEERIE HOKKY FRUIT DARMO HARAPAN SBY'],
            'merr'            => ['code' => 'MFHMR', 'name' => 'MAISON FEERIE HOKKY FRUIT MERR SBY'],
            'lippo mall'      => ['code' => 'MFLMN', 'name' => 'MAISON FEERIE LIPPO MALL NUSANTARA'],
            'nusantara'       => ['code' => 'MFLMN', 'name' => 'MAISON FEERIE LIPPO MALL NUSANTARA'],
            'sidoarjo'        => ['code' => 'MFLPS', 'name' => 'MAISON FEERIE LIPPO PLAZA SIDOARJO SBY'],
            'living world'    => ['code' => 'MFLW',  'name' => 'MAISON FEERIE LIVING WORLD'],
            'pakuwon city'    => ['code' => 'MFPCM', 'name' => 'MAISON FEERIE PAKUWON CITY MALL SBY'],
            'pakuwon bekasi'  => ['code' => 'MFPMB', 'name' => 'MAISON FEERIE PAKUWON MALL BEKASI'],
            'pakuwon mall'    => ['code' => 'MFPWM', 'name' => 'MAISON FEERIE PAKUWON MALL SBY'],
            'siloam'          => ['code' => 'MFSIL', 'name' => 'MAISON FEERIE SILOAM SBY'],
            'summarecon'      => ['code' => 'MFSMB', 'name' => 'MAISON FEERIE SUMMARECON MALL BEKASI'],
            'central park'    => ['code' => 'MFCP',  'name' => 'MAISON FEERIE CENTRAL PARK'],
            'bidakara'        => ['code' => 'MFBDK', 'name' => 'MAISON FEERIE BIDAKARA 2'],
            'halim'           => ['code' => 'MFKCH', 'name' => 'MAISON FEERIE KERETA CEPAT HALIM'],
            'galaxy'          => ['code' => 'MFGM3', 'name' => 'MAISON FEERIE GALAXY MALL 3 SBY'],
            'world capital'   => ['code' => 'MFWCT', 'name' => 'MAISON FEERIE WORLD CAPITAL TOWER'],
            'head office'     => ['code' => 'HOTNG', 'name' => 'HEAD OFFICE TANGERANG'],
            'tangerang'       => ['code' => 'HOTNG', 'name' => 'HEAD OFFICE TANGERANG'],
        ];

        $lowerBranch = strtolower($rawBranch);

        foreach ($branches as $keyword => $data) {
            if (strpos($lowerBranch, $keyword) !== false) {
                return $data;
            }
        }

        // Jika tidak cocok, coba ambil kode dari Asset ID (contoh: BOP-2025-09-30-0002)
        return [
            'code' => 'HOTNG',
            'name' => $rawBranch ?: 'HEAD OFFICE TANGERANG'
        ];
    }

    public function onError(Throwable $e)
    {
        // Abaikan error baris individu
    }
}