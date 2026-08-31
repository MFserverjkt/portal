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
        .nav-link { border-radius: 0.375rem; transition: background-color 0.2s; }
        .nav-link:hover { background-color: rgba(255, 255, 255, 0.1); }
        .fs-7 { font-size: 0.85rem !important; }
        .fs-8 { font-size: 0.75rem !important; }

        @media (min-width: 768px) {
            #sidebarDesktop { width: 260px; min-height: 100vh; }
        }
    </style>
</head>
<body>
@php
    $user = auth()->user();

    $userName = $user?->name ?? $user?->username ?? 'User';

    $detectedRole = 'GUEST';
    if ($user) {
        if (method_exists($user, 'getRoleNames') && $user->getRoleNames()->count() > 0) {
            $detectedRole = $user->getRoleNames()->first();
        } else {
            $detectedRole = $user->role ?? 'GUEST';
        }
    }
    $userRole = strtoupper(trim($detectedRole));

    $hasRole = function(...$roles) use ($user, $userRole) {
        if (!$user) return false;

        if (in_array($userRole, ['ADMIN', 'ADMINISTRATOR'])) {
            return true;
        }

        if (method_exists($user, 'hasAnyRole')) {
            if ($user->hasAnyRole($roles) || $user->hasAnyRole(array_map('strtolower', $roles))) {
                return true;
            }
        }

        $upperRoles = array_map('strtoupper', $roles);
        return in_array($userRole, $upperRoles);
    };

    $isIT          = $hasRole('IT');
    $isMaintenance = $hasRole('MAINTENANCE');
    $isHC          = $hasRole('HC');
    $isOutlet      = $hasRole('OUTLET');

    $branchInfo = $user?->branch_name 
        ?? $user?->outlet_name 
        ?? $user?->branch_code 
        ?? $user?->outlet_code 
        ?? null;

    $roleBadgeClass = match($userRole) {
        'ADMIN', 'ADMINISTRATOR' => 'bg-danger text-white',
        'IT'                    => 'bg-primary text-white',
        'MAINTENANCE'           => 'bg-warning text-dark',
        'OUTLET'                => 'bg-info text-white',
        'HC'                    => 'bg-success text-white',
        default                 => 'bg-secondary text-white'
    };
@endphp

<!-- NAVBAR MOBILE (< 768px) -->
<nav class="navbar navbar-dark bg-dark d-md-none sticky-top shadow-sm px-3">
    <div class="container-fluid p-0">
        <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile" aria-controls="sidebarMobile">
            <span class="navbar-toggler-icon"></span>
        </button>
        <span class="navbar-brand fw-bold text-primary mb-0 fs-6">PORTAL APP</span>
        <div class="d-flex align-items-center">
            <span class="badge {{ $roleBadgeClass }} fs-8">({{ $userRole }})</span>
        </div>
    </div>
</nav>

<div class="d-flex">
    <!-- SIDEBAR DESKTOP (≥ 768px) -->
    <div class="bg-dark text-white p-3 d-none d-md-flex flex-column flex-shrink-0" id="sidebarDesktop">
        <h4 class="text-center my-3 fw-bold text-primary">PORTAL APP</h4>
        <hr class="border-secondary">
        
        <ul class="nav nav-pills flex-column mb-auto">
            @if($isIT)
                <li class="nav-item mt-2">
                    <small class="text-secondary fw-bold text-uppercase px-2 fs-8">IT</small>
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

            @if($isIT || $isHC || $isOutlet)
                <li class="nav-item mt-2">
                    <small class="text-secondary fw-bold text-uppercase px-2 fs-8">HC LEARNING</small>
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

            @if($isMaintenance)
                <li class="nav-item mt-2">
                    <small class="text-secondary fw-bold text-uppercase px-2 fs-8">MAINTENANCE</small>
                </li>
                <li>
                    <a href="{{ route('report.maintenance') }}" class="nav-link text-white {{ request()->routeIs('report.maintenance*') ? 'active bg-primary' : '' }}">
                        <i class="bi bi-file-earmark-text me-2"></i> Report Corrective
                    </a>
                </li>
                <hr class="my-2 border-secondary">
            @endif

            @if($isIT || $isMaintenance || $isOutlet)
                <li class="nav-item mt-2">
                    <small class="text-secondary fw-bold text-uppercase px-2 fs-8">ASSET & SUPPORT</small>
                </li>
                @if($isIT || $isMaintenance)
                <li>
                    <a href="{{ route('assets.index') }}" class="nav-link text-white {{ request()->routeIs('assets*') ? 'active bg-primary' : '' }}">
                        <i class="bi bi-box-seam me-2"></i> Inventori
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('tickets.index') }}" class="nav-link text-white {{ request()->routeIs('tickets*') ? 'active bg-primary' : '' }}">
                        <i class="bi bi-wrench me-2"></i> Corrective/Tiket
                    </a>
                </li>
                <hr class="my-2 border-secondary">
            @endif

            <li class="nav-item mt-2">
                <small class="text-secondary fw-bold text-uppercase px-2 fs-8">PANDUAN</small>
            </li>
            <li>
                <a href="{{ route('panduan.index') }}" class="nav-link text-white {{ request()->routeIs('panduan*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-book me-2"></i> Panduan Penggunaan
                </a>
            </li>
            <hr class="my-2 border-secondary">
        </ul>
        
        <hr class="my-3 border-secondary">
        <div class="mt-auto">
            @auth
            <div class="d-flex align-items-center mb-3 text-white px-1">
                <i class="bi bi-person-circle fs-2 me-2 text-light flex-shrink-0"></i>
                <div class="lh-sm overflow-hidden w-100" style="min-width: 0;">
                    <div class="fw-bold text-truncate fs-7" title="{{ $userName }}">
                        {{ $userName }}
                    </div>
                    <div class="mt-1">
                        <span class="badge {{ $roleBadgeClass }} fs-8">({{ $userRole }})</span>
                    </div>
                    @if($branchInfo)
                    <div class="text-secondary text-truncate fs-8 mt-1" title="{{ $branchInfo }}">
                        <i class="bi bi-geo-alt me-1"></i>{{ $branchInfo }}
                    </div>
                    @endif
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center py-2">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
            @endauth
        </div>
    </div>

    <!-- SIDEBAR OFFCANVAS MOBILE -->
    <div class="offcanvas offcanvas-start bg-dark text-white d-md-none" tabindex="-1" id="sidebarMobile" aria-labelledby="sidebarMobileLabel" style="width: 280px;">
        <div class="offcanvas-header border-bottom border-secondary">
            <h5 class="offcanvas-title fw-bold text-primary" id="sidebarMobileLabel">PORTAL APP</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column justify-content-between p-3">
            <ul class="nav nav-pills flex-column mb-auto">
                @if($isIT)
                    <li class="nav-item mt-2"><small class="text-secondary fw-bold text-uppercase px-2 fs-8">IT</small></li>
                    <li><a href="{{ route('users.index') }}" class="nav-link text-white {{ request()->routeIs('users.index*') ? 'active bg-primary' : '' }}"><i class="bi bi-people me-2"></i> User Management</a></li>
                    <li><a href="{{ route('users.roles') }}" class="nav-link text-white {{ request()->routeIs('users.roles*') ? 'active bg-primary' : '' }}"><i class="bi bi-shield-lock me-2"></i> User Role</a></li>
                    <li><a href="{{ route('report.it') }}" class="nav-link text-white {{ request()->routeIs('report.it*') ? 'active bg-primary' : '' }}"><i class="bi bi-file-earmark-text me-2"></i> Report Corrective</a></li>
                    <hr class="my-2 border-secondary">
                @endif
                @if($isIT || $isHC || $isOutlet)
                    <li class="nav-item mt-2"><small class="text-secondary fw-bold text-uppercase px-2 fs-8">HC LEARNING</small></li>
                    <li><a href="{{ route('hc.elearning.index') }}" class="nav-link text-white {{ request()->routeIs('hc.elearning*') ? 'active bg-primary' : '' }}"><i class="bi bi-mortarboard me-2"></i> e-Learning</a></li>
                    <li><a href="{{ route('hc.pretest.index') }}" class="nav-link text-white {{ request()->routeIs('hc.pretest*') ? 'active bg-primary' : '' }}"><i class="bi bi-file-earmark-text me-2"></i> Pre-Test</a></li>
                    <li><a href="{{ route('hc.posttest.index') }}" class="nav-link text-white {{ request()->routeIs('hc.posttest*') ? 'active bg-primary' : '' }}"><i class="bi bi-file-earmark-check me-2"></i> Post-Test</a></li>
                    <hr class="my-2 border-secondary">
                @endif
                @if($isMaintenance)
                    <li class="nav-item mt-2"><small class="text-secondary fw-bold text-uppercase px-2 fs-8">MAINTENANCE</small></li>
                    <li><a href="{{ route('report.maintenance') }}" class="nav-link text-white {{ request()->routeIs('report.maintenance*') ? 'active bg-primary' : '' }}"><i class="bi bi-file-earmark-text me-2"></i> Report Corrective</a></li>
                    <hr class="my-2 border-secondary">
                @endif
                @if($isIT || $isMaintenance || $isOutlet)
                    <li class="nav-item mt-2"><small class="text-secondary fw-bold text-uppercase px-2 fs-8">ASSET & SUPPORT</small></li>
                    @if($isIT || $isMaintenance)
                    <li><a href="{{ route('assets.index') }}" class="nav-link text-white {{ request()->routeIs('assets*') ? 'active bg-primary' : '' }}"><i class="bi bi-box-seam me-2"></i> Inventori</a></li>
                    @endif
                    <li><a href="{{ route('tickets.index') }}" class="nav-link text-white {{ request()->routeIs('tickets*') ? 'active bg-primary' : '' }}"><i class="bi bi-wrench me-2"></i> Corrective/Tiket</a></li>
                    <hr class="my-2 border-secondary">
                @endif
                <li class="nav-item mt-2"><small class="text-secondary fw-bold text-uppercase px-2 fs-8">PANDUAN</small></li>
                <li><a href="{{ route('panduan.index') }}" class="nav-link text-white {{ request()->routeIs('panduan*') ? 'active bg-primary' : '' }}"><i class="bi bi-book me-2"></i> Panduan Penggunaan</a></li>
                <hr class="my-2 border-secondary">
            </ul>
            
            <div class="mt-4 pt-3 border-top border-secondary">
                @auth
                <div class="d-flex align-items-center mb-3 text-white px-1">
                    <i class="bi bi-person-circle fs-2 me-2 text-light flex-shrink-0"></i>
                    <div class="lh-sm overflow-hidden w-100" style="min-width: 0;">
                        <div class="fw-bold text-truncate fs-7" title="{{ $userName }}">{{ $userName }}</div>
                        <div class="mt-1"><span class="badge {{ $roleBadgeClass }} fs-8">({{ $userRole }})</span></div>
                        @if($branchInfo)
                        <div class="text-secondary text-truncate fs-8 mt-1" title="{{ $branchInfo }}"><i class="bi bi-geo-alt me-1"></i>{{ $branchInfo }}</div>
                        @endif
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center py-2">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
                @endauth
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT CONTAINER -->
    <div class="flex-grow-1 p-3 p-md-4 overflow-auto" style="min-width: 0;">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 px-3 fs-7" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 fs-7" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
@extends('layouts.footer')

</html>