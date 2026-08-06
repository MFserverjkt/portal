@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold"><i class="bi bi-shield-lock me-2"></i> Pengaturan Hak Akses Role</h3>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Nav Tab / Pilihan Role -->
    <ul class="nav nav-tabs border-bottom-0" id="roleTab" role="tablist">
        @foreach($roles as $index => $role)
            <li class="nav-item" role="presentation">
                <button class="nav-item nav-link fw-bold px-4 {{ $index === 0 ? 'active' : '' }}" 
                        id="tab-{{ $role }}" 
                        data-bs-toggle="tab" 
                        data-bs-target="#content-{{ $role }}" 
                        type="button" 
                        role="tab">
                    <i class="bi bi-person-badge me-1"></i> Role {{ $role }}
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content bg-white p-4 border rounded-bottom shadow-sm" id="roleTabContent">
        @foreach($roles as $index => $role)
            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="content-{{ $role }}" role="tabpanel">
                
                <form action="{{ route('users.roles.update') }}" method="POST">
                    @csrf
                    <!-- Hidden Field Role -->
                    <input type="hidden" name="role" value="{{ $role }}">

                    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                        <h5 class="text-primary fw-bold mb-0">Atur Hak Akses Menu untuk Role: <span class="badge bg-primary">{{ $role }}</span></h5>
                        <button type="submit" class="btn btn-success fw-bold">
                            <i class="bi bi-save me-1"></i> Simpan Hak Akses {{ $role }}
                        </button>
                    </div>

                    @if(isset($permissions) && $permissions->isNotEmpty())
                        <div class="row">
                            @foreach($permissions as $category => $permissionList)
                                @php
                                    $catSlug = Str::slug($category);
                                    // Hitung apakah semua checkbox di kategori ini tercentang
                                    $allChecked = $permissionList->every(function($p) use ($rolePermissions, $role) {
                                        return in_array($p->id, $rolePermissions[$role] ?? []);
                                    });
                                @endphp
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border shadow-sm">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center fw-bold text-uppercase">
                                            <span><i class="bi bi-folder2-open me-2 text-primary"></i> {{ $category }}</span>
                                            
                                            <!-- Switch Select All per Kategori -->
                                            <div class="form-check form-switch mb-0 fs-6">
                                                <input class="form-check-input select-all-category" 
                                                       type="checkbox" 
                                                       data-role="{{ $role }}" 
                                                       data-category="{{ $catSlug }}"
                                                       id="check_all_{{ $role }}_{{ $catSlug }}" 
                                                       title="Pilih Semua di Kategori Ini"
                                                       {{ $allChecked ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach($permissionList as $perm)
                                                @php
                                                    $isChecked = in_array($perm->id, $rolePermissions[$role] ?? []);
                                                @endphp
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input perm-checkbox perm-{{ $role }}-{{ $catSlug }}" 
                                                           type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $perm->id }}" 
                                                           id="perm_{{ $role }}_{{ $perm->id }}"
                                                           data-role="{{ $role }}"
                                                           data-category="{{ $catSlug }}"
                                                           {{ $isChecked ? 'checked' : '' }}>
                                                    <label class="form-check-label cursor-pointer" for="perm_{{ $role }}_{{ $perm->id }}">
                                                        <strong>{{ $perm->name }}</strong>
                                                        @if(!empty($perm->description))
                                                            <br><small class="text-muted d-block mt-1">{{ $perm->description }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-success btn-lg px-4 fw-bold">
                                <i class="bi bi-save me-1"></i> Simpan Hak Akses {{ $role }}
                            </button>
                        </div>
                    @else
                        <!-- FALLBACK JIKA DYNAMIC PERMISSIONS DARI DB KOSONG -->
                        <div class="row">
                            <!-- Group User -->
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-light fw-bold"><i class="bi bi-folder me-1 text-primary"></i> USER</div>
                                    <div class="card-body">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="users.index" id="p_u_idx_{{ $role }}"
                                                {{ in_array('users.index', $rolePermissions[$role] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_u_idx_{{ $role }}">User Management</label>
                                            <small class="d-block text-muted">Mengakses halaman daftar user</small>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="users.roles" id="p_u_roles_{{ $role }}"
                                                {{ in_array('users.roles', $rolePermissions[$role] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_u_roles_{{ $role }}">User Role & Hak Akses</label>
                                            <small class="d-block text-muted">Mengatur role dan hak akses pengguna</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group Asset -->
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-light fw-bold"><i class="bi bi-folder me-1 text-primary"></i> ASSET</div>
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="assets.index" id="p_a_idx_{{ $role }}"
                                                {{ in_array('assets.index', $rolePermissions[$role] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_a_idx_{{ $role }}">Inventori Asset</label>
                                            <small class="d-block text-muted">Melihat dan mengelola inventori aset</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group Tiket -->
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-light fw-bold"><i class="bi bi-folder me-1 text-primary"></i> TIKET</div>
                                    <div class="card-body">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="tickets.index" id="p_t_idx_{{ $role }}"
                                                {{ in_array('tickets.index', $rolePermissions[$role] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_t_idx_{{ $role }}">Lihat Daftar Tiket</label>
                                            <small class="d-block text-muted">Melihat daftar seluruh tiket perbaikan</small>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="tickets.create" id="p_t_crt_{{ $role }}"
                                                {{ in_array('tickets.create', $rolePermissions[$role] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_t_crt_{{ $role }}">Buat Tiket Baru</label>
                                            <small class="d-block text-muted">Membuat tiket pengajuan perbaikan baru</small>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="tickets.bast.create" id="p_t_bst_{{ $role }}"
                                                {{ in_array('tickets.bast.create', $rolePermissions[$role] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_t_bst_{{ $role }}">Proses BAST Tiket</label>
                                            <small class="d-block text-muted">Memproses Berita Acara Serah Terima (BAST)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group Report -->
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-light fw-bold"><i class="bi bi-folder me-1 text-primary"></i> REPORT</div>
                                    <div class="card-body">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="report.it" id="p_r_it_{{ $role }}"
                                                {{ in_array('report.it', $rolePermissions[$role] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_r_it_{{ $role }}">Report Corrective IT</label>
                                            <small class="d-block text-muted">Melihat laporan perbaikan divisi IT</small>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="report.maintenance" id="p_r_maint_{{ $role }}"
                                                {{ in_array('report.maintenance', $rolePermissions[$role] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_r_maint_{{ $role }}">Report Corrective Maintenance</label>
                                            <small class="d-block text-muted">Melihat laporan perbaikan divisi Maintenance</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-success btn-lg px-4 fw-bold">
                                <i class="bi bi-save me-1"></i> Simpan Hak Akses {{ $role }}
                            </button>
                        </div>
                    @endif
                </form>

            </div>
        @endforeach
    </div>
</div>

<!-- Script JS Interaktif -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Fitur Checkbox "Select All / Switch Category"
        const selectAllSwitches = document.querySelectorAll('.select-all-category');

        selectAllSwitches.forEach(switchEl => {
            switchEl.addEventListener('change', function () {
                const role = this.getAttribute('data-role');
                const category = this.getAttribute('data-category');
                const checkboxes = document.querySelectorAll(`.perm-${role}-${category}`);

                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
            });
        });

        // Auto Uncheck / Check Switch Header berdasarkan status item anak
        const itemCheckboxes = document.querySelectorAll('.perm-checkbox');
        itemCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const role = this.getAttribute('data-role');
                const category = this.getAttribute('data-category');
                const parentSwitch = document.getElementById(`check_all_${role}_${category}`);
                
                if (parentSwitch) {
                    const allInGroup = document.querySelectorAll(`.perm-${role}-${category}`);
                    const allChecked = Array.from(allInGroup).every(c => c.checked);
                    parentSwitch.checked = allChecked;
                }
            });
        });
    });
</script>
@endsection