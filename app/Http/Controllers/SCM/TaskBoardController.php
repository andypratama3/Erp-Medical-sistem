<?php

namespace App\Http\Controllers\SCM;

use App\Http\Controllers\Controller;
use App\Models\SalesDO;
use App\Models\SCMDriver;
use App\Models\SCMDelivery;
use App\Services\DocumentUploadService;
use App\Services\StateMachineService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class TaskBoardController extends Controller implements HasMiddleware
{
    protected $documentUpload;
    protected $stateMachine;
    protected $auditLog;

    public function __construct(
        DocumentUploadService $documentUpload,
        StateMachineService $stateMachine,
        AuditLogService $auditLog
    ) {
        $this->documentUpload = $documentUpload;
        $this->stateMachine = $stateMachine;
        $this->auditLog = $auditLog;
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
        $query = SalesDO::with(['customer', 'office', 'scmDelivery.driver'])
            ->whereIn('status', ['wqs_ready', 'scm_on_delivery']);

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
            $query->whereHas('scmDelivery', function($q) use ($request) {
                $q->where('driver_id', $request->driver);
            });
        }

        $salesDOs = $query->latest('do_date')->paginate(15);

        // Get statistics
        $stats = [
            'ready' => SalesDO::where('status', 'wqs_ready')->count(),
            'on_delivery' => SalesDO::where('status', 'scm_on_delivery')->count(),
            'delivered_today' => SalesDO::where('status', 'scm_delivered')
                ->whereDate('updated_at', today())
                ->count(),
        ];

        // Get available drivers
        $drivers = SCMDriver::active()->get();

        return view('pages.scm.task_board.index', compact('salesDOs', 'stats', 'drivers'));
    }

    /**
     * Show detailed delivery task
     */
    public function show(SalesDO $salesDo)
    {
        // Check if DO is in SCM stage
        if (!in_array($salesDo->status, ['wqs_ready', 'scm_on_delivery'])) {
            return redirect()
                ->route('scm.task-board.index')
                ->with('error', 'This DO is not in SCM stage.');
        }

        $salesDo->load([
            'customer',
            'office',
            'items.product',
            'scmDelivery.driver',
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
            'vehicle_number' => 'required|string|max:20',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'route_notes' => 'nullable|string',
        ]);

        // Check if already has delivery
        if ($salesDo->scmDelivery) {
            return redirect()
                ->back()
                ->with('error', 'Driver already assigned to this DO.');
        }

        DB::beginTransaction();
        try {
            // Create delivery record
            SCMDelivery::create([
                'sales_do_id' => $salesDo->id,
                'driver_id' => $validated['driver_id'],
                'vehicle_number' => $validated['vehicle_number'],
                'scheduled_date' => $validated['scheduled_date'],
                'scheduled_time' => $validated['scheduled_time'],
                'route_notes' => $validated['route_notes'],
                'status' => 'assigned',
            ]);

            // Update DO notes
            $driver = SCMDriver::find($validated['driver_id']);
            $salesDo->update([
                'notes_scm' => "Driver assigned: {$driver->name} - {$validated['vehicle_number']}",
                'updated_by' => auth()->id(),
            ]);

            // Audit log
            $this->auditLog->log('SCM_DRIVER_ASSIGNED', 'SCM', [
                'do_code' => $salesDo->do_code,
                'driver_id' => $validated['driver_id'],
                'vehicle' => $validated['vehicle_number'],
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
        $validated = $request->validate([
            'photos_loading.*' => 'nullable|image|max:2048',
            'departure_time' => 'nullable|date_format:H:i',
        ]);

        // Validate state transition
        if (!$this->stateMachine->canTransition('sales_do', $salesDo->status, 'scm_on_delivery')) {
            return redirect()
                ->back()
                ->with('error', 'Invalid status transition.');
        }

        // Check if driver assigned
        if (!$salesDo->scmDelivery) {
            return redirect()
                ->back()
                ->with('error', 'Please assign driver first.');
        }

        DB::beginTransaction();
        try {
            // Upload loading photos
            if ($request->hasFile('photos_loading')) {
                foreach ($request->file('photos_loading') as $photo) {
                    $this->documentUpload->upload(
                        'sales_do',
                        $salesDo->do_code,
                        'scm_loading',
                        $photo
                    );
                }
            }

            // Update delivery record
            $salesDo->scmDelivery->update([
                'actual_departure_time' => now(),
                'status' => 'on_delivery',
            ]);

            // Update DO status
            $salesDo->update([
                'status' => 'scm_on_delivery',
                'updated_by' => auth()->id(),
            ]);

            // Audit log
            $this->auditLog->log('SCM_DELIVERY_STARTED', 'SCM', [
                'do_code' => $salesDo->do_code,
            ]);

            DB::commit();

            return redirect()
                ->route('scm.task-board.show', $salesDo)
                ->with('success', 'Delivery started. Safe journey!');

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
        $validated = $request->validate([
            'photos_proof.*' => 'required|image|max:2048',
            'signature' => 'required|string', // base64 encoded signature
            'recipient_name' => 'required|string|max:100',
            'recipient_phone' => 'nullable|string|max:20',
            'delivery_notes' => 'nullable|string',
            'arrival_time' => 'nullable|date_format:H:i',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // Validate state transition
        if (!$this->stateMachine->canTransition('sales_do', $salesDo->status, 'scm_delivered')) {
            return redirect()
                ->back()
                ->with('error', 'Invalid status transition.');
        }

        DB::beginTransaction();
        try {
            // Upload proof photos
            if ($request->hasFile('photos_proof')) {
                foreach ($request->file('photos_proof') as $photo) {
                    $this->documentUpload->upload(
                        'sales_do',
                        $salesDo->do_code,
                        'scm_proof',
                        $photo
                    );
                }
            }

            // Save signature as image
            $signatureData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $validated['signature']));
            $signaturePath = 'signatures/' . $salesDo->do_code . '_' . time() . '.png';
            \Storage::disk('local')->put($signaturePath, $signatureData);

            // Update delivery record
            $salesDo->scmDelivery->update([
                'actual_arrival_time' => now(),
                'recipient_name' => $validated['recipient_name'],
                'recipient_phone' => $validated['recipient_phone'],
                'signature_path' => $signaturePath,
                'delivery_notes' => $validated['delivery_notes'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'status' => 'delivered',
                'completed_at' => now(),
            ]);

            // Update DO status
            $salesDo->update([
                'status' => 'scm_delivered',
                'notes_scm' => ($salesDo->notes_scm ?? '') . "\n\nDelivered to: {$validated['recipient_name']}",
                'updated_by' => auth()->id(),
            ]);

            // Audit log
            $this->auditLog->log('SCM_DELIVERY_COMPLETED', 'SCM', [
                'do_code' => $salesDo->do_code,
                'recipient' => $validated['recipient_name'],
            ]);

            DB::commit();

            return redirect()
                ->route('scm.task-board.index')
                ->with('success', 'Delivery completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to complete delivery: ' . $e->getMessage());
        }
    }

    /**
     * View delivery tracking
     */
    public function tracking(SalesDO $salesDo)
    {
        $salesDo->load(['scmDelivery.driver', 'customer', 'office']);

        return view('pages.scm.tracking', compact('salesDo'));
    }
}
