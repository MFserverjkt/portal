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
            @php $roleSlug = Str::slug($role); @endphp
            <li class="nav-item" role="presentation">
                <button class="nav-item nav-link fw-bold px-4 {{ $index === 0 ? 'active' : '' }}" 
                        id="tab-{{ $roleSlug }}" 
                        data-bs-toggle="tab" 
                        data-bs-target="#content-{{ $roleSlug }}" 
                        type="button" 
                        role="tab">
                    <i class="bi bi-person-badge me-1"></i> Role {{ $role }}
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content bg-white p-4 border rounded-bottom shadow-sm" id="roleTabContent">
        @foreach($roles as $index => $role)
            @php $roleSlug = Str::slug($role); @endphp
            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="content-{{ $roleSlug }}" role="tabpanel">
                
                <form action="{{ route('users.roles.update') }}" method="POST">
                    @csrf
                    <!-- Hidden Field Role -->
                    <input type="hidden" name="role" value="{{ $role }}">

                    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom flex-wrap gap-2">
                        <h5 class="text-primary fw-bold mb-0">Atur Hak Akses Menu untuk Role: <span class="badge bg-primary">{{ $role }}</span></h5>
                        
                        <div class="d-flex align-items-center gap-3">
                            <!-- Toggle Check All Global -->
                            <div class="form-check form-switch fs-6 mb-0">
                                <input class="form-check-input select-all-global" 
                                       type="checkbox" 
                                       data-role-slug="{{ $roleSlug }}"
                                       id="check_global_{{ $roleSlug }}">
                                <label class="form-check-label fw-bold text-secondary" for="check_global_{{ $roleSlug }}">Pilih Semua Menu</label>
                            </div>

                            <button type="submit" class="btn btn-success fw-bold">
                                <i class="bi bi-save me-1"></i> Simpan Hak Akses {{ $role }}
                            </button>
                        </div>
                    </div>

                    @if(isset($permissions) && $permissions->isNotEmpty())
                        <div class="row">
                            @foreach($permissions as $category => $permissionList)
                                @php
                                    $catSlug = Str::slug($category);
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
                                                       data-role-slug="{{ $roleSlug }}" 
                                                       data-category="{{ $catSlug }}"
                                                       id="check_all_{{ $roleSlug }}_{{ $catSlug }}" 
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
                                                    <input class="form-check-input perm-checkbox perm-{{ $roleSlug }} perm-{{ $roleSlug }}-{{ $catSlug }}" 
                                                           type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $perm->id }}" 
                                                           id="perm_{{ $roleSlug }}_{{ $perm->id }}"
                                                           data-role-slug="{{ $roleSlug }}"
                                                           data-category="{{ $catSlug }}"
                                                           {{ $isChecked ? 'checked' : '' }}>
                                                    <label class="form-check-label cursor-pointer" for="perm_{{ $roleSlug }}_{{ $perm->id }}">
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
                    @else
                        <!-- FALLBACK JIKA DYNAMIC PERMISSIONS KOSONG -->
                        <div class="row">
                            @php
                                $fallbackGroups = [
                                    'USER' => [
                                        ['val' => 'users.index', 'title' => 'User Management', 'desc' => 'Mengakses halaman daftar user'],
                                        ['val' => 'users.roles', 'title' => 'User Role & Hak Akses', 'desc' => 'Mengatur role dan hak akses pengguna']
                                    ],
                                    'ASSET' => [
                                        ['val' => 'assets.index', 'title' => 'Inventori Asset', 'desc' => 'Melihat dan mengelola inventori aset']
                                    ],
                                    'TIKET' => [
                                        ['val' => 'tickets.index', 'title' => 'Lihat Daftar Tiket', 'desc' => 'Melihat daftar seluruh tiket perbaikan'],
                                        ['val' => 'tickets.create', 'title' => 'Buat Tiket Baru', 'desc' => 'Membuat tiket pengajuan perbaikan baru'],
                                        ['val' => 'tickets.bast.create', 'title' => 'Proses BAST Tiket', 'desc' => 'Memproses Berita Acara Serah Terima (BAST)']
                                    ],
                                    'REPORT' => [
                                        ['val' => 'report.it', 'title' => 'Report Corrective IT', 'desc' => 'Melihat laporan perbaikan divisi IT'],
                                        ['val' => 'report.maintenance', 'title' => 'Report Corrective Maintenance', 'desc' => 'Melihat laporan perbaikan divisi Maintenance']
                                    ]
                                ];
                            @endphp

                            @foreach($fallbackGroups as $groupName => $items)
                                @php $groupSlug = Str::slug($groupName); @endphp
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100 border shadow-sm">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center fw-bold">
                                            <span><i class="bi bi-folder me-1 text-primary"></i> {{ $groupName }}</span>
                                            <div class="form-check form-switch mb-0 fs-6">
                                                <input class="form-check-input select-all-category" 
                                                       type="checkbox" 
                                                       data-role-slug="{{ $roleSlug }}" 
                                                       data-category="{{ $groupSlug }}"
                                                       id="check_all_{{ $roleSlug }}_{{ $groupSlug }}">
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach($items as $item)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input perm-checkbox perm-{{ $roleSlug }} perm-{{ $roleSlug }}-{{ $groupSlug }}" 
                                                           type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $item['val'] }}" 
                                                           id="fb_{{ $roleSlug }}_{{ Str::slug($item['val']) }}"
                                                           data-role-slug="{{ $roleSlug }}"
                                                           data-category="{{ $groupSlug }}"
                                                           {{ in_array($item['val'], $rolePermissions[$role] ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold cursor-pointer" for="fb_{{ $roleSlug }}_{{ Str::slug($item['val']) }}">
                                                        {{ $item['title'] }}
                                                    </label>
                                                    <small class="d-block text-muted">{{ $item['desc'] }}</small>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-success btn-lg px-4 fw-bold">
                            <i class="bi bi-save me-1"></i> Simpan Hak Akses {{ $role }}
                        </button>
                    </div>
                </form>

            </div>
        @endforeach
    </div>
</div>

<!-- Script JS Interaktif -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Fitur Checkbox "Select All per Kategori"
        document.querySelectorAll('.select-all-category').forEach(switchEl => {
            switchEl.addEventListener('change', function () {
                const roleSlug = this.getAttribute('data-role-slug');
                const category = this.getAttribute('data-category');
                const checkboxes = document.querySelectorAll(`.perm-${roleSlug}-${category}`);

                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
                
                updateGlobalSwitch(roleSlug);
            });
        });

        // 2. Fitur Checkbox "Select All Global" (Pilih Semua Menu)
        document.querySelectorAll('.select-all-global').forEach(globalSwitch => {
            globalSwitch.addEventListener('change', function () {
                const roleSlug = this.getAttribute('data-role-slug');
                const targetPane = document.getElementById(`content-${roleSlug}`);
                
                if (targetPane) {
                    const allCheckboxes = targetPane.querySelectorAll('.perm-checkbox, .select-all-category');
                    allCheckboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                }
            });
        });

        // 3. Auto Check/Uncheck Parent Switches saat Checkbox Item Berubah
        document.querySelectorAll('.perm-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const roleSlug = this.getAttribute('data-role-slug');
                const category = this.getAttribute('data-category');
                
                // Update switch kategori
                const categorySwitch = document.getElementById(`check_all_${roleSlug}_${category}`);
                if (categorySwitch) {
                    const categoryItems = document.querySelectorAll(`.perm-${roleSlug}-${category}`);
                    categorySwitch.checked = Array.from(categoryItems).every(c => c.checked);
                }

                // Update switch global
                updateGlobalSwitch(roleSlug);
            });
        });

        // Helper untuk sinkronisasi Switch Global
        function updateGlobalSwitch(roleSlug) {
            const globalSwitch = document.getElementById(`check_global_${roleSlug}`);
            if (globalSwitch) {
                const allItems = document.querySelectorAll(`.perm-${roleSlug}`);
                globalSwitch.checked = allItems.length > 0 && Array.from(allItems).every(c => c.checked);
            }
        }
    });
</script>
@endsection