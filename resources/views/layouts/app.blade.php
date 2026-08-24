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

    // 1. Ambil Nama User
    $userName = $user?->name ?? $user?->username ?? 'User';

    // 2. Deteksi Role dengan fleksibel (Spatie Role DB / Column Role)
    $detectedRole = 'GUEST';
    if ($user) {
        if (method_exists($user, 'getRoleNames') && $user->getRoleNames()->count() > 0) {
            $detectedRole = $user->getRoleNames()->first();
        } else {
            $detectedRole = $user->role ?? 'GUEST';
        }
    }
    $userRole = strtoupper(trim($detectedRole));

    // 3. Helper Closure Pengecekan Akses Role
    $hasRole = function(...$roles) use ($user, $userRole) {
        if (!$user) return false;

        // Admin & Administrator selalu Full Access
        if (in_array($userRole, ['ADMIN', 'ADMINISTRATOR'])) {
            return true;
        }

        // Pengecekan via Spatie method
        if (method_exists($user, 'hasAnyRole')) {
            if ($user->hasAnyRole($roles) || $user->hasAnyRole(array_map('strtolower', $roles))) {
                return true;
            }
        }

        // Pengecekan via string $userRole
        $upperRoles = array_map('strtoupper', $roles);
        return in_array($userRole, $upperRoles);
    };

    // Hak Akses Spesifik Per-Role:
    $isIT          = $hasRole('IT');
    $isMaintenance = $hasRole('MAINTENANCE');
    $isHC          = $hasRole('HC');
    $isOutlet      = $hasRole('OUTLET');

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
        'HC'                    => 'bg-success text-white',
        default                 => 'bg-secondary text-white'
    };
@endphp

<div class="d-flex">
    <!-- Sidebar navigation -->
    <div class="bg-dark text-white p-3 d-flex flex-column" id="sidebar">
        <h4 class="text-center my-3 fw-bold text-primary">PORTAL APP</h4>
        <hr class="border-secondary">
        
        <ul class="nav nav-pills flex-column mb-auto">

            <!-- SECTION MENU IT (Khusus Admin & IT) -->
            @if($isIT)
                <li class="nav-item mt-2">
                    <small class="text-secondary fw-bold text-uppercase px-2">IT</small>
                </li>
                
                <li>
                    <a href="{{ route('users.index') }}" class="nav-link text-white {{ request()->routeIs('users.index*') ? 'active bg-primary' : '' }}">
                        <i class="bi bi-people me-2"></i> User Management
                    </a>
                </li>

                <li>
                    <a href="{{ route('users.roles') }}" class="nav-link text-white {{ request()->routeIs('users.roles*') ? 'active bg-primary' : '' }}">
                        <i class="bi bi-shield-lock me-2"></i> User Role
                    </a>
                </li>

                <li>
                    <a href="{{ route('report.it') }}" class="nav-link text-white {{ request()->routeIs('report.it*') ? 'active bg-primary' : '' }}">
                        <i class="bi bi-file-earmark-text me-2"></i> Report Corrective
                    </a>
                </li>

                <hr class="my-2 border-secondary">
            @endif

            <!-- SECTION MENU HC LEARNING (Admin, IT, HC, Outlet) -->
            @if($isIT || $isHC || $isOutlet)
                <li class="nav-item mt-2">
                    <small class="text-secondary fw-bold text-uppercase px-2">HC LEARNING</small>
                </li>

                <li>
                    <a href="{{ route('hc.elearning.index') }}" class="nav-link text-white {{ request()->routeIs('hc.elearning*') ? 'active bg-primary' : '' }}">
                        <i class="bi bi-mortarboard me-2"></i> e-Learning
                    </a>
                </li>

                <li>
                    <a href="{{ route('hc.pretest.index') }}" class="nav-link text-white {{ request()->routeIs('hc.pretest*') ? 'active bg-primary' : '' }}">
                        <i class="bi bi-file-earmark-text me-2"></i> Pre-Test
                    </a>
                </li>

                <li>
                    <a href="{{ route('hc.posttest.index') }}" class="nav-link text-white {{ request()->routeIs('hc.posttest*') ? 'active bg-primary' : '' }}">
                        <i class="bi bi-file-earmark-check me-2"></i> Post-Test
                    </a>
                </li>

                <hr class="my-2 border-secondary">
            @endif

            <!-- SECTION MENU MAINTENANCE (Admin & Maintenance) -->
            @if($isMaintenance)
                <li class="nav-item mt-2">
                    <small class="text-secondary fw-bold text-uppercase px-2">MAINTENANCE</small>
                </li>

                <li>
                    <a href="{{ route('report.maintenance') }}" class="nav-link text-white {{ request()->routeIs('report.maintenance*') ? 'active bg-primary' : '' }}">
                        <i class="bi bi-file-earmark-text me-2"></i> Report Corrective
                    </a>
                </li>

                <hr class="my-2 border-secondary">
            @endif

            <!-- SECTION MENU ASSET & SUPPORT -->
            @if($isIT || $isMaintenance || $isOutlet)
                <li class="nav-item mt-2">
                    <small class="text-secondary fw-bold text-uppercase px-2">ASSET & SUPPORT</small>
                </li>

                {{-- Inventori Asset hanya untuk IT & Maintenance --}}
                @if($isIT || $isMaintenance)
                <li>
                    <a href="{{ route('assets.index') }}" class="nav-link text-white {{ request()->routeIs('assets*') ? 'active bg-primary' : '' }}">
                        <i class="bi bi-box-seam me-2"></i> Inventori
                    </a>
                </li>
                @endif

                {{-- Tiket Corrective untuk IT, Maintenance, dan Outlet --}}
                <li>
                    <a href="{{ route('tickets.index') }}" class="nav-link text-white {{ request()->routeIs('tickets*') ? 'active bg-primary' : '' }}">
                        <i class="bi bi-wrench me-2"></i> Corrective/Tiket
                    </a>
                </li>

                <hr class="my-2 border-secondary">
            @endif

            <!-- SECTION MENU PANDUAN (SEMUA ROLE) -->
            <li class="nav-item mt-2">
                <small class="text-secondary fw-bold text-uppercase px-2">PANDUAN</small>
            </li>

            <li>
                <a href="{{ route('panduan.index') }}" class="nav-link text-white {{ request()->routeIs('panduan*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-book me-2"></i> Panduan Penggunaan
                </a>
            </li>

            <hr class="my-2 border-secondary">

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