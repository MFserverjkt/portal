@extends('layouts.app')

@section('content')
<div class="container-fluid py-2 px-2 px-md-3">

    <!-- Tombol Kembali -->
    <div class="mb-2">
        <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary btn-sm py-1 px-3 fs-7">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Alert Notifikasi Error Validasi Global -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3 py-2 px-3 fs-7" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Gagal menyimpan data:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <!-- Header Hitam Kompak -->
        <div class="card-header bg-dark text-white py-2 px-3 rounded-top-3">
            <h6 class="m-0 fw-bold d-flex align-items-center gap-2 fs-7">
                <i class="bi bi-plus-circle text-warning"></i> REGISTRASI ASET BARU
            </h6>
        </div>

        <div class="card-body p-3">
            <form action="{{ route('assets.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <!-- Asset ID -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">Asset ID (Kode Unik) <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            name="asset_code" 
                            class="form-control form-control-mobile @error('asset_code') is-invalid @enderror" 
                            placeholder="Contoh: AST-001 / LAP-2024-01" 
                            value="{{ old('asset_code') }}" 
                            required
                        >
                        <small class="text-muted d-block mt-1 fs-8">*Masukkan kode unik aset secara manual.</small>
                        @error('asset_code')
                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Asset Name -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">Asset Name (Nama Aset) <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            name="asset_name" 
                            class="form-control form-control-mobile @error('asset_name') is-invalid @enderror" 
                            placeholder="Contoh: Laptop / Oven" 
                            value="{{ old('asset_name') }}" 
                            required
                        >
                        @error('asset_name')
                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Rumpun Category -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">Rumpun Category <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            name="category" 
                            class="form-control form-control-mobile @error('category') is-invalid @enderror" 
                            placeholder="Contoh: Perangkat IT" 
                            value="{{ old('category') }}" 
                            required
                        >
                        @error('category')
                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Product Name (Brand) -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">Product Name (Brand)</label>
                        <input 
                            type="text" 
                            name="brand" 
                            class="form-control form-control-mobile @error('brand') is-invalid @enderror" 
                            placeholder="Contoh: ThinkPad X1" 
                            value="{{ old('brand') }}"
                        >
                        @error('brand')
                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Branch -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">Branch (Cabang Penempatan) <span class="text-danger">*</span></label>
                        <select name="branch_code" class="form-select form-control-mobile @error('branch_code') is-invalid @enderror" required>
                            <option value="" disabled {{ old('branch_code') ? '' : 'selected' }}>-- Pilih Cabang --</option>
                            
                            @php
                                $hasBranches = isset($branches) && (is_countable($branches) ? count($branches) > 0 : !empty($branches));
                                $listBranches = $hasBranches ? $branches : \DB::table('branches')->orderBy('name', 'asc')->get();
                            @endphp

                            @foreach($listBranches as $key => $branch)
                                @php
                                    $code = is_object($branch) ? ($branch->code ?? $key) : (is_array($branch) ? ($branch['code'] ?? $key) : $key);
                                    $name = is_object($branch) ? ($branch->name ?? '') : (is_array($branch) ? ($branch['name'] ?? $branch) : $branch);
                                @endphp
                                <option value="{{ $code }}" {{ old('branch_code') == $code ? 'selected' : '' }}>
                                    [{{ $code }}] {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_code')
                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Register Date -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">Register Date (Tanggal Beli) <span class="text-danger">*</span></label>
                        <input 
                            type="date" 
                            name="register_date" 
                            class="form-control form-control-mobile @error('register_date') is-invalid @enderror" 
                            value="{{ old('register_date', date('Y-m-d')) }}" 
                            required
                        >
                        @error('register_date')
                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status Awal -->
                    <div class="col-12 mb-2">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">Status Awal <span class="text-danger">*</span></label>
                        <select name="status" class="form-select form-control-mobile @error('status') is-invalid @enderror" required>
                            <option value="Bagus / Normal" {{ old('status', 'Bagus / Normal') == 'Bagus / Normal' ? 'selected' : '' }}>Bagus / Normal</option>
                            <option value="Rusak Ringan" {{ old('status') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak Berat" {{ old('status') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Fixed Floating Bottom Save Button untuk Handphone -->
                <div class="mobile-bottom-bar d-md-none">
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-lg fs-7">
                        <i class="bi bi-save me-1"></i> Simpan Aset Baru
                    </button>
                </div>

                <!-- Tombol Desktop/Tablet -->
                <div class="d-none d-md-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Aset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CSS Khusus Mengoptimalkan Tampilan Mobile -->
<style>
    /* Font Size Khusus HP */
    .fs-7 { font-size: 0.875rem !important; }
    .fs-8 { font-size: 0.775rem !important; }

    /* Touch Area Input Lebih Nyaman di HP */
    .form-control-mobile {
        padding: 0.55rem 0.75rem;
        font-size: 0.9rem;
        border-radius: 0.375rem;
    }

    /* Fixed Floating Save Button di Bagian Bawah Layar HP */
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
            padding-bottom: 70px; /* Space agar tidak tertutup tombol fixed */
        }
    }
</style>
@endsection