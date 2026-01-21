<?php

namespace App\Http\Controllers\RegAlkes;

use App\Http\Controllers\Controller;
use App\Models\RegAlkesCase;
use App\Models\Manufacture;
use App\Services\DocumentUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaseController extends Controller
{
    protected $documentService;

    public function __construct(DocumentUploadService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function index()
    {
        $cases = RegAlkesCase::with(['manufacture'])
            ->latest()
            ->paginate(15);

        return view('pages.reg_alkes.cases.index', compact('cases'));
    }

    public function create()
    {
        $manufactures = Manufacture::active()->get();
        return view('pages.reg_alkes.cases.create', compact('manufactures'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'manufacture_id' => 'required|exists:master_manufactures,id',
            'registration_type' => 'required|in:new,renewal,variation',
            'case_title' => 'required|string|max:255',
            'total_sku' => 'required|integer|min:1',
            'catalog_pdf' => 'required|file|mimes:pdf|max:20480',
            'scope_excel' => 'nullable|file|mimes:xlsx,xls|max:10240',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate case number
            $caseNumber = 'PQP/' . date('Y/m') . '/' . str_pad(RegAlkesCase::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT);

            // Create Case
            $case = RegAlkesCase::create([
                'case_number' => $caseNumber,
                'manufacture_id' => $validated['manufacture_id'],
                'registration_type' => $validated['registration_type'],
                'case_title' => $validated['case_title'],
                'total_sku' => $validated['total_sku'],
                'status' => 'case_draft',
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Upload catalog PDF
            $this->documentService->upload(
                $request->file('catalog_pdf'),
                RegAlkesCase::class,
                $case->id,
                'reg_alkes_catalog',
                'Product catalog'
            );

            // Upload scope Excel if provided
            if ($request->hasFile('scope_excel')) {
                $this->documentService->upload(
                    $request->file('scope_excel'),
                    RegAlkesCase::class,
                    $case->id,
                    'reg_alkes_scope',
                    'Product scope'
                );
            }

            DB::commit();

            return redirect()->route('reg-alkes.cases.show', $case)
                ->with('success', 'Case created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create case: ' . $e->getMessage())->withInput();
        }
    }

    public function show(RegAlkesCase $case)
    {
        $case->load(['manufacture', 'items.product', 'documents']);
        return view('pages.reg_alkes.cases.show', compact('case'));
    }

    public function edit(RegAlkesCase $case)
    {
        if (!in_array($case->status, ['case_draft', 'case_submitted'])) {
            return redirect()->route('reg-alkes.cases.show', $case)
                ->with('error', 'Cannot edit case in current status');
        }

        $manufactures = Manufacture::active()->get();
        return view('pages.reg_alkes.cases.edit', compact('case', 'manufactures'));
    }

    public function update(Request $request, RegAlkesCase $case)
    {
        $validated = $request->validate([
            'manufacture_id' => 'required|exists:master_manufactures,id',
            'registration_type' => 'required|in:new,renewal,variation',
            'case_title' => 'required|string|max:255',
            'total_sku' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $case->update($validated);

        return redirect()->route('reg-alkes.cases.show', $case)
            ->with('success', 'Case updated successfully');
    }

    public function submit(RegAlkesCase $case)
    {
        if ($case->status !== 'case_draft') {
            return back()->with('error', 'Case cannot be submitted from current status');
        }

        $case->update([
            'status' => 'case_submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('reg-alkes.cases.show', $case)
            ->with('success', 'Case submitted successfully');
    }

    public function uploadNIE(Request $request, RegAlkesCase $case)
    {
        $validated = $request->validate([
            'nie_number' => 'required|string',
            'nie_date' => 'required|date',
            'nie_document' => 'required|file|mimes:pdf|max:10240',
        ]);

        DB::beginTransaction();
        try {
            // Upload NIE document
            $this->documentService->upload(
                $request->file('nie_document'),
                RegAlkesCase::class,
                $case->id,
                'reg_alkes_nie',
                'NIE Document ' . $validated['nie_number']
            );

            // Update case
            $case->update([
                'nie_number' => $validated['nie_number'],
                'nie_date' => $validated['nie_date'],
                'status' => 'nie_issued',
            ]);

            DB::commit();

            return back()->with('success', 'NIE document uploaded successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to upload NIE: ' . $e->getMessage());
        }
    }

    public function uploadAKL(Request $request, RegAlkesCase $case)
    {
        $validated = $request->validate([
            'akl_document' => 'required|file|mimes:pdf|max:10240',
        ]);

        $this->documentService->upload(
            $request->file('akl_document'),
            RegAlkesCase::class,
            $case->id,
            'reg_alkes_akl',
            'AKL/AKD Certificate'
        );

        return back()->with('success', 'AKL/AKD document uploaded successfully');
    }
}