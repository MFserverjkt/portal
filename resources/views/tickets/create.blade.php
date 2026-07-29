@extends('layouts.app')

@section('content')
<!-- 1. Tambahkan CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="card border-0 shadow-sm col-md-8 mx-auto">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Form Tiket Perbaikan Baru</h5>
    </div>
    <div class="card-body">
        <!-- Alert Notifikasi Error Validasi Global -->
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

        <form action="{{ route('tickets.store') }}" method="POST">
            @csrf

            <!-- Kolom Pembeda IT / MAINTENANCE -->
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

            <!-- Pilih Aset Kerusakan (Diubah ID-nya untuk Select2) -->
            <div class="mb-3">
                <label class="form-label fw-bold">Pilih Aset Kerusakan <span class="text-danger">*</span></label>
                <!-- Tambahkan ID "asset-select" -->
                <select name="asset_id" id="asset-select" class="form-select @error('asset_id') is-invalid @enderror" required>
                    <option value="">-- Ketik / Cari Aset --</option>
                    @forelse($assets as $asset)
                        <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                            {{ $asset->asset_code }} - {{ $asset->asset_name }} ({{ $asset->brand ?? $asset->branch_name }})
                        </option>
                    @empty
                        <option value="" disabled>Tidak ada aset terdaftar pada cabang Anda</option>
                    @endforelse
                </select>
                @error('asset_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Judul Keluhan / Kerusakan -->
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

            <!-- Tombol Navigasi -->
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary px-4 fw-bold">Kirim Tiket</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Tambahkan Library jQuery & JavaScript Select2 -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- 3. Inisialisasi Select2 pada ID asset-select -->
<script>
    $(document).ready(function() {
        $('#asset-select').select2({
            placeholder: "-- Ketik untuk mencari aset (Kode / Nama / Brand) --",
            allowClear: true,
            width: '100%' // Menyesuaikan lebar dengan framework Bootstrap
        });
    });
</script>

<style>
    /* Sedikit penyesuaian CSS agar tinggi Select2 sama dengan input form Bootstrap */
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