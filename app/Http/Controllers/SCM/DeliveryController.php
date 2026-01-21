<?php

namespace App\Http\Controllers\SCM;

use App\Http\Controllers\Controller;
use App\Models\SalesDO;
use App\Models\SCMDelivery;
use App\Models\SCMDriver;
use App\Services\DocumentUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    protected $documentService;

    public function __construct(DocumentUploadService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function index()
    {
        $deliveries = SCMDelivery::with(['salesDo.customer', 'driver'])
            ->latest()
            ->paginate(15);

        return view('pages.scm.deliveries.index', compact('deliveries'));
    }

    public function create(Request $request)
    {
        $salesDo = SalesDO::with(['customer', 'items.product'])
            ->findOrFail($request->do_id);

        if (!in_array($salesDo->status, ['scm_ready', 'scm_on_delivery'])) {
            return redirect()->route('scm.task-board')
                ->with('error', 'This DO is not ready for delivery');
        }

        $drivers = SCMDriver::active()->get();

        return view('pages.scm.deliveries.create', compact('salesDo', 'drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_do_id' => 'required|exists:sales_do,id',
            'driver_id' => 'required|exists:scm_drivers,id',
            'delivery_date' => 'required|date',
            'delivery_address' => 'required|string',
            'loading_photo' => 'nullable|image|max:5120',
            'delivery_proof' => 'nullable|image|max:5120',
            'received_by' => 'nullable|string',
            'received_at' => 'nullable|date',
            'delivery_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $salesDo = SalesDO::findOrFail($validated['sales_do_id']);

            // Create Delivery
            $delivery = SCMDelivery::create([
                'sales_do_id' => $salesDo->id,
                'driver_id' => $validated['driver_id'],
                'delivery_date' => $validated['delivery_date'],
                'delivery_address' => $validated['delivery_address'],
                'delivery_status' => 'on_delivery',
                'delivery_notes' => $validated['delivery_notes'] ?? null,
                'received_by' => $validated['received_by'] ?? null,
                'received_at' => $validated['received_at'] ?? null,
            ]);

            // Upload loading photo
            if ($request->hasFile('loading_photo')) {
                $this->documentService->upload(
                    $request->file('loading_photo'),
                    SCMDelivery::class,
                    $delivery->id,
                    'scm_loading_photo',
                    'Loading photo'
                );
            }

            // Upload delivery proof
            if ($request->hasFile('delivery_proof')) {
                $this->documentService->upload(
                    $request->file('delivery_proof'),
                    SCMDelivery::class,
                    $delivery->id,
                    'scm_delivery_proof',
                    'Delivery proof'
                );
            }

            // Update DO status
            $salesDo->update(['status' => 'scm_on_delivery']);

            // Update SCM task
            $salesDo->taskBoards()->where('module', 'scm')->update(['task_status' => 'in_progress']);

            DB::commit();

            return redirect()->route('scm.deliveries.show', $delivery)
                ->with('success', 'Delivery created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create delivery: ' . $e->getMessage())->withInput();
        }
    }

    public function show(SCMDelivery $delivery)
    {
        $delivery->load(['salesDo.customer', 'driver', 'documents']);
        return view('pages.scm.deliveries.show', compact('delivery'));
    }

    public function markAsDelivered(Request $request, SCMDelivery $delivery)
    {
        $validated = $request->validate([
            'received_by' => 'required|string',
            'received_at' => 'required|date',
            'signature' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Update delivery status
            $delivery->update([
                'delivery_status' => 'delivered',
                'received_by' => $validated['received_by'],
                'received_at' => $validated['received_at'],
            ]);

            // Upload signature
            if ($request->hasFile('signature')) {
                $this->documentService->upload(
                    $request->file('signature'),
                    SCMDelivery::class,
                    $delivery->id,
                    'scm_signature',
                    'Recipient signature'
                );
            }

            // Update DO status
            $delivery->salesDo->update(['status' => 'scm_delivered']);

            // Complete SCM task and create ACT task
            $delivery->salesDo->taskBoards()->where('module', 'scm')->update(['task_status' => 'completed']);
            
            $delivery->salesDo->taskBoards()->create([
                'module' => 'act',
                'task_status' => 'pending',
                'task_description' => 'Generate invoice for DO ' . $delivery->salesDo->do_number,
                'due_date' => now()->addDays(1),
            ]);

            DB::commit();

            return redirect()->route('scm.deliveries.show', $delivery)
                ->with('success', 'Delivery marked as delivered successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to mark as delivered: ' . $e->getMessage());
        }
    }
}
