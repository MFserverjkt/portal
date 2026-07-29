@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Detail Form Tiket (Dibuat Sebelum BAST) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Form Tiket: {{ $ticket->ticket_number }}</h5>
            <span class="badge bg-{{ $ticket->department == 'IT' ? 'info' : 'warning' }}">{{ $ticket->department }}</span>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Pelapor:</strong> {{ $ticket->user->name }}</p>
                    <p><strong>Aset Terkait:</strong> {{ $ticket->asset->asset_name }} ({{ $ticket->asset->asset_code }})</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Prioritas:</strong> {{ $ticket->priority }}</p>
                    <p><strong>Status Tiket:</strong> {{ $ticket->status }}</p>
                </div>
            </div>
            <h6><strong>Judul Keluhan:</strong></h6>
            <p class="border p-2 bg-light rounded">{{ $ticket->title }}</p>
            <h6><strong>Deskripsi Kerusakan:</strong></h6>
            <p class="border p-2 bg-light rounded">{{ $ticket->description }}</p>
        </div>
    </div>

    <!-- Tampilan Form BAST (Otomatis Muncul Setelah BAST Diisi & Tiket DONE) -->
    @if($ticket->bast)
    <div class="card border-success shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-file-earmark-check"></i> Form Berita Acara Serah Terima (BAST)</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Teknisi Penanggung Jawab:</strong> {{ $ticket->bast->technician->name }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Waktu Penyelesaian:</strong> {{ \Carbon\Carbon::parse($ticket->bast->completed_at)->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <h6><strong>Tindakan Perbaikan:</strong></h6>
            <p class="border p-2 bg-light rounded">{{ $ticket->bast->action_taken }}</p>

            <h6><strong>Penggantian Sparepart / Suku Cadang:</strong></h6>
            <p class="border p-2 bg-light rounded">{{ $ticket->bast->parts_replaced ?? 'Tidak ada penggantian sparepart.' }}</p>
        </div>
    </div>
    @else
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-circle me-1"></i> Form BAST belum diisi oleh teknisi. Status tiket masih dalam antrean/pengerjaan.
    </div>
    @endif

    <div class="mt-3">
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection