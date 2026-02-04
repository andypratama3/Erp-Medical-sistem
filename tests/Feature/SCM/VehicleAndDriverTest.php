<?php

namespace Tests\Feature\SCM;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\SCMDriver;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleAndDriverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_view_vehicles_index(): void
    {
        $user = User::first();
        
        $response = $this->actingAs($user)
            ->get(route('scm.vehicles.index'));

        $response->assertStatus(200);
    }

    public function test_can_create_vehicle(): void
    {
        $user = User::first();
        $branch = Branch::first();

        $response = $this->actingAs($user)
            ->post(route('scm.vehicles.store'), [
                'license_plate' => 'B 1234 TEST',
                'vehicle_type' => 'truck',
                'brand' => 'Toyota',
                'model' => 'Dyna',
                'year' => 2023,
                'capacity_kg' => 3000,
                'fuel_type' => 'diesel',
                'status' => 'available',
                'branch_id' => $branch->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vehicles', [
            'license_plate' => 'B 1234 TEST',
        ]);
    }

    public function test_can_update_vehicle(): void
    {
        $user = User::first();
        $vehicle = Vehicle::first();

        $response = $this->actingAs($user)
            ->put(route('scm.vehicles.update', $vehicle), [
                'license_plate' => $vehicle->license_plate,
                'vehicle_type' => $vehicle->vehicle_type,
                'brand' => 'Updated Brand',
                'model' => $vehicle->model,
                'year' => $vehicle->year,
                'capacity_kg' => $vehicle->capacity_kg,
                'fuel_type' => $vehicle->fuel_type,
                'status' => 'maintenance',
                'branch_id' => $vehicle->branch_id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'status' => 'maintenance',
        ]);
    }

    public function test_can_delete_vehicle(): void
    {
        $user = User::first();
        $vehicle = Vehicle::factory()->create();

        $response = $this->actingAs($user)
            ->delete(route('scm.vehicles.destroy', $vehicle));

        $response->assertRedirect();
        $this->assertDatabaseMissing('vehicles', ['id' => $vehicle->id]);
    }

    public function test_can_view_drivers_index(): void
    {
        $user = User::first();
        
        $response = $this->actingAs($user)
            ->get(route('scm.drivers.index'));

        $response->assertStatus(200);
    }

    public function test_can_create_driver(): void
    {
        $user = User::first();
        $branch = Branch::first();

        $response = $this->actingAs($user)
            ->post(route('scm.drivers.store'), [
                'driver_code' => 'DRV-TEST',
                'name' => 'Test Driver',
                'license_number' => 'TEST-123456',
                'license_type' => 'B2',
                'phone' => '081234567890',
                'email' => 'test.driver@example.com',
                'address' => 'Test Address',
                'status' => 'available',
                'branch_id' => $branch->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('scm_drivers', [
            'driver_code' => 'DRV-TEST',
        ]);
    }

    public function test_can_update_driver(): void
    {
        $user = User::first();
        $driver = SCMDriver::first();

        $response = $this->actingAs($user)
            ->put(route('scm.drivers.update', $driver), [
                'driver_code' => $driver->driver_code,
                'name' => 'Updated Name',
                'license_number' => $driver->license_number,
                'license_type' => $driver->license_type,
                'phone' => $driver->phone,
                'email' => $driver->email,
                'address' => $driver->address,
                'status' => 'on_delivery',
                'branch_id' => $driver->branch_id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('scm_drivers', [
            'id' => $driver->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_can_delete_driver(): void
    {
        $user = User::first();
        $driver = SCMDriver::factory()->create();

        $response = $this->actingAs($user)
            ->delete(route('scm.drivers.destroy', $driver));

        $response->assertRedirect();
        $this->assertDatabaseMissing('scm_drivers', ['id' => $driver->id]);
    }
}
