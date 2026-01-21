<?php

namespace App\Http\Controllers\RegAlkes;

use App\Http\Controllers\Controller;
use App\Models\RegAlkesCase;
use App\Models\Product;
use App\Services\RegAlkesImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportSKUController extends Controller
{
    protected $importService;

    public function __construct(RegAlkesImportService $importService)
    {
        $this->importService = $importService;
    }

    public function showImportForm(RegAlkesCase $case)
    {
        if ($case->status !== 'nie_issued') {
            return redirect()->route('reg-alkes.cases.show', $case)
                ->with('error', 'Case is not ready for SKU import');
        }

        return view('pages.reg_alkes.import_sku.form', compact('case'));
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'case_id' => 'required|exists:reg_alkes_cases,id',
            'akl_excel' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $case = RegAlkesCase::findOrFail($validated['case_id']);

            // Import SKU from Excel
            $imported = $this->importService->importFromExcel(
                $request->file('akl_excel'),
                $case
            );

            // Update case status
            $case->update([
                'status' => 'sku_imported',
                'imported_sku_count' => $imported['count'],
            ]);

            DB::commit();

            return redirect()->route('reg-alkes.cases.show', $case)
                ->with('success', "Successfully imported {$imported['count']} SKU(s)");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to import SKU: ' . $e->getMessage());
        }
    }

    public function activateSKU(Request $request, RegAlkesCase $case)
    {
        if ($case->status !== 'sku_imported') {
            return back()->with('error', 'Case is not ready for SKU activation');
        }

        DB::beginTransaction();
        try {
            // Activate all products in this case
            $activated = $case->items()->update([
                'status' => 'active',
            ]);

            // Also activate in master products
            $productIds = $case->items()->pluck('product_id')->toArray();
            Product::whereIn('id', $productIds)->update(['status' => 'active']);

            // Update case status
            $case->update([
                'status' => 'sku_active',
                'activated_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', "Successfully activated {$activated} SKU(s)");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to activate SKU: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        // Generate Excel template for SKU import
        // Using maatwebsite/excel
        
        $headers = [
            'SKU Code',
            'Product Name',
            'AKL/AKD Number',
            'Registration Date',
            'Expiry Date',
            'Category',
            'Unit',
            'Unit Price',
        ];

        // Implementation depends on your Excel library
        // Return downloadable template
    }
}