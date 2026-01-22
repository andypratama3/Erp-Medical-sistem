<?php

namespace App\Http\Controllers\SCM;

use App\Http\Controllers\Controller;
use App\Models\SalesDO;
use App\Models\SCMDriver;
use App\Models\SCMDelivery;
use App\Services\DocumentUploadService;
use App\Services\AuditLogService;
use App\Services\StateMachineService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class TaskBoardController extends Controller implements HasMiddleware
{
    protected $documentUpload;
    protected $auditLog;
    protected $stateMachine;

    public function __construct(
        DocumentUploadService $documentUpload,
        AuditLogService $auditLog,
        StateMachineService $stateMachine
    ) {
        $this->documentUpload = $documentUpload;
        $this->auditLog = $auditLog;
        $this->stateMachine = $stateMachine;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_scm', only: ['index', 'show']),
            new Middleware('permission:process_scm', only: ['assignDriver', 'startDelivery', 'completeDelivery']),
        ];
    }

    /**
     * Display SCM Task Board
     */
    public function index(Request $request)
    {
        $query = SalesDO::with(['customer', 'office', 'items', 'delivery'])
            ->whereIn('status', ['wqs_ready', 'scm_on_delivery', 'scm_delivered']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('do_code', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by driver
        if ($request->filled('driver')) {
            $query->whereHas('delivery', function($q) use ($request) {
                $q->where('driver_id', $request->driver);
            });
        }

        $salesDOs = $query->latest('do_date')->paginate(15);

        // Get statistics
        $stats = [
            'ready' => SalesDO::where('status', 'wqs_ready')->count(),
            'on_delivery' => SalesDO::where('status', 'scm_on_delivery')->count(),
            'delivered' => SalesDO::where('status', 'scm_delivered')->count(),
        ];

        // Get drivers
        $drivers = SCMDriver::active()->get();

        return view('pages.scm.task_board.index', compact('salesDOs', 'stats', 'drivers'));
    }

    /**
     * Show detailed task for delivery
     */
    public function show(SalesDO $salesDo)
    {
        // Check if DO is in SCM stage
        if (!in_array($salesDo->status, ['wqs_ready', 'scm_on_delivery', 'scm_delivered'])) {
            return redirect()
                ->route('scm.task-board.index')
                ->with('error', 'This DO is not in SCM stage.');
        }

        $salesDo->load([
            'customer',
            'office',
            'items.product',
            'delivery.driver',
            'documents' => function($query) {
                $query->whereIn('stage', ['scm_loading', 'scm_delivery', 'scm_proof']);
            }
        ]);

        $drivers = SCMDriver::active()->get();

        return view('pages.scm.task_board.show', compact('salesDo', 'drivers'));
    }

    /**
     * Assign driver to DO
     */
    public function assignDriver(Request $request, SalesDO $salesDo)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:scm_drivers,id',
            'vehicle_plate' => 'required|string|max:20',
            'scheduled_date' => 'required|date',
            'route_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create or update delivery record
            $delivery = SCMDelivery::updateOrCreate(
                ['sales_do_id' => $salesDo->id],
                [
                    'driver_id' => $validated['driver_id'],
                    'vehicle_plate' => $validated['vehicle_plate'],
                    'scheduled_date' => $validated['scheduled_date'],
                    'route_notes' => $validated['route_notes'],
                    'assigned_by' => auth()->id(),
                ]
            );

            // Audit log
            $this->auditLog->log('SCM_DRIVER_ASSIGNED', 'SCM', [
                'do_code' => $salesDo->do_code,
                'driver_id' => $validated['driver_id'],
                'scheduled_date' => $validated['scheduled_date'],
            ]);

            DB::commit();

            return redirect()
                ->route('scm.task-board.show', $salesDo)
                ->with('success', 'Driver assigned successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to assign driver: ' . $e->getMessage());
        }
    }

    /**
     * Start delivery (change status to on_delivery)
     */
    public function startDelivery(Request $request, SalesDO $salesDo)
    {
        // Validate state transition
        if (!$this->stateMachine->canTransition('sales_do', $salesDo->status, 'scm_on_delivery')) {
            return redirect()
                ->back()
                ->with('error', 'Invalid status transition.');
        }

        // Check if driver assigned
        if (!$salesDo->delivery) {
            return redirect()
                ->back()
                ->with('error', 'Please assign driver first.');
        }

        $validated = $request->validate([
            'actual_departure' => 'required|date',
            'loading_photos.*' => 'nullable|image|max:2048',
            'notes_scm' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update delivery
            $salesDo->delivery->update([
                'actual_departure' => $validated['actual_departure'],
                'status' => 'on_delivery',
            ]);

            // Update DO status
            $salesDo->update([
                'status' => 'scm_on_delivery',
                'notes_scm' => $validated['notes_scm'],
                'updated_by' => auth()->id(),
            ]);

            // Upload loading photos
            if ($request->hasFile('loading_photos')) {
                foreach ($request->file('loading_photos') as $photo) {
                    $this->documentUpload->upload(
                        'sales_do',
                        $salesDo->do_code,
                        'scm_loading',
                        $photo
                    );
                }
            }

            // Audit log
            $this->auditLog->log('SCM_DELIVERY_STARTED', 'SCM', [
                'do_code' => $salesDo->do_code,
                'driver_id' => $salesDo->delivery->driver_id,
                'departure' => $validated['actual_departure'],
            ]);

            DB::commit();

            return redirect()
                ->route('scm.task-board.index')
                ->with('success', 'Delivery started successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to start delivery: ' . $e->getMessage());
        }
    }

    /**
     * Complete delivery with proof
     */
    public function completeDelivery(Request $request, SalesDO $salesDo)
    {
        // Validate state transition
        if (!$this->stateMachine->canTransition('sales_do', $salesDo->status, 'scm_delivered')) {
            return redirect()
                ->back()
                ->with('error', 'Invalid status transition.');
        }

        $validated = $request->validate([
            'actual_arrival' => 'required|date',
            'received_by' => 'required|string|max:100',
            'received_position' => 'nullable|string|max:100',
            'delivery_photos.*' => 'nullable|image|max:2048',
            'signature_data' => 'required|string', // Base64 signature
            'delivery_notes' => 'nullable|string',
            'gps_latitude' => 'nullable|numeric',
            'gps_longitude' => 'nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            // Update delivery
            $salesDo->delivery->update([
                'actual_arrival' => $validated['actual_arrival'],
                'received_by' => $validated['received_by'],
                'received_position' => $validated['received_position'],
                'delivery_notes' => $validated['delivery_notes'],
                'gps_latitude' => $validated['gps_latitude'],
                'gps_longitude' => $validated['gps_longitude'],
                'status' => 'delivered',
                'completed_at' => now(),
            ]);

            // Update DO status
            $salesDo->update([
                'status' => 'scm_delivered',
                'notes_scm' => ($salesDo->notes_scm ?? '') . "\n\nDelivery completed: " . now(),
                'updated_by' => auth()->id(),
            ]);

            // Upload delivery proof photos
            if ($request->hasFile('delivery_photos')) {
                foreach ($request->file('delivery_photos') as $photo) {
                    $this->documentUpload->upload(
                        'sales_do',
                        $salesDo->do_code,
                        'scm_proof',
                        $photo
                    );
                }
            }

            // Save signature
            if ($validated['signature_data']) {
                $signatureImage = $this->saveSignature(
                    $validated['signature_data'],
                    $salesDo->do_code
                );

                $salesDo->delivery->update([
                    'signature_path' => $signatureImage
                ]);
            }

            // Audit log
            $this->auditLog->log('SCM_DELIVERY_COMPLETED', 'SCM', [
                'do_code' => $salesDo->do_code,
                'received_by' => $validated['received_by'],
                'arrival' => $validated['actual_arrival'],
            ]);

            DB::commit();

            return redirect()
                ->route('scm.task-board.index')
                ->with('success', 'Delivery completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to complete delivery: ' . $e->getMessage());
        }
    }

    /**
     * Save signature as image
     */
    private function saveSignature($base64Data, $doCode)
    {
        // Remove data:image/png;base64, prefix
        $image = str_replace('data:image/png;base64,', '', $base64Data);
        $image = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);

        // Create filename
        $filename = 'signature_' . $doCode . '_' . time() . '.png';
        $path = 'documents/sales_do/signatures/' . $filename;

        // Save to storage
        \Storage::disk('local')->put($path, $imageData);

        return $path;
    }

    /**
     * Update delivery location (for real-time tracking)
     */
    public function updateLocation(Request $request, SalesDO $salesDo)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $salesDo->delivery->update([
            'current_latitude' => $validated['latitude'],
            'current_longitude' => $validated['longitude'],
            'last_location_update' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location updated.',
        ]);
    }
}
