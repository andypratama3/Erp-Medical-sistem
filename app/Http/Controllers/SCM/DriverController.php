<?php

namespace App\Http\Controllers\SCM;

use App\Models\SCMDriver;
use Illuminate\Http\Request;
use App\Helpers\StatusBadgeHelper;
use App\Http\Controllers\Controller;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $drivers = SCMDriver::orderBy('created_at', 'asc');

        $columns = [
            ['key' => 'code', 'label' => 'Code', 'type' => 'text'],
            ['key' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['key' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['key' => 'license_number', 'label' => 'License Number', 'type' => 'text'],
            ['key' => 'vehicle_type', 'label' => 'Vehicle Type', 'type' => 'text'],
            ['key' => 'vehicle_number', 'label' => 'Vehicle Number', 'type' => 'text'],
        ];

        if($request->has('search')) {
            $search = $request->search;
            $drivers->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $drivers = $drivers->paginate(15);


        $deliveriesData = $drivers->getCollection(function ($driver) use ($request) {
            return [
                'id' => $driver->id,
                'code' => $driver->code,
                'name' => $driver->name,
                'phone' => $driver->phone ?? '-',
                'actions' => [
                    'show' => route('scm.drivers.show', $driver),
                    'edit' => route('scm.drivers.edit', $driver),
                    'delete' => route('scm.drivers.destroy', $driver),
                ],
            ];
        })->toArray();


        return view('pages.scm.drivers.index', compact('drivers','deliveriesData','columns'));
    }

    public function create()
    {
        return view('pages.scm.drivers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:scm_drivers',
            'name' => 'required',
            'phone' => 'nullable',
            'license_number' => 'required|unique:scm_drivers',
            'vehicle_type' => 'required',
            'vehicle_number' => 'required|unique:scm_drivers',
        ]);

        SCMDriver::create($validated);

        return redirect()->route('scm.drivers.index')->with('success', 'Driver created successfully');
    }

    public function show(SCMDriver $driver)
    {
        return view('pages.scm.drivers.show', compact('driver'));
    }

    public function edit(SCMDriver $driver)
    {
        return view('pages.scm.drivers.edit', compact('driver'));
    }

    public function update(Request $request, SCMDriver $driver)
    {
        $validated = $request->validate([
            'code' => 'required|unique:scm_drivers,code,' . $driver->id,
            'name' => 'required',
            'phone' => 'nullable',
            'license_number' => 'required|unique:scm_drivers,license_number,' . $driver->id,
            'vehicle_type' => 'required',
            'vehicle_number' => 'required|unique:scm_drivers,vehicle_number,' . $driver->id,
        ]);

        $driver->update($validated);

        return redirect()->route('scm.drivers.index')->with('success', 'Driver updated successfully');
    }

    public function destroy(SCMDriver $driver)
    {
        $driver->delete();

        return redirect()->route('scm.drivers.index')->with('success', 'Driver deleted successfully');
    }
}
