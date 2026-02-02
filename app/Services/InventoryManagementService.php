<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Branch;
use App\Models\SalesDO;
use App\Models\SalesDOItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\InventoryAdjustment;
use Exception;

/**
 * Inventory Management Service
 * 
 * Handles all inventory operations including:
 * - Stock checking and reservation
 * - Stock adjustments and releases
 * - Branch-level inventory management
 * - Automatic stock updates based on workflow
 */
class InventoryManagementService
{
    /**
     * Check if sufficient stock is available for a Sales DO
     * 
     * @param SalesDO $salesDO
     * @param int|null $branchId Branch ID (defaults to current branch)
     * @return array ['available' => bool, 'details' => array]
     */
    public function checkStockAvailability(SalesDO $salesDO, ?int $branchId = null): array
    {
        $branchId = $branchId ?? auth()->user()->current_branch_id;
        $available = true;
        $details = [];

        foreach ($salesDO->items as $item) {
            $branchStock = DB::table('branch_product_stock')
                ->where('branch_id', $branchId)
                ->where('product_id', $item->product_id)
                ->first();

            $availableQty = $branchStock ? $branchStock->available_quantity : 0;
            $isAvailable = $availableQty >= $item->qty_ordered;

            $details[] = [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'product_sku' => $item->product_sku,
                'qty_ordered' => $item->qty_ordered,
                'stock_available' => $branchStock ? $branchStock->stock_quantity : 0,
                'reserved_quantity' => $branchStock ? $branchStock->reserved_quantity : 0,
                'available_quantity' => $availableQty,
                'is_available' => $isAvailable,
                'shortage' => $isAvailable ? 0 : ($item->qty_ordered - $availableQty),
            ];

            if (!$isAvailable) {
                $available = false;
            }
        }

        return [
            'available' => $available,
            'branch_id' => $branchId,
            'details' => $details,
        ];
    }

    /**
     * Reserve stock for a Sales DO
     * This is called when Sales DO is submitted from CRM to WQS
     * 
     * @param SalesDO $salesDO
     * @param int|null $branchId
     * @return bool
     * @throws Exception
     */
    public function reserveStock(SalesDO $salesDO, ?int $branchId = null): bool
    {
        $branchId = $branchId ?? auth()->user()->current_branch_id;

        DB::beginTransaction();
        try {
            // First check availability
            $checkResult = $this->checkStockAvailability($salesDO, $branchId);
            
            if (!$checkResult['available']) {
                $shortages = collect($checkResult['details'])
                    ->where('is_available', false)
                    ->map(fn($item) => "{$item['product_name']}: need {$item['shortage']} more")
                    ->implode(', ');
                
                throw new Exception("Insufficient stock for: {$shortages}");
            }

            // Reserve stock for each item
            foreach ($salesDO->items as $item) {
                // Update branch stock
                DB::table('branch_product_stock')
                    ->where('branch_id', $branchId)
                    ->where('product_id', $item->product_id)
                    ->update([
                        'reserved_quantity' => DB::raw('reserved_quantity + ' . $item->qty_ordered),
                        'available_quantity' => DB::raw('available_quantity - ' . $item->qty_ordered),
                        'updated_at' => now(),
                    ]);

                // Update product total
                DB::table('master_products')
                    ->where('id', $item->product_id)
                    ->update([
                        'reserved_quantity' => DB::raw('reserved_quantity + ' . $item->qty_ordered),
                        'available_quantity' => DB::raw('available_quantity - ' . $item->qty_ordered),
                        'updated_at' => now(),
                    ]);

                // Update item status
                $item->update(['item_status' => 'confirmed']);

                // Log adjustment
                $this->logInventoryAdjustment([
                    'branch_id' => $branchId,
                    'product_id' => $item->product_id,
                    'adjustment_type' => 'reserve',
                    'quantity' => $item->qty_ordered,
                    'reference_type' => 'SalesDO',
                    'reference_id' => $salesDO->id,
                    'notes' => "Reserved stock for DO: {$salesDO->do_code}",
                ]);
            }

            DB::commit();
            
            Log::info("Stock reserved for SalesDO", [
                'sales_do_id' => $salesDO->id,
                'do_code' => $salesDO->do_code,
                'branch_id' => $branchId,
            ]);

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to reserve stock", [
                'sales_do_id' => $salesDO->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Release reserved stock (when Sales DO is cancelled or rejected)
     * 
     * @param SalesDO $salesDO
     * @param int|null $branchId
     * @return bool
     */
    public function releaseReservedStock(SalesDO $salesDO, ?int $branchId = null): bool
    {
        $branchId = $branchId ?? auth()->user()->current_branch_id;

        DB::beginTransaction();
        try {
            foreach ($salesDO->items as $item) {
                if ($item->item_status === 'pending' || $item->item_status === 'cancelled') {
                    continue; // Already not reserved
                }

                // Release branch stock
                DB::table('branch_product_stock')
                    ->where('branch_id', $branchId)
                    ->where('product_id', $item->product_id)
                    ->update([
                        'reserved_quantity' => DB::raw('reserved_quantity - ' . $item->qty_ordered),
                        'available_quantity' => DB::raw('available_quantity + ' . $item->qty_ordered),
                        'updated_at' => now(),
                    ]);

                // Release product total
                DB::table('master_products')
                    ->where('id', $item->product_id)
                    ->update([
                        'reserved_quantity' => DB::raw('reserved_quantity - ' . $item->qty_ordered),
                        'available_quantity' => DB::raw('available_quantity + ' . $item->qty_ordered),
                        'updated_at' => now(),
                    ]);

                // Update item status
                $item->update(['item_status' => 'cancelled']);

                // Log adjustment
                $this->logInventoryAdjustment([
                    'branch_id' => $branchId,
                    'product_id' => $item->product_id,
                    'adjustment_type' => 'release',
                    'quantity' => $item->qty_ordered,
                    'reference_type' => 'SalesDO',
                    'reference_id' => $salesDO->id,
                    'notes' => "Released reserved stock for DO: {$salesDO->do_code}",
                ]);
            }

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to release reserved stock", [
                'sales_do_id' => $salesDO->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Deduct stock after delivery is completed
     * This moves stock from reserved to actually sold/delivered
     * 
     * @param SalesDO $salesDO
     * @param int|null $branchId
     * @return bool
     */
    public function deductStockAfterDelivery(SalesDO $salesDO, ?int $branchId = null): bool
    {
        $branchId = $branchId ?? auth()->user()->current_branch_id;

        DB::beginTransaction();
        try {
            foreach ($salesDO->items as $item) {
                // Update branch stock - remove from both reserved and total
                DB::table('branch_product_stock')
                    ->where('branch_id', $branchId)
                    ->where('product_id', $item->product_id)
                    ->update([
                        'stock_quantity' => DB::raw('stock_quantity - ' . $item->qty_delivered),
                        'reserved_quantity' => DB::raw('reserved_quantity - ' . $item->qty_delivered),
                        'updated_at' => now(),
                    ]);

                // Update product total
                DB::table('master_products')
                    ->where('id', $item->product_id)
                    ->update([
                        'stock_quantity' => DB::raw('stock_quantity - ' . $item->qty_delivered),
                        'reserved_quantity' => DB::raw('reserved_quantity - ' . $item->qty_delivered),
                        'updated_at' => now(),
                    ]);

                // Update item status
                $item->update(['item_status' => 'delivered']);

                // Log adjustment
                $this->logInventoryAdjustment([
                    'branch_id' => $branchId,
                    'product_id' => $item->product_id,
                    'adjustment_type' => 'sale',
                    'quantity' => -$item->qty_delivered,
                    'reference_type' => 'SalesDO',
                    'reference_id' => $salesDO->id,
                    'notes' => "Stock sold via DO: {$salesDO->do_code}",
                ]);
            }

            DB::commit();
            
            Log::info("Stock deducted after delivery", [
                'sales_do_id' => $salesDO->id,
                'do_code' => $salesDO->do_code,
                'branch_id' => $branchId,
            ]);

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to deduct stock", [
                'sales_do_id' => $salesDO->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Adjust inventory (manual adjustment by warehouse staff)
     * 
     * @param array $data
     * @return bool
     */
    public function adjustInventory(array $data): bool
    {
        DB::beginTransaction();
        try {
            $branchId = $data['branch_id'] ?? auth()->user()->current_branch_id;
            $productId = $data['product_id'];
            $quantity = $data['quantity']; // Can be positive (add) or negative (remove)
            $type = $data['adjustment_type']; // 'received', 'damaged', 'lost', 'returned', 'manual'

            // Get current stock
            $branchStock = DB::table('branch_product_stock')
                ->where('branch_id', $branchId)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            if (!$branchStock) {
                // Create new branch stock record if doesn't exist
                DB::table('branch_product_stock')->insert([
                    'branch_id' => $branchId,
                    'product_id' => $productId,
                    'stock_quantity' => max(0, $quantity),
                    'reserved_quantity' => 0,
                    'available_quantity' => max(0, $quantity),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // Update existing stock
                $newStockQty = $branchStock->stock_quantity + $quantity;
                $newAvailableQty = $newStockQty - $branchStock->reserved_quantity;

                if ($newStockQty < 0) {
                    throw new Exception("Adjustment would result in negative stock");
                }

                if ($newAvailableQty < 0) {
                    throw new Exception("Cannot reduce stock below reserved quantity");
                }

                DB::table('branch_product_stock')
                    ->where('branch_id', $branchId)
                    ->where('product_id', $productId)
                    ->update([
                        'stock_quantity' => $newStockQty,
                        'available_quantity' => $newAvailableQty,
                        'updated_at' => now(),
                    ]);
            }

            // Update product total
            DB::table('master_products')
                ->where('id', $productId)
                ->update([
                    'stock_quantity' => DB::raw("stock_quantity + {$quantity}"),
                    'available_quantity' => DB::raw("available_quantity + {$quantity}"),
                    'updated_at' => now(),
                ]);

            // Log adjustment
            $this->logInventoryAdjustment([
                'branch_id' => $branchId,
                'product_id' => $productId,
                'adjustment_type' => $type,
                'quantity' => $quantity,
                'reference_type' => $data['reference_type'] ?? 'Manual',
                'reference_id' => $data['reference_id'] ?? null,
                'notes' => $data['notes'] ?? "Manual inventory adjustment",
            ]);

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to adjust inventory", [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get stock level for a product at a specific branch
     */
    public function getStockLevel(int $productId, ?int $branchId = null): array
    {
        $branchId = $branchId ?? auth()->user()->current_branch_id;

        $branchStock = DB::table('branch_product_stock')
            ->where('branch_id', $branchId)
            ->where('product_id', $productId)
            ->first();

        $product = Product::find($productId);

        return [
            'product_id' => $productId,
            'branch_id' => $branchId,
            'stock_quantity' => $branchStock->stock_quantity ?? 0,
            'reserved_quantity' => $branchStock->reserved_quantity ?? 0,
            'available_quantity' => $branchStock->available_quantity ?? 0,
            'min_stock' => $branchStock->min_stock ?? $product->min_stock ?? 0,
            'max_stock' => $branchStock->max_stock ?? $product->max_stock ?? 0,
            'is_low_stock' => ($branchStock->available_quantity ?? 0) < ($branchStock->min_stock ?? $product->min_stock ?? 0),
        ];
    }

    /**
     * Log inventory adjustment for audit trail
     */
    protected function logInventoryAdjustment(array $data): void
    {
        InventoryAdjustment::create([
            'branch_id' => $data['branch_id'],
            'product_id' => $data['product_id'],
            'adjustment_type' => $data['adjustment_type'],
            'quantity' => $data['quantity'],
            'reference_type' => $data['reference_type'],
            'reference_id' => $data['reference_id'],
            'notes' => $data['notes'],
            'adjusted_by' => auth()->id(),
        ]);
    }
}
