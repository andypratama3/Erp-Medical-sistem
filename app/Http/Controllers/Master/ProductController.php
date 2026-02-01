<?php

namespace App\Http\Controllers\Master;

use App\Models\Product;
use App\Models\Category;
use App\Models\Manufacture;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use App\Services\AuditLogService;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function __construct(AuditLogService $auditLog)
    {
        $this->auditLog = $auditLog;
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'productGroup', 'manufacture']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate(15);

        /* ============================
        TABLE COLUMNS (KEY BASED)
        ============================ */
        $columns = [
            ['key' => 'code', 'label' => 'Code', 'type' => 'text'],
            ['key' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['key' => 'category', 'label' => 'Category', 'type' => 'text'],
            ['key' => 'manufacture', 'label' => 'Manufacture', 'type' => 'text'],
            ['key' => 'unit_price', 'label' => 'Unit Price', 'type' => 'text'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];

        /* ============================
        FORMAT DATA FOR TABLE
        ============================ */
        $productsData = $products->getCollection()->map(function ($product) {
            return [
                'id' => $product->id,
                'code' => $product->sku,
                'name' => $product->name,
                'category' => $product->category?->name ?? '-',
                'manufacture' => $product->manufacture?->name ?? '-',
                'unit_price' => 'Rp ' . number_format($product->unit_price, 0, ',', '.'),

                'status' => [
                    'value' => $product->status,
                    'label' => ucfirst($product->status),
                    'color' => match ($product->status) {
                        'active' => 'active',
                        'inactive' => 'inactive',
                        'discontinued' => 'warning',
                        default => 'gray',
                    }
                ],

                'actions' => [
                    'show' => route('master.products.show', $product),
                    'edit' => route('master.products.edit', $product),
                    'delete' => route('master.products.destroy', $product),
                ],
            ];
        })->toArray();

        $categories = Category::active()->get();
        $manufactures = Manufacture::active()->get();

        return view('pages.master.products.index', compact(
            'columns',
            'products',
            'productsData',
            'categories',
            'manufactures'
        ));
    }

    public function create()
    {
        $categories = Category::active()->get();
        $productGroups = ProductGroup::active()->get();
        $manufactures = Manufacture::active()->get();

        return view('pages.master.products.create', compact('productGroups', 'categories', 'manufactures'));
    }

    public function store(Request $request)
    {
       
        // ✅ FIXED: Validate file uploads properly
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'product_group_id' => 'required|exists:product_groups,id',
            'manufacture_id' => 'required|exists:master_manufactures,id',
            'sku' => 'required|unique:master_products|max:100',
            'name' => 'required|max:255',
            'description' => 'nullable',
            'unit' => 'required|max:50',
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'barcode' => 'nullable|max:100',
            'product_type' => 'required|in:medical_device,pharmaceutical,consumable,other',
            'is_taxable' => 'nullable|boolean',
            'is_importable' => 'nullable|boolean',
            'status' => 'required|in:active,inactive,discontinued',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,gif,webp|max:2048',
            'video' => 'nullable|mimes:mp4,mov,avi,quicktime|max:10240',
        ]);

        // Handle checkboxes - set to false if not present
        $validated['is_taxable'] = $request->has('is_taxable') ? true : false;
        $validated['is_importable'] = $request->has('is_importable') ? true : false;

        // Convert price from string format to numeric
        if (isset($validated['unit_price'])) {
            $validated['unit_price'] = (float) str_replace(['.', ','], ['', '.'], $validated['unit_price']);
        }

        if (isset($validated['cost_price'])) {
            $validated['cost_price'] = (float) str_replace(['.', ','], ['', '.'], $validated['cost_price']);
        }

        // Create product
        $product = Product::create($validated);

        // ✅ FIXED: Handle image uploads
        if ($request->hasFile('images') && is_array($request->file('images'))) {
            foreach ($request->file('images') as $image) {
                // Validate that it's actually a file
                if ($image->isValid()) {
                    $product
                        ->addMedia($image)
                        ->toMediaCollection('product_images');
                }
            }
        }

        // ✅ FIXED: Handle video upload
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            if ($video->isValid()) {
                $product
                    ->addMedia($video)
                    ->toMediaCollection('product_videos');
            }
        }

        $this->auditLog->logCreate('master', $product);

        return redirect()->route('master.products.index')
            ->with('success', 'Product created successfully');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'productGroup', 'manufacture']);
        return view('pages.master.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        $productGroups = ProductGroup::active()->get();
        $manufactures = Manufacture::active()->get();

        return view('pages.master.products.edit', compact('product', 'categories', 'productGroups', 'manufactures'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'product_group_id' => 'required|exists:product_groups,id',
            'manufacture_id' => 'required|exists:master_manufactures,id',
            'sku' => 'required|max:100|unique:master_products,sku,' . $product->id,
            'name' => 'required|max:255',
            'description' => 'nullable',
            'unit' => 'required|max:50',
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'barcode' => 'nullable|max:100',
            'product_type' => 'required|in:medical_device,pharmaceutical,consumable,other',
            'is_taxable' => 'nullable|boolean',
            'is_importable' => 'nullable|boolean',
            'status' => 'required|in:active,inactive,discontinued',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,gif,webp|max:2048',
            'video' => 'nullable|mimes:mp4,mov,avi,quicktime|max:10240',
        ]);

        // Handle checkboxes - set to false if not present
        $validated['is_taxable'] = $request->has('is_taxable') ? true : false;
        $validated['is_importable'] = $request->has('is_importable') ? true : false;

        // Convert price from string format to numeric
        if (isset($validated['unit_price'])) {
            $validated['unit_price'] = (float) str_replace(['.', ','], ['', '.'], $validated['unit_price']);
        }

        if (isset($validated['cost_price'])) {
            $validated['cost_price'] = (float) str_replace(['.', ','], ['', '.'], $validated['cost_price']);
        }

        $oldData = $product->toArray();

        $product->update($validated);

        // ✅ FIXED: Handle image uploads
        if ($request->hasFile('images') && is_array($request->file('images'))) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $product
                        ->addMedia($image)
                        ->toMediaCollection('product_images');
                }
            }
        }

        // ✅ FIXED: Handle video upload
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            if ($video->isValid()) {
                // Clear old video first
                $product->clearMediaCollection('product_videos');
                $product
                    ->addMedia($video)
                    ->toMediaCollection('product_videos');
            }
        }

        $this->auditLog->logUpdate('master', $product, $oldData);

        return redirect()->route('master.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return redirect()->route('master.products.index')
                ->with('success', 'Product deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('master.products.index')
                ->with('error', 'Failed to delete product. It may be in use.');
        }
    }

    public function import (Request $request)
    {
        // TODO: Implement import functionality
    }
}
