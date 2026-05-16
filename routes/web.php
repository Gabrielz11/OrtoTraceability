<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\DivergenceController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SurgeryController;
use Illuminate\Support\Facades\Route;

// ── Dashboard (todos os autenticados) ──────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Admin + Instrumentador ──────────────────────────────────────
Route::middleware(['auth', 'role:admin,instrumentator'])->group(function () {
    Route::resource('materials', MaterialController::class);
    Route::post('materials/{material}/status', [MaterialController::class, 'changeStatus'])->name('materials.change_status');

    Route::resource('surgeries', SurgeryController::class);
    Route::post('surgeries/{surgery}/status', [SurgeryController::class, 'changeStatus'])->name('surgeries.change_status');
    Route::post('surgeries/{surgery}/link', [SurgeryController::class, 'linkMaterial'])->name('surgeries.link');
    Route::delete('surgeries/{surgery}/unlink/{material}', [SurgeryController::class, 'unlinkMaterial'])->name('surgeries.unlink');
    Route::post('surgeries/{surgery}/use/{material}', [SurgeryController::class, 'markAsUsed'])->name('surgeries.use');
    Route::get('surgeries/{surgery}/display-url', [DisplayController::class, 'generateUrl'])->name('surgeries.display_url');
});

// ── Admin + Auditor ─────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,auditor'])->group(function () {
    Route::get('audit', [AuditController::class, 'index'])->name('audit.index');
    Route::get('divergences', [DivergenceController::class, 'index'])->name('divergences.index');
    Route::patch('divergences/{divergence}/acknowledge', [DivergenceController::class, 'acknowledge'])->name('divergences.acknowledge');
});

// ── Display (TV/Kiosk) — público com URL assinada ───────────────
Route::get('/display/surgery/{surgery}', [DisplayController::class, 'show'])
    ->name('display.surgery')
    ->middleware('signed');

Route::get('/api/surgeries/{surgery}/materials-status', [DisplayController::class, 'materialsStatus']);

require __DIR__ . '/auth.php';
