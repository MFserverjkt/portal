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
                        
                        <!-- Kolom Pelapor Disesuaikan -->
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                {{-- Prioritaskan nama pelapor manual (reporter_name) --}}
                                <span class="fw-semibold">
                                    {{ $ticket->reporter_name ?? $ticket->user->name ?? '-' }}
                                </span>
                                
                                @if($ticket->user?->role)
                                    <span class="badge bg-secondary text-uppercase" style="font-size: 0.65rem;">
                                        {{ $ticket->user->role }}
                                    </span>
                                @endif
                            </div>

                            {{-- Prioritaskan branch_code dari tiket --}}
                            @php
                                $branchInfo = $ticket->branch_code ?? $ticket->user?->branch_code;
                            @endphp

                            @if($branchInfo)
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-geo-alt me-1"></i>[{{ $branchInfo }}]
                                </small>
                            @endif
                        </td>

                        <td>{{ $ticket->asset->asset_name ?? '-' }}</td>
                        <td>{{ $ticket->title }}</td>
                        <td>
                            @php
                                $statusBadge = 'secondary';
                                if ($ticket->status === 'Terbuka') {
                                    $statusBadge = 'danger';
                                } elseif ($ticket->status === 'Menunggu Konfirmasi') {
                                    $statusBadge = 'warning text-dark';
                                } elseif (in_array($ticket->status, ['Selesai', 'Selesai (DONE)'])) {
                                    $statusBadge = 'success';
                                }
                            @endphp
                            <span class="badge bg-{{ $statusBadge }}">
                                {{ $ticket->status }}
                            </span>
                        </td>
                        <td>{{ $ticket->bast->technician->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
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