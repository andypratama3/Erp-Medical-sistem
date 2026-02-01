<?php

namespace App\Http\Controllers\SCM;

use App\Http\Controllers\Controller;
use App\Models\SCMDriver;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class SCMDriverController extends Controller
{
    protected $auditLog;

    public function __construct(AuditLogService $auditLog)
    {
        $this->auditLog = $auditLog;
    }

    public function index(Request $request)
    {
        $query = SCMDriver::with('branch');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('vehicle_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', $request->vehicle_type);
        }

        $drivers = $query->latest()->paginate(15);

        $columns = [
            ['key' => 'code', 'label' => 'Code', 'type' => 'text'],
            ['key' => 'name', 'label' => 'Driver Name', 'type' => 'text'],
            ['key' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['key' => 'vehicle', 'label' => 'Vehicle', 'type' => 'text'],
            ['key' => 'license', 'label' => 'License', 'type' => 'text'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];

        $driversData = $drivers->getCollection()->map(function ($driver) {
            return [
                'id' => $driver->id,
                'code' => $driver->code,
                'name' => $driver->name,
                'phone' => $driver->phone ?? '-',
                'vehicle' => "{$driver->vehicle_type} - {$driver->vehicle_number}",
                'license' => $driver->license_number ?? '-',
                'status' => [
                    'value' => $driver->status,
                    'label' => ucfirst($driver->status),
                    'color' => $driver->status === 'active' ? 'active' : 'inactive',
                ],
                'actions' => [
                    'show' => route('scm.drivers.show', $driver),
                    'edit' => route('scm.drivers.edit', $driver),
                    'delete' => route('scm.drivers.destroy', $driver),
                ],
            ];
        })->toArray();

        return view('pages.scm.drivers.index', compact(
            'columns',
            'drivers',
            'driversData'
        ));
    }

    public function create()
    {
        return view('pages.scm.drivers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:scm_drivers,code',
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'license_number' => 'required|string|max:50',
            'license_expiry' => 'nullable|date',
            'vehicle_type' => 'required|string|max:50',
            'vehicle_number' => 'required|string|max:50',
            'vehicle_capacity' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,on_leave',
            'notes' => 'nullable|string',
        ]);

        $driver = SCMDriver::create($validated);

        $this->auditLog->logCreate('scm', $driver, "Created driver: {$driver->name}");

        return redirect()->route('scm.drivers.index')
            ->with('success', 'Driver created successfully.');
    }

    public function show(SCMDriver $driver)
    {
        $driver->load(['branch', 'deliveries.salesDO']);

        $this->auditLog->logView('scm', $driver);

        return view('pages.scm.drivers.show', compact('driver'));
    }

    public function edit(SCMDriver $driver)
    {
        return view('pages.scm.drivers.edit', compact('driver'));
    }

    public function update(Request $request, SCMDriver $driver)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:scm_drivers,code,' . $driver->id,
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'license_number' => 'required|string|max:50',
            'license_expiry' => 'nullable|date',
            'vehicle_type' => 'required|string|max:50',
            'vehicle_number' => 'required|string|max:50',
            'vehicle_capacity' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,on_leave',
            'notes' => 'nullable|string',
        ]);

        $originalData = $driver->toArray();
        $driver->update($validated);

        $this->auditLog->logUpdate('scm', $driver, $originalData, "Updated driver: {$driver->name}");

        return redirect()->route('scm.drivers.index')
            ->with('success', 'Driver updated successfully.');
    }

    public function destroy(SCMDriver $driver)
    {
        $driverName = $driver->name;

        $this->auditLog->logDelete('scm', $driver, "Deleted driver: {$driverName}");

        $driver->delete();

        return redirect()->route('scm.drivers.index')
            ->with('success', 'Driver deleted successfully.');
    }
}
