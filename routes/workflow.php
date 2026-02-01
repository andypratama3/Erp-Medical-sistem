<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CRM\SalesDOController;
use App\Http\Controllers\ACT\{TaskBoardController as ACTTaskBoard, InvoiceController};
use App\Http\Controllers\SCM\{TaskBoardController as SCMTaskBoard, DeliveryController, DriverController};
use App\Http\Controllers\WQS\{TaskBoardController as WQSTaskBoard, StockCheckController};
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
    // Task Board
        Route::get('task-board', [SCMTaskBoard::class, 'index'])->name('task-board.index');
        Route::get('task-board/{salesDo}', [SCMTaskBoard::class, 'show'])->name('task-board.show');

        // Driver Assignment & Delivery Actions
        Route::post('task-board/{salesDo}/assign-driver', [SCMTaskBoard::class, 'assignDriver'])->name('task-board.assign-driver');
        Route::post('task-board/{salesDo}/start-delivery', [SCMTaskBoard::class, 'startDelivery'])->name('task-board.start-delivery');
        Route::post('task-board/{salesDo}/complete-delivery', [SCMTaskBoard::class, 'completeDelivery'])->name('task-board.complete-delivery');
        Route::post('task-board/{salesDo}/update-location', [SCMTaskBoard::class, 'updateLocation'])->name('task-board.update-location');

        // Deliveries Resource

         Route::resource('drivers', App\Http\Controllers\SCM\SCMDriverController::class);

        // Deliveries
        Route::resource('deliveries', App\Http\Controllers\SCM\SCMDeliveryController::class);

        // Additional delivery actions
        Route::post('deliveries/{delivery}/depart', [App\Http\Controllers\SCM\SCMDeliveryController::class, 'markDeparted'])
            ->name('deliveries.depart');
        Route::post('deliveries/{delivery}/delivered', [App\Http\Controllers\SCM\SCMDeliveryController::class, 'markDelivered'])
            ->name('deliveries.delivered');

        // Vehicles Management (Optional)
        Route::prefix('vehicles')->name('vehicles.')->group(function () {
            Route::get('/', [VehicleController::class, 'index'])->name('index');
            Route::get('create', [VehicleController::class, 'create'])->name('create');
            Route::post('/', [VehicleController::class, 'store'])->name('store');
            Route::get('{vehicle}', [VehicleController::class, 'show'])->name('show');
            Route::get('{vehicle}/edit', [VehicleController::class, 'edit'])->name('edit');
            Route::put('{vehicle}', [VehicleController::class, 'update'])->name('update');
            Route::delete('{vehicle}', [VehicleController::class, 'destroy'])->name('destroy');
        });

        // Delivery Tracking
        Route::prefix('tracking')->name('tracking.')->group(function () {
            Route::get('/', [DeliveryTrackingController::class, 'index'])->name('index');
            Route::get('{salesDo}', [DeliveryTrackingController::class, 'show'])->name('show');
            Route::get('{salesDo}/live', [DeliveryTrackingController::class, 'liveTracking'])->name('live');
            Route::get('{salesDo}/history', [DeliveryTrackingController::class, 'history'])->name('history');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('delivery-performance', [SCMReportController::class, 'deliveryPerformance'])->name('delivery-performance');
            Route::get('driver-performance', [SCMReportController::class, 'driverPerformance'])->name('driver-performance');
            Route::get('delivery-summary', [SCMReportController::class, 'deliverySummary'])->name('delivery-summary');
            Route::get('export', [SCMReportController::class, 'export'])->name('export');
        });


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
