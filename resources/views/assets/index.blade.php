@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Inventori Aset (IT & Maintenance)</h3>
    <div>
        <!-- Tombol Export Excel -->
        <a href="{{ route('assets.export') }}" class="btn btn-success me-1">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>

        <!-- Tombol Import Excel (Membuka Modal) -->
        <button type="button" class="btn btn-outline-success me-1" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-arrow-up"></i> Import Excel
        </button>

        <!-- Tombol Tambah Aset -->
        <a href="{{ route('assets.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Aset
        </a>
    </div>
</div>

{{-- Notifikasi Sukses / Error --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Kode Aset</th>
                    <th>Nama Aset</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Kondisi</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                <tr>
                    <td><strong>{{ $asset->asset_code }}</strong></td>
                    <td>{{ $asset->asset_name }}</td>
                    <td><span class="badge bg-{{ $asset->category === 'IT' ? 'info' : 'warning' }}">{{ $asset->category }}</span></td>
                    <td>{{ $asset->location }}</td>
                    <td>
                        <span class="badge bg-{{ $asset->condition === 'Baik' ? 'success' : ($asset->condition === 'Rusak Ringan' ? 'warning' : 'danger') }}">
                            {{ $asset->condition }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus aset ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada data aset.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('assets.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="importModalLabel"><i class="bi bi-file-earmark-excel"></i> Import Data Aset</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih File Excel (.xlsx / .xls)</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                        <div class="form-text">Gunakan file Excel sesuai dengan format template yang ditentukan.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-upload"></i> Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection