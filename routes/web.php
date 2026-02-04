<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FIN\AgingController;

// CRM Controllers
use App\Http\Controllers\CRM\SalesDOController;

// WQS Controllers
use App\Http\Controllers\WQS\InventoryController;
use App\Http\Controllers\WQS\StockCheckController;
use App\Http\Controllers\WQS\TaskBoardController as WQSTaskBoardController;

// SCM Controllers
use App\Http\Controllers\SCM\VehicleController;
use App\Http\Controllers\SCM\SCMDriverController;
use App\Http\Controllers\SCM\SCMDeliveryController;
use App\Http\Controllers\SCM\DeliveryTrackingController;
use App\Http\Controllers\SCM\TaskBoardController as SCMTaskBoardController;

// ACT Controllers
use App\Http\Controllers\ACT\InvoiceController;
use App\Http\Controllers\ACT\TaskBoardController as ACTTaskBoardController;

// FIN Controllers
use App\Http\Controllers\FIN\PaymentController;
use App\Http\Controllers\FIN\CollectionController;
use App\Http\Controllers\FIN\TaskBoardController as FINTaskBoardController;

// Master Data Controllers
use App\Http\Controllers\Master\TaxController;
use App\Http\Controllers\Master\BranchController;
use App\Http\Controllers\Master\OfficeController;
use App\Http\Controllers\Master\VendorController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\PaymentTermController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\ManufactureController;

// Admin Controllers
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
require __DIR__.'/reg_alkes.php';

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/recent', [NotificationController::class, 'recent'])->name('recent');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });

    /*
    |--------------------------------------------------------------------------
    | CRM Module - Customer Relationship Management
    | Flow: Sales DO creation → Submission to WQS
    |--------------------------------------------------------------------------
    */
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::prefix('sales-do')->name('sales-do.')->group(function () {
            Route::get('/', [SalesDOController::class, 'index'])->name('index');
            Route::get('/create', [SalesDOController::class, 'create'])->name('create');
            Route::post('/', [SalesDOController::class, 'store'])->name('store');
            Route::get('/{salesDo}', [SalesDOController::class, 'show'])->name('show');
            Route::get('/{salesDo}/edit', [SalesDOController::class, 'edit'])->name('edit');
            Route::put('/{salesDo}', [SalesDOController::class, 'update'])->name('update');
            Route::delete('/{salesDo}', [SalesDOController::class, 'destroy'])->name('destroy');

            // Workflow Actions
            Route::post('/{salesDo}/submit', [SalesDOController::class, 'submit'])->name('submit');
            Route::get('/{salesDo}/export-pdf', [SalesDOController::class, 'exportPDF'])->name('export-pdf');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | WQS Module - Warehouse Quality System
    | Flow: Receive from CRM → Stock Check → Inventory Prep → Handover to SCM
    |--------------------------------------------------------------------------
    */
    Route::prefix('wqs')->name('wqs.')->group(function () {

        // Task Board - Manage incoming Sales DOs
        Route::prefix('task-board')->name('task-board.')->group(function () {
            Route::get('/', [WQSTaskBoardController::class, 'index'])->name('index');
            Route::get('/{salesDo}', [WQSTaskBoardController::class, 'show'])->name('show');
            Route::get('/stats', [WQSTaskBoardController::class, 'dashboardStats'])->name('stats');

            // Task Actions
            Route::post('/{salesDo}/start', [WQSTaskBoardController::class, 'start'])->name('start');
            Route::post('/{salesDo}/hold', [WQSTaskBoardController::class, 'hold'])->name('hold');
            Route::post('/{salesDo}/complete', [WQSTaskBoardController::class, 'complete'])->name('complete');
            Route::post('/{salesDo}/reject', [WQSTaskBoardController::class, 'reject'])->name('reject');

            // Assignment & Priority
            Route::post('/{taskBoard}/assign', [WQSTaskBoardController::class, 'assign'])->name('assign');
            Route::put('/{taskBoard}/priority', [WQSTaskBoardController::class, 'updatePriority'])->name('priority');
        });

        // Stock Check Management
        Route::prefix('stock-checks')->name('stock-checks.')->group(function () {
            Route::get('/', [StockCheckController::class, 'index'])->name('index');
            Route::get('/create', [StockCheckController::class, 'create'])->name('create');
            Route::post('/', [StockCheckController::class, 'store'])->name('store');
            Route::get('/{stockCheck}', [StockCheckController::class, 'show'])->name('show');
            Route::get('/{stockCheck}/edit', [StockCheckController::class, 'edit'])->name('edit');
            Route::put('/{stockCheck}', [StockCheckController::class, 'update'])->name('update');
            Route::delete('/{stockCheck}', [StockCheckController::class, 'destroy'])->name('destroy');

            // Workflow Actions
            Route::post('/{stockCheck}/approve', [StockCheckController::class, 'approve'])->name('approve');
            Route::post('/{stockCheck}/mark-failed', [StockCheckController::class, 'markFailed'])->name('mark-failed');
            Route::post('/{stockCheck}/link-sales-do', [StockCheckController::class, 'linkSalesDO'])->name('link-sales-do');

            // Reports
            Route::get('/{stockCheck}/problematic-items', [StockCheckController::class, 'getProblematicItems'])->name('problematic-items');
            Route::get('/{stockCheck}/report', [StockCheckController::class, 'generateReport'])->name('report');
        });

        // Inventory Management
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('index');
            Route::get('/adjustments', [InventoryController::class, 'adjustments'])->name('adjustments');
            Route::post('/adjust', [InventoryController::class, 'adjust'])->name('adjust');
            Route::get('/stock-levels', [InventoryController::class, 'stockLevels'])->name('stock-levels');

            // API Endpoint
            Route::get('/api/check-stock/{product}', [InventoryController::class, 'checkStock'])->name('api.check-stock');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | SCM Module - Supply Chain Management
    | Flow: Receive from WQS → Assign Driver → Delivery → Proof of Delivery → Handover to ACT
    |--------------------------------------------------------------------------
    */
    Route::prefix('scm')->name('scm.')->group(function () {

        // Task Board - Ready to deliver orders
        Route::prefix('task-board')->name('task-board.')->group(function () {
            Route::get('/', [SCMTaskBoardController::class, 'index'])->name('index');
            Route::get('/{salesDo}', [SCMTaskBoardController::class, 'show'])->name('show');

            // Delivery Actions
            Route::post('/{salesDo}/assign-driver', [SCMTaskBoardController::class, 'assignDriver'])->name('assign-driver');
            Route::post('/{salesDo}/start-delivery', [SCMTaskBoardController::class, 'startDelivery'])->name('start-delivery');
            Route::post('/{salesDo}/complete-delivery', [SCMTaskBoardController::class, 'completeDelivery'])->name('complete-delivery');
            Route::post('/{salesDo}/update-location', [SCMTaskBoardController::class, 'updateLocation'])->name('update-location');
        });

        // Delivery Management
        Route::prefix('deliveries')->name('deliveries.')->group(function () {
            Route::get('/', [SCMDeliveryController::class, 'index'])->name('index');
            Route::get('/create', [SCMDeliveryController::class, 'create'])->name('create');
            Route::post('/', [SCMDeliveryController::class, 'store'])->name('store');
            Route::get('/{delivery}', [SCMDeliveryController::class, 'show'])->name('show');
            Route::get('/{delivery}/edit', [SCMDeliveryController::class, 'edit'])->name('edit');
            Route::put('/{delivery}', [SCMDeliveryController::class, 'update'])->name('update');
            Route::delete('/{delivery}', [SCMDeliveryController::class,'destroy'])->name('destroy');


            // Workflow Actions
            Route::post('/{delivery}/assign-driver', [SCMDeliveryController::class, 'assignDriver'])->name('assign-driver');
            Route::post('/{delivery}/dispatch', [SCMDeliveryController::class, 'dispatch'])->name('dispatch');
            Route::post('/{delivery}/mark-delivered', [SCMDeliveryController::class, 'markDelivered'])->name('delivered');
            Route::post('/{delivery}/upload-pod', [SCMDeliveryController::class, 'uploadPOD'])->name('upload-pod');
        });

        // Delivery Tracking
        Route::prefix('tracking')->name('tracking.')->group(function () {
            Route::get('/', [DeliveryTrackingController::class, 'index'])->name('index');
            Route::get('/{tracking}', [DeliveryTrackingController::class, 'show'])->name('show');
            Route::post('/{tracking}/update-location', [DeliveryTrackingController::class, 'updateLocation'])->name('update-location');
        });

        // Resources
        Route::resource('drivers', SCMDriverController::class);
        Route::resource('vehicles', VehicleController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | ACT Module - Accounting
    | Flow: Receive delivered orders → Create Invoice → Tukar Faktur → Approve → Handover to FIN
    |--------------------------------------------------------------------------
    */
    Route::prefix('act')->name('act.')->group(function () {

        // Task Board - Delivered orders awaiting invoicing
        Route::prefix('task-board')->name('task-board.')->group(function () {
            Route::get('/', [ACTTaskBoardController::class, 'index'])->name('index');
            Route::get('/{salesDo}', [ACTTaskBoardController::class, 'show'])->name('show');
        });

        // Invoice Management
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index');
            Route::get('/create', [InvoiceController::class, 'create'])->name('create');
            Route::post('/', [InvoiceController::class, 'store'])->name('store');
            Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
            Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
            Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');

            // Workflow Actions
            Route::post('/from-sales-do/{salesDo}', [InvoiceController::class, 'createFromSalesDO'])->name('from-sales-do');
            Route::post('/{invoice}/tukar-faktur', [InvoiceController::class, 'tukarFaktur'])->name('tukar-faktur');
            Route::post('/{invoice}/approve', [InvoiceController::class, 'approve'])->name('approve');

            // Export
            Route::get('/{invoice}/export-pdf', [InvoiceController::class, 'exportPDF'])->name('export-pdf');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | FIN Module - Finance
    | Flow: Receive invoiced orders → Collections → Payments → Aging Reports
    |--------------------------------------------------------------------------
    */
    Route::prefix('fin')->name('fin.')->group(function () {

        // Task Board - Invoiced orders awaiting collection
        Route::prefix('task-board')->name('task-board.')->group(function () {
            Route::get('/', [FINTaskBoardController::class, 'index'])->name('index');
            Route::get('/{salesDo}', [FINTaskBoardController::class, 'show'])->name('show');
        });

        // Collections (AR Management)
        Route::prefix('collections')->name('collections.')->group(function () {
            Route::get('/', [CollectionController::class, 'index'])->name('index');
            Route::get('/create', [CollectionController::class, 'create'])->name('create');
            Route::post('/', [CollectionController::class, 'store'])->name('store');
            Route::get('/{collection}', [CollectionController::class, 'show'])->name('show');

            // Invoice Actions
            Route::post('/{invoice}/start-collection', [CollectionController::class, 'startCollection'])->name('start-collection');
            Route::post('/{invoice}/record-payment', [CollectionController::class, 'recordPayment'])->name('record-payment');
            Route::post('/{invoice}/mark-overdue', [CollectionController::class, 'markOverdue'])->name('mark-overdue');
        });

        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [PaymentController::class, 'index'])->name('index');
            Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
            Route::post('/{payment}/confirm', [PaymentController::class, 'confirm'])->name('confirm');
            Route::get('/{payment}/receipt', [PaymentController::class, 'receipt'])->name('receipt');
        });

        // Aging Reports
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
    Route::prefix('master')->name('master.')->middleware(['role:owner|superadmin'])->group(function () {

        // Branch Management
        Route::resource('branches', BranchController::class);
        Route::post('/branches/switch', [BranchController::class, 'switchBranch'])->name('branches.switch');

        // Customers
        Route::resource('customers', CustomerController::class);
        Route::post('/customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');

        // Vendors
        Route::resource('vendors', VendorController::class);
        Route::post('/vendors/{vendor}/toggle-status', [VendorController::class, 'toggleStatus'])->name('vendors.toggle-status');

        // Products
        Route::resource('products', ProductController::class);
        Route::get('/products/import/template', [ProductController::class, 'downloadTemplate'])->name('products.import.template');
        Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
        Route::post('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');

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
    | User & Role Management (Admin)
    |--------------------------------------------------------------------------
    */

      Route::prefix('management-system')->name('management.')->middleware(['role:owner|admin'])->group(function () {

        // Users
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // Roles & Permissions
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
    });

});
