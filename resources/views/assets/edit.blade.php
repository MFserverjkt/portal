@extends('layouts.app')

@section('content')
<div class="container-fluid py-2 px-2 px-md-3">

    <!-- Tombol Kembali -->
    <div class="mb-2">
        <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary btn-sm py-1 px-3 fs-7">
            <i class="bi bi-arrow-left me-1"></i> Batal / Kembali
        </a>
    </div>

    <!-- Alert Notifikasi Error Validasi Global -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3 py-2 px-3 fs-7" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Gagal memperbarui data:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <!-- Header Kuning Kompak -->
        <div class="card-header bg-warning text-dark py-2 px-3 rounded-top-3">
            <h6 class="m-0 fw-bold d-flex align-items-center gap-2 fs-7">
                <i class="bi bi-pencil-square"></i> EDIT DATA ASET
            </h6>
        </div>

        <div class="card-body p-3">
            <form action="{{ route('assets.update', $asset->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- Kode Aset (Readonly) -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">Kode Aset</label>
                        <input 
                            type="text" 
                            name="asset_code" 
                            value="{{ old('asset_code', $asset->asset_code) }}" 
                            class="form-control form-control-mobile bg-light @error('asset_code') is-invalid @enderror" 
                            required 
                            readonly
                        >
                        @error('asset_code')
                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nama Aset -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">Nama Aset <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            name="asset_name" 
                            value="{{ old('asset_name', $asset->asset_name) }}" 
                            class="form-control form-control-mobile @error('asset_name') is-invalid @enderror" 
                            required
                        >
                        @error('asset_name')
                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Kategori Aset -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">Kategori Aset <span class="text-danger">*</span></label>
                        <select name="category" class="form-select form-control-mobile @error('category') is-invalid @enderror" required>
                            <option value="IT" {{ old('category', $asset->category) == 'IT' ? 'selected' : '' }}>IT</option>
                            <option value="MAINTENANCE" {{ old('category', $asset->category) == 'MAINTENANCE' ? 'selected' : '' }}>MAINTENANCE</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Lokasi Aset -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">Lokasi Aset <span class="text-danger">*</span></label>
                        <select name="branch_code" class="form-select form-control-mobile @error('branch_code') is-invalid @enderror" required>
                            <option value="" disabled>-- Pilih Lokasi Cabang --</option>
                            
                            @php
                                $hasBranches = isset($branches) && (is_countable($branches) ? count($branches) > 0 : !empty($branches));
                                $listBranches = $hasBranches ? $branches : \DB::table('branches')->orderBy('name', 'asc')->pluck('name', 'code')->toArray();
                            @endphp

                            @foreach($listBranches as $code => $name)
                                <option value="{{ $code }}" {{ old('branch_code', $asset->branch_code ?? $asset->location) == $code ? 'selected' : '' }}>
                                    [{{ $code }}] {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_code')
                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Kondisi Aset -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">Kondisi Aset <span class="text-danger">*</span></label>
                        <select name="status" class="form-select form-control-mobile @error('status') is-invalid @enderror" required>
                            <option value="Baik" {{ old('status', $asset->status ?? $asset->condition) == 'Baik' || old('status', $asset->status ?? $asset->condition) == 'Bagus / Normal' ? 'selected' : '' }}>Baik</option>
                            <option value="Rusak Ringan" {{ old('status', $asset->status ?? $asset->condition) == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak Berat" {{ old('status', $asset->status ?? $asset->condition) == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Deskripsi / Spesifikasi -->
                    <div class="col-12 mb-2">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">Deskripsi / Spesifikasi (Opsional)</label>
                        <textarea name="description" class="form-control form-control-mobile @error('description') is-invalid @enderror" rows="3">{{ old('description', $asset->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Floating Bottom Bar Khusus Mobile -->
                <div class="mobile-bottom-bar d-md-none">
                    <div class="row g-2">
                        <div class="col-4">
                            <a href="{{ route('assets.index') }}" class="btn btn-secondary w-100 fw-bold py-2 fs-7">
                                Batal
                            </a>
                        </div>
                        <div class="col-8">
                            <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm fs-7">
                                <i class="bi bi-check-circle me-1"></i> Perbarui
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tombol Versi Desktop / Tablet -->
                <div class="d-none d-md-flex justify-content-between mt-4">
                    <a href="{{ route('assets.index') }}" class="btn btn-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-success px-4 fw-semibold">
                        <i class="bi bi-check-circle me-1"></i> Perbarui Aset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Styling Khusus Penyesuaian Mobile -->
<style>
    .fs-7 { font-size: 0.875rem !important; }
    .fs-8 { font-size: 0.775rem !important; }

    .form-control-mobile {
        padding: 0.55rem 0.75rem;
        font-size: 0.9rem;
        border-radius: 0.375rem;
    }

    @media (max-width: 767.98px) {
        .mobile-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            padding: 10px 15px;
            box-shadow: 0 -3px 10px rgba(0, 0, 0, 0.1);
            z-index: 1030;
            backdrop-filter: blur(5px);
        }
        body {
            padding-bottom: 75px;
        }
    }
</style>
@endsection