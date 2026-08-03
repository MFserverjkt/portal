<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportCorrectiveExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tickets;

    public function __construct($tickets)
    {
        $this->tickets = $tickets;
    }

    public function collection()
    {
        return $this->tickets;
    }

    // Header kolom di Excel
    public function headings(): array
    {
        return [
            'No. Tiket',
            'Pelapor',
            'Aset',
            'Keluhan',
            'Status',
            'Teknisi BAST',
        ];
    }

    // Pemetaan data dari object Ticket ke baris Excel
    public function map($ticket): array
    {
        return [
            $ticket->ticket_number,
            $ticket->user->name ?? '-',
            $ticket->asset->asset_name ?? '-',
            $ticket->title,
            $ticket->status,
            $ticket->bast->technician->name ?? '-',
        ];
    }
}