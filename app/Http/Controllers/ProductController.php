<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Manufacture;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_products', only: ['index', 'show']),
            new Middleware('permission:create_product', only: ['create', 'store', 'import', 'processImport', 'downloadTemplate']),
            new Middleware('permission:edit_product', only: ['edit', 'update', 'activate', 'deactivate']),
            new Middleware('permission:delete_product', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'manufacture', 'productGroup']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('akl_akd', 'like', "%{$search}%");
            });
        }

        // Filter by manufacture
        if ($request->filled('manufacture')) {
            $query->where('manufacture_id', $request->manufacture);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }


        $columns = [
            [
                'key' => 'sku',
                'label' => 'SKU',
                'type' => 'text',
            ],
            [
                'key' => 'name',
                'label' => 'Product Name',
                'type' => 'text',
            ],
            [
                'key' => 'type',
                'label' => 'Type',
                'type' => 'badge',
            ],
            [
                'key' => 'unit',
                'label' => 'Unit',
                'type' => 'text',
            ],
            [
                'key' => 'barcode',
                'label' => 'Barcode',
                'type' => 'text',
            ],
            [
                'key' => 'manufacture.name',
                'label' => 'Manufacture',
                'type' => 'text',
            ],
            [
                'key' => 'akl_akd',
                'label' => 'AKL/AKD',
                'type' => 'text',
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'badge',
                'options' => [
                    'active' => 'success',
                    'inactive' => 'danger',
                ],
            ],

        ];


        $products = $query->latest()->paginate(10);

        $manufactures = Manufacture::active()->get();
        $categories = Category::active()->get();

        return view('products.index', compact('products', 'columns', 'manufactures', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        $manufactures = Manufacture::active()->get();
        $productGroups = ProductGroup::active()->get();

        return view('products.create', compact('categories', 'manufactures', 'productGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:products,sku',
            'name' => 'required|string|max:255',
            'type' => 'required|in:SINGLE,BUNDLE',
            'unit' => 'required|string|max:50',
            'barcode' => 'nullable|string|max:255',
            'manufacture_id' => 'nullable|exists:manufactures,id',
            'category_id' => 'nullable|exists:categories,id',
            'product_group_id' => 'nullable|exists:product_groups,id',
            'stock_qty' => 'required|integer|min:0',
            'current_stock' => 'required|integer|min:0',
            'akl_akd' => 'nullable|string|max:255',
            'akl_reg_no' => 'nullable|string|max:255',
            'expired_registration' => 'nullable|date',
            'general_name' => 'nullable|string|max:255',
            'licence_number' => 'nullable|string|max:255',
            'listing_level' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'videos.*' => 'nullable|mimes:mp4,avi,mov|max:10240',
        ]);

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            $photos = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('products/photos', 'public');
                $photos[] = $path;
            }
            $validated['photos'] = $photos;
        }

        // Handle video uploads
        if ($request->hasFile('videos')) {
            $videos = [];
            foreach ($request->file('videos') as $video) {
                $path = $video->store('products/videos', 'public');
                $videos[] = $path;
            }
            $validated['videos'] = $videos;
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'manufacture', 'productGroup']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        $manufactures = Manufacture::active()->get();
        $productGroups = ProductGroup::active()->get();

        return view('products.edit', compact('product', 'categories', 'manufactures', 'productGroups'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:SINGLE,BUNDLE',
            'unit' => 'required|string|max:50',
            'barcode' => 'nullable|string|max:255',
            'manufacture_id' => 'nullable|exists:manufactures,id',
            'category_id' => 'nullable|exists:categories,id',
            'product_group_id' => 'nullable|exists:product_groups,id',
            'stock_qty' => 'required|integer|min:0',
            'current_stock' => 'required|integer|min:0',
            'akl_akd' => 'nullable|string|max:255',
            'akl_reg_no' => 'nullable|string|max:255',
            'expired_registration' => 'nullable|date',
            'general_name' => 'nullable|string|max:255',
            'licence_number' => 'nullable|string|max:255',
            'listing_level' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'videos.*' => 'nullable|mimes:mp4,avi,mov|max:10240',
        ]);

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            // Delete old photos
            if ($product->photos) {
                foreach ($product->photos as $oldPhoto) {
                    Storage::disk('public')->delete($oldPhoto);
                }
            }

            $photos = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('products/photos', 'public');
                $photos[] = $path;
            }
            $validated['photos'] = $photos;
        }

        // Handle video uploads
        if ($request->hasFile('videos')) {
            // Delete old videos
            if ($product->videos) {
                foreach ($product->videos as $oldVideo) {
                    Storage::disk('public')->delete($oldVideo);
                }
            }

            $videos = [];
            foreach ($request->file('videos') as $video) {
                $path = $video->store('products/videos', 'public');
                $videos[] = $path;
            }
            $validated['videos'] = $videos;
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Delete associated files
        if ($product->photos) {
            foreach ($product->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        if ($product->videos) {
            foreach ($product->videos as $video) {
                Storage::disk('public')->delete($video);
            }
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function activate(Product $product)
    {
        $product->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Product activated successfully.');
    }

    public function deactivate(Product $product)
    {
        $product->update(['status' => 'inactive']);

        return redirect()->back()->with('success', 'Product deactivated successfully.');
    }

    public function import()
    {
        $manufactures = Manufacture::active()->get();
        $categories = Category::active()->get();

        return view('products.import', compact('manufactures', 'categories'));
    }

    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'skip_existing' => 'nullable|boolean',
        ]);

        $skipExisting = $request->boolean('skip_existing', true);

        try {
            $import = new ProductImport($skipExisting);
            Excel::import($import, $request->file('file'));

            $failures = $import->failures();

            if ($failures->isNotEmpty()) {
                return redirect()->back()->with('warning', 'Import completed with some errors. Please check the file format.');
            }

            return redirect()->route('products.index')->with('success', 'Products imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'sku',
            'products_name',
            'type',
            'unit',
            'barcode',
            'manufacture',
            'category',
            'stock_qty',
            'current_stock',
            'akl_akd',
            'akl_reg_no',
            'expired_registration',
            'general_name',
            'licence_number',
            'listing_level',
            'status',
            'description',
            'price',
            'cost'
        ];

        $filename = 'product_import_template.csv';
        $handle = fopen('php://temp', 'w');
        fputcsv($handle, $headers);
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
