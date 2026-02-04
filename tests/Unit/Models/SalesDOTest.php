<?php

namespace Tests\Unit\Models;

use App\Models\SalesDO;
use App\Models\Customer;
use App\Models\SalesDOItem;
use App\Models\User;
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

    public function test_sales_do_belongs_to_customer(): void
    {
        $salesDO = SalesDO::first();
        
        $this->assertInstanceOf(Customer::class, $salesDO->customer);
    }

    public function test_sales_do_has_many_items(): void
    {
        $salesDO = SalesDO::first();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $salesDO->items);
        $this->assertGreaterThan(0, $salesDO->items->count());
    }

    public function test_sales_do_belongs_to_created_by_user(): void
    {
        $salesDO = SalesDO::first();
        
        if ($salesDO->created_by) {
            $this->assertInstanceOf(User::class, $salesDO->createdBy);
        } else {
            $this->assertNull($salesDO->createdBy);
        }
    }

    public function test_sales_do_can_calculate_totals(): void
    {
        $salesDO = SalesDO::first();
        
        $this->assertIsFloat((float) $salesDO->subtotal);
        $this->assertIsFloat((float) $salesDO->tax_amount);
        $this->assertIsFloat((float) $salesDO->grand_total);
        $this->assertEquals(
            $salesDO->subtotal + $salesDO->tax_amount,
            $salesDO->grand_total
        );
    }

    public function test_sales_do_generates_unique_code(): void
    {
        $salesDO1 = SalesDO::first();
        $salesDO2 = SalesDO::skip(1)->first();
        
        $this->assertNotEquals($salesDO1->do_code, $salesDO2->do_code);
    }

    public function test_sales_do_status_transitions_are_valid(): void
    {
        $validStatuses = [
            'crm_to_wqs',
            'wqs_ready',
            'wqs_on_hold',
            'scm_on_delivery',
            'scm_delivered',
            'act_tukar_faktur',
            'act_invoiced',
            'fin_on_collect',
            'fin_paid',
            'fin_overdue',
        ];

        $salesDO = SalesDO::first();
        
        $this->assertContains($salesDO->status, $validStatuses);
    }

    public function test_sales_do_soft_deletes(): void
    {
        $salesDO = SalesDO::factory()->create();
        $id = $salesDO->id;
        
        $salesDO->delete();
        
        $this->assertSoftDeleted('sales_do', ['id' => $id]);
        $this->assertNotNull(SalesDO::withTrashed()->find($id)->deleted_at);
    }
}
