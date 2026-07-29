@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Report Corrective - IT</h3>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer"></i> Cetak Report</button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>No. Tiket</th>
                    <th>Pelapor</th>
                    <th>Aset</th>
                    <th>Keluhan</th>
                    <th>Status</th>
                    <th>Teknisi BAST</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td><strong>{{ $ticket->ticket_number }}</strong></td>
                    <td>{{ $ticket->user->name }}</td>
                    <td>{{ $ticket->asset->asset_name }}</td>
                    <td>{{ $ticket->title }}</td>
                    <td>
                        <span class="badge bg-{{ $ticket->status === 'Selesai' ? 'success' : 'danger' }}">
                            {{ $ticket->status }}
                        </span>
                    </td>
                    <td>{{ $ticket->bast->technician->name ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada laporan corrective IT.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection