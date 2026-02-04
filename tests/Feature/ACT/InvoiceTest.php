<?php

namespace Tests\Feature\ACT;

use App\Models\User;
use App\Models\ACTInvoice;
use App\Models\SalesDO;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_view_invoices_index(): void
    {
        $user = User::first();
        
        $response = $this->actingAs($user)
            ->get(route('act.invoices.index'));

        $response->assertStatus(200);
    }

    public function test_can_create_invoice_from_sales_do(): void
    {
        $user = User::first();
        $salesDO = SalesDO::factory()->create([
            'status' => 'scm_delivered',
        ]);

        $response = $this->actingAs($user)
            ->post(route('act.invoices.from-sales-do', $salesDO), [
                'invoice_date' => now()->format('Y-m-d'),
                'due_date' => now()->addDays(30)->format('Y-m-d'),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('act_invoices', [
            'sales_do_id' => $salesDO->id,
        ]);
    }

    public function test_can_update_invoice(): void
    {
        $user = User::first();
        $invoice = ACTInvoice::factory()->create();

        $response = $this->actingAs($user)
            ->put(route('act.invoices.update', $invoice), [
                'invoice_date' => $invoice->invoice_date,
                'due_date' => now()->addDays(45)->format('Y-m-d'),
                'faktur_pajak_number' => 'FP-123456',
                'notes' => 'Updated notes',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('act_invoices', [
            'id' => $invoice->id,
            'faktur_pajak_number' => 'FP-123456',
        ]);
    }

    public function test_can_perform_tukar_faktur(): void
    {
        $user = User::first();
        $invoice = ACTInvoice::factory()->create([
            'invoice_status' => 'issued',
        ]);

        $response = $this->actingAs($user)
            ->post(route('act.invoices.tukar-faktur', $invoice), [
                'tukar_faktur_pic' => 'Finance Manager',
                'faktur_pajak_number' => 'FP-789012',
                'faktur_pajak_date' => now()->format('Y-m-d'),
            ]);

        $response->assertRedirect();
        $invoice->refresh();
        $this->assertEquals('tukar_faktur', $invoice->invoice_status);
        $this->assertNotNull($invoice->tukar_faktur_at);
    }

    public function test_can_approve_invoice(): void
    {
        $user = User::first();
        $invoice = ACTInvoice::factory()->create([
            'invoice_status' => 'tukar_faktur',
        ]);

        $response = $this->actingAs($user)
            ->post(route('act.invoices.approve', $invoice));

        $response->assertRedirect();
        $invoice->refresh();
        $this->assertEquals('completed', $invoice->invoice_status);
    }

    public function test_can_export_invoice_to_pdf(): void
    {
        $user = User::first();
        $invoice = ACTInvoice::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('act.invoices.export-pdf', $invoice));

        $response->assertStatus(200);
    }

    public function test_can_view_act_task_board(): void
    {
        $user = User::first();
        
        $response = $this->actingAs($user)
            ->get(route('act.task-board.index'));

        $response->assertStatus(200);
    }
}
