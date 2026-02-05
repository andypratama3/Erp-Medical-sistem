<?php

namespace App\Http\Controllers\WQS;

use App\Http\Controllers\Controller;
use App\Models\{Product, InventoryAdjustment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $inventory = InventoryAdjustment::with('product')
            ->when($request->search, fn($q, $search) =>
                $q->where('product.name', 'like', "%{$search}%")
                  ->orWhere('product.sku', 'like', "%{$search}%")
            )
            ->paginate(20);

        return view('pages.wqs.inventory.index', compact('inventory'));
    }

    public function adjustments(Request $request)
    {
        $adjustments = InventoryAdjustment::with(['product', 'createdBy'])
            ->latest()
            ->paginate(20);

        return view('pages.wqs.inventory.adjustments', compact('adjustments'));
    }

    public function storeAdjustment(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'adjustment_type' => 'required|in:increase,decrease,correction',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::findOrFail($validated['product_id']);

            // Create adjustment record
            $adjustment = InventoryAdjustment::create([
                'product_id' => $product->id,
                'adjustment_type' => $validated['adjustment_type'],
                'quantity' => $validated['quantity'],
                'old_quantity' => $product->stock_quantity,
                'new_quantity' => $this->calculateNewQuantity($product, $validated),
                'reason' => $validated['reason'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
                'branch_id' => auth()->user()->current_branch_id,
            ]);

            // Update product stock
            $product->update(['stock_quantity' => $adjustment->new_quantity]);

            DB::commit();

            return back()->with('success', 'Inventory adjusted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to adjust inventory: ' . $e->getMessage());
        }
    }

    private function calculateNewQuantity(Product $product, array $data): int
    {
        switch ($data['adjustment_type']) {
            case 'increase':
                return $product->stock_quantity + $data['quantity'];
            case 'decrease':
                return max(0, $product->stock_quantity - $data['quantity']);
            case 'correction':
                return $data['quantity'];
            default:
                return $product->stock_quantity;
        }
    }
}
