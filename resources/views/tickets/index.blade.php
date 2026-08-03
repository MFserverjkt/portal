@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Daftar Tiket Perbaikan (Corrective)</h3>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Buat Tiket Baru
    </a>
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

<!-- TABEL DAFTAR TIKET -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>No. Tiket</th>
                        <th>Pelapor</th>
                        <th>Divisi Tujuan</th>
                        <th>Nama Aset</th>
                        <th>Keluhan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td><strong>{{ $ticket->ticket_number }}</strong></td>
                        
                        <!-- Kolom Pelapor & Lokasi Branch Tiket -->
                        <td>
                            <div class="fw-bold">
                                {{ $ticket->reporter_name ?? $ticket->user->name ?? '-' }}
                            </div>
                            
                            {{-- Tampilkan lokasi branch dari record TIKET terlebih dahulu --}}
                            @php
                                $branchInfo = $ticket->branch_code 
                                    ?? $ticket->branch_name 
                                    ?? $ticket->user?->branch_code 
                                    ?? $ticket->user?->branch_name;
                            @endphp

                            @if($branchInfo)
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-geo-alt me-1"></i>[{{ $branchInfo }}]
                                </small>
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-warning text-dark text-uppercase">
                                {{ $ticket->department }}
                            </span>
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
                        <td>
                            <div class="d-flex gap-1">
                                <!-- Tombol View Detail -->
                                <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-info btn-sm text-white" title="Lihat Detail">
                                    <i class="bi bi-eye"></i> View
                                </a>

                                <!-- Tombol BAST (Muncul jika BAST belum ada & tiket belum selesai) -->
                                @if(!$ticket->bast && in_array(auth()->user()->role, ['ADMIN', 'IT', 'MAINTENANCE']) && !in_array($ticket->status, ['Selesai', 'Selesai (DONE)']))
                                    <a href="{{ route('tickets.bast.create', $ticket->id) }}" class="btn btn-primary btn-sm" title="Buat BAST">
                                        <i class="bi bi-file-earmark-text"></i> BAST
                                    </a>
                                @endif

                                <!-- Tombol DONE hanya untuk Pelapor Tiket (OUTLET/Pembuat Tiket) atau ADMIN -->
                                <!-- Role IT & MAINTENANCE sengaja dikecualikan -->
                                @if($ticket->status === 'Menunggu Konfirmasi' && ($ticket->user_id === auth()->id() || auth()->user()->role === 'ADMIN') && !in_array(auth()->user()->role, ['IT', 'MAINTENANCE']))
                                    <form action="{{ route('tickets.markAsDone', $ticket->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Apakah Anda yakin masalah sudah terselesaikan?')" title="Konfirmasi Selesai">
                                            <i class="bi bi-check-circle me-1"></i> DONE
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">
                            <i class="bi bi-inbox d-block fs-3 mb-1"></i>
                            Tidak ada data tiket terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection