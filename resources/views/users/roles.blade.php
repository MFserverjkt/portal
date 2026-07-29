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
                    <input type="hidden" name="role" value="{{ $role }}">

                    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                        <h5 class="text-primary fw-bold mb-0">Atur Hak Akses Menu untuk Role: <span class="badge bg-primary">{{ $role }}</span></h5>
                        <button type="submit" class="btn btn-success fw-bold">
                            <i class="bi bi-save me-1"></i> Simpan Hak Akses {{ $role }}
                        </button>
                    </div>

                    @if($permissions->isNotEmpty())
                        <div class="row">
                            @foreach($permissions as $category => $permissionList)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center fw-bold text-uppercase">
                                            <span><i class="bi bi-folder2-open me-2 text-primary"></i> {{ $category }}</span>
                                            <!-- Checkbox Select All per Kategori -->
                                            <div class="form-check form-switch mb-0 fs-6">
                                                <input class="form-check-input select-all-category" 
                                                       type="checkbox" 
                                                       data-role="{{ $role }}" 
                                                       data-category="{{ Str::slug($category) }}"
                                                       id="check_all_{{ $role }}_{{ Str::slug($category) }}" 
                                                       title="Pilih Semua di Kategori Ini">
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach($permissionList as $perm)
                                                @php
                                                    $isChecked = in_array($perm->id, $rolePermissions[$role] ?? []);
                                                @endphp
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input perm-checkbox perm-{{ $role }}-{{ Str::slug($category) }}" 
                                                           type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $perm->id }}" 
                                                           id="perm_{{ $role }}_{{ $perm->id }}"
                                                           {{ $isChecked ? 'checked' : '' }}>
                                                    <label class="form-check-label cursor-pointer" for="perm_{{ $role }}_{{ $perm->id }}">
                                                        <strong>{{ $perm->name }}</strong>
                                                        @if(!empty($perm->description))
                                                            <br><small class="text-muted">{{ $perm->description }}</small>
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
                        <div class="alert alert-warning text-center my-4">
                            <i class="bi bi-exclamation-circle me-2"></i> Belum ada data <strong>Permission</strong> di database. Silakan isi seeder tabel permissions terlebih dahulu.
                        </div>
                    @endif
                </form>

            </div>
        @endforeach
    </div>
</div>

<!-- Script JS Select All / Unselect All -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Fitur Checkbox "Select All / Pilih Semua" per Kategori
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
    });
</script>
@endsection