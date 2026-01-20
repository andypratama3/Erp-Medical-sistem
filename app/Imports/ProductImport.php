<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Manufacture;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class ProductImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected $skipIfExists;

    public function __construct($skipIfExists = true)
    {
        $this->skipIfExists = $skipIfExists;
    }

    public function model(array $row)
    {
        // Skip if SKU already exists and skip mode is enabled
        if ($this->skipIfExists && Product::where('sku', $row['sku'])->exists()) {
            return null;
        }

        // Find or create manufacture by name
        $manufacture = null;
        if (!empty($row['manufacture'])) {
            $manufacture = Manufacture::firstOrCreate(
                ['name' => $row['manufacture']],
                [
                    'code' => strtoupper(substr($row['manufacture'], 0, 3)) . '-' . time(),
                    'status' => 'active'
                ]
            );
        }

        // Find or create category by name
        $category = null;
        if (!empty($row['category'])) {
            $category = Category::firstOrCreate(
                ['name' => $row['category']],
                [
                    'code' => strtoupper(substr($row['category'], 0, 3)) . '-' . time(),
                    'status' => 'active'
                ]
            );
        }

        return new Product([
            'sku' => $row['sku'],
            'name' => $row['products_name'],
            'type' => $row['type'] ?? 'SINGLE',
            'unit' => $row['unit'] ?? 'unit',
            'barcode' => $row['barcode'] ?? null,
            'manufacture_id' => $manufacture?->id,
            'category_id' => $category?->id,
            'stock_qty' => $row['stock_qty'] ?? 0,
            'current_stock' => $row['current_stock'] ?? 0,
            'akl_akd' => $row['akl_akd'] ?? null,
            'akl_reg_no' => $row['akl_reg_no'] ?? null,
            'expired_registration' => $row['expired_registration'] ?? null,
            'general_name' => $row['general_name'] ?? null,
            'licence_number' => $row['licence_number'] ?? null,
            'listing_level' => $row['listing_level'] ?? null,
            'status' => $row['status'] ?? 'inactive',
            'description' => $row['description'] ?? null,
            'price' => $row['price'] ?? null,
            'cost' => $row['cost'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'sku' => 'required|string',
            'products_name' => 'required|string',
            'type' => 'nullable|in:SINGLE,BUNDLE',
            'unit' => 'nullable|string',
            'stock_qty' => 'nullable|integer|min:0',
            'current_stock' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'sku.required' => 'SKU is required',
            'products_name.required' => 'Product name is required',
        ];
    }
}
