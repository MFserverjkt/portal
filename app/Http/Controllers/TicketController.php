<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Asset;
use App\Models\Bast;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $selectedBranch = $request->input('branch');

        // Query awal dengan Eager Loading
        $query = Ticket::with(['asset', 'user', 'bast'])->latest();

        // 1. Filter Role OUTLET: Hanya melihat tiket buatan sendiri ATAU tiket dari cabang yang sama
        if ($user->role === 'OUTLET') {
            $userBranch = trim($user->branch_code ?? '');

            $query->where(function ($q) use ($user, $userBranch) {
                $q->where('user_id', $user->id);

                if (!empty($userBranch)) {
                    $q->orWhere(function ($sub) use ($userBranch) {
                        $sub->whereNotNull('branch_code')
                            ->where('branch_code', '!=', '')
                            ->where('branch_code', $userBranch);
                    });
                }
            });
        } 
        // 2. Filter Role IT / MAINTENANCE: Melihat tiket sesuai divisi penanggung jawab
        elseif (in_array($user->role, ['IT', 'MAINTENANCE'])) {
            $query->where('department', $user->role);
        }
        // 3. Role ADMIN: Melihat seluruh data tiket tanpa filter role

        // Filter Tambahan: Filter Branch dari Form / Dropdown jika dipilih
        if (!empty($selectedBranch)) {
            $query->where(function ($q) use ($selectedBranch) {
                $q->where('branch_code', $selectedBranch)
                  ->orWhere('branch_name', $selectedBranch)
                  ->orWhereHas('user', function ($subQuery) use ($selectedBranch) {
                      $subQuery->where('branch_code', $selectedBranch)
                               ->orWhere('branch_name', $selectedBranch);
                  });
            });
        }

        $tickets = $query->get();

        // Ambil daftar Branch unik dari Users & Assets
        $branchCodes = User::whereNotNull('branch_code')->pluck('branch_code');
        $assetBranches = Asset::whereNotNull('branch_code')->pluck('branch_code');

        $branches = $branchCodes
            ->merge($assetBranches)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('tickets.index', compact('tickets', 'branches'));
    }

    public function create()
    {
        // Tampilkan SELURUH aset terdaftar tanpa membatasi cabang pengguna
        $assets = Asset::orderBy('asset_name', 'asc')->get();

        // Kumpulan Cabang Dinamis dari Tabel Assets
        $branches = Asset::whereNotNull('branch_code')
                         ->where('branch_code', '!=', '')
                         ->select('branch_code', 'branch_name')
                         ->distinct()
                         ->pluck('branch_name', 'branch_code')
                         ->toArray();

        // Fallback default jika data cabang kosong
        if (empty($branches)) {
            $branches = [
                'HOTNG' => 'HEAD OFFICE TANGERANG',
            ];
        }

        return view('tickets.create', compact('assets', 'branches'));
    }

    public function store(Request $request, TelegramService $telegram)
    {
        $request->validate([
            'reporter_name' => 'required|string|max:255',
            'asset_id'      => 'required|exists:assets,id',
            'department'    => 'required|in:IT,MAINTENANCE',
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'priority'      => 'required|in:Rendah,Sedang,Tinggi',
            'branch_code'   => 'nullable|string',
            'attachment'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // Max 5MB
        ]);

        $asset = Asset::find($request->asset_id);
        $user  = auth()->user();

        // Handle Upload Lampiran Kerusakan Tiket
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('tickets/attachments', 'public');
        }

        // --- LOGIC BRANCH ---
        $branchCode = $request->filled('branch_code') 
            ? $request->branch_code 
            : ($asset->branch_code ?? $user->branch_code ?? 'HOTNG');

        $branchName = Asset::where('branch_code', $branchCode)->value('branch_name')
            ?? User::where('branch_code', $branchCode)->value('branch_name')
            ?? $asset->branch_name 
            ?? $user->branch_name 
            ?? 'HEAD OFFICE TANGERANG';

        $ticketNumber = 'TKT-' . date('Ymd') . '-' . rand(1000, 9999);

        $ticket = Ticket::create([
            'ticket_number' => $ticketNumber,
            'user_id'       => $user->id,
            'reporter_name' => $request->reporter_name,
            'asset_id'      => $request->asset_id,
            'department'    => $request->department,
            'title'         => $request->title,
            'description'   => $request->description,
            'attachment'    => $attachmentPath,
            'priority'      => $request->priority,
            'branch_code'   => $branchCode,
            'branch_name'   => $branchName,
            'status'        => 'Terbuka',
        ]);

        // --- KIRIM NOTIFIKASI TELEGRAM ---
        try {
            $cleanTitle    = htmlspecialchars($request->title, ENT_QUOTES, 'UTF-8');
            $cleanDesc     = htmlspecialchars($request->description, ENT_QUOTES, 'UTF-8');
            $cleanReporter = htmlspecialchars($request->reporter_name, ENT_QUOTES, 'UTF-8');

            $message  = "🚨 <b>TIKET PERBAIKAN BARU</b>\n\n";
            $message .= "<b>No. Tiket:</b> #" . $ticketNumber . "\n";
            $message .= "<b>Cabang:</b> " . $branchName . "\n";
            $message .= "<b>Pelapor:</b> " . $cleanReporter . "\n";
            $message .= "<b>Divisi Tujuan:</b> " . $request->department . "\n";
            $message .= "<b>Aset:</b> " . ($asset->asset_name ?? '-') . "\n";
            $message .= "<b>Prioritas:</b> " . $request->priority . "\n";
            $message .= "<b>Judul:</b> " . $cleanTitle . "\n";
            $message .= "<b>Kendala:</b> " . $cleanDesc . "\n\n";
            $message .= "<i>Silakan login ke portal untuk memproses tiket ini.</i>";

            // Jika ada file lampiran gambar, kirim sebagai foto ke Telegram
            if ($attachmentPath && in_array(pathinfo($attachmentPath, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png'])) {
                $fullPhotoPath = storage_path('app/public/' . $attachmentPath);
                if (file_exists($fullPhotoPath)) {
                    $telegram->sendPhoto($fullPhotoPath, $message);
                } else {
                    $telegram->sendMessage($message);
                }
            } else {
                $telegram->sendMessage($message);
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengirim notifikasi Telegram tiket baru: ' . $e->getMessage());
        }

        return redirect()->route('tickets.index')->with('success', 'Tiket perbaikan berhasil dikirim.');
    }

    public function show($id)
    {
        $ticket = Ticket::with(['asset', 'user', 'bast.technician'])->findOrFail($id);
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Memperbarui informasi Pengerjaan (WORK) oleh Teknisi/Admin
     */
    public function updateWork(Request $request, $id)
    {
        // Validasi: target_completion_date wajib diisi KECUALI jika work_status bernilai 'Completed'
        $request->validate([
            'technician_name'        => 'required|string|max:255',
            'action_taken'           => 'required|string',
            'work_status'            => 'required|in:On Check,Pengajuan Sparepart,Completed',
            'target_completion_date' => 'required_unless:work_status,Completed|nullable|date',
        ]);

        $ticket = Ticket::findOrFail($id);

        // Ubah status utama menjadi 'Diproses' jika status pengerjaan masih berjalan
        $newMainStatus = ($request->work_status === 'Completed') ? $ticket->status : 'Diproses';

        $ticket->update([
            'technician_name'        => $request->technician_name,
            'action_taken'           => $request->action_taken,
            'target_completion_date' => $request->work_status === 'Completed' ? null : $request->target_completion_date,
            'work_status'            => $request->work_status,
            'status'                 => $newMainStatus,
        ]);

        return redirect()->back()->with('success', 'Progress pengerjaan (WORK) berhasil diperbarui.');
    }

    public function createBast($id)
    {
        $ticket = Ticket::findOrFail($id);
        
        // Mencegah akses ke form BAST jika BAST sudah terisi
        if ($ticket->bast) {
            return redirect()->route('tickets.show', $id)->with('info', 'BAST untuk tiket ini sudah diisi.');
        }

        return view('tickets.bast.create', compact('ticket'));
    }

    public function storeBast(Request $request, $id)
    {
        $request->validate([
            'action_taken'   => 'required|string',
            'parts_replaced' => 'nullable|string',
            'attachment'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // Max 5MB
        ]);

        $ticket = Ticket::findOrFail($id);

        // Handle Upload Lampiran Bukti BAST
        $attachmentPath = $ticket->bast->attachment ?? null;
        if ($request->hasFile('attachment')) {
            if ($attachmentPath && Storage::disk('public')->exists($attachmentPath)) {
                Storage::disk('public')->delete($attachmentPath);
            }
            $attachmentPath = $request->file('attachment')->store('basts/attachments', 'public');
        }

        // Menggunakan updateOrCreate untuk mencegah error UNIQUE constraint
        $ticket->bast()->updateOrCreate(
            ['ticket_id' => $ticket->id],
            [
                'technician_id'   => auth()->id(),
                'technician_name' => $request->technician_name ?? auth()->user()->name,
                'action_taken'    => $request->action_taken,
                'parts_replaced'  => $request->parts_replaced,
                'attachment'      => $attachmentPath ?? $ticket->bast->attachment ?? null,
            ]
        );

        // Status diubah menjadi 'Menunggu Konfirmasi'
        $ticket->update(['status' => 'Menunggu Konfirmasi']);

        return redirect()->route('tickets.show', $ticket->id)->with('success', 'Form BAST berhasil dibuat. Menunggu konfirmasi DONE dari user.');
    }

    /**
     * Konfirmasi Tiket Selesai (DONE) oleh User Pembuat Tiket / Admin
     */
    public function markAsDone(Request $request, $id, TelegramService $telegram)
    {
        $ticket = Ticket::findOrFail($id);

        // Cek Hak Akses: IT & MAINTENANCE Dilarang Menyelesaikan Tiket
        if (in_array(auth()->user()->role, ['IT', 'MAINTENANCE'])) {
            return redirect()->back()->with('error', 'Hanya Pelapor / Outlet yang berhak mengonfirmasi tiket Selesai (DONE).');
        }

        // Pastikan hanya pembuat tiket atau Admin yang bisa menekan DONE
        if ($ticket->user_id !== auth()->id() && auth()->user()->role !== 'ADMIN') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah status tiket ini.');
        }

        // Validasi input tanggal penyelesaian
        $request->validate([
            'completed_at' => 'nullable|date',
        ]);

        // Format tanggal penyelesaian dari modal atau default waktu sekarang
        $completedAt = $request->filled('completed_at') 
            ? Carbon::parse($request->completed_at) 
            : now();

        // Update Status Tiket dan Simpan Tanggal Selesai
        $ticket->update([
            'status'       => 'Selesai',
            'completed_at' => $completedAt,
        ]);

        // Jika BAST tersedia, simpan juga tanggal penyelesaian ke tabel BAST
        if ($ticket->bast) {
            $ticket->bast->update([
                'completed_at' => $completedAt,
            ]);
        }

        // --- KIRIM NOTIFIKASI TIKET SELESAI KE TELEGRAM ---
        try {
            $message  = "✅ <b>TIKET PERBAIKAN SELESAI</b>\n\n";
            $message .= "<b>No. Tiket:</b> #" . $ticket->ticket_number . "\n";
            $message .= "<b>Cabang:</b> " . $ticket->branch_name . "\n";
            $message .= "<b>Pelapor:</b> " . htmlspecialchars($ticket->reporter_name, ENT_QUOTES, 'UTF-8') . "\n";
            $message .= "<b>Status:</b> Selesai (DONE)\n";
            $message .= "<b>Tanggal Selesai:</b> " . $completedAt->format('d/m/Y H:i') . "\n";

            $telegram->sendMessage($message);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim notifikasi Telegram tiket selesai: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Tiket berhasil dikonfirmasi Selesai (DONE) pada tanggal ' . $completedAt->format('d/m/Y H:i') . '.');
    }
}