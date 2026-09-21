<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetImportController;
use App\Http\Controllers\AssetQrController;
use App\Http\Controllers\AssetScanController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContainerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryCheckController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ToolReplacementRequestController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/scan/{qrCode}', [AssetScanController::class, 'show'])->name('assets.scan');
Route::get('/qr/assets/{qrCode}', [AssetScanController::class, 'assetQr'])->name('qr.assets.show');
Route::get('/qr/storages/{qrCode}', [AssetScanController::class, 'storageQr'])->name('qr.storages.show');
Route::get('/penggantian-alat-rusak/aset/{asset}', [ToolReplacementRequestController::class, 'create'])->name('tool-replacements.public.create');
Route::post('/penggantian-alat-rusak/aset/{asset}', [ToolReplacementRequestController::class, 'store'])->name('tool-replacements.public.store');
Route::get('/penggantian-alat-rusak/berhasil/{toolReplacement}', [ToolReplacementRequestController::class, 'success'])->name('tool-replacements.public.success');
Route::get('/penggantian-alat-rusak/status', [ToolReplacementRequestController::class, 'statusForm'])->name('tool-replacements.public.status');
Route::post('/penggantian-alat-rusak/status', [ToolReplacementRequestController::class, 'statusCheck'])->name('tool-replacements.public.status.check');
Route::get('/cek-status-penggantian', [ToolReplacementRequestController::class, 'statusForm'])->name('tool-replacements.public.status.short');
Route::post('/cek-status-penggantian', [ToolReplacementRequestController::class, 'statusCheck'])->name('tool-replacements.public.status.short.check');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/scan-qr-code', [AssetScanController::class, 'index'])
        ->middleware('role:admin,pengelola,pimpinan')
        ->name('scan.qr.index');
    Route::post('/scan-qr-code', [AssetScanController::class, 'process'])
        ->middleware('role:admin,pengelola,pimpinan')
        ->name('scan.qr.process');

    Route::get('/admin/dashboard', DashboardController::class)
        ->middleware('role:admin')
        ->name('dashboard.admin');

    Route::get('/pengelola/dashboard', DashboardController::class)
        ->middleware('role:pengelola')
        ->name('dashboard.pengelola');

    Route::get('/pimpinan/dashboard', DashboardController::class)
        ->middleware('role:pimpinan')
        ->name('dashboard.pimpinan');

    Route::middleware('role:admin')->group(function () {
        Route::resource('units', UnitController::class)->except('show');
        Route::resource('users', UserController::class)->except('show');
    });

    Route::middleware('role:admin,pengelola,pimpinan')->group(function () {
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
    });

    Route::middleware('role:admin,pengelola')->group(function () {
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::match(['put', 'patch'], '/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create');
        Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
        Route::get('/locations/{location}/edit', [LocationController::class, 'edit'])->name('locations.edit');
        Route::match(['put', 'patch'], '/locations/{location}', [LocationController::class, 'update'])->name('locations.update');
        Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');
    });

    Route::middleware('role:admin,pengelola,pimpinan')->group(function () {
        Route::get('/containers', [ContainerController::class, 'index'])->name('containers.index');
        Route::get('/containers/{container}/qr', [ContainerController::class, 'qr'])->name('containers.qr');
        Route::get('/assets/{asset}/qr', [AssetQrController::class, 'show'])->name('assets.qr');
        Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
        Route::get('/assets/export/excel', [AssetController::class, 'export'])->name('assets.export');
        Route::get('/tool-replacements', [ToolReplacementRequestController::class, 'index'])->name('tool-replacements.index');
        Route::get('/tool-replacements/{toolReplacement}', [ToolReplacementRequestController::class, 'show'])->name('tool-replacements.show');
        Route::get('/inventory-checks', [InventoryCheckController::class, 'index'])->name('inventory-checks.index');
        Route::get('/inventory-checks/{inventoryCheck}', [InventoryCheckController::class, 'show'])
            ->whereNumber('inventoryCheck')
            ->name('inventory-checks.show');
    });

    Route::middleware('role:admin,pengelola')->group(function () {
        Route::get('/assets/import', [AssetImportController::class, 'create'])->name('assets.import');
        Route::get('/assets/import/template', [AssetImportController::class, 'template'])->name('assets.import.template');
        Route::post('/assets/import', [AssetImportController::class, 'store'])->name('assets.import.store');
        Route::get('/containers/create', [ContainerController::class, 'create'])->name('containers.create');
        Route::post('/containers', [ContainerController::class, 'store'])->name('containers.store');
        Route::get('/containers/{container}/edit', [ContainerController::class, 'edit'])->name('containers.edit');
        Route::match(['put', 'patch'], '/containers/{container}', [ContainerController::class, 'update'])->name('containers.update');
        Route::delete('/containers/{container}', [ContainerController::class, 'destroy'])->name('containers.destroy');
        Route::post('/containers/{container}/qr', [ContainerController::class, 'generateQr'])->name('containers.qr.generate');
        Route::post('/containers/{container}/assets', [ContainerController::class, 'storeAsset'])->name('containers.assets.store');
        Route::delete('/containers/{container}/assets/{asset}', [ContainerController::class, 'removeAsset'])->name('containers.assets.destroy');
        Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
        Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
        Route::match(['put', 'patch'], '/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
        Route::post('/assets/{asset}/quantity-additions', [AssetController::class, 'addQuantity'])->name('assets.quantity-additions.store');
        Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
        Route::post('/assets/{asset}/qr', [AssetQrController::class, 'generate'])->name('assets.qr.generate');
        Route::patch('/tool-replacements/{toolReplacement}/status', [ToolReplacementRequestController::class, 'updateStatus'])->name('tool-replacements.status');
        Route::get('/inventory-checks/create', [InventoryCheckController::class, 'create'])->name('inventory-checks.create');
        Route::post('/inventory-checks', [InventoryCheckController::class, 'store'])->name('inventory-checks.store');
        Route::get('/inventory-checks/{inventoryCheck}/import', [InventoryCheckController::class, 'importForm'])->whereNumber('inventoryCheck')->name('inventory-checks.import');
        Route::get('/inventory-checks/{inventoryCheck}/import/template', [InventoryCheckController::class, 'conditionTemplate'])->whereNumber('inventoryCheck')->name('inventory-checks.import.template');
        Route::post('/inventory-checks/{inventoryCheck}/import', [InventoryCheckController::class, 'importConditions'])->whereNumber('inventoryCheck')->name('inventory-checks.import.store');
        Route::get('/inventory-checks/{inventoryCheck}/scan', [InventoryCheckController::class, 'scanForm'])->whereNumber('inventoryCheck')->name('inventory-checks.scan.form');
        Route::post('/inventory-checks/{inventoryCheck}/scan', [InventoryCheckController::class, 'processScan'])->whereNumber('inventoryCheck')->name('inventory-checks.scan.process');
        Route::get('/inventory-checks/{inventoryCheck}/scan/{qrCode}', [InventoryCheckController::class, 'scan'])->whereNumber('inventoryCheck')->name('inventory-checks.scan');
        Route::patch('/inventory-checks/{inventoryCheck}', [InventoryCheckController::class, 'update'])->whereNumber('inventoryCheck')->name('inventory-checks.update');
    });

    Route::middleware('role:admin,pengelola,pimpinan')->group(function () {
        Route::get('/containers/{container}', [ContainerController::class, 'show'])->name('containers.show');
    });

    Route::middleware('role:admin,pengelola,pimpinan')->group(function () {
        Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
    });

    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('role:admin,pengelola,pimpinan')
        ->name('reports.index');
});
