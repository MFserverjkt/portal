@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="card border-0 shadow-sm col-md-8 mx-auto mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-ticket-perforated me-2"></i>Form Tiket Perbaikan Baru</h5>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <strong><i class="bi bi-exclamation-triangle-fill me-2"></i> Gagal mengirim tiket:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Informasi Pelapor Tiket (Input Teks Manual) -->
            <div class="mb-3">
                <label for="reporter_name" class="form-label fw-bold">Pelapor Tiket / Nama Staf <span class="text-danger">*</span></label>
                <input 
                    type="text" 
                    name="reporter_name" 
                    id="reporter_name" 
                    class="form-control @error('reporter_name') is-invalid @enderror" 
                    placeholder="Masukkan nama pelapor / staf..." 
                    value="{{ old('reporter_name', auth()->user()->name) }}" 
                    required
                >
                <small class="text-muted">Isi nama staf/personil yang melaporkan kendala ini.</small>
                @error('reporter_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Divisi Penanggung Jawab -->
            <div class="mb-3">
                <label class="form-label fw-bold">Divisi Penanggung Jawab (Tujuan Tiket) <span class="text-danger">*</span></label>
                <select name="department" class="form-select @error('department') is-invalid @enderror" required>
                    <option value="" disabled {{ old('department') ? '' : 'selected' }}>-- Pilih Divisi --</option>
                    <option value="IT" {{ old('department') == 'IT' ? 'selected' : '' }}>IT</option>
                    <option value="MAINTENANCE" {{ old('department') == 'MAINTENANCE' ? 'selected' : '' }}>MAINTENANCE</option>
                </select>
                @error('department')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Cabang / Outlet -->
            <div class="mb-3">
                <label class="form-label fw-bold">Cabang / Outlet <span class="text-danger">*</span></label>
                @if(auth()->user()->role === 'OUTLET')
                    <!-- Jika Outlet, kunci cabang sesuai profilnya -->
                    <input type="text" class="form-control bg-light" value="[{{ auth()->user()->branch_code }}] {{ auth()->user()->branch_name ?? 'Cabang Outlet' }}" readonly>
                    <input type="hidden" name="branch_code" value="{{ auth()->user()->branch_code }}">
                @else
                    <!-- Jika Admin / IT, berikan dropdown opsi semua cabang -->
                    <select name="branch_code" id="branch-select" class="form-select @error('branch_code') is-invalid @enderror" required>
                        <option value="">-- Pilih Cabang / Outlet --</option>
                        @if(isset($branches) && count($branches) > 0)
                            @foreach($branches as $code => $name)
                                <option value="{{ $code }}" {{ old('branch_code', auth()->user()->branch_code ?? '') == $code ? 'selected' : '' }}>
                                    [{{ $code }}] {{ $name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                @endif
                @error('branch_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Aset Kerusakan (Menampilkan Seluruh Aset Terdaftar) -->
            <div class="mb-3">
                <label class="form-label fw-bold">Pilih Aset Kerusakan <span class="text-danger">*</span></label>
                <select name="asset_id" id="asset-select" class="form-select @error('asset_id') is-invalid @enderror" required>
                    <option value="">-- Ketik / Cari Aset --</option>
                    @forelse($assets as $asset)
                        <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                            {{ $asset->asset_code }} - {{ $asset->asset_name }} {{ $asset->brand ? '('.$asset->brand.')' : '' }} [{{ $asset->branch_name ?? $asset->branch_code ?? 'Semua Cabang' }}]
                        </option>
                    @empty
                        <option value="" disabled>Tidak ada data aset terdaftar dalam sistem</option>
                    @endforelse
                </select>
                @error('asset_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Judul Keluhan -->
            <div class="mb-3">
                <label class="form-label fw-bold">Judul Keluhan / Kerusakan <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="Contoh: AC Mati Total / PC Hank" value="{{ old('title') }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Prioritas -->
            <div class="mb-3">
                <label class="form-label fw-bold">Prioritas <span class="text-danger">*</span></label>
                <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                    <option value="Rendah" {{ old('priority', 'Rendah') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
                    <option value="Sedang" {{ old('priority') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="Tinggi" {{ old('priority') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                </select>
                @error('priority')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Deskripsi Masalah -->
            <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi Masalah <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Jelaskan detail kendala/kerusakan aset..." required>{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- INPUT UPLOAD FOTO / LAMPIRAN KERUSAKAN -->
            <div class="mb-3">
                <label class="form-label fw-bold">Foto / Lampiran Kerusakan <span class="text-muted fw-normal">(Opsional)</span></label>
                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept="image/*,.pdf">
                <small class="text-muted d-block mt-1">Format yang diperbolehkan: JPG, PNG, PDF. Maksimal size 5MB.</small>
                @error('attachment')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tombol Navigasi -->
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary px-4 fw-bold">
                    <i class="bi bi-send me-1"></i> Kirim Tiket
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#asset-select').select2({
            placeholder: "-- Ketik untuk mencari aset (Kode / Nama / Brand / Cabang) --",
            allowClear: true,
            width: '100%'
        });

        if ($('#branch-select').length) {
            $('#branch-select').select2({
                placeholder: "-- Pilih Cabang / Outlet --",
                allowClear: true,
                width: '100%'
            });
        }
    });
</script>

<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        color: #212529 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
</style>
@endsection