<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Exports\AssetExport;
use App\Imports\AssetImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AssetController extends Controller
{
    /**
     * Helper privat untuk mengambil daftar cabang dinamis dari database.
     * Jika database kosong, menggunakan fallback daftar cabang bawaan.
     */
    private function getBranches()
    {
        try {
            // Ambil dari tabel 'branches' dengan format [code => name]
            $branches = DB::table('branches')->orderBy('name', 'asc')->pluck('name', 'code')->toArray();

            if (!empty($branches)) {
                return $branches;
            }
        } catch (\Exception $e) {
            // Abaikan jika tabel belum terbuat/error
        }

        // Fallback jika tabel branches di DB belum di-seed/kosong
        return [
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
    }

    // Tampilan Inventori Aset IT & Maintenance
    public function index()
    {
        $assets = Asset::latest()->get();
        return view('assets.index', compact('assets'));
    }

    public function create(Request $request)
    {
        // Ambil cabang dinamis dari database
        $branches = $this->getBranches();

        return view('assets.create', compact('branches'));
    }

    public function store(Request $request)
    {
        // Validasi input form (Termasuk penambahan unique check untuk asset_code)
        $request->validate([
            'asset_code'    => 'required|string|max:255|unique:assets,asset_code',
            'asset_name'    => 'required|string|max:255',
            'category'      => 'required|string|max:255',
            'brand'         => 'nullable|string|max:255',
            'branch_code'   => 'required|string',
            'register_date' => 'required|date',
            'status'        => 'required|string',
            'description'   => 'nullable|string',
        ], [
            'asset_code.required' => 'Asset ID / Kode Aset wajib diisi.',
            'asset_code.unique'   => 'Asset ID sudah digunakan! Harap gunakan Asset ID lain.',
        ]);

        $branches = $this->getBranches();

        // Tangkap kode branch yang dipilih user, jika kosong fallback ke cabang user / 'HOTNG'
        $branchCode   = $request->branch_code ?? (auth()->user()->branch_code ?? 'HOTNG');
        $registerDate = $request->register_date ?? date('Y-m-d');

        // Simpan data aset dengan Kode Aset dari Input Manual
        Asset::create([
            'asset_code'    => $request->asset_code,
            'asset_name'    => $request->asset_name,
            'category'      => $request->category,
            'brand'         => $request->brand,
            'branch_code'   => $branchCode,
            'branch_name'   => $branches[$branchCode] ?? $branchCode,
            'register_date' => $registerDate,
            'status'        => $request->status,
            'description'   => $request->description,
        ]);

        return redirect()->route('assets.index')->with('success', "Aset berhasil ditambahkan dengan Kode: {$request->asset_code}");
    }

    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        $branches = $this->getBranches();

        return view('assets.edit', compact('asset', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            'asset_code'  => 'required|string|max:255|unique:assets,asset_code,' . $asset->id,
            'asset_name'  => 'required|string|max:255',
            'category'    => 'required|string|max:255',
            'branch_code' => 'required|string',
            'status'      => 'required|string',
            'description' => 'nullable|string',
        ]);

        $branches = $this->getBranches();
        $data = $request->all();
        $data['branch_name'] = $branches[$request->branch_code] ?? $request->branch_code;

        $asset->update($data);

        return redirect()->route('assets.index')->with('success', 'Data aset berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();

        return redirect()->route('assets.index')->with('success', 'Aset berhasil dihapus.');
    }

    /**
     * Method/Fungsi untuk menghapus beberapa aset sekaligus (Bulk Delete)
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:assets,id',
        ], [
            'ids.required' => 'Pilih setidaknya satu aset yang ingin dihapus.'
        ]);

        $count = count($request->ids);
        
        // Hapus data berdasarkan array ID yang dikirim dari form Blade
        Asset::whereIn('id', $request->ids)->delete();

        return redirect()->route('assets.index')->with('success', "Sebanyak {$count} data aset berhasil dihapus.");
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
}