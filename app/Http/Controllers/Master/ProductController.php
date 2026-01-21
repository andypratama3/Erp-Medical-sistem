<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductGroup;
use App\Models\Manufacture;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'productGroup', 'manufacture']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate(15);
        $categories = Category::active()->get();

        return view('pages.master.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        $manufactures = Manufacture::active()->get();

        return view('pages.master.products.create', compact('categories', 'manufactures'));
    }

    public function store(Request $request)
    {
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
            'is_taxable' => 'boolean',
            'is_importable' => 'boolean',
            'status' => 'required|in:active,inactive,discontinued',
        ]);

        Product::create($validated);

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
        $manufactures = Manufacture::active()->get();

        return view('pages.master.products.edit', compact('product', 'categories', 'manufactures'));
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
            'product_type' => 'required',
            'is_taxable' => 'boolean',
            'is_importable' => 'boolean',
            'status' => 'required|in:active,inactive,discontinued',
        ]);

        $product->update($validated);

        return redirect()->route('master.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('master.products.index')
            ->with('success', 'Product deleted successfully');
    }
}