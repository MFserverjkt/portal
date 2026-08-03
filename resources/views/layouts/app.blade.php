<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { min-height: 100vh; background-color: #f8f9fa; }
        #sidebar { width: 260px; min-height: 100vh; }
    </style>
</head>
<body>
@php
    $user = auth()->user();
    $userRole = $user?->role;
    
    // Ambil daftar permission dari database
    $permissions = \DB::table('role_has_permissions')
        ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
        ->where('role_has_permissions.role', $userRole)
        ->pluck('permissions.name')
        ->toArray();

    // Helper closure untuk cek apakah user punya akses ke suatu menu
    $canAccess = function($permName) use ($userRole, $permissions) {
        // ADMIN selalu punya akses
        if ($userRole === 'ADMIN') return true;
        
        // OUTLET diperbolehkan secara khusus membuka menu tiket
        if ($userRole === 'OUTLET' && $permName === 'tickets.index') return true;

        return in_array($permName, $permissions);
    };

    // Ambil info nama/kode cabang dari berbagai kemungkinan kolom database
    $branchInfo = $user?->branch_name 
        ?? $user?->outlet_name 
        ?? $user?->branch_code 
        ?? $user?->outlet_code 
        ?? null;
@endphp

<div class="d-flex">
    <!-- Sidebar navigation -->
    <div class="bg-dark text-white p-3 d-flex flex-column" id="sidebar">
        <h4 class="text-center my-3 fw-bold text-primary">PORTAL APP</h4>
        <hr class="border-secondary">
        
        <ul class="nav nav-pills flex-column mb-auto">

            <!-- Sidebar MENU IT -->
            @if($canAccess('users.index') || $canAccess('users.roles') || $canAccess('report.it'))
            <li class="nav-item">
                <small class="text-secondary fw-bold text-uppercase px-2">IT</small>
            </li>
            @if($canAccess('users.index'))
            <li>
                <a href="{{ route('users.index') }}" class="nav-link text-white">
                    <i class="bi bi-people me-2"></i> User Management
                </a>
            </li>
            @endif
            @if($canAccess('users.roles'))
            <li>
                <a href="{{ route('users.roles') }}" class="nav-link text-white">
                    <i class="bi bi-shield-lock me-2"></i> User Role
                </a>
            </li>
            @endif
            @if($canAccess('report.it'))
            <li>
                <a href="{{ route('report.it') }}" class="nav-link text-white">
                    <i class="bi bi-file-earmark-text me-2"></i> Report Corrective
                </a>
            </li>
            @endif
            <hr class="my-2 border-secondary">
            @endif

            <!-- Sidebar MENU MAINTENANCE -->
            @if($canAccess('report.maintenance'))
            <li class="nav-item">
                <small class="text-secondary fw-bold text-uppercase px-2">MAINTENANCE</small>
            </li>
            <li>
                <a href="{{ route('report.maintenance') }}" class="nav-link text-white">
                    <i class="bi bi-file-earmark-text me-2"></i> Report Corrective
                </a>
            </li>
            <hr class="my-2 border-secondary">
            @endif

            <!-- Sidebar MENU ASSET / TIKET (Akan Muncul untuk Role OUTLET) -->
            @if($canAccess('assets.index') || $canAccess('tickets.index'))
            <li class="nav-item">
                <small class="text-secondary fw-bold text-uppercase px-2">ASSET</small>
            </li>
            @if($canAccess('assets.index'))
            <li>
                <a href="{{ route('assets.index') }}" class="nav-link text-white">
                    <i class="bi bi-box-seam me-2"></i> Inventori
                </a>
            </li>
            @endif
            @if($canAccess('tickets.index'))
            <li>
                <a href="{{ route('tickets.index') }}" class="nav-link text-white">
                    <i class="bi bi-wrench me-2"></i> Corrective/Tiket
                </a>
            </li>
            @endif
            @endif

        </ul>
        
        <!-- BAGIAN FOOTER USER PROFILE & LOGOUT -->
        <hr class="my-3 border-secondary">
        <div class="mt-auto">
            @auth
            <!-- Informasi User Login -->
            <div class="d-flex align-items-center mb-3 text-white px-1">
                <i class="bi bi-person-circle fs-3 me-2 text-light flex-shrink-0"></i>
                <div class="lh-sm overflow-hidden" style="min-width: 0;">
                    <div class="fw-semibold text-truncate">
                        {{ $user?->name }}
                    </div>
                    <div class="small text-white-50 fw-bold">
                        ({{ $userRole }})
                    </div>
                    
                    {{-- Tampilkan Info Cabang jika User Memiliki Branch --}}
                    @if($branchInfo)
                    <div class="text-secondary text-truncate small mt-1" title="{{ $branchInfo }}">
                        <i class="bi bi-geo-alt me-1"></i>{{ $branchInfo }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tombol Logout -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
            @endauth
        </div>
    </div>

    <!-- Container Content -->
    <div class="flex-grow-1 p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif
        @yield('content')
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>