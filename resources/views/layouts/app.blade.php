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
        .nav-link { border-radius: 0.375rem; transition: background-color 0.2s; }
        .nav-link:hover { background-color: rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body>
@php
    $user = auth()->user();

    // 1. Ambil Nama User & Role (Kapital & Clean)
    $userName = $user?->name ?? $user?->username ?? 'User';
    $primaryRole = $user?->getRoleNames()->first() ?? $user?->role ?? 'GUEST';
    $userRole = strtoupper(trim($primaryRole));

    // 2. Ambil PERMISSION resmi milik User dari Spatie Permission
    $userPermissions = [];
    if ($user) {
        $userPermissions = $user->getAllPermissions()
            ->pluck('name')
            ->map(fn($item) => strtolower(trim($item)))
            ->toArray();
    }

    // 3. Helper Closure Pengecekan Akses Menu Presisi
    $canAccess = function(...$permNames) use ($user, $userRole, $userPermissions) {
        if (!$user) return false;

        // Hanya Role ADMIN / ADMINISTRATOR yang otomatis dapat Full Access
        if (in_array($userRole, ['ADMIN', 'ADMINISTRATOR'])) {
            return true;
        }

        // Jika tidak ada permission sama sekali untuk user ini
        if (empty($userPermissions)) {
            return false;
        }

        // Cek apakah parameter yang dicari ada di daftar permission user
        foreach ($permNames as $perm) {
            if (in_array(strtolower(trim($perm)), $userPermissions)) {
                return true;
            }
        }

        return false;
    };

    // Ambil info Cabang
    $branchInfo = $user?->branch_name 
        ?? $user?->outlet_name 
        ?? $user?->branch_code 
        ?? $user?->outlet_code 
        ?? null;

    // Badge Warna Role
    $roleBadgeClass = match($userRole) {
        'ADMIN', 'ADMINISTRATOR' => 'bg-danger text-white',
        'IT'                    => 'bg-primary text-white',
        'MAINTENANCE'           => 'bg-warning text-dark',
        'OUTLET'                => 'bg-info text-white',
        default                 => 'bg-secondary text-white'
    };
@endphp

<div class="d-flex">
    <!-- Sidebar navigation -->
    <div class="bg-dark text-white p-3 d-flex flex-column" id="sidebar">
        <h4 class="text-center my-3 fw-bold text-primary">PORTAL APP</h4>
        <hr class="border-secondary">
        
        <ul class="nav nav-pills flex-column mb-auto">

            <!-- SECTION MENU IT -->
            @if($canAccess('users.index', 'users.roles', 'report.it', 'User Management', 'User Role & Hak Akses', 'Report Corrective IT'))
                <li class="nav-item mt-2">
                    <small class="text-secondary fw-bold text-uppercase px-2">IT</small>
                </li>
                
                @if($canAccess('users.index', 'User Management'))
                <li>
                    <a href="{{ route('users.index') }}" class="nav-link text-white">
                        <i class="bi bi-people me-2"></i> User Management
                    </a>
                </li>
                @endif

                @if($canAccess('users.roles', 'User Role & Hak Akses'))
                <li>
                    <a href="{{ route('users.roles') }}" class="nav-link text-white">
                        <i class="bi bi-shield-lock me-2"></i> User Role
                    </a>
                </li>
                @endif

                @if($canAccess('report.it', 'Report Corrective IT'))
                <li>
                    <a href="{{ route('report.it') }}" class="nav-link text-white">
                        <i class="bi bi-file-earmark-text me-2"></i> Report Corrective
                    </a>
                </li>
                @endif

                <hr class="my-2 border-secondary">
            @endif

            <!-- SECTION MENU HC LEARNING -->
            @if($canAccess('hc.elearning', 'hc.pretest', 'hc.posttest', 'e-Learning', 'Pre-Test', 'Post-Test', 'HC Learning'))
                <li class="nav-item mt-2">
                    <small class="text-secondary fw-bold text-uppercase px-2">HC LEARNING</small>
                </li>

                <!-- 1. Menu e-Learning (Materi & Modul) -->
                @if($canAccess('hc.elearning', 'e-Learning', 'Materi Learning'))
                <li>
                    <a href="{{ route('hc.elearning.index') }}" class="nav-link text-white">
                        <i class="bi bi-mortarboard me-2"></i> e-Learning
                    </a>
                </li>
                @endif

                <!-- 2. Menu Pre-Test -->
                @if($canAccess('hc.pretest', 'Pre-Test'))
                <li>
                    <a href="{{ route('hc.pretest.index') }}" class="nav-link text-white">
                        <i class="bi bi-file-earmark-text me-2"></i> Pre-Test
                    </a>
                </li>
                @endif

                <!-- 3. Menu Post-Test -->
                @if($canAccess('hc.posttest', 'Post-Test'))
                <li>
                    <a href="{{ route('hc.posttest.index') }}" class="nav-link text-white">
                        <i class="bi bi-file-earmark-check me-2"></i> Post-Test
                    </a>
                </li>
                @endif

                <hr class="my-2 border-secondary">
            @endif


            <!-- SECTION MENU MAINTENANCE -->
            @if($canAccess('report.maintenance', 'Report Corrective Maintenance'))
                <li class="nav-item mt-2">
                    <small class="text-secondary fw-bold text-uppercase px-2">MAINTENANCE</small>
                </li>

                <li>
                    <a href="{{ route('report.maintenance') }}" class="nav-link text-white">
                        <i class="bi bi-file-earmark-text me-2"></i> Report Corrective
                    </a>
                </li>

                <hr class="my-2 border-secondary">
            @endif


            <!-- SECTION MENU ASSET / TIKET -->
            @if($canAccess('assets.index', 'tickets.index', 'tickets.create', 'Inventori Asset', 'Lihat Daftar Tiket', 'Buat Tiket Baru'))
                <li class="nav-item mt-2">
                    <small class="text-secondary fw-bold text-uppercase px-2">ASSET</small>
                </li>

                @if($canAccess('assets.index', 'Inventori Asset'))
                <li>
                    <a href="{{ route('assets.index') }}" class="nav-link text-white">
                        <i class="bi bi-box-seam me-2"></i> Inventori
                    </a>
                </li>
                @endif

                @if($canAccess('tickets.index', 'tickets.create', 'Lihat Daftar Tiket', 'Buat Tiket Baru'))
                <li>
                    <a href="{{ route('tickets.index') }}" class="nav-link text-white">
                        <i class="bi bi-wrench me-2"></i> Corrective/Tiket
                    </a>
                </li>
                @endif

                <hr class="my-2 border-secondary">
            @endif


            <!-- SECTION MENU PANDUAN -->
            @if($canAccess('panduan.index', 'Panduan', 'Lihat Panduan', 'Panduan Penggunaan'))
                <li class="nav-item mt-2">
                    <small class="text-secondary fw-bold text-uppercase px-2">PANDUAN</small>
                </li>

                <li>
                    <a href="{{ route('panduan.index') }}" class="nav-link text-white">
                        <i class="bi bi-book me-2"></i> Panduan Penggunaan
                    </a>
                </li>

                <hr class="my-2 border-secondary">
            @endif

        </ul>
        
        <!-- FOOTER USER PROFILE -->
        <hr class="my-3 border-secondary">
        <div class="mt-auto">
            @auth
            <div class="d-flex align-items-center mb-3 text-white px-1">
                <i class="bi bi-person-circle fs-2 me-2 text-light flex-shrink-0"></i>
                <div class="lh-sm overflow-hidden w-100" style="min-width: 0;">
                    <div class="fw-bold text-truncate" title="{{ $userName }}">
                        {{ $userName }}
                    </div>
                    
                    <div class="mt-1">
                        <span class="badge {{ $roleBadgeClass }}" style="font-size: 0.7rem;">
                            ({{ $userRole }})
                        </span>
                    </div>
                    
                    @if($branchInfo)
                    <div class="text-secondary text-truncate small mt-1" style="font-size: 0.72rem;" title="{{ $branchInfo }}">
                        <i class="bi bi-geo-alt me-1"></i>{{ $branchInfo }}
                    </div>
                    @endif
                </div>
            </div>

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
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>