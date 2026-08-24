@extends('layouts.app')

@section('content')
{{-- FORM BUNGKUS UNTUK BULK DELETE --}}
<form id="bulkDeleteForm" action="{{ route('assets.bulk-delete') }}" method="POST">
    @csrf
    @method('DELETE')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Inventori Aset (IT & Maintenance)</h3>
        <div>
            {{-- Tombol Hapus Terpilih (Awalnya tersembunyi/d-none) --}}
            <button type="button" id="btnBulkDelete" class="btn btn-danger me-1 d-none" onclick="confirmBulkDelete()">
                <i class="bi bi-trash"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
            </button>

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
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            {{-- Checkbox Select All --}}
                            <th width="40" class="text-center">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Kode Aset</th>
                            <th>QR Code</th> <!-- Kolom QR Code -->
                            <th>Nama Aset</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Kondisi</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                        <tr>
                            {{-- Checkbox per Baris --}}
                            <td class="text-center">
                                <input type="checkbox" name="ids[]" value="{{ $asset->id }}" class="form-check-input asset-checkbox">
                            </td>
                            <td><strong>{{ $asset->asset_code }}</strong></td>
                            
                            <!-- Container Render QR Code -->
                            <td>
                                <div class="qrcode-item" data-code="{{ $asset->asset_code }}"></div>
                            </td>

                            <td>{{ $asset->asset_name }}</td>
                            <td><span class="badge bg-{{ $asset->category === 'IT' ? 'info' : 'warning' }}">{{ $asset->category }}</span></td>
                            
                            <!-- Perbaikan Lokasi: Membaca branch_name, branch_code, atau location -->
                            <td>{{ $asset->branch_name ?? $asset->branch_code ?? $asset->location ?? '-' }}</td>
                            
                            <!-- Perbaikan Kondisi: Membaca status atau condition -->
                            <td>
                                @php
                                    $status = $asset->status ?? $asset->condition ?? 'Baik';
                                @endphp
                                <span class="badge bg-{{ in_array($status, ['Baik', 'Bagus / Normal', 'Bagus']) ? 'success' : ($status === 'Rusak Ringan' ? 'warning' : 'danger') }}">
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
                            <td colspan="8" class="text-center text-muted">Belum ada data aset.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

{{-- FORM HIDDEN UNTUK SINGLE DELETE --}}
<form id="singleDeleteForm" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

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

<!-- Library QRCode.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<!-- Script Inisialisasi QR Code & Logika Checkbox / Bulk Delete -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // --- 1. RENDER QR CODE ---
        document.querySelectorAll(".qrcode-item").forEach(function (element) {
            let code = element.getAttribute("data-code");
            if (code) {
                new QRCode(element, {
                    text: code,
                    width: 60,
                    height: 60,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }
        });

        // --- 2. LOGIKA BULK DELETE CHECKBOX ---
        const selectAll = document.getElementById('selectAll');
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

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBulkButton();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                if (!this.checked) selectAll.checked = false;
                if (document.querySelectorAll('.asset-checkbox:checked').length === checkboxes.length) {
                    selectAll.checked = true;
                }
                updateBulkButton();
            });
        });
    });

    // --- 3. KONFIRMASI HAPUS BANYAK (BULK DELETE) ---
    function confirmBulkDelete() {
        const count = document.querySelectorAll('.asset-checkbox:checked').length;
        if (confirm(`Apakah Anda yakin ingin menghapus ${count} data aset yang dipilih?`)) {
            document.getElementById('bulkDeleteForm').submit();
        }
    }

    // --- 4. KONFIRMASI HAPUS SATUAN (SINGLE DELETE) ---
    function confirmSingleDelete(id) {
        if (confirm('Yakin hapus aset ini?')) {
            const form = document.getElementById('singleDeleteForm');
            form.action = `/assets/${id}`; // Sesuaikan dengan URI route destroy Anda
            form.submit();
        }
    }
</script>
@endsection