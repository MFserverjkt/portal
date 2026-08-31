@extends('layouts.app')

@section('content')
<div class="container-fluid px-2 px-md-3 py-2 py-md-3">
    <div class="row justify-content-center m-0">
        <div class="col-12 col-md-10 col-lg-8 p-0">
            <div class="card shadow-sm border-0 rounded-3">
                <!-- Header Card -->
                <div class="card-header bg-warning text-dark py-3 px-3 px-md-4">
                    <h5 class="fw-bold mb-1 fs-6 fs-md-5">
                        <i class="bi bi-file-earmark-text me-2"></i>Form Pre-Test Modul
                    </h5>
                    <small class="fs-7 text-dark-50 opacity-75">Uji pemahaman awal Anda sebelum mempelajari materi e-Learning.</small>
                </div>

                <!-- Body Card & Form -->
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('hc.pretest.store') }}" method="POST">
                        @csrf
                        
                        @foreach($questions as $index => $q)
                        <div class="question-block mb-4 pb-2 border-bottom">
                            <!-- Pertanyaan -->
                            <div class="d-flex align-items-start mb-3">
                                <span class="badge bg-warning text-dark me-2 mt-1 fs-8 fw-bold">{{ $index + 1 }}</span>
                                <p class="fw-bold text-dark mb-0 fs-6 fs-md-6">{{ $q['question'] }}</p>
                            </div>

                            <!-- Pilihan Jawaban -->
                            <div class="options-container d-flex flex-column gap-2">
                                @foreach($q['options'] as $key => $option)
                                <label for="q{{ $q['id'] }}_{{ $key }}" class="custom-option-card d-flex align-items-center p-2 px-3 rounded border">
                                    <input class="form-check-input me-3 my-0 flex-shrink-0" type="radio" name="answers[{{ $q['id'] }}]" id="q{{ $q['id'] }}_{{ $key }}" value="{{ $key }}" required>
                                    <span class="form-check-label fs-7 text-dark w-100 cursor-pointer">
                                        <b class="me-1 text-warning-emphasis">{{ $key }}.</b> {{ $option }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach

                        <!-- Tombol Aksi -->
                        <div class="d-flex flex-column-reverse flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2 mt-4 pt-2">
                            <a href="{{ route('hc.elearning.index') }}" class="btn btn-light text-muted fw-semibold py-2 px-4 fs-7 border">
                                <i class="bi bi-x-circle me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-warning fw-bold py-2 px-4 fs-7 shadow-sm text-dark">
                                <i class="bi bi-send me-1"></i> Kirim Jawaban Pre-Test
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stylings Khusus Tampilan Mobile & Interaksi Card -->
<style>
    .fs-7 { font-size: 0.875rem !important; }
    .fs-8 { font-size: 0.75rem !important; }
    .cursor-pointer { cursor: pointer; }

    /* Desain pilihan jawaban ala kartu interaktif */
    .custom-option-card {
        transition: all 0.2s ease-in-out;
        background-color: #fdfdfd;
        cursor: pointer;
    }

    .custom-option-card:hover {
        background-color: #fffde7;
        border-color: #ffc107 !important;
    }

    /* Status saat radio button di-check/dipilih */
    .custom-option-card:has(input[type="radio"]:checked) {
        background-color: #fff9c4;
        border-color: #ffc107 !important;
        box-shadow: 0 2px 4px rgba(255, 193, 7, 0.25);
    }

    /* Pembesar area radio button untuk touch screen HP */
    .custom-option-card input[type="radio"] {
        transform: scale(1.15);
        accent-color: #ffc107;
    }
</style>
@endsection