@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2"></i>Form Pre-Test Modul</h5>
                    <small>Uji pemahaman awal Anda sebelum mempelajari materi e-Learning.</small>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('hc.pretest.store') }}" method="POST">
                        @csrf
                        
                        @foreach($questions as $index => $q)
                        <div class="mb-4">
                            <p class="fw-bold mb-2">{{ $index + 1 }}. {{ $q['question'] }}</p>
                            @foreach($q['options'] as $key => $option)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="answers[{{ $q['id'] }}]" id="q{{ $q['id'] }}_{{ $key }}" value="{{ $key }}" required>
                                <label class="form-check-label" for="q{{ $q['id'] }}_{{ $key }}">
                                    <b>{{ $key }}.</b> {{ $option }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <hr class="my-3">
                        @endforeach

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('hc.elearning.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-warning fw-bold">Kirim Jawaban Pre-Test</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection