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
    
    // Ambil daftar permission (nama menu) yang diizinkan untuk role user dari database
    $permissions = \DB::table('role_has_permissions')
        ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
        ->where('role_has_permissions.role', $userRole)
        ->pluck('permissions.name')
        ->toArray();

    // Helper closure untuk cek apakah user punya akses ke suatu menu
    $canAccess = function($permName) use ($userRole, $permissions) {
        return $userRole === 'ADMIN' || in_array($permName, $permissions);
    };
@endphp

<div class="d-flex">
    <!-- Sidebar navigation -->
    <div class="bg-dark text-white p-3 d-flex flex-column" id="sidebar">
        <h4 class="text-center my-3 fw-bold text-primary">PORTAL APP</h4>
        <hr>
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
            <hr class="my-2">
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
            <hr class="my-2">
            @endif

            <!-- Sidebar MENU ASSET -->
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
        <hr>
        <div>
            @auth
            <div class="mb-2"><i class="bi bi-person-circle me-1"></i> {{ $user?->name }} (<strong>{{ $userRole }}</strong>)</div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-box-arrow-right"></i> Logout</button>
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