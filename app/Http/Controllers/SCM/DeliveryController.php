<?php

namespace App\Http\Controllers\SCM;

use App\Http\Controllers\Controller;
use App\Models\SCMDelivery;
use App\Models\SCMDriver;
use App\Models\SalesDO;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

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
                'delivery_date' => $delivery->delivery_date->format('d M Y'),
                'status' => $delivery->status_badge,
                'actions' => [
                    'show' => route('scm.deliveries.show', $delivery),
                    'edit' => route('scm.deliveries.edit', $delivery),
                    'delete' => route('scm.deliveries.destroy', $delivery),
                ],
            ];
        })->toArray();

        $drivers = SCMDriver::active()->get();
        $salesDOs = SalesDO::whereIn('status', ['wqs_quality_ok', 'scm_picking'])->get();

        return view('pages.scm.deliveries.index', compact(
            'columns',
            'deliveries',
            'deliveriesData',
            'drivers',
            'salesDOs'
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
            'receiver_name' => 'required|string|max:100',
            'receiver_position' => 'nullable|string|max:100',
        ]);

        $delivery->update([
            'delivery_status' => 'delivered',
            'arrival_time' => now(),
            'received_at' => now(),
            'receiver_name' => $validated['receiver_name'],
            'receiver_position' => $validated['receiver_position'] ?? null,
        ]);

        // Update Sales DO status
        $delivery->salesDO->update(['status' => 'scm_delivered']);

        $this->auditLog->logAction('scm', 'delivered', $delivery, "Delivery completed for DO: {$delivery->salesDO->do_code}");

        return redirect()->back()->with('success', 'Delivery marked as completed.');
    }
}
