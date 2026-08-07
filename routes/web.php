<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HcLearningController;

// 1. Redirect halaman utama ke login
Route::get('/', function () { 
    return redirect()->route('login'); 
});

// 2. Authentication Routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// 3. Protected Routes (Wajib Login)
Route::middleware(['auth'])->group(function () {

    // --- FITUR ADMIN & IT (USER & HAK AKSES MANAGEMENT) ---
    Route::middleware(['role:ADMIN,IT'])->group(function () {
        Route::get('user-roles', [UserController::class, 'roles'])->name('users.roles');
        Route::post('user-roles/update', [UserController::class, 'updateRolePermissions'])->name('users.roles.update');
        Route::resource('users', UserController::class);
    });

    // --- FITUR HC LEARNING ---
    Route::middleware(['role:ADMIN,IT,HC,OUTLET'])->group(function () {
        Route::get('hc/e-learning', [HcLearningController::class, 'index'])->name('hc.elearning.index');
        
        // Pre-Test
        Route::get('hc/pre-test', [HcLearningController::class, 'pretest'])->name('hc.pretest.index');
        Route::post('hc/pre-test', [HcLearningController::class, 'storePretest'])->name('hc.pretest.store');
        
        // Post-Test
        Route::get('hc/post-test', [HcLearningController::class, 'posttest'])->name('hc.posttest.index');
        Route::post('hc/post-test', [HcLearningController::class, 'storePosttest'])->name('hc.posttest.store');
    });

    // --- FITUR REPORT CORRECTIVE ---
    Route::middleware(['role:ADMIN,IT,MAINTENANCE'])->group(function () {
        // Report IT
        Route::get('report/it', [ReportController::class, 'reportIt'])->name('report.it');
        Route::get('report/it/export', [ReportController::class, 'exportItExcel'])->name('report.it.export');

        // Report Maintenance
        Route::get('report/maintenance', [ReportController::class, 'reportMaintenance'])->name('report.maintenance');
        Route::get('report/maintenance/export', [ReportController::class, 'exportMaintenanceExcel'])->name('report.maintenance.export');
    });

    // --- FITUR INVENTORI ASSET (Termasuk Export & Import Excel) ---
    Route::middleware(['role:ADMIN,IT,MAINTENANCE'])->group(function () {
        Route::get('assets/export', [AssetController::class, 'export'])->name('assets.export');
        Route::post('assets/import', [AssetController::class, 'import'])->name('assets.import');
        Route::resource('assets', AssetController::class);
    });

    // --- FITUR BAST (KHUSUS ADMIN, IT, & MAINTENANCE) ---
    Route::middleware(['role:ADMIN,IT,MAINTENANCE'])->group(function () {
        // Route Utama Pembuatan BAST
        Route::get('tickets/{id}/bast/create', [TicketController::class, 'createBast'])->name('tickets.bast.create');
        Route::post('tickets/{id}/bast', [TicketController::class, 'storeBast'])->name('tickets.bast.store');

        // Alias Route untuk Kompatibilitas Pemanggilan View Lama (jika ada)
        Route::get('tickets/{id}/bast-create-alias', [TicketController::class, 'createBast'])->name('tickets.createBast');
        Route::get('tickets/{id}/bast-create-alt', [TicketController::class, 'createBast'])->name('tickets.bast_create');
        Route::post('tickets/{id}/bast-store-alias', [TicketController::class, 'storeBast'])->name('tickets.storeBast');
    });

    // --- FITUR TIKET (DAPAT DIAKSES SEMUA ROLE TERMASUK OUTLET) ---
    Route::middleware(['role:ADMIN,IT,MAINTENANCE,OUTLET'])->group(function () {
        Route::get('tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('tickets/create', [TicketController::class, 'create'])->name('tickets.create');
        Route::post('tickets', [TicketController::class, 'store'])->name('tickets.store');
        Route::get('tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');

        // Konfirmasi DONE oleh User/Outlet Pembuat Tiket
        Route::post('tickets/{id}/done', [TicketController::class, 'markAsDone'])->name('tickets.markAsDone');
        Route::patch('tickets/{id}/done-patch', [TicketController::class, 'markAsDone'])->name('tickets.done');
    });

});