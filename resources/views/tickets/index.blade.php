@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Daftar Tiket Perbaikan (Corrective)</h3>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Buat Tiket Baru</a>
</div>

<!-- Alert Pesan Sukses / Error -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>No. Tiket</th>
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
                    <td><span class="badge bg-{{ $ticket->department == 'IT' ? 'info' : 'warning' }}">{{ $ticket->department }}</span></td>
                    <td>{{ $ticket->asset->asset_name ?? '-' }}</td>
                    <td>{{ $ticket->title }}</td>
                    <td>
                        @if($ticket->status === 'Selesai')
                            <span class="badge bg-success">DONE / Selesai</span>
                        @elseif($ticket->status === 'Menunggu Konfirmasi')
                            <span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>
                        @else
                            <span class="badge bg-danger">Terbuka</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            <!-- View Detail Tiket -->
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye"></i> View
                            </a>

                            <!-- 1. Tombol ISI BAST (Untuk Teknisi IT/MAINTENANCE/ADMIN jika BAST belum ada) -->
                            @if(!$ticket->bast)
                                @if(in_array(auth()->user()->role, ['ADMIN', 'IT', 'MAINTENANCE']))
                                    <a href="{{ route('tickets.createBast', $ticket->id) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-pencil-square"></i> Isi BAST
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-secondary" disabled title="Menunggu BAST Teknisi">DONE</button>
                                @endif
                            @endif

                            <!-- 2. Tombol DONE (Untuk User Pembuat Tiket / Admin saat status Menunggu Konfirmasi) -->
                            @if($ticket->status === 'Menunggu Konfirmasi')
                                @if(auth()->id() === $ticket->user_id || auth()->user()->role === 'ADMIN')
                                    <form action="{{ route('tickets.done', $ticket->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-success fw-bold" onclick="return confirm('Apakah pekerjaan perbaikan aset ini sudah selesai dan berfungsi dengan baik?')">
                                            <i class="bi bi-check-all"></i> DONE
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-secondary" disabled title="Hanya pembuat tiket yang dapat menyelesaikannya">Menunggu User</button>
                                @endif
                            @endif

                            <!-- 3. Status Tiket Sudah Selesai -->
                            @if($ticket->status === 'Selesai')
                                <button class="btn btn-sm btn-outline-success" disabled><i class="bi bi-check-all"></i> DONE</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">Belum ada data tiket perbaikan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection