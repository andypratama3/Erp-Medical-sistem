<?php

namespace Tests\Feature\SCM;

use App\Models\User;
use App\Models\SCMDelivery;
use App\Models\SCMDriver;
use App\Models\Vehicle;
use App\Models\SalesDO;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeliveryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_can_view_deliveries_index(): void
    {
        $user = User::first();
        
        $response = $this->actingAs($user)
            ->get(route('scm.deliveries.index'));

        $response->assertStatus(200);
    }

    public function test_can_create_delivery(): void
    {
        $user = User::first();
        $salesDO = SalesDO::factory()->create();
        $driver = SCMDriver::first();
        $vehicle = Vehicle::first();
        $branch = Branch::first();

        $response = $this->actingAs($user)
            ->post(route('scm.deliveries.store'), [
                'sales_do_id' => $salesDO->id,
                'branch_id' => $branch->id,
                'driver_id' => $driver->id,
                'vehicle_id' => $vehicle->id,
                'scheduled_date' => now()->addDays(1)->format('Y-m-d'),
                'delivery_address' => $salesDO->shipping_address,
                'recipient_name' => $salesDO->pic_customer,
                'recipient_phone' => '081234567890',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('scm_deliveries', [
            'sales_do_id' => $salesDO->id,
        ]);
    }

    public function test_can_assign_driver_to_delivery(): void
    {
        $user = User::first();
        $delivery = SCMDelivery::factory()->create();
        $driver = SCMDriver::skip(1)->first();

        $response = $this->actingAs($user)
            ->post(route('scm.deliveries.assign-driver', $delivery), [
                'driver_id' => $driver->id,
            ]);

        $response->assertRedirect();
        $delivery->refresh();
        $this->assertEquals($driver->id, $delivery->driver_id);
    }

    public function test_can_dispatch_delivery(): void
    {
        $user = User::first();
        $delivery = SCMDelivery::factory()->create([
            'delivery_status' => 'scheduled',
        ]);

        $response = $this->actingAs($user)
            ->post(route('scm.deliveries.dispatch', $delivery));

        $response->assertRedirect();
        $delivery->refresh();
        $this->assertEquals('in_transit', $delivery->delivery_status);
        $this->assertNotNull($delivery->departure_time);
    }

    public function test_can_mark_delivery_as_delivered(): void
    {
        $user = User::first();
        $delivery = SCMDelivery::factory()->create([
            'delivery_status' => 'in_transit',
        ]);

        $response = $this->actingAs($user)
            ->post(route('scm.deliveries.mark-delivered', $delivery), [
                'recipient_name' => 'John Doe',
            ]);

        $response->assertRedirect();
        $delivery->refresh();
        $this->assertEquals('delivered', $delivery->delivery_status);
        $this->assertNotNull($delivery->arrival_time);
    }

    public function test_can_upload_pod(): void
    {
        $user = User::first();
        $delivery = SCMDelivery::factory()->create();
        $file = UploadedFile::fake()->image('pod.jpg');

        $response = $this->actingAs($user)
            ->post(route('scm.deliveries.upload-pod', $delivery), [
                'pod_photo' => $file,
                'pod_signature' => UploadedFile::fake()->image('signature.jpg'),
            ]);

        $response->assertRedirect();
        Storage::disk('public')->assertExists('pod/' . $file->hashName());
    }
}
