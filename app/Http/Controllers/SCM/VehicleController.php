<?php

namespace App\Http\Controllers\SCM;

use App\Models\Vehicle;
use App\Models\SCMDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class VehicleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_scm', only: ['index', 'show']),
            new Middleware('permission:process_scm', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }


    /**
     * Display a listing of vehicles
     */
    public function index(Request $request)
    {
        $query = Vehicle::with(['driver', 'branch']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('plate_number', 'like', '%' . $request->search . '%')
                  ->orWhere('brand', 'like', '%' . $request->search . '%')
                  ->orWhere('model', 'like', '%' . $request->search . '%');
            });
        }

        $vehicles = $query->latest()->paginate(15);

        return view('pages.scm.vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new vehicle
     */
    public function create()
    {
        $drivers = SCMDriver::where('status', 'active')
            ->doesntHave('vehicle')
            ->get();

        return view('pages.scm.vehicles.create', compact('drivers'));
    }

    /**
     * Store a newly created vehicle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20|unique:vehicles',
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:50',
            'capacity_weight' => 'required|numeric|min:0',
            'capacity_volume' => 'nullable|numeric|min:0',
            'fuel_type' => 'required|in:gasoline,diesel,electric,hybrid',
            'driver_id' => 'nullable|exists:scm_drivers,id',
            'insurance_number' => 'nullable|string|max:100',
            'insurance_expiry' => 'nullable|date',
            'tax_expiry' => 'nullable|date',
            'last_service_date' => 'nullable|date',
            'next_service_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,maintenance,inactive',
        ]);

        $validated['branch_id'] = auth()->user()->current_branch_id;

        DB::beginTransaction();
        try {
            $vehicle = Vehicle::create($validated);

            DB::commit();

            return redirect()
                ->route('scm.vehicles.show', $vehicle)
                ->with('success', 'Vehicle created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified vehicle
     */
    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['driver', 'branch', 'deliveries' => function($q) {
            $q->latest()->limit(10);
        }]);

        $stats = [
            'total_deliveries' => $vehicle->deliveries()->count(),
            'completed_deliveries' => $vehicle->deliveries()->where('delivery_status', 'delivered')->count(),
            'total_distance' => $vehicle->deliveries()->sum('distance_km'),
            'fuel_consumed' => $vehicle->deliveries()->sum('fuel_consumed'),
        ];

        return view('pages.scm.vehicles.show', compact('vehicle', 'stats'));
    }

    /**
     * Show the form for editing the specified vehicle
     */
    public function edit(Vehicle $vehicle)
    {
        $drivers = SCMDriver::where('status', 'active')
            ->where(function($q) use ($vehicle) {
                $q->doesntHave('vehicle')
                  ->orWhere('id', $vehicle->driver_id);
            })
            ->get();

        return view('pages.scm.vehicles.edit', compact('vehicle', 'drivers'));
    }

    /**
     * Update the specified vehicle
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number,' . $vehicle->id,
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:50',
            'capacity_weight' => 'required|numeric|min:0',
            'capacity_volume' => 'nullable|numeric|min:0',
            'fuel_type' => 'required|in:gasoline,diesel,electric,hybrid',
            'driver_id' => 'nullable|exists:scm_drivers,id',
            'insurance_number' => 'nullable|string|max:100',
            'insurance_expiry' => 'nullable|date',
            'tax_expiry' => 'nullable|date',
            'last_service_date' => 'nullable|date',
            'next_service_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,maintenance,inactive',
        ]);

        DB::beginTransaction();
        try {
            $vehicle->update($validated);

            DB::commit();

            return redirect()
                ->route('scm.vehicles.show', $vehicle)
                ->with('success', 'Vehicle updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified vehicle
     */
    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->deliveries()->exists()) {
            return back()->with('error', 'Cannot delete vehicle with existing deliveries');
        }

        DB::beginTransaction();
        try {
            $vehicle->delete();
            DB::commit();

            return redirect()
                ->route('scm.vehicles.index')
                ->with('success', 'Vehicle deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete vehicle: ' . $e->getMessage());
        }
    }
}
