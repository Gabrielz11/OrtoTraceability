<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthorizationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\DivergenceController;
use App\Http\Controllers\KitTemplateController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SurgeryController;
use App\Http\Controllers\SurgeryKitController;
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

    // ── Stock ────────────────────────────────────────────────────
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');

    Route::get('stock/products/create', [StockController::class, 'createProduct'])->name('stock.products.create');
    Route::post('stock/products', [StockController::class, 'storeProduct'])->name('stock.products.store');
    Route::get('stock/products/{product}', [StockController::class, 'showProduct'])->name('stock.products.show');
    Route::get('stock/products/{product}/edit', [StockController::class, 'editProduct'])->name('stock.products.edit');
    Route::put('stock/products/{product}', [StockController::class, 'updateProduct'])->name('stock.products.update');

    Route::get('stock/products/{product}/items/create', [StockController::class, 'createItem'])->name('stock.items.create');
    Route::post('stock/products/{product}/items', [StockController::class, 'storeItem'])->name('stock.items.store');

    // ── Kit Templates ────────────────────────────────────────────
    Route::resource('kit-templates', KitTemplateController::class);
    Route::post('kit-templates/{kitTemplate}/items', [KitTemplateController::class, 'addItem'])->name('kit-templates.add-item');
    Route::delete('kit-templates/{kitTemplate}/items/{item}', [KitTemplateController::class, 'removeItem'])->name('kit-templates.remove-item');

    // ── Surgery Kits ─────────────────────────────────────────────
    Route::get('surgeries/{surgery}/kits/create', [SurgeryKitController::class, 'create'])->name('surgery-kits.create');
    Route::post('surgeries/{surgery}/kits', [SurgeryKitController::class, 'store'])->name('surgery-kits.store');
    Route::get('surgery-kits/{surgeryKit}', [SurgeryKitController::class, 'show'])->name('surgery-kits.show');
    Route::get('surgery-kits/{surgeryKit}/resultado', [SurgeryKitController::class, 'resultado'])->name('surgery-kits.resultado');
    Route::post('surgery-kits/{surgeryKit}/resultado', [SurgeryKitController::class, 'registrarResultados'])->name('surgery-kits.registrar-resultados');
    Route::post('surgery-kits/{surgeryKit}/conferir', [SurgeryKitController::class, 'conferir'])->name('surgery-kits.conferir');
    Route::post('surgery-kits/{surgeryKit}/despachar', [SurgeryKitController::class, 'despachar'])->name('surgery-kits.despachar');
    Route::post('surgery-kits/{surgeryKit}/receber', [SurgeryKitController::class, 'confirmarRecebimento'])->name('surgery-kits.receber');
    Route::post('surgery-kits/{surgeryKit}/devolver', [SurgeryKitController::class, 'devolver'])->name('surgery-kits.devolver');
    Route::post('surgery-kits/{surgeryKit}/items/{item}/vincular', [SurgeryKitController::class, 'vincularItem'])->name('surgery-kits.vincular-item');
    Route::delete('surgery-kits/{surgeryKit}/items/{item}', [SurgeryKitController::class, 'desvincularItem'])->name('surgery-kits.desvincular-item');

    // ── Authorizations ───────────────────────────────────────────
    Route::get('surgeries/{surgery}/authorization/create', [AuthorizationController::class, 'create'])->name('authorizations.create');
    Route::post('surgeries/{surgery}/authorization', [AuthorizationController::class, 'store'])->name('authorizations.store');
    Route::put('authorizations/{authorization}', [AuthorizationController::class, 'update'])->name('authorizations.update');
    Route::post('authorizations/{authorization}/items', [AuthorizationController::class, 'addItem'])->name('authorizations.add-item');
    Route::delete('authorizations/{authorization}/items/{item}', [AuthorizationController::class, 'removeItem'])->name('authorizations.remove-item');
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
