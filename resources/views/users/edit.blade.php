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
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- Username -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- Password Baru -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Password Baru (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" minlength="6">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- Role Hak Akses -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Role Hak Akses</label>
                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                    <option value="">-- Pilih Role --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                            {{ $role }}
                        </option>
                    @endforeach
                </select>
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- Cabang / Outlet -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Cabang / Outlet</label>
                <select name="branch_code" class="form-select @error('branch_code') is-invalid @enderror" required>
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($branches as $code => $name)
                        <option value="{{ $code }}" {{ old('branch_code', $user->branch_code) == $code ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('branch_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
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