<?php

namespace Tests\Feature\Master;

use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Branch;
use App\Models\PaymentTerm;
use App\Models\Tax;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_view_customers_index(): void
    {
        $user = User::first();
        $user->assignRole('owner');
        
        $response = $this->actingAs($user)
            ->get(route('master.customers.index'));

        $response->assertStatus(200);
    }

    public function test_can_create_customer(): void
    {
        $user = User::first();
        $user->assignRole('owner');
        $branch = Branch::first();
        $paymentTerm = PaymentTerm::first();

        $response = $this->actingAs($user)
            ->post(route('master.customers.store'), [
                'code' => 'CUST-TEST',
                'name' => 'Test Customer',
                'legal_name' => 'Test Customer Ltd',
                'npwp' => '123456789012345',
                'address' => 'Test Address',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'phone' => '021123456',
                'email' => 'test@customer.com',
                'contact_person' => 'John Doe',
                'payment_term_id' => $paymentTerm->id,
                'customer_type' => 'hospital',
                'status' => 'active',
                'branch_id' => $branch->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('master_customers', [
            'code' => 'CUST-TEST',
        ]);
    }

    public function test_can_toggle_customer_status(): void
    {
        $user = User::first();
        $user->assignRole('owner');
        $customer = Customer::first();

        $response = $this->actingAs($user)
            ->post(route('master.customers.toggle-status', $customer));

        $response->assertRedirect();
        $customer->refresh();
        $this->assertNotEquals($customer->getOriginal('status'), $customer->status);
    }

    public function test_can_view_products_index(): void
    {
        $user = User::first();
        $user->assignRole('owner');
        
        $response = $this->actingAs($user)
            ->get(route('master.products.index'));

        $response->assertStatus(200);
    }

    public function test_can_create_product(): void
    {
        $user = User::first();
        $user->assignRole('owner');
        $product = Product::first();

        $response = $this->actingAs($user)
            ->post(route('master.products.store'), [
                'sku' => 'TEST-SKU-001',
                'name' => 'Test Product',
                'category_id' => $product->category_id,
                'product_group_id' => $product->product_group_id,
                'manufacture_id' => $product->manufacture_id,
                'unit' => 'PCS',
                'unit_price' => 100000,
                'cost_price' => 80000,
                'product_type' => 'medical_device',
                'status' => 'active',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('master_products', [
            'sku' => 'TEST-SKU-001',
        ]);
    }

    public function test_can_toggle_product_status(): void
    {
        $user = User::first();
        $user->assignRole('owner');
        $product = Product::first();

        $response = $this->actingAs($user)
            ->post(route('master.products.toggle-status', $product));

        $response->assertRedirect();
    }

    public function test_can_view_vendors_index(): void
    {
        $user = User::first();
        $user->assignRole('owner');
        
        $response = $this->actingAs($user)
            ->get(route('master.vendors.index'));

        $response->assertStatus(200);
    }

    public function test_can_create_vendor(): void
    {
        $user = User::first();
        $user->assignRole('owner');
        $branch = Branch::first();

        $response = $this->actingAs($user)
            ->post(route('master.vendors.store'), [
                'code' => 'VEND-TEST',
                'name' => 'Test Vendor',
                'address' => 'Test Address',
                'city' => 'Jakarta',
                'phone' => '021123456',
                'email' => 'test@vendor.com',
                'contact_person' => 'Jane Doe',
                'status' => 'active',
                'branch_id' => $branch->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('master_vendors', [
            'code' => 'VEND-TEST',
        ]);
    }

    public function test_can_view_branches_index(): void
    {
        $user = User::first();
        $user->assignRole('owner');
        
        $response = $this->actingAs($user)
            ->get(route('master.branches.index'));

        $response->assertStatus(200);
    }

    public function test_can_switch_branch(): void
    {
        $user = User::first();
        $branch = Branch::skip(1)->first() ?? Branch::first();

        $response = $this->actingAs($user)
            ->post(route('master.branches.switch'), [
                'branch_id' => $branch->id,
            ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals($branch->id, $user->current_branch_id);
    }

    public function test_can_view_payment_terms_index(): void
    {
        $user = User::first();
        $user->assignRole('owner');
        
        $response = $this->actingAs($user)
            ->get(route('master.payment-terms.index'));

        $response->assertStatus(200);
    }

    public function test_can_view_taxes_index(): void
    {
        $user = User::first();
        $user->assignRole('owner');
        
        $response = $this->actingAs($user)
            ->get(route('master.taxes.index'));

        $response->assertStatus(200);
    }
}
