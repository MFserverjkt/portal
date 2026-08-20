@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- Tombol Kembali -->
    <div class="mb-3">
        <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> &larr; Kembali
        </a>
    </div>

    <!-- Alert Notifikasi Error Validasi Global -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i> Gagal menyimpan data:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <!-- Header Hitam -->
        <div class="card-header bg-dark text-white py-3">
            <h6 class="m-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> FORMULIR REGISTRASI ASET BARU
            </h6>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('assets.store') }}" method="POST">
                @csrf

                <!-- Baris 1: Asset ID, Asset Name, Rumpun Category -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Asset ID (Kode Unik)</label>
                        <input type="text" class="form-control bg-light" value="Otomatis oleh Sistem" disabled readonly>
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">*Kode akan ter-generate otomatis saat disimpan.</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Asset Name (Nama Aset) <span class="text-danger">*</span></label>
                        <input type="text" name="asset_name" class="form-control @error('asset_name') is-invalid @enderror" placeholder="Contoh: Laptop / Oven" value="{{ old('asset_name') }}" required>
                        @error('asset_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Rumpun Category <span class="text-danger">*</span></label>
                        <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" placeholder="Contoh: Perangkat IT" value="{{ old('category') }}" required>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Baris 2: Product Name (Brand), Branch, Register Date -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Product Name (Brand)</label>
                        <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" placeholder="Contoh: ThinkPad X1" value="{{ old('brand') }}">
                        @error('brand')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Branch (Cabang Penempatan) <span class="text-danger">*</span></label>
                        <select name="branch_code" class="form-select @error('branch_code') is-invalid @enderror" required>
                            <option value="" disabled {{ old('branch_code') ? '' : 'selected' }}>-- Pilih Cabang Penempatan --</option>
                            
                            @php
                                // Pengecekan ketat: Jika $branches tidak ada atau nilainya kosong ([] / null / empty Collection)
                                $hasBranches = isset($branches) && (is_countable($branches) ? count($branches) > 0 : !empty($branches));
                                
                                // Ambil langsung dari tabel 'branches' jika $branches dari Controller kosong
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
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Register Date (Tanggal Beli) <span class="text-danger">*</span></label>
                        <input type="date" name="register_date" class="form-control @error('register_date') is-invalid @enderror" value="{{ old('register_date', date('Y-m-d')) }}" required>
                        @error('register_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Baris 3: Status Awal -->
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-dark">Status Awal <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Bagus / Normal" {{ old('status', 'Bagus / Normal') == 'Bagus / Normal' ? 'selected' : '' }}>Bagus / Normal</option>
                            <option value="Rusak Ringan" {{ old('status') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak Berat" {{ old('status') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Aset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection