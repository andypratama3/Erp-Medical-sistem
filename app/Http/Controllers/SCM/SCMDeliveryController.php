<?php

namespace App\Http\Controllers\SCM;

use App\Models\SalesDO;
use App\Models\SCMDriver;
use App\StatusBadgeHelper;
use App\Models\SCMDelivery;
use Illuminate\Http\Request;
use App\Services\AuditLogService;
use App\Http\Controllers\Controller;

class SCMDeliveryController extends Controller
{
    protected $auditLog;

    public function __construct(AuditLogService $auditLog)
    {
        $this->auditLog = $auditLog;
    }

    public function index(Request $request)
    {
        $query = SCMDelivery::with(['salesDO.customer', 'driver', 'branch']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                  ->orWhereHas('salesDO', function($q) use ($search) {
                      $q->where('do_code', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('delivery_status')) {
            $query->where('delivery_status', $request->delivery_status);
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('delivery_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('delivery_date', '<=', $request->date_to);
        }

        $deliveries = $query->latest()->paginate(15);

        $columns = [
            ['key' => 'tracking_number', 'label' => 'Tracking #', 'type' => 'text'],
            ['key' => 'do_code', 'label' => 'DO Code', 'type' => 'text'],
            ['key' => 'customer', 'label' => 'Customer', 'type' => 'text'],
            ['key' => 'driver', 'label' => 'Driver', 'type' => 'text'],
            ['key' => 'delivery_date', 'label' => 'Delivery Date', 'type' => 'date'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];

        $deliveriesData = $deliveries->getCollection()->map(function ($delivery) {
            return [
                'id' => $delivery->id,
                'tracking_number' => $delivery->tracking_number ?? '-',
                'do_code' => $delivery->salesDO->do_code ?? '-',
                'customer' => $delivery->salesDO->customer->name ?? '-',
                'driver' => $delivery->driver->name ?? '-',
                'delivery_date' => $delivery->delivery_date ? $delivery->delivery_date->format('d M Y') : '-',
                'status' => $delivery->status_badge,
                'actions' => [
                    'show' => route('scm.deliveries.show', $delivery),
                    'edit' => route('scm.deliveries.edit', $delivery),
                    'delete' => route('scm.deliveries.destroy', $delivery),
                ],
            ];
        })->toArray();

        // $statusKey = match($delivery->delivery_status) {
        //     'pending'   => 'light',
        //     'scheduled' => 'warning',
        //     'on_route'  => 'scm_on_delivery',
        //     'delivered' => 'scm_delivered',
        //     'failed'    => 'error',
        //     'cancelled' => 'inactive',
        //     default     => 'light',
        // };
        // $badge = StatusBadgeHelper::getStatusConfigByKey($statusKey);

        // $statusLabel = match($delivery->delivery_status) {
        //     'pending'   => 'Pending',
        //     'scheduled' => 'Scheduled',
        //     'on_route'  => 'On Route',
        //     'delivered' => 'Delivered',
        //     'failed'    => 'Failed',
        //     'cancelled' => 'Cancelled',
        //     default     => ucfirst($delivery->delivery_status ?? '-'),
        // };

        $drivers = SCMDriver::active()->get();
        $salesDOs = SalesDO::whereIn('status', ['wqs_quality_ok', 'scm_picking'])->get();

        return view('pages.scm.deliveries.index', compact(
            'columns',
            'deliveries',
            'deliveriesData',
            'drivers',
            'salesDOs',
            // 'badge',
        ));
    }

    public function create()
    {
        $drivers = SCMDriver::active()->get();
        $salesDOs = SalesDO::whereIn('status', ['wqs_quality_ok', 'scm_picking'])->get();

        return view('pages.scm.deliveries.create', compact('drivers', 'salesDOs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_do_id' => 'required|exists:sales_do,id',
            'driver_id' => 'required|exists:scm_drivers,id',
            'delivery_date' => 'required|date',
            'shipping_address' => 'required|string',
            'tracking_number' => 'nullable|string|max:100',
            'delivery_status' => 'required|in:pending,scheduled,on_route,delivered,failed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $delivery = SCMDelivery::create($validated);

        // Update Sales DO status
        $delivery->salesDO->update(['status' => 'scm_shipping']);

        // Audit Log
        $this->auditLog->logCreate('scm', $delivery, "Created delivery for DO: {$delivery->salesDO->do_code}");

        return redirect()->route('scm.deliveries.index')
            ->with('success', 'Delivery created successfully.');
    }

    public function show(SCMDelivery $delivery)
    {
        $delivery->load(['salesDO.customer', 'driver', 'branch']);

        $this->auditLog->logView('scm', $delivery);

        return view('pages.scm.deliveries.show', compact('delivery'));
    }

    public function edit(SCMDelivery $delivery)
    {
        $drivers = SCMDriver::active()->get();
        $salesDOs = SalesDO::whereIn('status', ['wqs_quality_ok', 'scm_picking'])->get();

        return view('pages.scm.deliveries.edit', compact('delivery', 'drivers', 'salesDOs'));
    }

    public function update(Request $request, SCMDelivery $delivery)
    {
        $validated = $request->validate([
            'sales_do_id' => 'required|exists:sales_do,id',
            'driver_id' => 'required|exists:scm_drivers,id',
            'delivery_date' => 'required|date',
            'shipping_address' => 'required|string',
            'tracking_number' => 'nullable|string|max:100',
            'delivery_status' => 'required|in:pending,scheduled,on_route,delivered,failed,cancelled',
            'departure_time' => 'nullable|date',
            'arrival_time' => 'nullable|date',
            'receiver_name' => 'nullable|string|max:100',
            'receiver_position' => 'nullable|string|max:100',
            'received_at' => 'nullable|date',
            'delivery_notes' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $originalData = $delivery->toArray();
        $delivery->update($validated);

        // Update Sales DO status if delivered
        if ($validated['delivery_status'] === 'delivered') {
            $delivery->salesDO->update(['status' => 'scm_delivered']);
        }

        $this->auditLog->logUpdate('scm', $delivery, $originalData, "Updated delivery for DO: {$delivery->salesDO->do_code}");

        return redirect()->route('scm.deliveries.index')
            ->with('success', 'Delivery updated successfully.');
    }

    public function destroy(SCMDelivery $delivery)
    {
        $doCode = $delivery->salesDO->do_code;

        $this->auditLog->logDelete('scm', $delivery, "Deleted delivery for DO: {$doCode}");

        $delivery->delete();

        return redirect()->route('scm.deliveries.index')
            ->with('success', 'Delivery deleted successfully.');
    }

    public function dispatch(Request $request, SCMDelivery $delivery)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        // Ensure driver is assigned
        if (!$delivery->driver_id) {
            return redirect()
                ->back()
                ->with('error', 'Please assign a driver before dispatching.');
        }

        DB::beginTransaction();
        try {
            // Update delivery status
            $delivery->update([
                'delivery_status' => 'on_route',
                'dispatched_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update Sales DO status
            $delivery->salesDO->update([
                'status' => 'scm_on_delivery',
            ]);

            // Audit log
            $this->auditLog->log('DELIVERY_DISPATCHED', 'SCM', [
                'delivery_id' => $delivery->id,
                'do_code' => $delivery->salesDO->do_code,
                'driver' => $delivery->driver->name,
            ]);

            // *** ADD THIS: Dispatch DeliveryDispatched event ***
            event(new DeliveryDispatched($delivery));

            DB::commit();

            return redirect()
                ->route('scm.deliveries.show', $delivery)
                ->with('success', 'Delivery dispatched successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to dispatch delivery: ' . $e->getMessage());
        }
    }

    /**
     * Mark delivery as departed
     */
    public function markDeparted(SCMDelivery $delivery)
    {
        $delivery->update([
            'delivery_status' => 'on_route',
            'departure_time' => now(),
        ]);

        $this->auditLog->logAction('scm', 'depart', $delivery, "Delivery departed for DO: {$delivery->salesDO->do_code}");

        return redirect()->back()->with('success', 'Delivery marked as departed.');
    }

    /**
     * Mark delivery as delivered
     */
    public function markDelivered(Request $request, SCMDelivery $delivery)
    {
        $validated = $request->validate([
            'delivery_notes' => 'nullable|string',
            'delivered_at' => 'nullable|date',
        ]);

        // Ensure POD is uploaded
        if (!$delivery->pod_file) {
            return redirect()
                ->back()
                ->with('error', 'Please upload Proof of Delivery (POD) first.');
        }

        DB::beginTransaction();
        try {
            // Update delivery status
            $delivery->update([
                'delivery_status' => 'delivered',
                'delivered_at' => $validated['delivered_at'] ?? now(),
                'delivery_notes' => $validated['delivery_notes'] ?? null,
            ]);

            // Update Sales DO status
            $delivery->salesDO->update([
                'status' => 'scm_delivered',
            ]);

            // Audit log
            $this->auditLog->log('DELIVERY_COMPLETED', 'SCM', [
                'delivery_id' => $delivery->id,
                'do_code' => $delivery->salesDO->do_code,
                'delivered_at' => $delivery->delivered_at,
            ]);

            // *** ADD THIS: Dispatch DeliveryCompleted event ***
            event(new DeliveryCompleted($delivery));

            DB::commit();

            return redirect()
                ->route('scm.deliveries.show', $delivery)
                ->with('success', 'Delivery marked as completed.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to mark delivery: ' . $e->getMessage());
        }
    }
    
    public function uploadPOD(Request $request, SCMDelivery $delivery)
    {
        $validated = $request->validate([
            'pod_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ]);

        try {
            if ($request->hasFile('pod_file')) {
                $file = $request->file('pod_file');
                $filename = 'POD_' . $delivery->tracking_number . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('pod', $filename, 'public');

                $delivery->update([
                    'pod_file' => $path,
                    'pod_uploaded_at' => now(),
                ]);

                $this->auditLog->log('POD_UPLOADED', 'SCM', [
                    'delivery_id' => $delivery->id,
                    'pod_file' => $filename,
                ]);

                return redirect()
                    ->back()
                    ->with('success', 'POD uploaded successfully.');
            }

            return redirect()
                ->back()
                ->with('error', 'No file uploaded.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to upload POD: ' . $e->getMessage());
        }
    }
}
