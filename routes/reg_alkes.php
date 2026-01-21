<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegAlkes\{
    ControlTowerController,
    CaseController,
    ImportSKUController
};

Route::middleware(['auth'])->prefix('reg-alkes')->name('reg-alkes.')->group(function () {
    Route::get('control-tower', [ControlTowerController::class, 'index'])->name('control-tower');
    Route::resource('cases', CaseController::class);
    Route::post('import-sku', [ImportSKUController::class, 'import'])->name('import-sku');
});
