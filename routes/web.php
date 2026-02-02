<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

// CRM Controllers
use App\Http\Controllers\CRM\SalesDOController;

// WQS Controllers
use App\Http\Controllers\WQS\StockCheckController;
use App\Http\Controllers\WQS\InventoryController;
use App\Http\Controllers\WQS\TaskBoardController as WQSTaskBoardController;

// SCM Controllers
use App\Http\Controllers\SCM\SCMDeliveryController;
use App\Http\Controllers\SCM\SCMDriverController;
use App\Http\Controllers\SCM\VehicleController;
use App\Http\Controllers\SCM\DeliveryTrackingController;
use App\Http\Controllers\SCM\TaskBoardController as SCMTaskBoardController;

// ACT Controllers
use App\Http\Controllers\ACT\InvoiceController;
use App\Http\Controllers\ACT\TaskBoardController as ACTTaskBoardController;

// FIN Controllers
use App\Http\Controllers\FIN\PaymentController;
use App\Http\Controllers\FIN\CollectionController;
use App\Http\Controllers\FIN\AgingController;
use App\Http\Controllers\FIN\TaskBoardController as FINTaskBoardController;

// Master Data Controllers
use App\Http\Controllers\Master\BranchController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\VendorController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Master\PaymentTermController;
use App\Http\Controllers\Master\TaxController;
use App\Http\Controllers\Master\ManufactureController;
use App\Http\Controllers\Master\OfficeController;
use App\Http\Controllers\Master\DepartmentController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Switch Branch
    // Branch switch route (outside master prefix)
    Route::post('/branches/switch', [BranchController::class, 'switchBranch'])
        ->middleware('auth')
        ->name('branches.switch');


    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | CRM Module - Sales Delivery Order (SalesDO)
    | Flow Start: SalesDO creation and submission
    |--------------------------------------------------------------------------
    */
    Route::prefix('crm')->name('crm.')->group(function () {
        // Sales DO Management
        Route::prefix('sales-do')->name('sales-do.')->group(function () {
            Route::get('/', [SalesDOController::class, 'index'])->name('index');
            Route::get('/create', [SalesDOController::class, 'create'])->name('create');
            Route::post('/', [SalesDOController::class, 'store'])->name('store');
            Route::get('/{salesDo}', [SalesDOController::class, 'show'])->name('show');
            Route::get('/{salesDo}/edit', [SalesDOController::class, 'edit'])->name('edit');
            Route::put('/{salesDo}', [SalesDOController::class, 'update'])->name('update');
            Route::delete('/{salesDo}', [SalesDOController::class, 'destroy'])->name('destroy');

            // Submit to WQS (Move to wqs_ready status)
            Route::post('/{salesDo}/submit', [SalesDOController::class, 'submit'])->name('submit');

            // Export
            Route::get('/{salesDo}/export-pdf', [SalesDOController::class, 'exportPDF'])->name('exportPDF');

            // API endpoints for product selection
            Route::get('/api/products/search', [SalesDOController::class, 'searchProducts'])->name('api.products.search');
            Route::get('/api/products/{product}', [SalesDOController::class, 'getProduct'])->name('api.products.show');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | WQS Module - Warehouse Quality & Stock Management
    | Flow: Receives from CRM, prepares stock, creates delivery
    |--------------------------------------------------------------------------
    */
    Route::prefix('wqs')->name('wqs.')->group(function () {
        // Task Board - View incoming SalesDOs
        Route::prefix('task-board')->name('task-board.')->group(function () {
            Route::get('/', [WQSTaskBoardController::class, 'index'])->name('index');
            Route::get('/{salesDo}', [WQSTaskBoardController::class, 'show'])->name('show');

            // Actions on Sales DO
            Route::post('/{salesDo}/start-processing', [WQSTaskBoardController::class, 'start'])->name('start');
            Route::post('/{salesDo}/hold', [WQSTaskBoardController::class, 'hold'])->name('hold');
            Route::post('/{salesDo}/complete', [WQSTaskBoardController::class, 'complete'])->name('complete');
            Route::post('task-board/{taskBoard}/assign', [WQSTaskBoardController::class, 'assign'])->name('assign');
            Route::put('task-board/{taskBoard}/priority', [WQSTaskBoardController::class, 'updatePriority'])->name('priority');
            Route::get('task-board/stats', [WQSTaskBoardController::class, 'dashboardStats'])->name('stats');
            Route::post('task-board/{taskBoard}/reject', [WQSTaskBoardController::class, 'reject'])->name('reject');
        });

        // Stock Check - Verify product availability
        Route::prefix('stock-checks')->name('stock-checks.')->group(function () {
            Route::get('/', [StockCheckController::class, 'index'])->name('index');
            Route::get('/create', [StockCheckController::class, 'create'])->name('create');
            Route::post('/', [StockCheckController::class, 'store'])->name('store');
            Route::get('/{stockCheck}', [StockCheckController::class, 'show'])->name('show');
            Route::get('/{stockCheck}/edit', [StockCheckController::class, 'edit'])->name('edit');
            Route::put('/{stockCheck}', [StockCheckController::class, 'update'])->name('update');
            Route::delete('/{stockCheck}', [StockCheckController::class, 'destroy'])->name('destroy');

            // Approve stock check (validates inventory)
            Route::post('/{stockCheck}/approve', [StockCheckController::class, 'approve'])->name('approve');

            // Link to Sales DO
            Route::post('/{stockCheck}/link-sales-do', [StockCheckController::class, 'linkSalesDO'])->name('link-sales-do');
        });

         Route::resource('stock-checks', StockCheckController::class);
        Route::post('stock-checks/{stockCheck}/mark-failed', [StockCheckController::class, 'markFailed'])
            ->name('stock-checks.mark-failed');
        Route::get('stock-checks/{stockCheck}/problematic-items', [StockCheckController::class, 'getProblematicItems'])
            ->name('stock-checks.problematic-items');
        Route::get('stock-checks/{stockCheck}/report', [StockCheckController::class, 'generateReport'])
            ->name('stock-checks.report');

        // Inventory Adjustments
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('index');
            Route::get('/adjustments', [InventoryController::class, 'adjustments'])->name('adjustments');
            Route::post('/adjust', [InventoryController::class, 'adjust'])->name('adjust');
            Route::get('/stock-levels', [InventoryController::class, 'stockLevels'])->name('stock-levels');

            // Check available stock for a product
            Route::get('/api/check-stock/{product}', [InventoryController::class, 'checkStock'])->name('api.check-stock');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | SCM Module - Supply Chain Management & Delivery
    | Flow: Creates delivery from WQS, assigns driver, tracks delivery
    |--------------------------------------------------------------------------
    */
    Route::prefix('scm')->name('scm.')->group(function () {
        // Task Board - View ready-to-deliver orders
        Route::prefix('task-board')->name('task-board.')->group(function () {
            Route::get('/', [SCMTaskBoardController::class, 'index'])->name('index');
            Route::get('/{salesDo}', [SCMTaskBoardController::class, 'show'])->name('show');


            Route::post('task-board/{salesDo}/assign-driver', [SCMTaskBoardController::class, 'assignDriver'])->name('assign-driver');
            Route::post('task-board/{salesDo}/start-delivery', [SCMTaskBoardController::class, 'startDelivery'])->name('start-delivery');
            Route::post('task-board/{salesDo}/complete-delivery', [SCMTaskBoardController::class, 'completeDelivery'])->name('complete-delivery');
            Route::post('task-board/{salesDo}/update-location', [SCMTaskBoardController::class, 'updateLocation'])->name('update-location');
        });

        // Deliveries - Create and manage deliveries
        Route::prefix('deliveries')->name('deliveries.')->group(function () {
            Route::get('/', [SCMDeliveryController::class, 'index'])->name('index');
            Route::get('/create', [SCMDeliveryController::class, 'create'])->name('create');
            Route::post('/', [SCMDeliveryController::class, 'store'])->name('store');
            Route::get('/{delivery}', [SCMDeliveryController::class, 'show'])->name('show');
            Route::get('/{delivery}/edit', [SCMDeliveryController::class, 'edit'])->name('edit');
            Route::put('/{delivery}', [SCMDeliveryController::class, 'update'])->name('update');

            // Assign driver & vehicle
            Route::post('/{delivery}/assign-driver', [SCMDeliveryController::class, 'assignDriver'])->name('assign-driver');

            // Dispatch delivery (scm_on_delivery status)
            Route::post('/{delivery}/dispatch', [SCMDeliveryController::class, 'dispatch'])->name('dispatch');

            // Mark as delivered (scm_delivered status -> moves to ACT)
            Route::post('/{delivery}/mark-delivered', [SCMDeliveryController::class, 'markDelivered'])->name('mark-delivered');

            // Upload POD (Proof of Delivery)
            Route::post('/{delivery}/upload-pod', [SCMDeliveryController::class, 'uploadPOD'])->name('upload-pod');
        });

        // Delivery Tracking - Real-time tracking
        Route::prefix('tracking')->name('tracking.')->group(function () {
            Route::get('/', [DeliveryTrackingController::class, 'index'])->name('index');
            Route::get('/{tracking}', [DeliveryTrackingController::class, 'show'])->name('show');
            Route::post('/{delivery}/update-location', [DeliveryTrackingController::class, 'updateLocation'])->name('update-location');
        });



        // Drivers
        Route::resource('drivers', SCMDriverController::class);

        // Vehicles
        Route::resource('vehicles', VehicleController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | ACT Module - Accounting Invoice Management
    | Flow: Receives delivered orders, creates invoices
    |--------------------------------------------------------------------------
    */
    Route::prefix('act')->name('act.')->group(function () {
        // Task Board - View delivered orders
        Route::prefix('task-board')->name('task-board.')->group(function () {
            Route::get('/', [ACTTaskBoardController::class, 'index'])->name('index');
            Route::get('/{salesDo}', [ACTTaskBoardController::class, 'show'])->name('show');
        });

        // Invoices
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index');
            Route::get('/create', [InvoiceController::class, 'create'])->name('create');
            Route::post('/', [InvoiceController::class, 'store'])->name('store');
            Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
            Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
            Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');

            // Create invoice from Sales DO
            Route::post('/from-sales-do/{salesDo}', [InvoiceController::class, 'createFromSalesDO'])->name('from-sales-do');

            // Mark as Tukar Faktur (act_tukar_faktur status)
            Route::post('/{invoice}/tukar-faktur', [InvoiceController::class, 'tukarFaktur'])->name('tukar-faktur');

            // Approve invoice (act_invoiced status -> moves to FIN)
            Route::post('/{invoice}/approve', [InvoiceController::class, 'approve'])->name('approve');

            // Export invoice
            Route::get('/{invoice}/export-pdf', [InvoiceController::class, 'exportPDF'])->name('export-pdf');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | FIN Module - Finance Payment & Collection
    | Flow: Receives invoiced orders, manages collections and payments
    |--------------------------------------------------------------------------
    */
    Route::prefix('fin')->name('fin.')->group(function () {
        // Task Board - View invoiced orders
        Route::prefix('task-board')->name('task-board.')->group(function () {
            Route::get('/', [FINTaskBoardController::class, 'index'])->name('index');
            Route::get('/{salesDo}', [FINTaskBoardController::class, 'show'])->name('show');
        });

        // Collections - Manage AR collections
        Route::prefix('collections')->name('collections.')->group(function () {
            Route::get('/', [CollectionController::class, 'index'])->name('index');
            Route::get('/create', [CollectionController::class, 'create'])->name('create');
            Route::post('/', [CollectionController::class, 'store'])->name('store');
            Route::get('/{collection}', [CollectionController::class, 'show'])->name('show');

            // Record payment (fin_paid status)
            Route::post('/{invoice}/record-payment', [CollectionController::class, 'recordPayment'])->name('record-payment');

            // Mark as collecting (fin_on_collect status)
            Route::post('/{invoice}/start-collection', [CollectionController::class, 'startCollection'])->name('start-collection');

            // Mark as overdue (fin_overdue status)
            Route::post('/{invoice}/mark-overdue', [CollectionController::class, 'markOverdue'])->name('mark-overdue');
        });

        // Payments - View and manage payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [PaymentController::class, 'index'])->name('index');
            Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
            Route::post('/{payment}/confirm', [PaymentController::class, 'confirm'])->name('confirm');
            Route::get('/{payment}/receipt', [PaymentController::class, 'receipt'])->name('receipt');
        });

        // Aging Report - Track overdue invoices
        Route::prefix('aging')->name('aging.')->group(function () {
            Route::get('/', [AgingController::class, 'index'])->name('index');
            Route::get('/export', [AgingController::class, 'export'])->name('export');
            Route::get('/by-customer/{customer}', [AgingController::class, 'byCustomer'])->name('by-customer');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Master Data Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('master')->name('master.')->middleware('role:owner|admin')->group(function () {
        // Branches - Multi-branch support
        Route::resource('branches', BranchController::class);
        Route::post('branches/{branch}/switch', [BranchController::class, 'switchBranch'])->name('branches.switch');

        // Customers
        Route::resource('customers', CustomerController::class);
        Route::post('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');

        // Vendors
        Route::resource('vendors', VendorController::class);
        Route::post('vendors/{vendor}/toggle-status', [VendorController::class, 'toggleStatus'])->name('vendors.toggle-status');

        // Products
        Route::resource('products', ProductController::class);
        Route::get('products/import/template', [ProductController::class, 'downloadTemplate'])->name('products.import.template');
        Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
        Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');

        // Payment Terms
        Route::resource('payment-terms', PaymentTermController::class);

        // Taxes
        Route::resource('taxes', TaxController::class);

        // Manufactures
        Route::resource('manufactures', ManufactureController::class);

        // Offices
        Route::resource('offices', OfficeController::class);

        // Departments
        Route::resource('departments', DepartmentController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | User & Role Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('role:owner|admin')->group(function () {
        // Users
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // Roles
        Route::resource('roles', RoleController::class);

        // Permissions
        Route::resource('permissions', PermissionController::class);
    });
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
require __DIR__.'/reg_alkes.php';
