<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\{
    OfficeController,
    DepartmentController,
    ProductController,
    CustomerController,
    VendorController,
    ManufactureController,
    TaxController,
    PaymentTermController,
    BranchController,
};
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;


Route::middleware(['auth'])->prefix('master')->name('master.')->group(function () {
    Route::resource('offices', OfficeController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('products', ProductController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('vendors', VendorController::class);
    Route::resource('manufactures', ManufactureController::class);
    Route::resource('taxes', TaxController::class);
    Route::resource('payment-terms', PaymentTermController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('users', UserController::class);
});

// Branch switch route (outside master prefix)
Route::post('/branches/switch', [BranchController::class, 'switchBranch'])
    ->middleware('auth')
    ->name('branches.switch');
