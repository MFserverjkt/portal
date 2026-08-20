@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Page -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-book me-2 text-primary"></i>Panduan Penggunaan Sistem</h3>
            <p class="text-muted mb-0">Petunjuk langkah demi langkah pengoperasian fitur-fitur di Portal App.</p>
        </div>
    </div>

    <!-- Quick Search Input (Optional Dynamic Filter) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchPanduan" class="form-control border-start-0" placeholder="Cari panduan (contoh: tiket, BAST, asset, dll)...">
            </div>
        </div>
    </div>

    <!-- List Panduan Accordion -->
    <div class="accordion" id="accordionPanduan">

        <!-- PANDUAN 1: BIKIN TIKET / CORRECTIVE -->
        <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden panduan-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    <i class="bi bi-ticket-perforated me-3 text-primary fs-5"></i>
                    <div>
                        <span class="d-block text-dark fw-bold">Panduan Membuat Tiket / Corrective</span>
                        <small class="text-muted">Prosedur pelaporan masalah atau pengajuan perbaikan aset/sistem oleh Outlet/User.</small>
                    </div>
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionPanduan">
                <div class="accordion-body bg-light border-top">
                    <ol class="mb-0 lh-lg">
                        <td>Masuk ke menu <strong>ASSET &gt; Corrective/Tiket</strong> pada sidebar.</td>
                        <td>Klik tombol <strong><i class="bi bi-plus-circle me-1"></i>Buat Tiket Baru</strong>.</td>
                        <td>Pilih aset yang mengalami keluhan/kerusakan.</td>
                        <td>Isi judul keluhan, deskripsi permasalahan secara rinci, serta upload foto/lampiran pendukung.</td>
                        <td>Klik tombol <strong>Simpan / Kirim Tiket</strong>. Tiket akan secara otomatis diteruskan ke tim IT/Maintenance.</td>
                    </ol>
                </div>
            </div>
        </div>

        <!-- PANDUAN 2: BAST (BERITA ACARA SERAH TERIMA) -->
        <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden panduan-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    <i class="bi bi-file-earmark-check me-3 text-success fs-5"></i>
                    <div>
                        <span class="d-block text-dark fw-bold">Panduan Pengisian & Konfirmasi Form BAST</span>
                        <small class="text-muted">Alur pembuatan Berita Acara Serah Terima pengerjaan teknisi dan konfirmasi tanda tangan.</small>
                    </div>
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionPanduan">
                <div class="accordion-body bg-light border-top">
                    <h6 class="fw-bold text-success mb-2"><i class="bi bi-tools me-1"></i>Untuk Teknisi / IT / Maintenance:</h6>
                    <ol class="mb-3 lh-lg">
                        <td>Buka detail tiket yang berstatus diproses.</td>
                        <td>Klik tombol <strong><i class="bi bi-file-earmark-plus me-1"></i>Buat Form BAST</strong>.</td>
                        <td>Isi rincian tindakan perbaikan, penggantian sparepart (jika ada), serta tanda tangan teknisi secara digital.</td>
                        <td>Simpan form BAST. Tiket akan diperbarui menjadi siap dikonfirmasi/selesai.</td>
                    </ol>
                    <h6 class="fw-bold text-primary mb-2"><i class="bi bi-shop me-1"></i>Untuk Pelapor / Outlet:</h6>
                    <ol class="mb-0 lh-lg">
                        <td>Buka detail tiket yang sudah dilengkapi BAST oleh teknisi.</td>
                        <td>Periksa hasil pekerjaan teknisi pada rincian BAST.</td>
                        <td>Klik tombol <strong>Konfirmasi Selesai / DONE</strong> serta bubuhkan tanda tangan penerimaan.</td>
                    </ol>
                </div>
            </div>
        </div>

        <!-- PANDUAN 3: INVENTORI ASSET -->
        <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden panduan-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    <i class="bi bi-box-seam me-3 text-warning fs-5"></i>
                    <div>
                        <span class="d-block text-dark fw-bold">Panduan Pengelolaan Inventori Asset</span>
                        <small class="text-muted">Cara melihat daftar aset, melakukan pencarian, ekspor data, serta impor via Excel.</small>
                    </div>
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionPanduan">
                <div class="accordion-body bg-light border-top">
                    <ul class="mb-0 lh-lg">
                        <li><strong>Melihat Aset:</strong> Buka menu <strong>ASSET &gt; Inventori</strong> untuk melihat daftar aset terdaftar di cabang Anda.</li>
                        <li><strong>Filter &amp; Pencarian:</strong> Gunakan kolom pencarian untuk memfilter berdasarkan Kode Asset, Nama Asset, atau Kategori.</li>
                        <li><strong>Export Data (Admin/IT/Maintenance):</strong> Klik tombol <code>Export Excel</code> di bagian atas tabel untuk mengunduh laporan inventori.</li>
                        <li><strong>Import Data (Admin/IT/Maintenance):</strong> Klik <code>Import Excel</code> untuk memperbarui atau menambahkan daftar aset dalam jumlah banyak sekaligus via file template Excel.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- PANDUAN 4: EXPORT REPORT CORRECTIVE -->
        <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden panduan-item">
            <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    <i class="bi bi-file-earmark-spreadsheet me-3 text-info fs-5"></i>
                    <div>
                        <span class="d-block text-dark fw-bold">Panduan Laporan &amp; Export Excel Corrective</span>
                        <small class="text-muted">Prosedur penarikan laporan rekap perbaikan IT / Maintenance ke format Excel.</small>
                    </div>
                </button>
            </h2>
            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionPanduan">
                <div class="accordion-body bg-light border-top">
                    <ol class="mb-0 lh-lg">
                        <td>Pilih menu <strong>Report Corrective</strong> di bawah kategori IT atau MAINTENANCE sesuai divisi.</td>
                        <td>Gunakan dropdown filter <strong>Branch / Cabang</strong> jika ingin memfilter lokasi tertentu.</td>
                        <td>Klik tombol <strong>Export Excel</strong>.</td>
                        <td>File Excel akan terunduh secara otomatis berisi riwayat tiket, Tanggal Tiket, Tanggal BAST, Tanggal Done, serta rincian tindakan perbaikan.</td>
                    </ol>
                </div>
            </div>
        </div>

        <!-- PANDUAN 5: HC LEARNING (PRE-TEST & POST-TEST) -->
        <!-- <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden panduan-item">
            <h2 class="accordion-header" id="headingFive">
                <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                    <i class="bi bi-mortarboard me-3 text-purple fs-5"></i>
                    <div>
                        <span class="d-block text-dark fw-bold">Panduan HC Learning (Modul &amp; Ujian)</span>
                        <small class="text-muted">Petunjuk pengerjaan e-Learning, Pre-Test, dan Post-Test karyawan/outlet.</small>
                    </div>
                </button>
            </h2>
            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#accordionPanduan">
                <div class="accordion-body bg-light border-top">
                    <ol class="mb-0 lh-lg">
                        <td>Buka menu <strong>HC LEARNING &gt; e-Learning</strong> untuk membaca materi dan modul pelatihan.</td>
                        <td>Sebelum memulai pembelajaran, ikuti pengerjaan soal di menu <strong>Pre-Test</strong>.</td>
                        <td>Setelah selesai mempelajari materi, akses menu <strong>Post-Test</strong> untuk mengukur pemahaman materi pelatihan.</td>
                        <td>Pastikan menekan tombol <strong>Kirim / Submit</strong> setelah menyelesaikan ujian.</td>
                    </ol>
                </div>
            </div>
        </div> -->

    </div>
</div>

{{-- Script pencarian interaktif sederhana --}}
<script>
    document.getElementById('searchPanduan').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll('.panduan-item');

        items.forEach(function(item) {
            let text = item.textContent.toLowerCase();
            if (text.includes(filter)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endsection