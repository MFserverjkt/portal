@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm col-md-6 mx-auto">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Tambah User Baru</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Password</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Role Hak Akses</label>
                <select name="role" class="form-select" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="ADMIN">ADMIN</option>
                    <option value="IT">IT</option>
                    <option value="MAINTENANCE">MAINTENANCE</option>
                    <option value="OUTLET">OUTLET</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Cabang / Outlet</label>
                <select name="branch_code" class="form-select @error('branch_code') is-invalid @enderror" required>
                <option value="">-- Pilih Cabang --</option>
                    @foreach($branches as $code => $name)
                        <option value="{{ $code }}" {{ old('branch_code') == $code ? 'selected' : '' }}>
                                    {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('branch_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex justify-content-between">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan User</button>
            </div>
        </form>
    </div>
</div>
@endsection