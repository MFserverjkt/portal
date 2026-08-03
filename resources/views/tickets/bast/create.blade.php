@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Form Berita Acara Serah Terima (BAST)</h5>
        </div>
        <div class="card-body">
            <!-- WAJIB: enctype="multipart/form-data" AGAR FILE BISA DIUPLOAD -->
            <form action="{{ route('tickets.storeBast', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="action_taken" class="form-label fw-bold">Tindakan Perbaikan <span class="text-danger">*</span></label>
                    <textarea name="action_taken" id="action_taken" class="form-class form-control @error('action_taken') is-invalid @enderror" rows="3" placeholder="Jelaskan tindakan perbaikan yang dilakukan..." required>{{ old('action_taken', $ticket->bast->action_taken ?? '') }}</textarea>
                    @error('action_taken')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="parts_replaced" class="form-label fw-bold">Penggantian Sparepart / Suku Cadang (Opsional)</label>
                    <textarea name="parts_replaced" id="parts_replaced" class="form-control @error('parts_replaced') is-invalid @enderror" rows="2" placeholder="Sebutkan sparepart yang diganti (jika ada)...">{{ old('parts_replaced', $ticket->bast->parts_replaced ?? '') }}</textarea>
                    @error('parts_replaced')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- INPUT FILE / LAMPIRAN BAST -->
                <div class="mb-4">
                    <label for="attachment" class="form-label fw-bold">Unggah Bukti Pekerjaan / Lampiran BAST (Foto/PDF)</label>
                    <input type="file" name="attachment" id="attachment" class="form-control @error('attachment') is-invalid @enderror" accept="image/*,.pdf">
                    <small class="text-muted">Format yang didukung: JPG, JPEG, PNG, PDF (Maksimal 5MB)</small>
                    @error('attachment')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan BAST
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection