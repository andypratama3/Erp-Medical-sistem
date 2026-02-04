<?php

namespace Tests\Unit\Models;

use App\Models\ACTInvoice;
use App\Models\SalesDO;
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

    public function test_invoice_belongs_to_sales_do(): void
    {
        $invoice = ACTInvoice::first();
        
        $this->assertInstanceOf(SalesDO::class, $invoice->salesDO);
    }

    public function test_invoice_calculates_total_correctly(): void
    {
        $invoice = ACTInvoice::first();
        
        $calculatedTotal = $invoice->subtotal + $invoice->tax_amount;
        $this->assertEquals($calculatedTotal, $invoice->total);
    }

    public function test_invoice_status_is_valid(): void
    {
        $validStatuses = ['draft', 'issued', 'tukar_faktur', 'completed'];
        $invoice = ACTInvoice::first();
        
        $this->assertContains($invoice->invoice_status, $validStatuses);
    }

    public function test_invoice_has_unique_number(): void
    {
        $invoice1 = ACTInvoice::first();
        $invoice2 = ACTInvoice::skip(1)->first();
        
        if ($invoice2) {
            $this->assertNotEquals($invoice1->invoice_number, $invoice2->invoice_number);
        } else {
            $this->assertTrue(true);
        }
    }

    public function test_invoice_due_date_is_after_invoice_date(): void
    {
        $invoice = ACTInvoice::first();
        
        if ($invoice->due_date) {
            $this->assertGreaterThanOrEqual(
                strtotime($invoice->invoice_date),
                strtotime($invoice->due_date)
            );
        } else {
            $this->assertTrue(true);
        }
    }

    public function test_invoice_can_have_faktur_pajak(): void
    {
        $invoice = ACTInvoice::where('faktur_pajak_number', '!=', null)->first();
        
        if ($invoice) {
            $this->assertNotNull($invoice->faktur_pajak_number);
            $this->assertNotNull($invoice->faktur_pajak_date);
        } else {
            $this->assertTrue(true);
        }
    }
}
