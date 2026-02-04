<?php

namespace Tests\Feature\FIN;

use App\Models\User;
use App\Models\FINCollection;
use App\Models\FINPayment;
use App\Models\ACTInvoice;
use App\Models\SalesDO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionAndPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_view_collections_index(): void
    {
        $user = User::first();
        
        $response = $this->actingAs($user)
            ->get(route('fin.collections.index'));

        $response->assertStatus(200);
    }

    public function test_can_start_collection(): void
    {
        $user = User::first();
        $invoice = ACTInvoice::factory()->create([
            'invoice_status' => 'completed',
        ]);

        $response = $this->actingAs($user)
            ->post(route('fin.collections.start-collection', $invoice));

        $response->assertRedirect();
        $this->assertDatabaseHas('fin_collections', [
            'invoice_id' => $invoice->id,
        ]);
    }

    public function test_can_record_payment(): void
    {
        $user = User::first();
        $invoice = ACTInvoice::factory()->create();
        $collection = FINCollection::factory()->create([
            'invoice_id' => $invoice->id,
            'sales_do_id' => $invoice->sales_do_id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('fin.collections.record-payment', $invoice), [
                'payment_date' => now()->format('Y-m-d'),
                'payment_amount' => 5000000,
                'payment_method' => 'transfer',
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
                'reference_number' => 'REF-123456',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('fin_payments', [
            'collection_id' => $collection->id,
        ]);
    }

    public function test_can_mark_invoice_as_overdue(): void
    {
        $user = User::first();
        $invoice = ACTInvoice::factory()->create([
            'due_date' => now()->subDays(10),
        ]);

        $response = $this->actingAs($user)
            ->post(route('fin.collections.mark-overdue', $invoice));

        $response->assertRedirect();
    }

    public function test_can_view_payments_index(): void
    {
        $user = User::first();
        
        $response = $this->actingAs($user)
            ->get(route('fin.payments.index'));

        $response->assertStatus(200);
    }

    public function test_can_confirm_payment(): void
    {
        $user = User::first();
        $payment = FINPayment::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('fin.payments.confirm', $payment));

        $response->assertRedirect();
    }

    public function test_can_generate_payment_receipt(): void
    {
        $user = User::first();
        $payment = FINPayment::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('fin.payments.receipt', $payment));

        $response->assertStatus(200);
    }

    public function test_can_view_aging_report(): void
    {
        $user = User::first();
        
        $response = $this->actingAs($user)
            ->get(route('fin.aging.index'));

        $response->assertStatus(200);
    }

    public function test_can_export_aging_report(): void
    {
        $user = User::first();
        
        $response = $this->actingAs($user)
            ->get(route('fin.aging.export'));

        $response->assertStatus(200);
    }

    public function test_can_view_aging_by_customer(): void
    {
        $user = User::first();
        $invoice = ACTInvoice::factory()->create();
        $salesDO = $invoice->salesDO;

        $response = $this->actingAs($user)
            ->get(route('fin.aging.by-customer', $salesDO->customer));

        $response->assertStatus(200);
    }
}
