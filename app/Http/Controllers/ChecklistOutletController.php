<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChecklistOutletController extends Controller
{
    public function create()
    {
        // Kategori dan item pekerjaan sesuai lampiran gambar
        $categories = [
            'ESB' => [
                'Touch screen layar POS / Kalibrasi layar',
                'Printer kasir',
                'TV LED Outlet',
                'Kabel LAN di POS / Kabel Printer',
                'UPS POS kasir',
                'Cash Drawer kasir',
            ],
            'CCTV' => [
                'Kabel LAN',
                'DVR',
                'Hardisk CCTV',
                'Hasil Rekaman (bisa record)',
                'Adaptor CCTV',
                'Kamera CCTV Outlet',
            ],
            'INTERNET (MODEM)' => [
                'Kabel LAN',
                'Indikator Modem',
                'Adaptor Modem',
                'Switch',
                'Router Ruijie',
                'Hasil SpeedTest Internet',
            ],
            'HP OUTLET' => [
                'Cek Kelayakan HP Outlet (Memory internal)',
                'Charger HP Outlet',
                'Update WA, Online Food & Volume HP',
            ],
            'TV OUTLET' => [
                'Cek TV kondisi normal nyala',
                'Remot TV berfungsi',
                'Cek usb flashdisk untuk TV Promo',
            ],
            'LAPTOP OUTLET' => [
                'Cek Kelayakan Laptop Outlet',
                'Charger Laptop Outlet',
                'Wifi Laptop Berfungsi',
            ]
        ];

        $outlets = User::where('role', 'OUTLET')->get();

        return view('it.checklist.create', compact('categories', 'outlets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dikerjakan_oleh'   => 'required|string',
            'outlet'            => 'required|string',
            'order_type'        => 'required|string',
            'tanggal_pekerjaan' => 'required|date',
            'items'             => 'required|array',
        ]);

        DB::table('outlet_checklists')->insert([
            'dikerjakan_oleh'   => $request->dikerjakan_oleh,
            'outlet'            => $request->outlet,
            'order_type'        => $request->order_type,
            'tanggal_pekerjaan' => $request->tanggal_pekerjaan,
            'items'             => json_encode($request->items),
            'ttd_it'            => $request->ttd_it,
            'ttd_leader'        => $request->ttd_leader,
            'ttd_fa_manager'    => $request->ttd_fa_manager,
            'nama_it'           => $request->nama_it,
            'nama_leader'       => $request->nama_leader,
            'nama_fa_manager'   => $request->nama_fa_manager,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return redirect()->back()->with('success', 'Form Checklist Outlet berhasil disimpan.');
    }
}