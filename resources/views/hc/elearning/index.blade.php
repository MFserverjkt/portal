@extends('layouts.app')

@section('content')
<div class="container-fluid px-2 px-md-3 py-2">
    <!-- Header Page & Action Buttons -->
    <div class="row g-2 align-items-center mb-3">
        <!-- Title & Subtitle -->
        <div class="col-12 col-md-7 text-center text-md-start">
            <h4 class="fw-bold mb-1 fs-5 text-dark">
                <i class="bi bi-mortarboard-fill text-primary me-2"></i>HC e-Learning Portal
            </h4>
            <p class="text-muted fs-7 mb-0">Akses modul pembelajaran & tingkatkan kompetensi Anda.</p>
        </div>

        <!-- Tombol Ujian (Stacking & Full Width di Mobile) -->
        <div class="col-12 col-md-5">
            <div class="row g-2">
                <div class="col-6 col-md-auto ms-md-auto">
                    <a href="{{ route('hc.pretest.index') }}" class="btn btn-outline-warning btn-sm w-100 py-2 py-md-1 fs-7 fw-semibold">
                        <i class="bi bi-file-earmark-text me-1"></i> Pre-Test
                    </a>
                </div>
                <div class="col-6 col-md-auto">
                    <a href="{{ route('hc.posttest.index') }}" class="btn btn-outline-success btn-sm w-100 py-2 py-md-1 fs-7 fw-semibold">
                        <i class="bi bi-file-earmark-check me-1"></i> Post-Test
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modul Cards Grid -->
    <div class="row g-2 g-md-3">
        @foreach($modules as $module)
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 rounded-3 card-module">
                <div class="card-body p-3 d-flex flex-column">
                    <!-- Category & Status Badge -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-secondary-subtle text-secondary border fs-8 fw-semibold px-2 py-1">
                            {{ $module['category'] }}
                        </span>
                        <span class="badge {{ $module['status'] == 'Selesai' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' }} fs-8 fw-bold px-2 py-1">
                            {{ $module['status'] }}
                        </span>
                    </div>

                    <!-- Module Title -->
                    <h6 class="card-title fw-bold text-dark mb-2 fs-6">
                        {{ $module['title'] }}
                    </h6>

                    <!-- Module Meta Info -->
                    <p class="text-muted fs-7 mb-3">
                        <span class="me-2"><i class="bi bi-clock me-1 text-primary"></i>{{ $module['duration'] }}</span>
                        <span><i class="bi bi-file-earmark-pdf me-1 text-danger"></i>{{ $module['type'] }}</span>
                    </p>

                    <!-- Action Button -->
                    <div class="mt-auto">
                        <a href="{{ $module['link'] }}" class="btn btn-primary w-100 btn-sm py-2 fs-7 fw-bold shadow-sm">
                            <i class="bi bi-play-circle me-1"></i> Pelajari Materi
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Mobile Optimization Style Extensions -->
<style>
    .fs-7 { font-size: 0.85rem !important; }
    .fs-8 { font-size: 0.75rem !important; }

    .card-module {
        transition: transform 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .card-module:active {
        transform: scale(0.98);
    }

    @media (min-width: 768px) {
        .card-module:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
        }
    }
</style>
@endsection