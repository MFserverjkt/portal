@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Report Corrective - MAINTENANCE</h3>
    <div class="d-flex gap-2">
        <!-- Tombol Export Excel (membawa query parameter branch) -->
        <a href="{{ route('report.maintenance.export', ['branch' => request('branch')]) }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
        
        <!-- Tombol Cetak Report (Print) -->
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer me-1"></i> Cetak Report
        </button>
    </div>
</div>

<!-- FILTER BOX BRANCH -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ url()->current() }}" class="row g-2 align-items-center">
            <div class="col-md-4 col-sm-6">
                <label for="branch" class="form-label small fw-bold mb-1">Filter Branch / Cabang</label>
                <select name="branch" id="branch" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Branch --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch }}" {{ request('branch') == $branch ? 'selected' : '' }}>
                            [{{ $branch }}]
                        </option>
                    @endforeach
                </select>
            </div>
            
            @if(request('branch'))
            <div class="col-auto mt-4">
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle me-1"></i> Reset
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle mb-0" style="font-size: 0.875rem;">
                <thead class="table-dark text-nowrap">
                    <tr>
                        <th>No. Tiket</th>
                        <th>Tanggal Tiket</th>
                        <th>Tanggal BAST</th>
                        <th>Tanggal Done</th>
                        <th>Pelapor</th>
                        <th>Branch</th>
                        <th>Nomor Asset</th>
                        <th>Aset</th>
                        <th>Keluhan</th>
                        <th>Tindakan Perbaikan</th>
                        <th>Penggantian Sparepart</th>
                        <th>Status</th>
                        <th>Teknisi BAST</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <!-- 1. No. Tiket -->
                        <td><strong>{{ $ticket->ticket_number }}</strong></td>
                        
                        <!-- 2. Tanggal Tiket -->
                        <td class="text-nowrap">{{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-' }}</td>
                        
                        <!-- 3. Tanggal BAST -->
                        <td class="text-nowrap">{{ $ticket->bast?->created_at ? $ticket->bast->created_at->format('d/m/Y H:i') : '-' }}</td>
                        
                        <!-- 4. Tanggal Done -->
                        <td class="text-nowrap">
                            {{ $ticket->completed_at ? \Carbon\Carbon::parse($ticket->completed_at)->format('d/m/Y H:i') : '-' }}
                        </td>
                        
                        <!-- 5. Pelapor -->
                        <td>
                            <span class="fw-semibold">
                                {{ $ticket->reporter_name ?? $ticket->user->name ?? '-' }}
                            </span>
                        </td>

                        <!-- 6. Branch -->
                        <td>{{ $ticket->branch_name ?? $ticket->branch_code ?? $ticket->user?->branch_code ?? '-' }}</td>

                        <!-- 7. Nomor Asset -->
                        <td>{{ $ticket->asset->asset_code ?? $ticket->asset->code ?? '-' }}</td>

                        <!-- 8. Aset -->
                        <td>{{ $ticket->asset->asset_name ?? $ticket->asset->name ?? '-' }}</td>
                        
                        <!-- 9. Keluhan -->
                        <td>{{ $ticket->title }}</td>

                        <!-- 10. Tindakan Perbaikan -->
                        <td>{{ $ticket->bast->action_taken ?? '-' }}</td>

                        <!-- 11. Penggantian Sparepart -->
                        <td>{{ $ticket->bast->parts_replaced ?? '-' }}</td>

                        <!-- 12. Status -->
                        <td class="text-nowrap">
                            @php
                                $statusBadge = 'secondary';
                                if (in_array($ticket->status, ['Terbuka', 'open'])) {
                                    $statusBadge = 'danger';
                                } elseif (in_array($ticket->status, ['Menunggu Konfirmasi', 'pending', 'in_progress'])) {
                                    $statusBadge = 'warning text-dark';
                                } elseif (in_array($ticket->status, ['Selesai', 'Selesai (DONE)', 'closed', 'resolved'])) {
                                    $statusBadge = 'success';
                                }
                            @endphp
                            <span class="badge bg-{{ $statusBadge }}">
                                {{ $ticket->status }}
                            </span>
                        </td>

                        <!-- 13. Teknisi BAST -->
                        <td>{{ $ticket->bast->technician_name ?? $ticket->bast->technician->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center text-muted py-4">
                            <i class="bi bi-inbox d-block fs-3 mb-1"></i>
                            Belum ada laporan corrective Maintenance.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection