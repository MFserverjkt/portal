<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Exports\AssetExport;
use App\Imports\AssetImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AssetController extends Controller
{
    // Daftar Cabang / Branch Resmi Maison Feerie
    private $branches = [
        'HOTNG' => 'HEAD OFFICE TANGERANG',
        'MFLW'  => 'MAISON FEERIE LIVING WORLD',
        'MFBX'  => 'MAISON FEERIE BINTARO EXCHANGE',
        'MFCP'  => 'MAISON FEERIE CENTRAL PARK',
        'MFLMN' => 'MAISON FEERIE LIPPO MALL NUSANTARA',
        'MFWCT' => 'MAISON FEERIE WORLD CAPITAL TOWER',
        'MFBDK' => 'MAISON FEERIE BIDAKARA 2',
        'MFKCH' => 'MAISON FEERIE KERETA CEPAT HALIM',
        'MFPMB' => 'MAISON FEERIE PAKUWON MALL BEKASI',
        'MFSMB' => 'MAISON FEERIE SUMMARECON MALL BEKASI',
    ];

    // Tampilan Inventori Aset IT & Maintenance
    public function index()
    {
        $assets = Asset::latest()->get();
        return view('assets.index', compact('assets'));
    }

    public function create(Request $request)
    {
        // Ambil daftar branches dari properti kelas
        $branches = $this->branches;

        return view('assets.create', compact('branches'));
    }

    public function store(Request $request)
    {
        // Validasi input form
        $request->validate([
            'asset_name'    => 'required|string|max:255',
            'category'      => 'required|string|max:255',
            'brand'         => 'nullable|string|max:255',
            'branch_code'   => 'required|string',
            'register_date' => 'required|date',
            'status'        => 'required|string',
            'description'   => 'nullable|string',
        ]);

        // Tangkap kode branch yang dipilih user, jika kosong fallback ke cabang user / 'HOTNG'
        $branchCode = $request->branch_code ?? (auth()->user()->branch_code ?? 'HOTNG');
        $registerDate = $request->register_date ?? date('Y-m-d');

        // Generate kode aset otomatis di backend
        $assetCode = $this->generateAssetCode($branchCode, $registerDate);

        // Simpan data aset
        Asset::create([
            'asset_code'    => $assetCode,
            'asset_name'    => $request->asset_name,
            'category'      => $request->category,
            'brand'         => $request->brand,
            'branch_code'   => $branchCode,
            'branch_name'   => $this->branches[$branchCode] ?? $branchCode,
            'register_date' => $registerDate,
            'status'        => $request->status,
            'description'   => $request->description,
        ]);

        return redirect()->route('assets.index')->with('success', "Aset berhasil ditambahkan dengan Kode: {$assetCode}");
    }

    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        $branches = $this->branches;

        return view('assets.edit', compact('asset', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            'asset_code'  => 'required|string|unique:assets,asset_code,' . $asset->id,
            'asset_name'  => 'required|string|max:255',
            'category'    => 'required|string|max:255',
            'branch_code' => 'required|string',
            'status'      => 'required|string',
            'description' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['branch_name'] = $this->branches[$request->branch_code] ?? $request->branch_code;

        $asset->update($data);

        return redirect()->route('assets.index')->with('success', 'Data aset berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();

        return redirect()->route('assets.index')->with('success', 'Aset berhasil dihapus.');
    }

    // Method/Fungsi Export Data Aset ke Excel
    public function export()
    {
        return Excel::download(new AssetExport, 'Asset_Data_' . date('Y-m-d') . '.xlsx');
    }

    // Method/Fungsi Import Data Aset dari Excel
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:4096'
        ]);

        try {
            Excel::import(new AssetImport, $request->file('file'));
            return redirect()->route('assets.index')->with('success', 'Data aset berhasil di-import dari Excel.');
        } catch (\Exception $e) {
            return redirect()->route('assets.index')->with('error', 'Gagal meng-import file: ' . $e->getMessage());
        }
    }

    /**
     * Helper Function Private untuk Generate Kode Aset Otomatis
     * Format: MF-{BRANCH}-{TANGGAL}-{NOMOR_URUT} (Contoh: MF-HOTNG-20260729-0001)
     */
    private function generateAssetCode($branchCode = 'HOTNG', $date = null)
    {
        $dateStr = Carbon::parse($date ?? Carbon::now())->format('Ymd');
        $prefix = "MF-{$branchCode}-{$dateStr}-";

        // Cari aset terakhir dengan prefix yang sama
        $lastAsset = Asset::where('asset_code', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastAsset) {
            $lastNumber = (int) substr($lastAsset->asset_code, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $sequence = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return $prefix . $sequence;
    }
}