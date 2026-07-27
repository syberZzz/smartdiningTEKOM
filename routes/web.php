<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\MenuManagementController;
use App\Http\Controllers\Owner\CategoryController;
use App\Http\Controllers\Owner\TableController;
use App\Http\Controllers\Owner\ReportController;
use App\Http\Controllers\Owner\SettingController;
use App\Http\Controllers\Cashier\DashboardController as CashierDashboardController;
use App\Http\Controllers\Cashier\QrOrderController;
use App\Http\Controllers\Cashier\ManualOrderController;
use App\Http\Controllers\Cashier\OrderManagementController;
use App\Http\Controllers\Cashier\ReceiptController;

Route::get('/', function () {
    return redirect('/menu');
});

Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

// Customer scan QR code → langsung ke halaman menu dengan nomor meja terisi otomatis
Route::get('/order/{table}', [MenuController::class, 'showForTable'])->name('order.table');

Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

// ─── Authentication ───
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// ─── Owner Dashboard & Management ───
Route::prefix('owner')->name('owner.')->middleware(['auth', 'role:owner'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Menu Management
    Route::resource('menus', MenuManagementController::class);
    Route::patch('menus/{menu}/toggle-status', [MenuManagementController::class, 'toggleStatus'])->name('menus.toggle-status');

    // Category Management
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Table Management
    Route::get('tables/qr/download-all-pdf', [TableController::class, 'downloadAllQrPdf'])->name('tables.qr.download-all-pdf');
    Route::resource('tables', TableController::class)->except(['show']);
    Route::get('tables/{table}/qr', [TableController::class, 'showQr'])->name('tables.qr');
    Route::get('tables/{table}/qr/download', [TableController::class, 'downloadQr'])->name('tables.qr.download');
    Route::get('tables/{table}/qr/print', [TableController::class, 'printQr'])->name('tables.qr.print');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/chart-data', [ReportController::class, 'chartData'])->name('reports.chart-data');
    Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

    // Settings (PPN / Tax)
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
});

// ─── Cashier Module ───
Route::prefix('cashier')->name('cashier.')->middleware(['auth', 'role:cashier'])->group(function () {

    // Dashboard
    Route::get('/', [CashierDashboardController::class, 'index'])->name('dashboard');

    // ── QR Orders ──────────────────────────────────────────────────────────
    Route::prefix('qr-orders')->name('qr-orders.')->group(function () {
        Route::get('/', [QrOrderController::class, 'index'])->name('index');
        Route::get('/poll', [QrOrderController::class, 'poll'])->name('poll');
        Route::get('/{order}', [QrOrderController::class, 'show'])->name('show');
        Route::post('/{order}/confirm', [QrOrderController::class, 'confirm'])->name('confirm');
        Route::patch('/{order}/status', [QrOrderController::class, 'updateStatus'])->name('status');
        Route::delete('/{order}/cancel', [QrOrderController::class, 'cancel'])->name('cancel');
    });

    // ── All Orders Management ──────────────────────────────────────────────
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderManagementController::class, 'index'])->name('index');
        Route::get('/create', [ManualOrderController::class, 'create'])->name('create');
        Route::post('/', [ManualOrderController::class, 'store'])->name('store');
        Route::get('/{order}', [OrderManagementController::class, 'show'])->name('show');
        Route::patch('/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('status');
        Route::delete('/{order}/cancel', [OrderManagementController::class, 'cancel'])->name('cancel');
    });

    // ── Receipt ───────────────────────────────────────────────────────────
    Route::get('/receipt/{order}', [ReceiptController::class, 'show'])->name('receipt')->whereNumber('order');
});
