@extends('layouts.app')

@section('content')
<style>
    /* Styling khusus saat fungsi Print / Simpan PDF dijalankan */
    @media print {
        /* Sembunyikan seluruh elemen di dalam body */
        body * {
            visibility: hidden;
        }
        
        /* Tampilkan HANYA area form checklist */
        #printable-area, #printable-area * {
            visibility: visible;
        }

        /* Atur posisi printable area agar pas di pojok kiri atas halaman */
        #printable-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
        }

        /* Sembunyikan tombol aksi */
        .d-print-none {
            display: none !important;
        }
    }
</style>

<div class="container-fluid bg-white p-4 rounded shadow-sm border" id="printable-area">
    <!-- Header Form -->
    <div class="row border-bottom border-2 border-dark pb-2 mb-3 align-items-center">
        <div class="col-3 text-center border-end border-dark">
            <h4 class="fw-bold m-0">MAISON FÉERIE</h4>
            <small class="text-muted">BUSY BAKING MAGIC</small>
        </div>
        <div class="col-6 text-center">
            <h5 class="fw-bold mb-1">FORM CHECKLIST OUTLET</h5>
            <span>(Routine/IT Helpdesk)</span>
        </div>
        <div class="col-3 text-end fs-7 border-start border-dark">
            <div><strong>No Dokumen :</strong> MF-IT-FRM-01</div>
            <div><strong>Revisi :</strong> 0</div>
            <div><strong>Tanggal Berlaku :</strong> 16 Agustus 2024</div>
        </div>
    </div>

    <form action="{{ route('it.checklist.store') }}" method="POST" id="checklistForm">
        @csrf
        <!-- Metadata Header -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="mb-2">
                    <label class="fw-bold">Dikerjakan Oleh :</label>
                    <input type="text" name="dikerjakan_oleh" class="form-control form-control-sm border-dark-subtle" required>
                </div>
                <div>
                    <label class="fw-bold">Outlet :</label>
                    <select name="outlet" class="form-select form-select-sm border-dark-subtle" required>
                        <option value="">-- Pilih Outlet --</option>
                        @foreach($outlets as $out)
                            <option value="{{ $out->branch_name }}">{{ $out->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-2">
                    <label class="fw-bold d-block">Order :</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="order_type" value="Jadwal Preventif" required>
                        <label class="form-check-label">Jadwal Preventif</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="order_type" value="IT Helpdesk">
                        <label class="form-check-label">IT Helpdesk</label>
                    </div>
                </div>
                <div>
                    <label class="fw-bold">Tanggal Pekerjaan :</label>
                    <input type="date" name="tanggal_pekerjaan" class="form-control form-control-sm border-dark-subtle" required>
                </div>
            </div>
        </div>

        <!-- Tabel Checklist -->
        <div class="table-responsive">
            <table class="table table-bordered border-dark text-center align-middle" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" style="width: 40px;">No</th>
                        <th rowspan="2" style="width: 250px;">Pekerjaan</th>
                        <th colspan="6">Kondisi (Isi &#10003;)</th>
                        <th rowspan="2">Keterangan</th>
                    </tr>
                    <tr>
                        <th style="width: 60px;">Baik</th>
                        <th style="width: 60px;">Rusak</th>
                        <th style="width: 60px;">Bersih</th>
                        <th style="width: 60px;">Kotor</th>
                        <th style="width: 60px;">Rapi</th>
                        <th style="width: 60px;">Tidak Rapi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category => $items)
                        <tr class="table-secondary fw-bold text-start">
                            <td colspan="9" class="text-center">{{ $category }}</td>
                        </tr>
                        @foreach($items as $index => $item)
                        @php $key = Str::slug($category . '_' . $item); @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-start">{{ $item }}</td>
                            <td><input type="radio" name="items[{{ $key }}][kondisi_fisik]" value="Baik" required></td>
                            <td><input type="radio" name="items[{{ $key }}][kondisi_fisik]" value="Rusak"></td>
                            <td><input type="radio" name="items[{{ $key }}][kebersihan]" value="Bersih" required></td>
                            <td><input type="radio" name="items[{{ $key }}][kebersihan]" value="Kotor"></td>
                            <td><input type="radio" name="items[{{ $key }}][kerapihan]" value="Rapi" required></td>
                            <td><input type="radio" name="items[{{ $key }}][kerapihan]" value="Tidak Rapi"></td>
                            <td><input type="text" name="items[{{ $key }}][keterangan]" class="form-control form-control-sm"></td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Section Tanda Tangan Canvas -->
        <div class="row text-center mt-4 border border-dark mx-0">
            <div class="col-4 border-end border-dark p-2">
                <div class="fw-bold mb-1">Dibuat Oleh</div>
                <canvas id="canvas-it" width="250" height="100" class="border rounded bg-light"></canvas>
                <button type="button" class="btn btn-danger btn-sm d-block mx-auto my-1 d-print-none" onclick="clearCanvas('canvas-it')">Hapus</button>
                <input type="hidden" name="ttd_it" id="ttd_it">
                <hr class="my-1">
                <input type="text" name="nama_it" class="form-control form-control-sm text-center border-0 fw-bold" placeholder="Nama Terang">
                <div class="small fw-bold">IT</div>
            </div>

            <div class="col-4 border-end border-dark p-2">
                <div class="fw-bold mb-1">Diperiksa oleh</div>
                <canvas id="canvas-leader" width="250" height="100" class="border rounded bg-light"></canvas>
                <button type="button" class="btn btn-danger btn-sm d-block mx-auto my-1 d-print-none" onclick="clearCanvas('canvas-leader')">Hapus</button>
                <input type="hidden" name="ttd_leader" id="ttd_leader">
                <hr class="my-1">
                <input type="text" name="nama_leader" class="form-control form-control-sm text-center border-0 fw-bold" placeholder="Nama Terang">
                <div class="small fw-bold">Leader/Supervisor Outlet</div>
            </div>

            <div class="col-4 p-2">
                <div class="fw-bold mb-1">Diketahui Oleh</div>
                <canvas id="canvas-fa" width="250" height="100" class="border rounded bg-light"></canvas>
                <button type="button" class="btn btn-danger btn-sm d-block mx-auto my-1 d-print-none" onclick="clearCanvas('canvas-fa')">Hapus</button>
                <input type="hidden" name="ttd_fa_manager" id="ttd_fa_manager">
                <hr class="my-1">
                <input type="text" name="nama_fa_manager" class="form-control form-control-sm text-center border-0 fw-bold" placeholder="Nama Terang">
                <div class="small fw-bold">FA Senior Manager</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center mt-4 d-print-none">
            <button type="button" class="btn btn-success me-2" onclick="saveAndPrint()">
                <i class="bi bi-printer me-1"></i> Simpan / Cetak PDF
            </button>
            <button type="button" class="btn btn-secondary me-2" onclick="saveAsJPG()">
                <i class="bi bi-camera me-1"></i> Simpan JPG (Kirim WA)
            </button>
            <button type="reset" class="btn btn-outline-primary" onclick="resetForm()">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Form
            </button>
        </div>
    </form>
</div>

<!-- Library html2canvas untuk Ekspor Gambar -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
    // Setup Signature Pad Canvas
    function initCanvas(id) {
        const canvas = document.getElementById(id);
        const ctx = canvas.getContext('2d');
        let painting = false;

        function startPosition(e) { painting = true; draw(e); }
        function finishedPosition() { painting = false; ctx.beginPath(); }
        function draw(e) {
            if (!painting) return;
            const rect = canvas.getBoundingClientRect();
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
        }

        // Touch support untuk perangkat mobile/tablet
        canvas.addEventListener('mousedown', startPosition);
        canvas.addEventListener('mouseup', finishedPosition);
        canvas.addEventListener('mousemove', draw);
        
        canvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent('mousedown', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        });
        canvas.addEventListener('touchend', (e) => {
            const mouseEvent = new MouseEvent('mouseup', {});
            canvas.dispatchEvent(mouseEvent);
        });
        canvas.addEventListener('touchmove', (e) => {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent('mousemove', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        });
    }

    ['canvas-it', 'canvas-leader', 'canvas-fa'].forEach(initCanvas);

    function clearCanvas(id) {
        const canvas = document.getElementById(id);
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    function saveAndPrint() {
        document.getElementById('ttd_it').value = document.getElementById('canvas-it').toDataURL();
        document.getElementById('ttd_leader').value = document.getElementById('canvas-leader').toDataURL();
        document.getElementById('ttd_fa_manager').value = document.getElementById('canvas-fa').toDataURL();
        
        window.print();
    }

    function saveAsJPG() {
        html2canvas(document.getElementById('printable-area')).then(canvas => {
            const link = document.createElement('a');
            link.download = 'Checklist_Outlet_' + new Date().toISOString().slice(0, 10) + '.jpg';
            link.href = canvas.toDataURL('image/jpeg');
            link.click();
        });
    }

    function resetForm() {
        ['canvas-it', 'canvas-leader', 'canvas-fa'].forEach(clearCanvas);
        document.getElementById('checklistForm').reset();
    }
</script>
@endsection