@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-success text-white py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Form Berita Acara Serah Terima (BAST)</h5>
        </div>
        <div class="card-body p-4">
            <!-- WAJIB: enctype="multipart/form-data" AGAR FILE BISA DIUPLOAD -->
            <form action="{{ route('tickets.storeBast', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- 1. NAMA PETUGAS YANG MENGERJAKAN -->
                <div class="mb-3">
                    <label for="technician_name" class="form-label fw-bold">
                        Nama Petugas yang Mengerjakan <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="technician_name" 
                           id="technician_name" 
                           class="form-control @error('technician_name') is-invalid @enderror" 
                           placeholder="Masukkan nama petugas / teknisi..." 
                           value="{{ old('technician_name', $ticket->bast->technician_name ?? auth()->user()->name ?? '') }}" 
                           required>
                    @error('technician_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 2. TINDAKAN PERBAIKAN -->
                <div class="mb-3">
                    <label for="action_taken" class="form-label fw-bold">
                        Tindakan Perbaikan <span class="text-danger">*</span>
                    </label>
                    <textarea name="action_taken" 
                              id="action_taken" 
                              class="form-control @error('action_taken') is-invalid @enderror" 
                              rows="3" 
                              placeholder="Jelaskan tindakan perbaikan yang dilakukan..." 
                              required>{{ old('action_taken', $ticket->bast->action_taken ?? '') }}</textarea>
                    @error('action_taken')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 3. PENGGANTIAN SPAREPART / SUKU CADANG (SEKARANG WAJIB) -->
                <div class="mb-3">
                    <label for="parts_replaced" class="form-label fw-bold">
                        Penggantian Sparepart / Suku Cadang <span class="text-danger">*</span>
                    </label>
                    <textarea name="parts_replaced" 
                              id="parts_replaced" 
                              class="form-control @error('parts_replaced') is-invalid @enderror" 
                              rows="2" 
                              placeholder="Sebutkan sparepart yang diganti (Isi '-' atau 'Tidak ada' jika tidak ada penggantian)..." 
                              required>{{ old('parts_replaced', $ticket->bast->parts_replaced ?? '') }}</textarea>
                    @error('parts_replaced')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 4. UNGGAH BUKTI PEKERJAAN / LAMPIRAN BAST (SEKARANG WAJIB) -->
                <div class="mb-4">
                    <label for="attachment" class="form-label fw-bold">
                        Unggah Bukti Pekerjaan / Lampiran BAST (Foto/PDF) <span class="text-danger">*</span>
                    </label>
                    <input type="file" 
                           name="attachment" 
                           id="attachment" 
                           class="form-control @error('attachment') is-invalid @enderror" 
                           accept="image/*,.pdf"
                           {{ isset($ticket->bast->attachment) ? '' : 'required' }}>
                    
                    <small class="text-muted d-block mt-1">
                        Format yang didukung: JPG, JPEG, PNG, PDF (Maksimal 5MB)
                    </small>

                    <!-- Menampilkan info file jika BAST sudah pernah dibuat/diunggah -->
                    @if(isset($ticket->bast->attachment))
                        <div class="mt-2 small text-success">
                            <i class="bi bi-file-check me-1"></i> File lampiran saat ini sudah terunggah.
                        </div>
                    @endif

                    @error('attachment')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- TOMBOL AKSI -->
                <div class="d-flex justify-content-between pt-2">
                    <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success fw-bold">
                        <i class="bi bi-box-arrow-down me-1"></i> Simpan BAST
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection