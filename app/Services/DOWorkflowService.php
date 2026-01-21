<?php

namespace App\Services;

use App\Models\RegAlkesCase;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class RegAlkesImportService
{
    public function importFromExcel(UploadedFile $file, RegAlkesCase $case): array
    {
        // Parse Excel file
        $data = Excel::toArray([], $file)[0];

        // Skip header row
        array_shift($data);

        $imported = 0;

        foreach ($data as $row) {
            if (empty($row[0])) continue; // Skip empty rows

            // Create or update product
            $product = Product::updateOrCreate(
                ['sku' => $row[0]],
                [
                    'name' => $row[1],
                    'manufacture_id' => $case->manufacture_id,
                    'unit' => $row[6] ?? 'PCS',
                    'unit_price' => $row[7] ?? 0,
                    'status' => 'inactive', // Will be activated later
                ]
            );

            // Create case item
            $case->items()->create([
                'product_id' => $product->id,
                'akl_number' => $row[2] ?? null,
                'registration_date' => $row[3] ?? null,
                'expiry_date' => $row[4] ?? null,
                'status' => 'pending',
            ]);

            $imported++;
        }

        return ['count' => $imported];
    }
}
