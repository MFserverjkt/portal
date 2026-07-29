<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ReportController;

// 1. Redirect halaman utama ke login / dashboard tiket
Route::get('/', function () { 
    return redirect()->route('login'); 
});

// 2. Authentication Route
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

    // --- FITUR REPORT CORRECTIVE ---
    Route::middleware(['role:ADMIN,IT,MAINTENANCE'])->group(function () {
        Route::get('report/it', [ReportController::class, 'reportIt'])->name('report.it');
        Route::get('report/maintenance', [ReportController::class, 'reportMaintenance'])->name('report.maintenance');
    });

    // --- FITUR INVENTORI ASSET (Termasuk Export & Import Excel) ---
    Route::middleware(['role:ADMIN,IT,MAINTENANCE'])->group(function () {
        Route::get('assets/export', [AssetController::class, 'export'])->name('assets.export');
        Route::post('assets/import', [AssetController::class, 'import'])->name('assets.import');
        Route::resource('assets', AssetController::class);
    });

    // --- FITUR CORRECTIVE / TIKET & BAST ---
    Route::middleware(['role:ADMIN,IT,MAINTENANCE,OUTLET'])->group(function () {
        Route::get('tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('tickets/create', [TicketController::class, 'create'])->name('tickets.create');
        Route::post('tickets', [TicketController::class, 'store'])->name('tickets.store');
        Route::get('tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');

        // Form BAST & Konfirmasi Done
        // Menggunakan alias .createBast & .bast.create agar kompatibel dengan kedua pemanggilan
        Route::get('tickets/{id}/bast/create', [TicketController::class, 'createBast'])->name('tickets.createBast');
        Route::post('tickets/{id}/bast', [TicketController::class, 'storeBast'])->name('tickets.bast.store');
        
        // Route Baru: Konfirmasi DONE oleh User/Outlet Pembuat Tiket
        Route::patch('tickets/{id}/done', [TicketController::class, 'markAsDone'])->name('tickets.done');
    });

});