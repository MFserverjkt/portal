@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="card border-0 shadow-sm col-md-9 mx-auto">
        <div class="card-header bg-success text-white py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Form Berita Acara Serah Terima (BAST)</h5>
        </div>
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <strong><i class="bi bi-exclamation-triangle-fill me-2"></i> Gagal menyimpan BAST:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('tickets.storeBast', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- 1. Nama Petugas yang Mengerjakan -->
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

                <!-- 2. Tindakan Perbaikan -->
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

                <!-- 3. Penggantian Sparepart / Suku Cadang -->
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

                <!-- 4. Unggah Bukti Pekerjaan / Lampiran BAST -->
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

                    @if(isset($ticket->bast->attachment))
                        <div class="mt-2 p-2 bg-light border rounded d-flex align-items-center justify-content-between">
                            <span class="small text-success fw-semibold">
                                <i class="bi bi-file-check-fill me-1"></i> File lampiran saat ini sudah terunggah.
                            </span>
                            <a href="{{ asset('storage/' . $ticket->bast->attachment) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-eye me-1"></i> Lihat File
                            </a>
                        </div>
                    @endif

                    @error('attachment')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex justify-content-between pt-3 border-top">
                    <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-secondary px-4">
                        <i class="bi bi-arrow-left me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="bi bi-check-circle me-1"></i> Simpan BAST
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection