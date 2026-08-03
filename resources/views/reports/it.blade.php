@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Report Corrective - IT</h3>
    <div class="d-flex gap-2">
        <!-- Tombol Export Excel -->
        <a href="{{ route('report.it.export') }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
        
        <!-- Tombol Cetak Report (Print) -->
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer me-1"></i> Cetak Report
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle mb-0">
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
                        
                        <!-- Kolom Pelapor Disesuaikan dengan Tampilan Tiket -->
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <span class="fw-semibold">{{ $ticket->user->name ?? '-' }}</span>
                                @if($ticket->user?->role)
                                    <span class="badge bg-secondary text-uppercase" style="font-size: 0.65rem;">
                                        {{ $ticket->user->role }}
                                    </span>
                                @endif
                            </div>

                            {{-- Tampilkan Info Cabang/Outlet jika ada --}}
                            @php
                                $branch = $ticket->user?->branch_name 
                                    ?? $ticket->user?->outlet_name 
                                    ?? $ticket->user?->branch_code 
                                    ?? $ticket->user?->outlet_code;
                            @endphp

                            @if($branch)
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $branch }}
                                </small>
                            @endif
                        </td>

                        <td>{{ $ticket->asset->asset_name ?? '-' }}</td>
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
                        <td colspan="6" class="text-center text-muted py-3">
                            <i class="bi bi-inbox d-block fs-3 mb-1"></i>
                            Belum ada laporan corrective IT.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection