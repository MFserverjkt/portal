@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm col-md-8 mx-auto">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">Form BAST - Pelaporan Perbaikan Selesai</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('tickets.bast.store', $ticket->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Nomor Tiket</label>
                <input type="text" class="form-control" value="{{ $ticket->ticket_number }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Tindakan Perbaikan</label>
                <textarea name="action_taken" class="form-control" rows="4" placeholder="Jelaskan tindakan teknis yang telah dilakukan..." required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Penggantian Sparepart (Opsional)</label>
                <input type="text" name="parts_replaced" class="form-control" placeholder="Contoh: RAM 8GB / Compressor AC">
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-success">Simpan BAST & Selesaikan Tiket (DONE)</button>
            </div>
        </form>
    </div>
</div>
@endsection