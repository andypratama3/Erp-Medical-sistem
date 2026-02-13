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
use App\Http\Controllers\WQS\StockController;
use App\Http\Controllers\WQS\PurchaseRequestController;
use App\Http\Controllers\WQS\StockAdjustmentController;
use App\Http\Controllers\WQS\StockSnapshotController;
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

// PURCHASING Controllers
use App\Http\Controllers\Purchasing\PurchaseOrderController;
use App\Http\Controllers\Purchasing\ImportControlController;
use App\Http\Controllers\Purchasing\InvoiceAPController;
use App\Http\Controllers\Purchasing\PaymentAPController;
use App\Http\Controllers\Purchasing\ForwarderQuoteController;
use App\Http\Controllers\Purchasing\ForwarderInvoiceController;
use App\Http\Controllers\Purchasing\CeisaPIBController;
use App\Http\Controllers\Purchasing\ForwardingDocsController;
use App\Http\Controllers\Purchasing\AuditLogController as PurchasingAuditController;

// PAYROLL Controllers
use App\Http\Controllers\Payroll\SalaryMatrixController;
use App\Http\Controllers\Payroll\EmployeeSettingsController;
use App\Http\Controllers\Payroll\PayrollRunController;
use App\Http\Controllers\Payroll\LoanController;
use App\Http\Controllers\Payroll\ReportController as PayrollReportController;

// HR Controllers
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\AttendanceController;
use App\Http\Controllers\HR\LeaveRequestController;
use App\Http\Controllers\HR\AttendanceSettingsController;
use App\Http\Controllers\HR\DocumentController as HRDocumentController;

// FIXED ASSET Controllers
use App\Http\Controllers\FixedAsset\AssetController;
use App\Http\Controllers\FixedAsset\DepreciationController;
use App\Http\Controllers\FixedAsset\MaintenanceController;
use App\Http\Controllers\FixedAsset\TransferController;
use App\Http\Controllers\FixedAsset\DisposalController;
use App\Http\Controllers\FixedAsset\AuditController as AssetAuditController;

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
use App\Http\Controllers\Master\EmployeeController as MasterEmployeeController;
use App\Http\Controllers\Master\CompanyBankAccountController;
use App\Http\Controllers\Master\EmailCompanyController;
use App\Http\Controllers\Master\DiscountPolicyController;

// System Controllers
use App\Http\Controllers\System\ConfigController;
use App\Http\Controllers\System\AuditLogController;

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
        Route::get('/', [ProfileController::class, 'index'])->name('index');
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
            Route::resource('/', InventoryController::class);
            Route::get('/adjustments', [InventoryController::class, 'adjustments'])->name('adjustments');
            Route::post('/adjust', [InventoryController::class, 'adjust'])->name('adjust');
            Route::get('/stock-levels', [InventoryController::class, 'stockLevels'])->name('stock-levels');

            // API Endpoint
            Route::get('/api/check-stock/{product}', [InventoryController::class, 'checkStock'])->name('api.check-stock');
        });

        // ✅ NEW: Stock Management
        Route::prefix('stock')->name('stock.')->group(function () {
            Route::get('/', [StockController::class, 'index'])->name('index');
            Route::get('/{product}', [StockController::class, 'show'])->name('show');
            Route::get('/{product}/history', [StockController::class, 'history'])->name('history');
        });

        // ✅ NEW: Purchase Request (PR)
        Route::prefix('purchase-requests')->name('purchase-requests.')->group(function () {
            Route::get('/', [PurchaseRequestController::class, 'index'])->name('index');
            Route::get('/create', [PurchaseRequestController::class, 'create'])->name('create');
            Route::post('/', [PurchaseRequestController::class, 'store'])->name('store');
            Route::get('/{pr}', [PurchaseRequestController::class, 'show'])->name('show');
            Route::get('/{pr}/edit', [PurchaseRequestController::class, 'edit'])->name('edit');
            Route::put('/{pr}', [PurchaseRequestController::class, 'update'])->name('update');
            Route::delete('/{pr}', [PurchaseRequestController::class, 'destroy'])->name('destroy');

            // Workflow
            Route::post('/{pr}/submit', [PurchaseRequestController::class, 'submit'])->name('submit');
            Route::post('/{pr}/approve', [PurchaseRequestController::class, 'approve'])->name('approve');
            Route::post('/{pr}/reject', [PurchaseRequestController::class, 'reject'])->name('reject');
            Route::get('/{pr}/export-pdf', [PurchaseRequestController::class, 'exportPDF'])->name('export-pdf');
        });

        // ✅ NEW: Stock Adjustments
        Route::prefix('adjustments')->name('adjustments.')->group(function () {
            Route::get('/', [StockAdjustmentController::class, 'index'])->name('index');
            Route::get('/create', [StockAdjustmentController::class, 'create'])->name('create');
            Route::post('/', [StockAdjustmentController::class, 'store'])->name('store');
            Route::get('/{adjustment}', [StockAdjustmentController::class, 'show'])->name('show');
            Route::delete('/{adjustment}', [StockAdjustmentController::class, 'destroy'])->name('destroy');

            // Approval
            Route::post('/{adjustment}/approve', [StockAdjustmentController::class, 'approve'])->name('approve');
            Route::post('/{adjustment}/reject', [StockAdjustmentController::class, 'reject'])->name('reject');
        });

        // ✅ NEW: Stock Snapshot
        Route::prefix('snapshots')->name('snapshots.')->group(function () {
            Route::get('/', [StockSnapshotController::class, 'index'])->name('index');
            Route::post('/create', [StockSnapshotController::class, 'create'])->name('create');
            Route::get('/{snapshot}', [StockSnapshotController::class, 'show'])->name('show');
            Route::get('/{snapshot}/export', [StockSnapshotController::class, 'export'])->name('export');
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
    | ✅ NEW: PURCHASING Module - Procurement & Import Management
    | Flow: PR → PO → Import Control → Ceisa/PIB → AP Invoice → Payment
    |--------------------------------------------------------------------------
    */
    Route::prefix('purchasing')->name('purchasing.')->group(function () {

        // Purchase Orders
        Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
            Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
            Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create');
            Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
            Route::get('/{po}', [PurchaseOrderController::class, 'show'])->name('show');
            Route::get('/{po}/edit', [PurchaseOrderController::class, 'edit'])->name('edit');
            Route::put('/{po}', [PurchaseOrderController::class, 'update'])->name('update');
            Route::delete('/{po}', [PurchaseOrderController::class, 'destroy'])->name('destroy');

            // Workflow
            Route::post('/from-pr/{pr}', [PurchaseOrderController::class, 'createFromPR'])->name('from-pr');
            Route::post('/{po}/submit', [PurchaseOrderController::class, 'submit'])->name('submit');
            Route::post('/{po}/approve', [PurchaseOrderController::class, 'approve'])->name('approve');
            Route::get('/{po}/export-pdf', [PurchaseOrderController::class, 'exportPDF'])->name('export-pdf');
        });

        // Import Control
        Route::prefix('import-control')->name('import-control.')->group(function () {
            Route::get('/', [ImportControlController::class, 'index'])->name('index');
            Route::get('/{po}', [ImportControlController::class, 'show'])->name('show');
            Route::put('/{po}', [ImportControlController::class, 'update'])->name('update');

            // Milestones
            Route::post('/{po}/update-production', [ImportControlController::class, 'updateProduction'])->name('update-production');
            Route::post('/{po}/update-shipping', [ImportControlController::class, 'updateShipping'])->name('update-shipping');
            Route::post('/{po}/update-arrival', [ImportControlController::class, 'updateArrival'])->name('update-arrival');
        });

        // Forwarder Quotes
        Route::prefix('forwarder-quotes')->name('forwarder-quotes.')->group(function () {
            Route::get('/', [ForwarderQuoteController::class, 'index'])->name('index');
            Route::get('/create', [ForwarderQuoteController::class, 'create'])->name('create');
            Route::post('/', [ForwarderQuoteController::class, 'store'])->name('store');
            Route::get('/{quote}', [ForwarderQuoteController::class, 'show'])->name('show');
            Route::put('/{quote}', [ForwarderQuoteController::class, 'update'])->name('update');
            Route::delete('/{quote}', [ForwarderQuoteController::class, 'destroy'])->name('destroy');

            // Selection
            Route::post('/{quote}/select', [ForwarderQuoteController::class, 'select'])->name('select');
        });

        // Forwarder Invoices
        Route::prefix('forwarder-invoices')->name('forwarder-invoices.')->group(function () {
            Route::get('/', [ForwarderInvoiceController::class, 'index'])->name('index');
            Route::get('/create', [ForwarderInvoiceController::class, 'create'])->name('create');
            Route::post('/', [ForwarderInvoiceController::class, 'store'])->name('store');
            Route::get('/{invoice}', [ForwarderInvoiceController::class, 'show'])->name('show');
            Route::put('/{invoice}', [ForwarderInvoiceController::class, 'update'])->name('update');
        });

        // CEISA / PIB
        Route::prefix('ceisa')->name('ceisa.')->group(function () {
            Route::get('/', [CeisaPIBController::class, 'index'])->name('index');
            Route::get('/{pib}', [CeisaPIBController::class, 'show'])->name('show');
            Route::put('/{pib}', [CeisaPIBController::class, 'update'])->name('update');

            // Workflow
            Route::post('/{pib}/submit', [CeisaPIBController::class, 'submit'])->name('submit');
            Route::post('/{pib}/reject', [CeisaPIBController::class, 'reject'])->name('reject');
            Route::post('/{pib}/sppb', [CeisaPIBController::class, 'sppb'])->name('sppb');
        });

        // Forwarding Documents
        Route::prefix('forwarding-docs')->name('forwarding-docs.')->group(function () {
            Route::get('/', [ForwardingDocsController::class, 'index'])->name('index');
            Route::post('/', [ForwardingDocsController::class, 'store'])->name('store');
            Route::delete('/{doc}', [ForwardingDocsController::class, 'destroy'])->name('destroy');
        });

        // AP Invoices
        Route::prefix('ap-invoices')->name('ap-invoices.')->group(function () {
            Route::get('/', [InvoiceAPController::class, 'index'])->name('index');
            Route::get('/create', [InvoiceAPController::class, 'create'])->name('create');
            Route::post('/', [InvoiceAPController::class, 'store'])->name('store');
            Route::get('/{invoice}', [InvoiceAPController::class, 'show'])->name('show');
            Route::put('/{invoice}', [InvoiceAPController::class, 'update'])->name('update');
        });

        // AP Payments
        Route::prefix('ap-payments')->name('ap-payments.')->group(function () {
            Route::get('/', [PaymentAPController::class, 'index'])->name('index');
            Route::post('/', [PaymentAPController::class, 'store'])->name('store');
            Route::get('/{payment}', [PaymentAPController::class, 'show'])->name('show');
        });

        // Audit Log
        Route::get('/audit-log', [PurchasingAuditController::class, 'index'])->name('audit-log');
    });

    /*
    |--------------------------------------------------------------------------
    | ✅ NEW: PAYROLL Module
    | Flow: Salary Matrix → Employee Settings → Payroll Run → Loans
    |--------------------------------------------------------------------------
    */
    Route::prefix('payroll')->name('payroll.')->group(function () {

        // Salary Matrix
        Route::prefix('salary-matrix')->name('salary-matrix.')->group(function () {
            Route::get('/', [SalaryMatrixController::class, 'index'])->name('index');
            Route::get('/create', [SalaryMatrixController::class, 'create'])->name('create');
            Route::post('/', [SalaryMatrixController::class, 'store'])->name('store');
            Route::get('/{matrix}', [SalaryMatrixController::class, 'show'])->name('show');
            Route::get('/{matrix}/edit', [SalaryMatrixController::class, 'edit'])->name('edit');
            Route::put('/{matrix}', [SalaryMatrixController::class, 'update'])->name('update');
            Route::delete('/{matrix}', [SalaryMatrixController::class, 'destroy'])->name('destroy');

            // Import/Export
            Route::get('/import/template', [SalaryMatrixController::class, 'downloadTemplate'])->name('import-template');
            Route::post('/import', [SalaryMatrixController::class, 'import'])->name('import');
            Route::get('/export', [SalaryMatrixController::class, 'export'])->name('export');
        });

        // Employee Settings
        Route::prefix('employee-settings')->name('employee-settings.')->group(function () {
            Route::get('/', [EmployeeSettingsController::class, 'index'])->name('index');
            Route::get('/{employee}/edit', [EmployeeSettingsController::class, 'edit'])->name('edit');
            Route::put('/{employee}', [EmployeeSettingsController::class, 'update'])->name('update');
        });

        // Payroll Runs
        Route::prefix('runs')->name('runs.')->group(function () {
            Route::get('/', [PayrollRunController::class, 'index'])->name('index');
            Route::get('/create', [PayrollRunController::class, 'create'])->name('create');
            Route::post('/', [PayrollRunController::class, 'store'])->name('store');
            Route::get('/{run}', [PayrollRunController::class, 'show'])->name('show');
            Route::get('/{run}/edit', [PayrollRunController::class, 'edit'])->name('edit');
            Route::put('/{run}', [PayrollRunController::class, 'update'])->name('update');

            // Workflow
            Route::post('/{run}/generate', [PayrollRunController::class, 'generate'])->name('generate');
            Route::post('/{run}/post', [PayrollRunController::class, 'post'])->name('post');
            Route::post('/{run}/paid', [PayrollRunController::class, 'markPaid'])->name('paid');

            // Export
            Route::get('/{run}/export-bank', [PayrollRunController::class, 'exportBank'])->name('export-bank');
            Route::get('/{run}/export-slips', [PayrollRunController::class, 'exportSlips'])->name('export-slips');
        });

        // Loans & Kasbon
        Route::prefix('loans')->name('loans.')->group(function () {
            Route::get('/', [LoanController::class, 'index'])->name('index');
            Route::get('/create', [LoanController::class, 'create'])->name('create');
            Route::post('/', [LoanController::class, 'store'])->name('store');
            Route::get('/{loan}', [LoanController::class, 'show'])->name('show');
            Route::put('/{loan}', [LoanController::class, 'update'])->name('update');

            // Actions
            Route::post('/{loan}/approve', [LoanController::class, 'approve'])->name('approve');
            Route::post('/{loan}/close', [LoanController::class, 'close'])->name('close');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/summary', [PayrollReportController::class, 'summary'])->name('summary');
            Route::get('/by-department', [PayrollReportController::class, 'byDepartment'])->name('by-department');
            Route::get('/tax-report', [PayrollReportController::class, 'taxReport'])->name('tax-report');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | ✅ NEW: HR Module - Human Resources
    | Flow: Employees → Attendance → Leave Requests → Documents
    |--------------------------------------------------------------------------
    */
    Route::prefix('hr')->name('hr.')->group(function () {

        // Employees (berbeda dengan Master Employees)
        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('/', [EmployeeController::class, 'index'])->name('index');
            Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
            Route::get('/{employee}/profile', [EmployeeController::class, 'profile'])->name('profile');
        });

        // Attendance
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [AttendanceController::class, 'index'])->name('index');
            Route::get('/my-attendance', [AttendanceController::class, 'myAttendance'])->name('my-attendance');
            Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
            Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('check-out');

            // Reports
            Route::get('/recap', [AttendanceController::class, 'recap'])->name('recap');
            Route::get('/export', [AttendanceController::class, 'export'])->name('export');
        });

        // Leave Requests
        Route::prefix('leave-requests')->name('leave-requests.')->group(function () {
            Route::get('/', [LeaveRequestController::class, 'index'])->name('index');
            Route::get('/create', [LeaveRequestController::class, 'create'])->name('create');
            Route::post('/', [LeaveRequestController::class, 'store'])->name('store');
            Route::get('/{request}', [LeaveRequestController::class, 'show'])->name('show');

            // Approval
            Route::post('/{request}/approve', [LeaveRequestController::class, 'approve'])->name('approve');
            Route::post('/{request}/reject', [LeaveRequestController::class, 'reject'])->name('reject');
        });

        // Attendance Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [AttendanceSettingsController::class, 'index'])->name('index');
            Route::put('/', [AttendanceSettingsController::class, 'update'])->name('update');

            // Office Geofence
            Route::get('/offices', [AttendanceSettingsController::class, 'offices'])->name('offices');
            Route::put('/offices/{office}', [AttendanceSettingsController::class, 'updateOffice'])->name('update-office');
        });

        // HR Documents
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [HRDocumentController::class, 'index'])->name('index');
            Route::get('/create', [HRDocumentController::class, 'create'])->name('create');
            Route::post('/', [HRDocumentController::class, 'store'])->name('store');
            Route::get('/{document}', [HRDocumentController::class, 'show'])->name('show');

            // Workflow
            Route::post('/{document}/submit', [HRDocumentController::class, 'submit'])->name('submit');
            Route::post('/{document}/approve', [HRDocumentController::class, 'approve'])->name('approve');
            Route::post('/{document}/acknowledge', [HRDocumentController::class, 'acknowledge'])->name('acknowledge');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | ✅ NEW: FIXED ASSET Module
    | Flow: Assets → Depreciation → Maintenance → Transfer → Disposal → Audit
    |--------------------------------------------------------------------------
    */
    Route::prefix('fixed-assets')->name('fixed-assets.')->group(function () {

        // Assets
        Route::resource('assets', AssetController::class);
        Route::post('/assets/{asset}/toggle-status', [AssetController::class, 'toggleStatus'])->name('assets.toggle-status');

        // Depreciation
        Route::prefix('depreciation')->name('depreciation.')->group(function () {
            Route::get('/', [DepreciationController::class, 'index'])->name('index');
            Route::post('/run', [DepreciationController::class, 'run'])->name('run');
            Route::get('/{run}', [DepreciationController::class, 'show'])->name('show');
            Route::get('/{run}/export', [DepreciationController::class, 'export'])->name('export');
        });

        // Maintenance
        Route::prefix('maintenance')->name('maintenance.')->group(function () {
            Route::get('/', [MaintenanceController::class, 'index'])->name('index');
            Route::get('/create', [MaintenanceController::class, 'create'])->name('create');
            Route::post('/', [MaintenanceController::class, 'store'])->name('store');
            Route::get('/{maintenance}', [MaintenanceController::class, 'show'])->name('show');
            Route::put('/{maintenance}', [MaintenanceController::class, 'update'])->name('update');
        });

        // Transfers
        Route::prefix('transfers')->name('transfers.')->group(function () {
            Route::get('/', [TransferController::class, 'index'])->name('index');
            Route::get('/create', [TransferController::class, 'create'])->name('create');
            Route::post('/', [TransferController::class, 'store'])->name('store');
            Route::get('/{transfer}', [TransferController::class, 'show'])->name('show');
        });

        // Disposals
        Route::prefix('disposals')->name('disposals.')->group(function () {
            Route::get('/', [DisposalController::class, 'index'])->name('index');
            Route::get('/create', [DisposalController::class, 'create'])->name('create');
            Route::post('/', [DisposalController::class, 'store'])->name('store');
            Route::get('/{disposal}', [DisposalController::class, 'show'])->name('show');
        });

        // Asset Audits
        Route::prefix('audits')->name('audits.')->group(function () {
            Route::get('/', [AssetAuditController::class, 'index'])->name('index');
            Route::get('/create', [AssetAuditController::class, 'create'])->name('create');
            Route::post('/', [AssetAuditController::class, 'store'])->name('store');
            Route::get('/{audit}', [AssetAuditController::class, 'show'])->name('show');

            // Workflow
            Route::post('/{audit}/close', [AssetAuditController::class, 'close'])->name('close');
            Route::get('/{audit}/report', [AssetAuditController::class, 'report'])->name('report');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Master Data Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('master')->name('master.')->group(function () {

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

        // ✅ NEW: Employees
        Route::resource('employees', MasterEmployeeController::class);
        Route::post('/employees/{employee}/toggle-status', [MasterEmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');

        // ✅ NEW: Company Bank Accounts
        Route::resource('bank-accounts', CompanyBankAccountController::class);

        // ✅ NEW: Company Emails
        Route::resource('email-company', EmailCompanyController::class);

        // ✅ NEW: Discount Policies
        Route::resource('discount-policy', DiscountPolicyController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | ✅ NEW: SYSTEM Module - Configuration & Audit
    |--------------------------------------------------------------------------
    */
    Route::prefix('system')->name('system.')->group(function () {

        // System Configuration
        Route::prefix('config')->name('config.')->group(function () {
            Route::get('/', [ConfigController::class, 'index'])->name('index');
            Route::put('/', [ConfigController::class, 'update'])->name('update');

            // Group-based config
            Route::get('/{group}', [ConfigController::class, 'byGroup'])->name('by-group');
        });

        // System Audit Log
        Route::prefix('audit-log')->name('audit-log.')->group(function () {
            Route::get('/', [AuditLogController::class, 'index'])->name('index');
            Route::get('/{log}', [AuditLogController::class, 'show'])->name('show');
            Route::get('/export', [AuditLogController::class, 'export'])->name('export');

            // Filter by module
            Route::get('/module/{module}', [AuditLogController::class, 'byModule'])->name('by-module');
        });
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