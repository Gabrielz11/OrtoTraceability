<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\SurgeryController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class , 'index'])->name('dashboard');

    Route::resource('materials', MaterialController::class);
    Route::resource('surgeries', SurgeryController::class);

    Route::post('surgeries/{surgery}/link', [SurgeryController::class , 'linkMaterial'])->name('surgeries.link');
    Route::delete('surgeries/{surgery}/unlink/{material}', [SurgeryController::class , 'unlinkMaterial'])->name('surgeries.unlink');
    Route::post('surgeries/{surgery}/use/{material}', [SurgeryController::class , 'markAsUsed'])->name('surgeries.use');

    Route::get('audit', [AuditController::class , 'index'])->name('audit.index');

    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
