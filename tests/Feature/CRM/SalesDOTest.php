<?php

namespace Tests\Feature\CRM;

use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PaymentTerm;
use App\Models\Tax;
use App\Models\MasterOffice;
use App\Models\SalesDO;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesDOTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_view_sales_do_index(): void
    {
        $user = User::first();
        
        $response = $this->actingAs($user)
            ->get(route('crm.sales-do.index'));

        $response->assertStatus(200);
    }

    public function test_can_create_sales_do(): void
    {
        $user = User::first();
        $customer = Customer::first();
        $office = MasterOffice::first();
        $paymentTerm = PaymentTerm::first();
        $tax = Tax::first();
        $product = Product::first();

        $response = $this->actingAs($user)
            ->post(route('crm.sales-do.store'), [
                'customer_id' => $customer->id,
                'office_id' => $office->id,
                'do_date' => now()->format('Y-m-d'),
                'shipping_address' => 'Test Shipping Address',
                'pic_customer' => 'Test PIC',
                'payment_term_id' => $paymentTerm->id,
                'tax_id' => $tax->id,
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 10,
                        'unit_price' => 100000,
                        'discount_percent' => 0,
                    ],
                ],
                'notes_crm' => 'Test notes',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sales_do', [
            'customer_id' => $customer->id,
            'shipping_address' => 'Test Shipping Address',
        ]);
    }

    public function test_can_submit_sales_do(): void
    {
        $user = User::first();
        $salesDO = SalesDO::factory()->create([
            'status' => 'crm_to_wqs',
        ]);

        $response = $this->actingAs($user)
            ->post(route('crm.sales-do.submit', $salesDO));

        $response->assertRedirect();
        $salesDO->refresh();
        $this->assertNotNull($salesDO->submitted_at);
        $this->assertEquals($user->id, $salesDO->submitted_by);
    }

    public function test_can_update_sales_do(): void
    {
        $user = User::first();
        $salesDO = SalesDO::factory()->create();

        $response = $this->actingAs($user)
            ->put(route('crm.sales-do.update', $salesDO), [
                'customer_id' => $salesDO->customer_id,
                'office_id' => $salesDO->office_id,
                'do_date' => now()->format('Y-m-d'),
                'shipping_address' => 'Updated Address',
                'pic_customer' => 'Updated PIC',
                'payment_term_id' => $salesDO->payment_term_id,
                'tax_id' => $salesDO->tax_id,
                'items' => [],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sales_do', [
            'id' => $salesDO->id,
            'shipping_address' => 'Updated Address',
        ]);
    }

    public function test_can_delete_sales_do(): void
    {
        $user = User::first();
        $salesDO = SalesDO::factory()->create();

        $response = $this->actingAs($user)
            ->delete(route('crm.sales-do.destroy', $salesDO));

        $response->assertRedirect();
        $this->assertSoftDeleted('sales_do', ['id' => $salesDO->id]);
    }

    public function test_can_export_sales_do_to_pdf(): void
    {
        $user = User::first();
        $salesDO = SalesDO::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('crm.sales-do.export-pdf', $salesDO));

        $response->assertStatus(200);
    }
}
