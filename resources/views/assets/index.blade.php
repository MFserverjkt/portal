@extends('layouts.app')

@section('content')
<div class="container-fluid px-2 px-md-3 py-2">
    {{-- FORM BUNGKUS UNTUK BULK DELETE --}}
    <form id="bulkDeleteForm" action="{{ route('assets.bulk-delete') }}" method="POST">
        @csrf
        @method('DELETE')

        <!-- Header & Action Buttons -->
        <div class="row g-2 align-items-center mb-3">
            <div class="col-12 col-md-5">
                <h4 class="m-0 fw-bold text-dark fs-5 text-center text-md-start">
                    <i class="bi bi-boxes me-1 text-primary"></i> Inventori Aset
                </h4>
            </div>

            <div class="col-12 col-md-7">
                <div class="d-flex flex-wrap gap-1 justify-content-center justify-content-md-end">
                    {{-- Tombol Hapus Terpilih --}}
                    <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm flex-fill flex-md-grow-0 d-none" onclick="confirmBulkDelete()">
                        <i class="bi bi-trash"></i> Hapus (<span id="selectedCount">0</span>)
                    </button>

                    <!-- Tombol Export Excel -->
                    <a href="{{ route('assets.export', request()->query()) }}" class="btn btn-success btn-sm flex-fill flex-md-grow-0">
                        <i class="bi bi-file-earmark-excel"></i> Export
                    </a>

                    <!-- Tombol Import Excel -->
                    <button type="button" class="btn btn-outline-success btn-sm flex-fill flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bi bi-file-earmark-arrow-up"></i> Import
                    </button>

                    <!-- Tombol Tambah Aset -->
                    <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm flex-fill flex-md-grow-0">
                        <i class="bi bi-plus-circle"></i> Tambah Aset
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- ========================================== -->
    <!-- PANEL FILTER DATA ASET -->
    <!-- ========================================== -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form action="{{ route('assets.index') }}" method="GET">
                <div class="row g-2">
                    <!-- Search Input -->
                    <div class="col-12 col-md-3">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Nama / Kode Aset..." value="{{ request('search') }}">
                    </div>

                    <!-- Filter Kategori -->
                    <div class="col-12 col-md-2">
                        <select name="category" class="form-select form-select-sm">
                            <option value="">Semua Kategori</option>
                            <option value="IT" {{ request('category') == 'IT' ? 'selected' : '' }}>IT</option>
                            <option value="Mesin & Elektronik operasional" {{ request('category') == 'Mesin & Elektronik operasional' ? 'selected' : '' }}>Mesin & Elektronik Operasional</option>
                        </select>
                    </div>

                    <!-- Filter Lokasi / Outlet -->
                    <div class="col-12 col-md-3">
                        <select name="location" class="form-select form-select-sm">
                            <option value="">Semua Lokasi</option>
                            @if(isset($locations))
                                @foreach($locations as $loc)
                                    <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Filter Kondisi -->
                    <div class="col-12 col-md-2">
                        <select name="condition" class="form-select form-select-sm">
                            <option value="">Semua Kondisi</option>
                            <option value="Baik" {{ request('condition') == 'Baik' ? 'selected' : '' }}>Baik / Active</option>
                            <option value="Rusak Ringan" {{ request('condition') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak Berat" {{ request('condition') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                    </div>

                    <!-- Tombol Filter & Reset -->
                    <div class="col-12 col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                        <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Notifikasi Sukses / Error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 fs-7" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 fs-7" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- 1. TAMPILAN CARD KHUSUS HANDPHONE (< 768px) -->
    <!-- ========================================== -->
    <div class="d-md-none">
        <!-- Select All Checkbox Card -->
        <div class="card border-0 shadow-sm mb-2 bg-light">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <span class="fs-7 fw-bold text-muted">Pilih Semua Aset</span>
                <div class="form-check m-0">
                    <input type="checkbox" id="selectAllMobile" class="form-check-input select-all-trigger" style="transform: scale(1.2);">
                </div>
            </div>
        </div>

        @forelse($assets as $asset)
            @php
                $status = $asset->status ?? $asset->condition ?? 'Baik';
                $statusBadge = in_array($status, ['Baik', 'Bagus / Normal', 'Bagus', 'Active']) ? 'success' : ($status === 'Rusak Ringan' ? 'warning' : 'danger');
            @endphp
            <div class="card border-0 shadow-sm mb-2 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <input type="checkbox" form="bulkDeleteForm" name="ids[]" value="{{ $asset->id }}" class="form-check-input asset-checkbox" style="transform: scale(1.1);">
                            <span class="fw-bold text-primary fs-6">{{ $asset->asset_code }}</span>
                        </div>
                        <span class="badge bg-{{ $asset->category === 'IT' ? 'info text-dark' : 'warning text-dark' }} fs-8">
                            {{ $asset->category }}
                        </span>
                    </div>

                    <h6 class="fw-bold mb-1 text-dark">{{ $asset->asset_name }}</h6>
                    
                    <div class="fs-7 text-muted mb-2">
                        <div><i class="bi bi-geo-alt me-1"></i>{{ $asset->branch_name ?? $asset->branch_code ?? $asset->location ?? '-' }}</div>
                        <div class="mt-1">
                            Kondisi: <span class="badge bg-{{ $statusBadge }} fs-8">{{ $status }}</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                        <!-- Tombol QR Code Modal Trigger -->
                        <button type="button" class="btn btn-sm btn-outline-dark fs-8 py-1 px-2" data-bs-toggle="modal" data-bs-target="#qrModal{{ $asset->id }}">
                            <i class="bi bi-qr-code me-1"></i> Lihat QR
                        </button>

                        <!-- Action Edit & Delete -->
                        <div class="d-flex gap-1">
                            <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-sm btn-warning text-dark fw-bold px-3 py-1 fs-7">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-danger px-3 py-1 fs-7" onclick="confirmSingleDelete({{ $asset->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Pop-up QR Code versi Mobile -->
            <div class="modal fade" id="qrModal{{ $asset->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content text-center">
                        <div class="modal-header py-2 bg-light">
                            <h6 class="modal-title fw-bold fs-7">{{ $asset->asset_code }}</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body d-flex flex-column align-items-center justify-content-center py-4">
                            <div class="qrcode-item mb-2" data-code="{{ $asset->asset_code }}"></div>
                            <small class="text-muted fw-bold">{{ $asset->asset_name }}</small>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm text-center py-4 text-muted fs-7">
                Belum ada data aset yang sesuai dengan filter.
            </div>
        @endforelse
    </div>

    <!-- ========================================== -->
    <!-- 2. TAMPILAN TABEL KHUSUS DESKTOP (≥ 768px) -->
    <!-- ========================================== -->
    <div class="card border-0 shadow-sm d-none d-md-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="40" class="text-center">
                                <input type="checkbox" id="selectAllDesktop" class="form-check-input select-all-trigger">
                            </th>
                            <th>Kode Aset</th>
                            <th class="text-center">QR Code</th>
                            <th>Nama Aset</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Kondisi</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" form="bulkDeleteForm" name="ids[]" value="{{ $asset->id }}" class="form-check-input asset-checkbox">
                            </td>
                            <td><strong>{{ $asset->asset_code }}</strong></td>
                            <td class="text-center">
                                <div class="qrcode-item d-inline-block" data-code="{{ $asset->asset_code }}"></div>
                            </td>
                            <td>{{ $asset->asset_name }}</td>
                            <td><span class="badge bg-{{ $asset->category === 'IT' ? 'info text-dark' : 'warning text-dark' }}">{{ $asset->category }}</span></td>
                            <td>{{ $asset->branch_name ?? $asset->branch_code ?? $asset->location ?? '-' }}</td>
                            <td>
                                @php
                                    $status = $asset->status ?? $asset->condition ?? 'Baik';
                                @endphp
                                <span class="badge bg-{{ in_array($status, ['Baik', 'Bagus / Normal', 'Bagus', 'Active']) ? 'success' : ($status === 'Rusak Ringan' ? 'warning text-dark' : 'danger') }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmSingleDelete({{ $asset->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada data aset yang sesuai dengan filter.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- FORM HIDDEN UNTUK SINGLE DELETE --}}
<form id="singleDeleteForm" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<!-- Modal Form Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('assets.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white py-2">
                    <h5 class="modal-title fs-6" id="importModalLabel"><i class="bi bi-file-earmark-excel me-1"></i> Import Data Aset</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold fs-7">Pilih File Excel (.xlsx / .xls)</label>
                        <input type="file" name="file" class="form-control form-control-sm" accept=".xlsx, .xls, .csv" required>
                        <div class="form-text fs-8">Gunakan file Excel sesuai dengan format template yang ditentukan.</div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-upload"></i> Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .fs-7 { font-size: 0.85rem !important; }
    .fs-8 { font-size: 0.75rem !important; }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // --- 1. RENDER QR CODE ---
        document.querySelectorAll(".qrcode-item").forEach(function (element) {
            let code = element.getAttribute("data-code");
            if (code) {
                new QRCode(element, {
                    text: code,
                    width: 55,
                    height: 55,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }
        });

        // --- 2. LOGIKA BULK DELETE CHECKBOX ---
        const selectAllTriggers = document.querySelectorAll('.select-all-trigger');
        const checkboxes = document.querySelectorAll('.asset-checkbox');
        const btnBulkDelete = document.getElementById('btnBulkDelete');
        const selectedCount = document.getElementById('selectedCount');

        function updateBulkButton() {
            const checkedCount = document.querySelectorAll('.asset-checkbox:checked').length;
            selectedCount.textContent = checkedCount;

            if (checkedCount > 0) {
                btnBulkDelete.classList.remove('d-none');
            } else {
                btnBulkDelete.classList.add('d-none');
            }
        }

        selectAllTriggers.forEach(trigger => {
            trigger.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = this.checked);
                selectAllTriggers.forEach(t => t.checked = this.checked);
                updateBulkButton();
            });
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                const checkedCount = document.querySelectorAll('.asset-checkbox:checked').length;
                const allChecked = checkedCount === checkboxes.length && checkboxes.length > 0;
                
                selectAllTriggers.forEach(t => t.checked = allChecked);
                updateBulkButton();
            });
        });
    });

    function confirmBulkDelete() {
        const count = document.querySelectorAll('.asset-checkbox:checked').length;
        if (confirm(`Apakah Anda yakin ingin menghapus ${count} data aset yang dipilih?`)) {
            document.getElementById('bulkDeleteForm').submit();
        }
    }

    function confirmSingleDelete(id) {
        if (confirm('Yakin hapus aset ini?')) {
            const form = document.getElementById('singleDeleteForm');
            form.action = `/assets/${id}`;
            form.submit();
        }
    }
</script>
@endsection