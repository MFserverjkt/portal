<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HcLearningController extends Controller
{
    // 1. Halaman e-Learning (Daftar Materi & Modul)
    public function index()
    {
        // Contoh data dummy modul pembelajaran
        $modules = [
            [
                'id' => 1,
                'title' => 'Standard Operating Procedure (SOP) Pelayanan Outlet',
                'category' => 'Customer Service',
                'type' => 'PDF',
                'duration' => '15 Menit',
                'status' => 'Belum Selesai',
                'link' => '#'
            ],
            [
                'id' => 2,
                'title' => 'Video Safety & Keselamatan Kerja di Area Kerja',
                'category' => 'K3 / Safety',
                'type' => 'Video',
                'duration' => '10 Menit',
                'status' => 'Selesai',
                'link' => '#'
            ],
        ];

        return view('hc.elearning.index', compact('modules'));
    }

    // 2. Halaman Pre-Test
    public function pretest()
    {
        // Contoh data dummy soal Pre-Test
        $questions = [
            [
                'id' => 1,
                'question' => 'Apa langkah pertama yang harus dilakukan saat menyambut pelanggan di outlet?',
                'options' => [
                    'A' => 'Langsung memberikan nota',
                    'B' => 'Mengucapkan salam ramah dan tersenyum',
                    'C' => 'Menanyakan metode pembayaran',
                    'D' => 'Meminta pelanggan menunggu'
                ]
            ],
            [
                'id' => 2,
                'question' => 'Berapa standar waktu penyelesaian laporan tiket insiden ringan?',
                'options' => [
                    'A' => '1 Jam',
                    'B' => '24 Jam',
                    'C' => '3 Hari',
                    'D' => '1 Minggu'
                ]
            ],
        ];

        return view('hc.pretest.index', compact('questions'));
    }

    // Simpan Jawaban Pre-Test
    public function storePretest(Request $request)
    {
        // Logika simpan skor/jawaban ke database bisa ditambahkan di sini
        return redirect()->route('hc.elearning.index')
            ->with('success', 'Pre-Test berhasil diselesaikan! Silakan lanjutkan mempelajari materi e-Learning.');
    }

    // 3. Halaman Post-Test
    public function posttest()
    {
        // Contoh data dummy soal Post-Test
        $questions = [
            [
                'id' => 1,
                'question' => 'Sebutkan prosedur utama penanganan komplain di area kerja!',
                'options' => [
                    'A' => 'Mendengarkan dengan empati dan memberikan solusi cepat',
                    'B' => 'Mengabaikan komplain jika pelanggan marah',
                    'C' => 'Menyerahkan masalah penuh ke pihak luar',
                    'D' => 'Meminta pelanggan pulang'
                ]
            ],
            [
                'id' => 2,
                'question' => 'Siapa yang bertanggung jawab menjaga kebersihan alat kerja di outlet?',
                'options' => [
                    'A' => 'Tim IT saja',
                    'B' => 'Seluruh staf dan tim outlet',
                    'C' => 'Petugas keamanan saja',
                    'D' => 'Manajemen pusat'
                ]
            ],
        ];

        return view('hc.posttest.index', compact('questions'));
    }

    // Simpan Jawaban Post-Test
    public function storePosttest(Request $request)
    {
        // Logika simpan skor/jawaban ke database bisa ditambahkan di sini
        return redirect()->route('hc.elearning.index')
            ->with('success', 'Post-Test berhasil dikirim! Nilai Anda telah tercatat di sistem HC.');
    }
}