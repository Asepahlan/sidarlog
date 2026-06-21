<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('/login', [LoginController::class, 'showLoginForm']);
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ── DASHBOARD ───────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')
        ->middleware('permission:dashboard.view');
    Route::post('/dashboard/optimize', [DashboardController::class, 'optimize'])->name('dashboard.optimize')
        ->middleware('permission:system.optimize');
    Route::get('/dashboard/realtime-data', [DashboardController::class, 'realtimeData'])->name('dashboard.realtime')
        ->middleware('permission:dashboard.view');

    // ── PROFILE (semua user boleh akses profil sendiri) ─────────────
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    // ── SETTINGS (super_admin only via permission) ──────────────────
    Route::get('/settings', [\App\Http\Controllers\ProfileController::class, 'settings'])->name('settings.index')
        ->middleware('permission:settings.manage');
    Route::put('/settings', [\App\Http\Controllers\ProfileController::class, 'updateSystemSettings'])->name('settings.update')
        ->middleware('permission:settings.manage');

    // ── MASTER BARANG ───────────────────────────────────────────────
    Route::get('/barang/qr/{code}.svg', [\App\Http\Controllers\ItemController::class, 'generateQr'])->name('barang.qr')
        ->middleware('permission:barang.view');

    Route::get('/barang/trash', [\App\Http\Controllers\ItemController::class, 'trash'])->name('barang.trash')
        ->middleware('permission:barang.delete');
    Route::post('/barang/{id}/restore', [\App\Http\Controllers\ItemController::class, 'restore'])->name('barang.restore')
        ->middleware('permission:barang.restore');
    Route::delete('/barang/{id}/force-delete', [\App\Http\Controllers\ItemController::class, 'forceDelete'])->name('barang.force-delete')
        ->middleware('permission:barang.force-delete');

    Route::resource('barang', \App\Http\Controllers\ItemController::class)
        ->middleware('permission:barang.view')
        ->except(['store', 'update', 'destroy']);
    Route::post('/barang', [\App\Http\Controllers\ItemController::class, 'store'])->name('barang.store')
        ->middleware('permission:barang.create');
    Route::put('/barang/{barang}', [\App\Http\Controllers\ItemController::class, 'update'])->name('barang.update')
        ->middleware('permission:barang.edit');
    Route::delete('/barang/{barang}', [\App\Http\Controllers\ItemController::class, 'destroy'])->name('barang.destroy')
        ->middleware('permission:barang.delete');

    // ── MASTER DATA ─────────────────────────────────────────────────
    Route::resource('lokasi-barang', \App\Http\Controllers\ItemLocationController::class)
        ->middleware('permission:master.lokasi');
    Route::resource('pihak-kesatu', \App\Http\Controllers\FirstPartyController::class)
        ->middleware('permission:master.pihak-kesatu');
    Route::resource('pihak-kedua', \App\Http\Controllers\SecondPartyController::class)
        ->middleware('permission:master.pihak-kedua');
    Route::resource('bap', \App\Http\Controllers\ReferenceBapController::class)
        ->middleware('permission:master.bap');
    Route::resource('sumber-anggaran', \App\Http\Controllers\BudgetSourceController::class)
        ->middleware('permission:master.sumber-anggaran');
    Route::resource('gudang', \App\Http\Controllers\WarehouseController::class)
        ->middleware('permission:gudang.view');
    Route::resource('kategori', \App\Http\Controllers\CategoryController::class)
        ->middleware('permission:master.kategori');
    Route::resource('satuan', \App\Http\Controllers\UnitController::class)
        ->middleware('permission:master.satuan');

    // ── MUTASI GUDANG ───────────────────────────────────────────────
    Route::resource('mutasi-gudang', \App\Http\Controllers\StockMutationController::class)
        ->middleware('permission:mutasi.view');

    // ── INVENTORY: BARANG MASUK ─────────────────────────────────────
    Route::get('/barang-masuk', [\App\Http\Controllers\StockTransactionController::class, 'index'])->name('barang-masuk.index')
        ->defaults('jenis', 'masuk')
        ->middleware('permission:transaksi.masuk.view');
    Route::get('/barang-masuk/create', [\App\Http\Controllers\StockTransactionController::class, 'create'])->name('barang-masuk.create')
        ->defaults('jenis', 'masuk')
        ->middleware('permission:transaksi.masuk.create');
    Route::post('/barang-masuk', [\App\Http\Controllers\StockTransactionController::class, 'store'])->name('barang-masuk.store')
        ->middleware('permission:transaksi.masuk.create');
    Route::delete('/barang-masuk/{id}', [\App\Http\Controllers\StockTransactionController::class, 'destroy'])->name('barang-masuk.destroy')
        ->middleware('permission:transaksi.delete');

    // ── INVENTORY: BARANG KELUAR ────────────────────────────────────
    Route::get('/barang-keluar', [\App\Http\Controllers\StockTransactionController::class, 'index'])->name('barang-keluar.index')
        ->defaults('jenis', 'keluar')
        ->middleware('permission:transaksi.keluar.view');
    Route::get('/barang-keluar/create', [\App\Http\Controllers\StockTransactionController::class, 'create'])->name('barang-keluar.create')
        ->defaults('jenis', 'keluar')
        ->middleware('permission:transaksi.keluar.create');
    Route::post('/barang-keluar', [\App\Http\Controllers\StockTransactionController::class, 'store'])->name('barang-keluar.store')
        ->middleware('permission:transaksi.keluar.create');
    Route::delete('/barang-keluar/{id}', [\App\Http\Controllers\StockTransactionController::class, 'destroy'])->name('barang-keluar.destroy')
        ->middleware('permission:transaksi.delete');

    Route::get('/transaksi/{id}/bast', [\App\Http\Controllers\StockTransactionController::class, 'printBast'])->name('transaksi.bast')
        ->middleware('permission:laporan.export');

    // ── STOCK OPNAME ────────────────────────────────────────────────
    Route::get('/stock-opname', [\App\Http\Controllers\StockOpnameController::class, 'index'])->name('stock-opname.index')
        ->middleware('permission:opname.view');
    Route::post('/stock-opname', [\App\Http\Controllers\StockOpnameController::class, 'store'])->name('stock-opname.store')
        ->middleware('permission:opname.create');

    // ── REPORTS & EXPORT ────────────────────────────────────────────
    Route::get('/laporan', [\App\Http\Controllers\ReportController::class, 'index'])->name('laporan.index')
        ->middleware('permission:laporan.view');
    Route::get('/laporan/barang/excel', [\App\Http\Controllers\ReportController::class, 'exportItemsExcel'])->name('laporan.barang.excel')
        ->middleware('permission:laporan.export');
    Route::get('/laporan/barang/pdf', [\App\Http\Controllers\ReportController::class, 'exportItemsPdf'])->name('laporan.barang.pdf')
        ->middleware('permission:laporan.export');
    Route::get('/laporan/transaksi/excel', [\App\Http\Controllers\ReportController::class, 'exportTransactionsExcel'])->name('laporan.transaksi.excel')
        ->middleware('permission:laporan.export');
    Route::get('/laporan/transaksi/pdf', [\App\Http\Controllers\ReportController::class, 'exportTransactionsPdf'])->name('laporan.transaksi.pdf')
        ->middleware('permission:laporan.export');
    Route::get('/laporan/opname/excel', [\App\Http\Controllers\ReportController::class, 'exportOpnameExcel'])->name('laporan.opname.excel')
        ->middleware('permission:laporan.export');
    Route::get('/laporan/opname/pdf', [\App\Http\Controllers\ReportController::class, 'exportOpnamePdf'])->name('laporan.opname.pdf')
        ->middleware('permission:laporan.export');
    Route::get('/laporan/mutasi/excel', [\App\Http\Controllers\ReportController::class, 'exportMutasiExcel'])->name('laporan.mutasi.excel')
        ->middleware('permission:laporan.export');
    Route::get('/laporan/mutasi/pdf', [\App\Http\Controllers\ReportController::class, 'exportMutasiPdf'])->name('laporan.mutasi.pdf')
        ->middleware('permission:laporan.export');

    // ── SISTEM: USER & ROLE MANAGEMENT ──────────────────────────────
    Route::resource('users', \App\Http\Controllers\UserController::class)
        ->middleware('permission:user.manage');
    Route::post('/users/{id}/reset-password', [\App\Http\Controllers\UserController::class, 'resetPassword'])
        ->name('users.reset-password')
        ->middleware('permission:user.manage');
    Route::resource('jabatan', \App\Http\Controllers\JabatanController::class)
        ->middleware('permission:user.manage');
    Route::resource('bidang', \App\Http\Controllers\BidangController::class)
        ->middleware('permission:user.manage');
    Route::resource('roles', \App\Http\Controllers\RoleController::class)
        ->middleware('permission:role.manage');

    // ── NOTIFICATIONS (semua user boleh) ────────────────────────────
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // ── ACTIVITY LOG ────────────────────────────────────────────────
    Route::get('/activity-log', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-log.index')
        ->middleware('permission:activity-log.view');
});
