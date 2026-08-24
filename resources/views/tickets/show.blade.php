@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    {{-- Alert Success / Error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- CARD DETAIL TIKET UTAMA --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-2">
            <h5 class="mb-0 fw-bold fs-6">Form Tiket: {{ $ticket->ticket_number }}</h5>
            <span class="badge bg-info text-uppercase px-2 py-1">{{ $ticket->department }}</span>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <p class="mb-2"><strong>Pelapor:</strong> {{ $ticket->reporter_name ?? $ticket->user->name ?? '-' }} <span class="badge bg-secondary text-uppercase ms-1">{{ $ticket->user->role ?? 'USER' }}</span></p>
                    <p class="mb-0">
                        <strong>Aset Terkait:</strong> 
                        {{ $ticket->asset->asset_name ?? '-' }} 
                        @if(isset($ticket->asset->asset_code))
                            ({{ $ticket->asset->asset_code }})
                        @endif
                    </p>
                </div>
                <div class="col-md-6 text-md-start">
                    <p class="mb-2"><strong>Prioritas:</strong> {{ $ticket->priority ?? 'Rendah' }}</p>
                    <p class="mb-2">
                        <strong>Status Tiket:</strong> 
                        @php
                            $statusBadge = 'danger';
                            if (in_array($ticket->status, ['Diproses', 'On Progress'])) {
                                $statusBadge = 'warning text-dark';
                            } elseif ($ticket->status === 'Menunggu Konfirmasi') {
                                $statusBadge = 'info text-dark';
                            } elseif (in_array($ticket->status, ['Selesai', 'Completed'])) {
                                $statusBadge = 'success';
                            }
                        @endphp
                        <span class="badge bg-{{ $statusBadge }}">
                            <i class="bi bi-info-circle me-1"></i>{{ $ticket->status }}
                        </span>
                    </p>
                    <p class="mb-0">
                        <strong>Cabang / Outlet:</strong> 
                        <span class="badge bg-light text-dark border ms-1">
                            <i class="bi bi-geo-alt text-danger me-1"></i>{{ $ticket->branch->name ?? $ticket->branch_name ?? $ticket->branch_code ?? '-' }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold mb-1">Judul Keluhan:</label>
                <input type="text" class="form-control bg-light" value="{{ $ticket->title }}" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold mb-1">Deskripsi Kerusakan:</label>
                <textarea class="form-control bg-light" rows="3" readonly>{{ $ticket->description }}</textarea>
            </div>

            {{-- BAGIAN LAMPIRAN FOTO TIKET --}}
            @php
                $ticketPhoto = $ticket->attachment ?? $ticket->image ?? $ticket->photo ?? $ticket->file ?? null;
            @endphp

            <div class="mb-2">
                <label class="form-label fw-bold mb-1">Lampiran / Foto Kerusakan:</label>
                <div>
                    @if($ticketPhoto)
                        @php
                            $photoUrl = Str::startsWith($ticketPhoto, 'http') 
                                ? $ticketPhoto 
                                : asset('storage/' . $ticketPhoto);
                        @endphp
                        <div class="d-inline-block position-relative">
                            <a href="{{ $photoUrl }}" target="_blank">
                                <img src="{{ $photoUrl }}" alt="Foto Kerusakan" class="img-thumbnail rounded shadow-sm" style="max-height: 250px; object-fit: cover;">
                            </a>
                            <div class="mt-1 small text-muted">
                                <i class="bi bi-zoom-in me-1"></i>Klik gambar untuk memperbesar
                            </div>
                        </div>
                    @else
                        <div class="p-3 bg-light text-muted border rounded">
                            <i class="bi bi-image me-1"></i> Tidak ada lampiran foto yang diunggah saat pembuatan tiket.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MANAJEMEN DATA WORK & BAST --}}
    @php
        $technicianName = $ticket->technician_name ?? $ticket->bast->technician_name ?? $ticket->bast->user->name ?? null;
        $actionTaken    = $ticket->action_taken ?? $ticket->bast->action_taken ?? $ticket->bast->notes ?? null;
        $targetDate     = $ticket->target_completion_date ?? ($ticket->bast ? $ticket->bast->created_at : null);
        $workStatus     = $ticket->work_status ?? ($ticket->bast ? 'Completed' : $ticket->status);
    @endphp

    {{-- KARTU TAMPILAN SHOW WORK --}}
    @if(!empty($technicianName) || !empty($actionTaken) || !empty($ticket->work_status) || $ticket->bast)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
            <h6 class="mb-0 fw-bold"><i class="bi bi-tools me-2"></i>Informasi Pengerjaan Teknisi (WORK)</h6>
            <span class="badge bg-light text-primary fw-bold text-uppercase">Progress Perbaikan</span>
        </div>
        <div class="card-body bg-light">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Petugas / Teknisi Penanggung Jawab:</label>
                    <div class="fw-bold text-dark fs-6">{{ $technicianName ?? '-' }}</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Tanggal Target Selesai / Pengerjaan:</label>
                    <div class="fw-bold text-dark fs-6">
                        <i class="bi bi-calendar-event me-1 text-primary"></i>
                        {{ $targetDate ? \Carbon\Carbon::parse($targetDate)->format('d F Y') : '-' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Status Pengerjaan (WORK):</label>
                    <div>
                        <span class="badge bg-{{ in_array($workStatus, ['Completed', 'Selesai']) ? 'success' : 'warning text-dark' }} fs-6">
                            {{ $workStatus ?? 'On Check' }}
                        </span>
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <label class="form-label small fw-bold text-muted mb-1">Tindakan Pengerjaan / Analisa Perbaikan:</label>
                    <div class="p-3 bg-white rounded border text-dark" style="min-height: 70px;">
                        {!! nl2br(e($actionTaken ?? 'Belum ada catatan tindakan.')) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- KARTU TAMPILAN FORM BAST (MUNCUL SAAT BAST SUDAH DIINPUT) --}}
    @if($ticket->bast)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center py-2">
            <h6 class="mb-0 fw-bold"><i class="bi bi-file-earmark-text me-2"></i>Form Berita Acara Serah Terima (BAST) - [Tersimpan]</h6>
            <span class="badge bg-success text-uppercase">Terverifikasi</span>
        </div>
        <div class="card-body bg-light">
            <div class="row g-3">
                <div class="col-md-6">
                    <p class="mb-2"><strong>No. BAST:</strong> {{ $ticket->bast->bast_number ?? 'BAST-'.$ticket->ticket_number }}</p>
                    <p class="mb-2">
                        <strong>Tanggal Serah Terima:</strong> 
                        {{ \Carbon\Carbon::parse($ticket->bast->created_at)->format('d F Y') }}
                    </p>
                    <p class="mb-2"><strong>Diserahkan Oleh (Teknisi):</strong> {{ $ticket->bast->technician_name ?? $technicianName ?? '-' }}</p>
                    <p class="mb-0"><strong>Diterima Oleh (Outlet/Cabang):</strong> {{ $ticket->bast->recipient_name ?? $ticket->reporter_name ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong>Pekerjaan Selesai:</strong> 
                        <span class="text-success fw-bold"><i class="bi bi-check-lg"></i> Sesuai Keluhan</span>
                    </p>
                    <p class="mb-2">
                        <strong>Kondisi Setelah Perbaikan:</strong> 
                        {{ $ticket->asset->asset_name ?? 'Aset' }} <strong>Baik/Normal</strong>
                    </p>
                    <p class="mb-0">
                        <strong>Catatan Tambahan:</strong> 
                        {{ $ticket->bast->notes ?? 'Semua fungsi berjalan normal.' }}
                    </p>
                </div>

                {{-- SECTION LAMPIRAN FOTO BAST & TANDA TANGAN DIGITAL --}}
                <div class="col-12 mt-3 pt-3 border-top">
                    <div class="row g-3">
                        {{-- Foto Bukti Perbaikan BAST --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted mb-2">Lampiran Foto / Bukti Perbaikan:</label>
                            @php
                                $bastPhoto = $ticket->bast->documentation ?? $ticket->bast->attachment ?? $ticket->bast->photo ?? $ticket->bast->image ?? null;
                            @endphp
                            <div>
                                @if($bastPhoto)
                                    @php
                                        $bastPhotoUrl = Str::startsWith($bastPhoto, 'http') ? $bastPhoto : asset('storage/' . $bastPhoto);
                                    @endphp
                                    <div class="d-inline-block">
                                        <a href="{{ $bastPhotoUrl }}" target="_blank">
                                            <img src="{{ $bastPhotoUrl }}" alt="Foto BAST" class="img-thumbnail rounded shadow-sm" style="max-height: 180px; object-fit: cover;">
                                        </a>
                                        <div class="mt-1 small text-muted"><i class="bi bi-zoom-in me-1"></i>Klik gambar untuk memperbesar</div>
                                    </div>
                                @else
                                    <div class="p-3 bg-white text-muted border rounded small">
                                        <i class="bi bi-image me-1"></i> Tidak ada lampiran foto pada BAST.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @else
        {{-- ALERT NOTIFIKASI BAST JIKA BELUM DIISI --}}
        <div class="alert alert-warning border-warning d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-circle fs-5 me-2"></i>
            <div>
                Form BAST belum diisi oleh teknisi. Status tiket masih dalam antrean/pengerjaan.
            </div>
        </div>
    @endif

    {{-- NAVIGASI TOMBOL UTAMA --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary px-4">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>

        @if(!$ticket->bast && in_array(auth()->user()->role, ['ADMIN', 'IT', 'MAINTENANCE']) && !in_array($ticket->status, ['Selesai', 'Selesai (DONE)']))
            <a href="{{ route('tickets.bast.create', $ticket->id) }}" class="btn btn-warning fw-bold px-4">
                <i class="bi bi-pencil-square me-1"></i> Isi Form BAST
            </a>
        @endif
    </div>
</div>
@endsection