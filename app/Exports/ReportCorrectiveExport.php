<?php

namespace App\Exports;

use App\Models\Ticket;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportCorrectiveExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $branch;
    protected $department;

    /**
     * Menerima parameter filter dari Controller
     * 
     * @param string|null $branch
     * @param string|null $department (Contoh: 'IT' atau 'MAINTENANCE')
     */
    public function __construct($branch = null, $department = null)
    {
        $this->branch = $branch;
        $this->department = $department;
    }

    public function query()
    {
        // Eager loading relasi agar query cepat & efisien
        $query = Ticket::query()->with(['user', 'asset', 'bast', 'bast.technician']);

        // Filter berdasarkan Branch / Cabang jika dipilih
        if ($this->branch) {
            $query->where(function ($q) {
                $q->where('branch_code', $this->branch)
                  ->orWhere('branch_name', $this->branch)
                  ->orWhereHas('user', function ($u) {
                      $u->where('branch_code', $this->branch);
                  });
            });
        }

        // Filter berdasarkan Department (misal: IT / MAINTENANCE)
        if ($this->department) {
            $query->where('department', $this->department);
        }

        return $query->latest();
    }

    // 1. HEADER KOLOM
    public function headings(): array
    {
        return [
            'No. Tiket',
            'Tanggal Tiket',
            'Tanggal BAST',
            'Tanggal Done',
            'Pelapor',
            'Branch',
            'Nomor Asset',
            'Aset',
            'Keluhan',
            'Tindakan Perbaikan',
            'Penggantian Sparepart',
            'Status',
            'Teknisi BAST',
        ];
    }

    // 2. MAPPING DATA KOLOM
    public function map($ticket): array
    {
        // Logika / Fallback Tanggal Done
        $tanggalDone = '-';
        if ($ticket->completed_at) {
            $tanggalDone = Carbon::parse($ticket->completed_at)->format('d/m/Y H:i');
        } elseif ($ticket->bast?->completed_at) {
            $tanggalDone = Carbon::parse($ticket->bast->completed_at)->format('d/m/Y H:i');
        } elseif (in_array(strtolower($ticket->status), ['done', 'selesai', 'closed', 'resolved']) && $ticket->updated_at) {
            $tanggalDone = $ticket->updated_at->format('d/m/Y H:i');
        }

        return [
            $ticket->ticket_number,
            
            // Tanggal Tiket
            $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-',
            
            // Tanggal BAST
            $ticket->bast?->created_at ? $ticket->bast->created_at->format('d/m/Y H:i') : '-',
            
            // Tanggal Done
            $tanggalDone,
            
            // Pelapor (Prioritas: reporter_name manual -> user->name)
            $ticket->reporter_name ?? $ticket->user?->name ?? '-',
            
            // Branch (Prioritas: branch_name -> branch_code -> user branch_code)
            $ticket->branch_name ?? $ticket->branch_code ?? $ticket->user?->branch_code ?? '-',
            
            // Nomor Asset
            $ticket->asset?->asset_code ?? $ticket->asset?->code ?? '-',
            
            // Nama Aset
            $ticket->asset?->asset_name ?? $ticket->asset?->name ?? '-',
            
            // Keluhan
            $ticket->title ?? $ticket->description ?? '-',
            
            // Tindakan Perbaikan
            $ticket->bast?->action_taken ?? '-',
            
            // Penggantian Sparepart
            $ticket->bast?->parts_replaced ?? '-',
            
            // Status Tiket
            ucfirst($ticket->status),
            
            // Teknisi BAST (Prioritas: technician_name manual -> relasi technician->name)
            $ticket->bast?->technician_name ?? $ticket->bast?->technician?->name ?? '-',
        ];
    }

    // 3. STYLING HEADER EXCEL
    public function styles(Worksheet $sheet)
    {
        return [
            // Cetak tebal (Bold) pada baris header pertama
            1 => ['font' => ['bold' => true]],
        ];
    }
}