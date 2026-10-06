@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Daftar Tiket Perbaikan (Corrective)</h3>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Buat Tiket Baru
    </a>
</div>

<!-- FILTER BOX BRANCH -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ url()->current() }}" class="row g-2 align-items-center">
            <div class="col-md-4 col-sm-6">
                <label for="branch" class="form-label small fw-bold mb-1">Filter Branch / Cabang</label>
                <select name="branch" id="branch" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Branch --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch }}" {{ request('branch') == $branch ? 'selected' : '' }}>
                            [{{ $branch }}]
                        </option>
                    @endforeach
                </select>
            </div>
            
            @if(request('branch'))
            <div class="col-auto mt-4">
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle me-1"></i> Reset
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- TABEL DAFTAR TIKET -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>No. Tiket</th>
                        <th>Pelapor</th>
                        <th>Divisi Tujuan</th>
                        <th>Nama Aset</th>
                        <th>Keluhan</th>
                        <th>Status</th>
                        <th width="220">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td><strong>{{ $ticket->ticket_number }}</strong></td>
                        
                        <!-- Kolom Pelapor & Lokasi Branch Tiket -->
                        <td>
                            <div class="fw-bold">
                                {{ $ticket->reporter_name ?? $ticket->user->name ?? '-' }}
                            </div>
                            
                            @php
                                $branchInfo = $ticket->branch_code 
                                    ?? $ticket->branch_name 
                                    ?? $ticket->user?->branch_code 
                                    ?? $ticket->user?->branch_name;
                            @endphp

                            @if($branchInfo)
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-geo-alt me-1"></i>[{{ $branchInfo }}]
                                </small>
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-warning text-dark text-uppercase">
                                {{ $ticket->department }}
                            </span>
                        </td>
                        <td>{{ $ticket->asset->asset_name ?? '-' }}</td>
                        <td>{{ $ticket->title }}</td>
                        <td>
                            @php
                                // Menentukan status visual dan badge
                                $workStatus = $ticket->work_status ?? $ticket->status;
                                $statusBadge = 'secondary';

                                if (in_array($workStatus, ['Terbuka', 'On Check', 'Pengajuan Sparepart', 'On Progress', 'Diproses'])) {$statusBadge = 'warning text-dark';
                                } elseif ($workStatus === 'Menunggu Konfirmasi') {$statusBadge = 'info text-dark';
                                } elseif (in_array($workStatus, ['Completed', 'Selesai', 'Selesai (DONE)'])) {$statusBadge = 'success';
                                }
                            @endphp
                            <span class="badge bg-{{ $statusBadge }}">
                                {{ in_array($workStatus, ['On Check', 'Pengajuan Sparepart']) ? 'On Progress (' . $workStatus . ')' :$workStatus }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                <!-- Tombol View Detail -->
                                <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-info btn-sm text-white" title="Lihat Detail">
                                    <i class="bi bi-eye"></i> View
                                </a>

                                <!-- Tombol WORK (Untuk teknisi IT / Maintenance / Admin) -->
                                @if(in_array(auth()->user()->role, ['ADMIN', 'IT', 'MAINTENANCE']) && !in_array($ticket->status, ['Selesai', 'Selesai (DONE)']))
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalWork{{ $ticket->id }}" title="Update Pengerjaan Tiket">
                                        <i class="bi bi-tools"></i> WORK
                                    </button>

                                    <!-- MODAL POPUP WORK -->
                                    <div class="modal fade" id="modalWork{{ $ticket->id }}" tabindex="-1" aria-labelledby="modalWorkLabel{{ $ticket->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <form action="{{ route('tickets.work.update', $ticket->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="modal-header bg-dark text-white">
                                                        <h5 class="modal-title fs-6 fw-bold" id="modalWorkLabel{{ $ticket->id }}">
                                                            <i class="bi bi-tools me-2"></i>Form Work Progress - {{ $ticket->ticket_number }}
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!-- Input Petugas -->
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-dark">Petugas / Teknisi <span class="text-danger">*</span></label>
                                                            <input type="text" name="technician_name" class="form-control" value="{{ $ticket->technician_name ?? auth()->user()->name }}" required placeholder="Nama teknisi penanggung jawab">
                                                        </div>

                                                        <!-- Input Tindakan Pengerjaan -->
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-dark">Tindakan Pengerjaan <span class="text-danger">*</span></label>
                                                            <textarea name="action_taken" class="form-control" rows="3" required placeholder="Jelaskan analisa / tindakan perbaikan yang dilakukan">{{ $ticket->action_taken }}</textarea>
                                                        </div>

                                                        <!-- Input Tanggal Target Selesai (Diberi minimal hari ini) -->
                                                        <div class="mb-3" id="target-wrapper-{{ $ticket->id }}">
                                                            <label class="form-label fw-bold text-dark">Tanggal Target Selesai <span class="text-danger">*</span></label>
                                                            <input 
                                                                type="date" 
                                                                name="target_completion_date" 
                                                                id="target-date-{{ $ticket->id }}" 
                                                                class="form-control" 
                                                                min="{{ date('Y-m-d') }}"
                                                                value="{{ $ticket->target_completion_date }}"
                                                            >
                                                        </div>

                                                        <!-- Select Status Pengerjaan -->
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-dark">Status Pengerjaan <span class="text-danger">*</span></label>
                                                            <select name="work_status" class="form-select status-pengerjaan-select" data-ticket-id="{{ $ticket->id }}" required>
                                                                <option value="On Check" {{ ($ticket->work_status ?? '') === 'On Check' ? 'selected' : '' }}>On Check</option>
                                                                <option value="Pengajuan Sparepart" {{ ($ticket->work_status ?? '') === 'Pengajuan Sparepart' ? 'selected' : '' }}>Pengajuan Sparepart</option>
                                                                <option value="Completed" {{ ($ticket->work_status ?? '') === 'Completed' ? 'selected' : '' }}>Completed</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary btn-sm fw-bold">
                                                            <i class="bi bi-save me-1"></i> Simpan Work
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Tombol BAST -->
                                @if(!$ticket->bast && in_array(auth()->user()->role, ['ADMIN', 'IT', 'MAINTENANCE']) && in_array($ticket->work_status, ['Completed', 'On Check', 'Pengajuan Sparepart']) && !in_array($ticket->status, ['Selesai', 'Selesai (DONE)']))
                                    <a href="{{ route('tickets.bast.create', $ticket->id) }}" class="btn btn-primary btn-sm" title="Buat BAST">
                                        <i class="bi bi-file-earmark-text"></i> BAST
                                    </a>
                                @endif

                                <!-- Tombol DONE -->
                                @if($ticket->status === 'Menunggu Konfirmasi' && ($ticket->user_id === auth()->id() || auth()->user()->role === 'ADMIN') && !in_array(auth()->user()->role, ['IT', 'MAINTENANCE']))
                                    <button type="button" class="btn btn-success btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalDone{{ $ticket->id }}" title="Konfirmasi Selesai">
                                        <i class="bi bi-check-circle me-1"></i> VALIDASI BAST
                                    </button>

                                    <!-- MODAL POPUP INPUT TANGGAL SELESAI -->
                                    <div class="modal fade" id="modalDone{{ $ticket->id }}" tabindex="-1" aria-labelledby="modalDoneLabel{{ $ticket->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <form action="{{ route('tickets.markAsDone', $ticket->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title fs-6 fw-bold" id="modalDoneLabel{{ $ticket->id }}">
                                                            <i class="bi bi-calendar-check me-2"></i>Konfirmasi Tiket Selesai
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-3 text-dark">
                                                            Silakan pilih tanggal & waktu penyelesaian untuk tiket <strong>{{ $ticket->ticket_number }}</strong>:
                                                        </p>
                                                        
                                                        <div class="mb-3">
                                                            <label for="completed_at_{{ $ticket->id }}" class="form-label fw-bold text-dark">Tanggal & Waktu Selesai <span class="text-danger">*</span></label>
                                                            <input 
                                                                type="datetime-local" 
                                                                name="completed_at" 
                                                                id="completed_at_{{ $ticket->id }}"
                                                                class="form-control" 
                                                                value="{{ now()->format('Y-m-d\TH:i') }}" 
                                                                min="{{ now()->format('Y-m-d\TH:i') }}"
                                                                required
                                                            >
                                                            <small class="text-muted">Tanggal tidak boleh memilih tanggal/waktu yang sudah berlalu.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-success btn-sm fw-bold">
                                                            <i class="bi bi-check-lg me-1"></i> Simpan & Selesaikan
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">
                            <i class="bi bi-inbox d-block fs-3 mb-1"></i>
                            Tidak ada data tiket terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusSelects = document.querySelectorAll('.status-pengerjaan-select');

    function toggleTargetDate(selectElement) {
        const ticketId = selectElement.getAttribute('data-ticket-id');
        const wrapper = document.getElementById(`target-wrapper-${ticketId}`);
        const input = document.getElementById(`target-date-${ticketId}`);

        if (selectElement.value === 'Completed') {
            wrapper.style.display = 'none';
            input.removeAttribute('required');
            input.value = ''; // Mengosongkan tanggal target
        } else {
            wrapper.style.display = 'block';
            input.setAttribute('required', 'required');
        }
    }

    statusSelects.forEach(function (selectElement) {
        // Run on initial load
        toggleTargetDate(selectElement);

        // Run on select change
        selectElement.addEventListener('change', function () {
            toggleTargetDate(this);
        });
    });
});
</script>
@endpush