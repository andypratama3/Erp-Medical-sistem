<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\{
    OfficeController,
    DepartmentController,
    ProductController,
    CustomerController,
    VendorController,
    ManufactureController,
    RoleController,
    PermissionController,
    UserContController,
};

Route::middleware(['auth'])->prefix('master')->name('master.')->group(function () {
    Route::resource('offices', OfficeController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('products', ProductController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('vendors', VendorController::class);
    Route::resource('manufactures', ManufactureController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('users', UserContController::class);


});
