<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InventoryManagementService;

class InventoryManagementController extends Controller
{
    protected $InventoryManagementService;

    public function __construct(InventoryManagementService $InventoryManagementService)
    {
        $this->InventoryManagementService = $InventoryManagementService;
    }

    public function checkStock()
    {
        $stock = $this->InventoryManagementService->checkStockAvailability();

        return $this->success([
            $stock
        ]);
    }


}
