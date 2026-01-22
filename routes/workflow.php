<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CRM\SalesDOController;
use App\Http\Controllers\WQS\{TaskBoardController as WQSTaskBoard, StockCheckController};
use App\Http\Controllers\SCM\{TaskBoardController as SCMTaskBoard, DeliveryController};
use App\Http\Controllers\ACT\{TaskBoardController as ACTTaskBoard, InvoiceController};
use App\Http\Controllers\FIN\{TaskBoardController as FINTaskBoard, CollectionController, AgingController};

Route::middleware(['auth'])->group(function () {
    // CRM Module
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::resource('sales-do', SalesDOController::class);
        Route::post('sales-do/{salesDo}/submit', [SalesDOController::class, 'submit'])->name('sales-do.submit');
        Route::get('/sales-do/{salesDo}/pdf', [SalesDOController::class, 'exportPDF'])->name('sales-do.exportPDF');
    });

    // WQS Module
    Route::prefix('wqs')->name('wqs.')->group(function () {
        Route::get('task-board', [WQSTaskBoard::class, 'index'])->name('task-board');
        Route::resource('stock-checks', StockCheckController::class);
    });

    // SCM Module
    Route::prefix('scm')->name('scm.')->group(function () {
        Route::get('task-board', [SCMTaskBoard::class, 'index'])->name('task-board');
        Route::resource('deliveries', DeliveryController::class);
    });

    // ACT Module
    Route::prefix('act')->name('act.')->group(function () {
        Route::get('task-board', [ACTTaskBoard::class, 'index'])->name('task-board');
        Route::resource('invoices', InvoiceController::class);
    });

    // FIN Module
    Route::prefix('fin')->name('fin.')->group(function () {
        Route::get('task-board', [FINTaskBoard::class, 'index'])->name('task-board');
        Route::resource('collections', CollectionController::class);
        Route::get('aging', [AgingController::class, 'index'])->name('aging');
    });
});
