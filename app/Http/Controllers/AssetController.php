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
     * Jika database kosong, menggunakan daftar cabang fallback yang sudah diperbarui.
     */
    private function getBranches()
    {
        try {
            $branches = DB::table('branches')->orderBy('name', 'asc')->pluck('name', 'code')->toArray();

            if (!empty($branches)) {
                return $branches;
            }
        } catch (\Exception $e) {
            // Abaikan jika tabel belum terbuat/error
        }

        // Master Data Cabang Terbaru (Fallback)
        $defaultBranches = [
            'HOTNG' => 'HEAD OFFICE TANGERANG',
            'MFBDK' => 'MAISON FEERIE BIDAKARA 2',
            'MFBX2'  => 'MAISON FEERIE BINTARO EXCHANGE MALL 2',
            'MFCP'  => 'MAISON FEERIE CENTRAL PARK',
            'MFGM3' => 'MAISON FEERIE GALAXY MALL 3 SBY',
            'MFHDH' => 'MAISON FEERIE HOKKY FRUIT DARMO HARAPAN SBY',
            'MFHGF' => 'MAISON FEERIE HOKKY FRUIT GRAHA FAMILY SBY',
            'MFHMR' => 'MAISON FEERIE HOKKY FRUIT MERR SBY',
            'MFKCH' => 'MAISON FEERIE KERETA CEPAT HALIM',
            'MFLMN' => 'MAISON FEERIE LIPPO MALL NUSANTARA',
            'MFLPS' => 'MAISON FEERIE LIPPO PLAZA SIDOARJO SBY',
            'MFLW'  => 'MAISON FEERIE LIVING WORLD',
            'MFPCM' => 'MAISON FEERIE PAKUWON CITY MALL SBY',
            'MFPMB' => 'MAISON FEERIE PAKUWON MALL BEKASI',
            'MFPWM' => 'MAISON FEERIE PAKUWON MALL SBY',
            'MFSIL' => 'MAISON FEERIE SILOAM SBY',
            'MFSMB' => 'MAISON FEERIE SUMMARECON MALL BEKASI',
            'MFWCT' => 'MAISON FEERIE WORLD CAPITAL TOWER',
        ];

        asort($defaultBranches);

        return $defaultBranches;
    }

    /**
     * Tampilan Inventori Aset IT & Maintenance (Dilengkapi Filter Query)
     */
    public function index(Request $request)
    {
        $query = Asset::query();

        // 1. Filter Pencarian Keyword (Nama / Kode Aset)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('asset_code', 'LIKE', "%{$search}%")
                  ->orWhere('asset_name', 'LIKE', "%{$search}%");
            });
        }

        // 2. Filter Kategori
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // 3. Filter Lokasi / Cabang
        if ($request->filled('location')) {
            $query->where(function($q) use ($request) {
                $q->where('branch_name', $request->location)
                  ->orWhere('branch_code', $request->location)
                  ->orWhere('location', $request->location);
            });
        }

        // 4. Filter Kondisi / Status
        if ($request->filled('condition')) {
            $condition = $request->condition;
            if ($condition === 'Baik') {
                $query->whereIn('status', ['Baik', 'Bagus / Normal', 'Bagus', 'Active']);
            } else {
                $query->where('status', $condition);
            }
        }

        $assets = $query->latest()->get();

        // Ambil daftar unik lokasi untuk dropdown filter
        $locations = Asset::select('branch_name')
            ->whereNotNull('branch_name')
            ->distinct()
            ->pluck('branch_name');

        return view('assets.index', compact('assets', 'locations'));
    }

    /**
     * Endpoint API / AJAX Khusus Pengecekan Scanner QR Code
     */
    public function scanCheck(Request $request)
    {
        $code = trim($request->input('asset_code'));

        if (!$code) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kode QR / Barcode tidak boleh kosong.'
            ], 400);
        }

        // Cari data aset tanpa batasan whitespace / case-sensitivity
        $asset = Asset::where(DB::raw('TRIM(asset_code)'), $code)->first();

        if (!$asset) {
            return response()->json([
                'status'  => 'error',
                'message' => "Aset ({$code}) tidak terdaftar!"
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Aset ditemukan.',
            'data'    => $asset
        ]);
    }

    public function create(Request $request)
    {
        $branches = $this->getBranches();
        return view('assets.create', compact('branches'));
    }

    public function store(Request $request)
    {
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
        $branchCode = $request->branch_code ?? (auth()->user()->branch_code ?? 'HOTNG');
        $registerDate = $request->register_date ?? date('Y-m-d');

        Asset::create([
            'asset_code'    => trim($request->asset_code),
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
        $data['asset_code'] = trim($request->asset_code);
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

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:assets,id',
        ], [
            'ids.required' => 'Pilih setidaknya satu aset yang ingin dihapus.'
        ]);

        $count = count($request->ids);
        Asset::whereIn('id', $request->ids)->delete();

        return redirect()->route('assets.index')->with('success', "Sebanyak {$count} data aset berhasil dihapus.");
    }

    public function export()
    {
        return Excel::download(new AssetExport, 'Asset_Data_' . date('Y-m-d') . '.xlsx');
    }

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