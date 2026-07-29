@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm col-md-8 mx-auto">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">Edit Data Aset</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('assets.update', $asset->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label fw-bold">Kode Aset</label>
                <input type="text" name="asset_code" value="{{ old('asset_code', $asset->asset_code) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Nama Aset</label>
                <input type="text" name="asset_name" value="{{ old('asset_name', $asset->asset_name) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Kategori Aset</label>
                <select name="category" class="form-select" required>
                    <option value="IT" {{ $asset->category == 'IT' ? 'selected' : '' }}>IT</option>
                    <option value="MAINTENANCE" {{ $asset->category == 'MAINTENANCE' ? 'selected' : '' }}>MAINTENANCE</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Lokasi Aset</label>
                <input type="text" name="location" value="{{ old('location', $asset->location) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Kondisi Aset</label>
                <select name="condition" class="form-select" required>
                    <option value="Baik" {{ $asset->condition == 'Baik' ? 'selected' : '' }}>Baik</option>
                    <option value="Rusak Ringan" {{ $asset->condition == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="Rusak Berat" {{ $asset->condition == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi / Spesifikasi (Opsional)</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $asset->description) }}</textarea>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('assets.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-success">Perbarui Aset</button>
            </div>
        </form>
    </div>
</div>
@endsection