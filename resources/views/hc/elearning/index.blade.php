@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-mortarboard text-primary me-2"></i>HC e-Learning Portal</h3>
            <p class="text-muted mb-0">Akses modul pembelajaran dan tingkatkan kompetensi kerja Anda.</p>
        </div>
        <div>
            <a href="{{ route('hc.pretest.index') }}" class="btn btn-outline-warning btn-sm me-1">
                <i class="bi bi-file-earmark-text me-1"></i> Ikuti Pre-Test
            </a>
            <a href="{{ route('hc.posttest.index') }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-check me-1"></i> Ikuti Post-Test
            </a>
        </div>
    </div>

    <div class="row g-3">
        @foreach($modules as $module)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-secondary">{{ $module['category'] }}</span>
                        <span class="badge {{ $module['status'] == 'Selesai' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $module['status'] }}
                        </span>
                    </div>
                    <h5 class="card-title fw-bold mt-2">{{ $module['title'] }}</h5>
                    <p class="text-muted small mb-3">
                        <i class="bi bi-clock me-1"></i> {{ $module['duration'] }} | 
                        <i class="bi bi-file-type-pdf me-1"></i> {{ $module['type'] }}
                    </p>
                    <div class="mt-auto">
                        <a href="{{ $module['link'] }}" class="btn btn-primary w-100 btn-sm">
                            <i class="bi bi-play-circle me-1"></i> Pelajari Materi
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection