@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm col-md-8 mx-auto">
    <div class="card-header bg-warning text-dark fw-bold">
        Edit User
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Nama Lengkap -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <!-- Username -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
            </div>

            <!-- Password Baru -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Password Baru (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••">
            </div>

            <!-- Role Hak Akses -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Role Hak Akses</label>
                <select name="role" class="form-select" required>
                    <option value="ADMIN" {{ old('role', $user->role) == 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                    <option value="IT" {{ old('role', $user->role) == 'IT' ? 'selected' : '' }}>IT</option>
                    <option value="MAINTENANCE" {{ old('role', $user->role) == 'MAINTENANCE' ? 'selected' : '' }}>MAINTENANCE</option>
                    <option value="ASSET" {{ old('role', $user->role) == 'ASSET' ? 'selected' : '' }}>ASSET</option>
                    <option value="OUTLET" {{ old('role', $user->role) == 'OUTLET' ? 'selected' : '' }}>OUTLET</option>
                </select>
            </div>

            <!-- Kode Cabang (Branch Code) -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Kode Cabang (Branch Code)</label>
                <input type="text" name="branch_code" class="form-control text-uppercase" placeholder="Contoh: MFBX / HOTNG" value="{{ old('branch_code', $user->branch_code) }}" required>
                <small class="text-muted">Gunakan kode cabang yang sesuai dengan kode pada Aset (misal: MFBX, MFLW, MFCP, dll).</small>
            </div>

            <!-- Nama Cabang (Branch Name) -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Cabang (Branch Name)</label>
                <input type="text" name="branch_name" class="form-control" placeholder="Contoh: MAISON FEERIE BINTARO EXCHANGE" value="{{ old('branch_name', $user->branch_name) }}" required>
            </div>

            <!-- Tombol Aksi -->
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-warning fw-bold px-4">Update User</button>
            </div>
        </form>
    </div>
</div>
@endsection