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
    Route::prefix('wqs')->name('wqs.')->middleware(['auth'])->group(function () {
        // Task Board
        Route::get('task-board', [WQSTaskBoard::class, 'index'])
            ->name('task-board');
        Route::get('task-board/{taskBoard}', [WQSTaskBoard::class, 'show'])
            ->name('task-board.show');
        Route::post('task-board/{taskBoard}/start', [WQSTaskBoard::class, 'start'])
            ->name('task-board.start');
        Route::post('task-board/{taskBoard}/hold', [WQSTaskBoard::class, 'hold'])
            ->name('task-board.hold');
        Route::post('task-board/{taskBoard}/resume', [WQSTaskBoard::class, 'resume'])
            ->name('task-board.resume');
        Route::post('task-board/{taskBoard}/complete', [WQSTaskBoard::class, 'complete'])
            ->name('task-board.complete');
        Route::post('task-board/{taskBoard}/reject', [WQSTaskBoard::class, 'reject'])
            ->name('task-board.reject');
        Route::post('task-board/{taskBoard}/assign', [WQSTaskBoard::class, 'assign'])
            ->name('task-board.assign');
        Route::put('task-board/{taskBoard}/priority', [WQSTaskBoard::class, 'updatePriority'])
            ->name('task-board.priority');
        Route::get('task-board/stats', [WQSTaskBoard::class, 'dashboardStats'])
            ->name('task-board.stats');

        // Stock Checks
        Route::resource('stock-checks', StockCheckController::class);
        Route::post('stock-checks/{stockCheck}/mark-failed', [StockCheckController::class, 'markFailed'])
            ->name('stock-checks.mark-failed');
        Route::get('stock-checks/{stockCheck}/problematic-items', [StockCheckController::class, 'getProblematicItems'])
            ->name('stock-checks.problematic-items');
        Route::get('stock-checks/{stockCheck}/report', [StockCheckController::class, 'generateReport'])
            ->name('stock-checks.report');
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
