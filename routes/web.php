<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HcLearningController;
use App\Http\Controllers\GuidanceController;
use App\Http\Controllers\ChecklistOutletController;

// Import Controller untuk Telegram Bot SDK
use Telegram\Bot\Laravel\Facades\Telegram;

// Import Controller untuk HC Recruitment
use App\Http\Controllers\HC\DashboardController as HcDashboardController;
use App\Http\Controllers\HC\ManpowerRequestController;
use App\Http\Controllers\HC\VacancyController;
use App\Http\Controllers\HC\CandidateController;
use App\Http\Controllers\HC\SlaReportController;
use App\Http\Controllers\HC\AuditLogController;

// 1. Redirect Halaman Utama ke Login
Route::get('/', function () { 
    return redirect()->route('login'); 
});

// 2. Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('login', 'showLogin')->name('login');
    Route::post('login', 'login')->name('login.post');
    Route::post('logout', 'logout')->name('logout');
});

// ==========================================
// TELEGRAM BOT ROUTES (Publik / Webhook)
// ==========================================

// Route untuk uji coba kirim pesan Telegram dari browser
Route::get('/test-telegram', function () {
    try {
        // Ganti dengan Chat ID Telegram Anda (atau ambil dari database/config)
        $chatId = '1226395565'; 

        $response = Telegram::sendMessage([
            'chat_id' => $chatId,
            'text'    => 'Halo Hans! Bot Telegram di Portal Maison Feerie berhasil terhubung dengan Laravel! 🚀'
        ]);

        return 'Pesan berhasil dikirim ke Telegram!';
    } catch (\Exception $e) {
        return 'Gagal mengirim pesan: ' . $e->getMessage();
    }
});

// Route untuk menerima update/pesan masuk dari Telegram (Webhook)
Route::post('/telegram/webhook', function () {
    $update = Telegram::commandsHandler(true);

    $chatId = $update->getChat()->getId();
    $text = $update->getText();

    // Contoh respon otomatis sederhana dari bot
    if ($text == '/start') {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text'    => 'Halo! Selamat datang di Portal Maison Feerie Bot.'
        ]);
    } else {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text'    => 'Pesan Anda diterima: ' . $text
        ]);
    }

    return 'OK';
});


// 3. Protected Routes (Wajib Login)
Route::middleware(['auth'])->group(function () {

    // --- FITUR PANDUAN / GUIDANCE (SEMUA ROLE) ---
    Route::get('guidance', [GuidanceController::class, 'index'])->name('guidance.index');
    Route::get('panduan', [GuidanceController::class, 'index'])->name('panduan.index'); // Alias untuk kompatibilitas

    // --- FITUR ADMIN & IT (USER, ROLE MANAGEMENT, EXPORT EXCEL & CHECKLIST OUTLET) ---
    Route::middleware(['role:ADMIN,IT'])->group(function () {
        // User & Role Management
        Route::controller(UserController::class)->group(function () {
            Route::get('users/export', 'exportExcel')->name('users.export'); // Route Export Excel User
            Route::get('user-roles', 'roles')->name('users.roles');
            Route::post('user-roles/update', 'updateRolePermissions')->name('users.roles.update');
            Route::resource('users', UserController::class);
        });

        // Checklist Outlet IT
        Route::controller(ChecklistOutletController::class)->prefix('it')->name('it.')->group(function () {
            Route::get('checklist-outlet', 'create')->name('checklist.create');
            Route::post('checklist-outlet', 'store')->name('checklist.store');
        });
    });

    // --- FITUR HC LEARNING ---
    Route::middleware(['role:ADMIN,IT,HC,OUTLET'])->prefix('hc')->name('hc.')->controller(HcLearningController::class)->group(function () {
        Route::get('e-learning', 'index')->name('elearning.index');
        
        // Pre-Test
        Route::get('pre-test', 'pretest')->name('pretest.index');
        Route::post('pre-test', 'storePretest')->name('pretest.store');
        
        // Post-Test
        Route::get('post-test', 'posttest')->name('posttest.index');
        Route::post('post-test', 'storePosttest')->name('posttest.store');
    });

    // --- FITUR HC RECRUITMENT & MANPOWER PLANNING ---
    Route::middleware(['role:ADMIN,IT,HC,MANAGEMENT'])->prefix('hc')->name('hc.')->group(function () {
        // Executive Dashboard SLA
        Route::get('dashboard', [HcDashboardController::class, 'index'])->name('dashboard');

        // Manpower Request & Approval Flow
        Route::resource('manpower', ManpowerRequestController::class);

        // Vacancies Requisition & Recruitment Pipeline
        Route::resource('vacancies', VacancyController::class);

        // Candidate Database & Tracking
        Route::resource('candidates', CandidateController::class);

        // SLA Analytics & Bottleneck Report
        Route::get('reports/sla', [SlaReportController::class, 'index'])->name('reports.sla');

        // Audit Trail System
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    // --- FITUR REPORT CORRECTIVE ---
    Route::middleware(['role:ADMIN,IT,MAINTENANCE'])->prefix('report')->name('report.')->controller(ReportController::class)->group(function () {
        // Report IT
        Route::get('it', 'reportIt')->name('it');
        Route::get('it/export', 'exportItExcel')->name('it.export');

        // Report Maintenance
        Route::get('maintenance', 'reportMaintenance')->name('maintenance');
        Route::get('maintenance/export', 'exportMaintenanceExcel')->name('maintenance.export');
    });

    // --- FITUR INVENTORI ASSET ---
    Route::middleware(['role:ADMIN,IT,MAINTENANCE,OUTLET'])->group(function () {
        Route::controller(AssetController::class)->group(function () {
            // Scanner QR Code & Bulk Actions
            Route::match(['get', 'post'], 'assets/scan-check', 'scanCheck')->name('assets.scan-check');
            Route::get('assets/export', 'export')->name('assets.export');
            Route::post('assets/import', 'import')->name('assets.import');
            Route::delete('assets/bulk-delete', 'bulkDelete')->name('assets.bulk-delete');
        });

        Route::resource('assets', AssetController::class);
    });

    // --- FITUR WORK PROGRESS & BAST (ADMIN, IT, & MAINTENANCE) ---
    Route::middleware(['role:ADMIN,IT,MAINTENANCE'])->controller(TicketController::class)->group(function () {
        // Work Progress Update
        Route::patch('tickets/{id}/work', 'updateWork')->name('tickets.work.update');

        // Pembuatan BAST & Alias
        Route::get('tickets/{id}/bast/create', 'createBast')->name('tickets.bast.create');
        Route::post('tickets/{id}/bast', 'storeBast')->name('tickets.bast.store');
        
        // Alias Route BAST
        Route::get('tickets/{id}/bast-create-alias', 'createBast')->name('tickets.createBast');
        Route::get('tickets/{id}/bast-create-alt', 'createBast')->name('tickets.bast_create');
        Route::post('tickets/{id}/bast-store-alias', 'storeBast')->name('tickets.storeBast');
    });

    // --- FITUR TIKET (SEMUA ROLE) ---
    Route::middleware(['role:ADMIN,IT,MAINTENANCE,OUTLET'])->controller(TicketController::class)->group(function () {
        Route::get('tickets', 'index')->name('tickets.index');
        Route::get('tickets/create', 'create')->name('tickets.create');
        Route::post('tickets', 'store')->name('tickets.store');
        Route::get('tickets/{id}', 'show')->name('tickets.show');

        // Status Done Confirmation
        Route::match(['post', 'patch'], 'tickets/{id}/done', 'markAsDone')->name('tickets.markAsDone');
        Route::match(['post', 'patch'], 'tickets/{id}/done-alias', 'markAsDone')->name('tickets.done');
    });

});