@extends('layouts.app')

@section('content')
<div class="container">
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

    <!-- Detail Form Tiket (Dibuat Sebelum BAST) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Form Tiket: {{ $ticket->ticket_number ?? $ticket->ticket_code }}</h5>
            <span class="badge bg-{{ ($ticket->department ?? $ticket->division) == 'IT' ? 'info' : 'warning' }}">
                {{ $ticket->department ?? $ticket->division }}
            </span>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong>Pelapor:</strong> 
                        {{ $ticket->reporter_name ?? $ticket->user->name ?? 'User Tidak Ditemukan' }}
                        @if(isset($ticket->user->role))
                            <span class="badge bg-secondary ms-1">{{ $ticket->user->role }}</span>
                        @endif
                    </p>
                    <p class="mb-2">
                        <strong>Aset Terkait:</strong> 
                        {{ $ticket->asset->asset_name ?? '-' }} 
                        ({{ $ticket->asset->asset_code ?? '-' }})
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2"><strong>Prioritas:</strong> {{ $ticket->priority }}</p>
                    <p class="mb-2">
                        <strong>Status Tiket:</strong> 
                        @if($ticket->status === 'Selesai')
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Selesai (DONE)</span>
                        @elseif($ticket->status === 'Menunggu Konfirmasi')
                            <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i> Menunggu Konfirmasi</span>
                        @else
                            <span class="badge bg-danger"><i class="bi bi-exclamation-circle me-1"></i> Terbuka</span>
                        @endif
                    </p>
                    <p class="mb-2">
                        <strong>Cabang / Outlet:</strong> 
                        <span class="badge bg-light text-dark border ms-1">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                            {{ $ticket->branch_name ?? $ticket->asset->branch_name ?? $ticket->branch_code }} 
                            ({{ $ticket->branch_code ?? $ticket->asset->branch_code ?? '-' }})
                        </span>
                    </p>
                </div>
            </div>
            
            <h6><strong>Judul Keluhan:</strong></h6>
            <p class="border p-2 bg-light rounded">{{ $ticket->title }}</p>
            
            <h6><strong>Deskripsi Kerusakan:</strong></h6>
            <p class="border p-2 bg-light rounded">{{ $ticket->description }}</p>

            <!-- TAMPILAN BUKTI KERUSAKAN TIKET -->
            @if($ticket->attachment)
                <h6 class="mt-3"><strong>Bukti Lampiran Kerusakan:</strong></h6>
                <div class="border p-3 bg-light rounded">
                    @php 
                        $ext = pathinfo($ticket->attachment, PATHINFO_EXTENSION); 
                        $ticketFileUrl = asset('storage/' . $ticket->attachment);
                    @endphp

                    @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                        <div class="mb-2">
                            <a href="{{ $ticketFileUrl }}" target="_blank">
                                <img src="{{ $ticketFileUrl }}" class="img-fluid img-thumbnail rounded shadow-sm" style="max-height: 300px;" alt="Bukti Kerusakan">
                            </a>
                        </div>
                    @endif
                    <a href="{{ $ticketFileUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat / Unduh Lampiran Kerusakan ({{ strtoupper($ext) }})
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Tampilan Form BAST (Otomatis Muncul Setelah BAST Diisi) -->
    @if($ticket->bast)
    <div class="card border-success shadow-sm mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-file-earmark-check me-2"></i> Form Berita Acara Serah Terima (BAST)</h5>
            <span class="badge bg-light text-success fw-bold"><i class="bi bi-check-all"></i> BAST Tersedia</span>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-2"><strong>Teknisi Penanggung Jawab:</strong> {{ $ticket->bast->technician->name ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong>Waktu Penyelesaian:</strong> 
                        {{ $ticket->bast->completed_at ? \Carbon\Carbon::parse($ticket->bast->completed_at)->translatedFormat('d F Y H:i') : '-' }}
                    </p>
                </div>
            </div>
            
            <h6><strong>Tindakan Perbaikan:</strong></h6>
            <p class="border p-2 bg-light rounded">{{ $ticket->bast->action_taken }}</p>

            <h6><strong>Penggantian Sparepart / Suku Cadang:</strong></h6>
            <p class="border p-2 bg-light rounded">{{ $ticket->bast->parts_replaced ?? 'Tidak ada penggantian sparepart.' }}</p>

            <!-- TAMPILAN BUKTI FOTO / LAMPIRAN BAST -->
            <div class="mt-3">
                <h6><strong>Bukti Pengerjaan / Lampiran BAST:</strong></h6>
                @if($ticket->bast->attachment)
                    <div class="border p-3 bg-light rounded">
                        @php 
                            $bastExt = pathinfo($ticket->bast->attachment, PATHINFO_EXTENSION); 
                            $bastFileUrl = asset('storage/' . $ticket->bast->attachment);
                        @endphp

                        @if(in_array(strtolower($bastExt), ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                            <div class="mb-2">
                                <a href="{{ $bastFileUrl }}" target="_blank">
                                    <img src="{{ $bastFileUrl }}" class="img-fluid img-thumbnail rounded shadow-sm" style="max-height: 300px;" alt="Bukti BAST">
                                </a>
                            </div>
                        @endif
                        <a href="{{ $bastFileUrl }}" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Lihat / Unduh Lampiran BAST ({{ strtoupper($bastExt) }})
                        </a>
                    </div>
                @else
                    <p class="text-muted fst-italic border p-2 bg-light rounded mb-0">Tidak ada file/foto lampiran BAST yang diunggah.</p>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-warning mb-4">
        <i class="bi bi-exclamation-circle me-1"></i> Form BAST belum diisi oleh teknisi. Status tiket masih dalam antrean/pengerjaan.
    </div>
    @endif

    <!-- TOMBOL AKSI ALUR TIKET -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>

    </div>
</div>
@endsection