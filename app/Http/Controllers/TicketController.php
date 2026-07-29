<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Asset;
use App\Models\Bast;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // OUTLET hanya membaca tiket buatannya sendiri
        if ($user->role === 'OUTLET') {
            $tickets = Ticket::with(['asset', 'bast'])->where('user_id', $user->id)->latest()->get();
        } else {
            $tickets = Ticket::with(['asset', 'user', 'bast'])->latest()->get();
        }

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $user = auth()->user();

        // 1. Jika role ADMIN atau IT, tampilkan SEMUA aset dari seluruh cabang
        if (in_array($user->role, ['ADMIN', 'IT'])) {
            $assets = Asset::orderBy('asset_name', 'asc')->get();
        } else {
            // 2. Untuk role OUTLET, filter berdasarkan branch_code user
            $branchCode = trim($user->branch_code);

            // Coba cari aset berdasarkan branch_code persis
            $assets = Asset::where('branch_code', $branchCode)
                           ->orderBy('asset_name', 'asc')
                           ->get();

            // 3. Cadangan (Fallback): Jika data aset kosong, cari berdasarkan pola kode aset
            if ($assets->isEmpty() && !empty($branchCode) && $branchCode !== 'HOTNG') {
                $assets = Asset::where('asset_code', 'LIKE', "%-{$branchCode}-%")
                               ->orderBy('asset_name', 'asc')
                               ->get();
            }
        }

        return view('tickets.create', compact('assets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id'    => 'required|exists:assets,id',
            'department'  => 'required|in:IT,MAINTENANCE',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'priority'    => 'required|in:Rendah,Sedang,Tinggi',
        ]);

        Ticket::create([
            'ticket_number' => 'TKT-' . date('Ymd') . '-' . rand(1000, 9999),
            'user_id'       => auth()->id(),
            'asset_id'      => $request->asset_id,
            'department'    => $request->department,
            'title'         => $request->title,
            'description'   => $request->description,
            'priority'      => $request->priority,
            'status'        => 'Terbuka',
        ]);

        return redirect()->route('tickets.index')->with('success', 'Tiket perbaikan berhasil dikirim.');
    }

    public function show($id)
    {
        $ticket = Ticket::with(['asset', 'user', 'bast.technician'])->findOrFail($id);
        return view('tickets.show', compact('ticket'));
    }

    public function createBast($id)
    {
        $ticket = Ticket::findOrFail($id);
        
        // Mencegah akses ke form BAST jika BAST sudah terisi
        if ($ticket->bast) {
            return redirect()->route('tickets.show', $id)->with('info', 'BAST untuk tiket ini sudah diisi.');
        }

        return view('tickets.bast_create', compact('ticket'));
    }

    public function storeBast(Request $request, $id)
    {
        $request->validate([
            'action_taken'   => 'required|string',
            'parts_replaced' => 'nullable|string',
        ]);

        $ticket = Ticket::findOrFail($id);

        // Menggunakan updateOrCreate untuk mencegah error UNIQUE constraint
        Bast::updateOrCreate(
            ['ticket_id' => $ticket->id],
            [
                'technician_id' => auth()->id(),
                'action_taken'   => $request->action_taken,
                'parts_replaced' => $request->parts_replaced,
                'completed_at'  => now(),
            ]
        );

        // Status diubah menjadi 'Menunggu Konfirmasi'
        $ticket->update(['status' => 'Menunggu Konfirmasi']);

        return redirect()->route('tickets.show', $ticket->id)->with('success', 'Form BAST berhasil dibuat. Menunggu konfirmasi DONE dari user.');
    }

    /**
     * Konfirmasi Tiket Selesai (DONE) oleh User Pembuat Tiket / Admin
     */
    public function markAsDone($id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = auth()->user();

        // Validasi Hak Akses: Hanya pembuat tiket atau Admin
        if ($ticket->user_id !== $user->id && $user->role !== 'ADMIN') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menyelesaikan tiket ini.');
        }

        $ticket->update(['status' => 'Selesai']);

        return redirect()->back()->with('success', 'Tiket berhasil dikonfirmasi SELESAI (DONE).');
    }
}