<?php

namespace Tests\Feature\WQS;

use App\Models\User;
use App\Models\WQSStockCheck;
use App\Models\SalesDO;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockCheckTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_view_stock_checks_index(): void
    {
        $user = User::first();
        
        $response = $this->actingAs($user)
            ->get(route('wqs.stock-checks.index'));

        $response->assertStatus(200);
    }

    public function test_can_create_stock_check(): void
    {
        $user = User::first();
        $salesDO = SalesDO::factory()->create();
        $product = Product::first();
        $branch = Branch::first();

        $response = $this->actingAs($user)
            ->post(route('wqs.stock-checks.store'), [
                'sales_do_id' => $salesDO->id,
                'branch_id' => $branch->id,
                'check_date' => now()->format('Y-m-d'),
                'items' => [
                    [
                        'product_id' => $product->id,
                        'expected_quantity' => 100,
                        'actual_quantity' => 98,
                        'batch_number' => 'BATCH-001',
                        'location' => 'A-01',
                    ],
                ],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('wqs_stock_checks', [
            'sales_do_id' => $salesDO->id,
        ]);
    }

    public function test_can_approve_stock_check(): void
    {
        $user = User::first();
        $stockCheck = WQSStockCheck::factory()->create([
            'check_status' => 'completed',
        ]);

        $response = $this->actingAs($user)
            ->post(route('wqs.stock-checks.approve', $stockCheck));

        $response->assertRedirect();
        $stockCheck->refresh();
        $this->assertEquals('completed', $stockCheck->check_status);
    }

    public function test_can_mark_stock_check_as_failed(): void
    {
        $user = User::first();
        $stockCheck = WQSStockCheck::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('wqs.stock-checks.mark-failed', $stockCheck), [
                'notes' => 'Failed due to discrepancy',
            ]);

        $response->assertRedirect();
        $stockCheck->refresh();
        $this->assertEquals('failed', $stockCheck->check_status);
    }

    public function test_can_get_problematic_items(): void
    {
        $user = User::first();
        $stockCheck = WQSStockCheck::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('wqs.stock-checks.problematic-items', $stockCheck));

        $response->assertStatus(200);
    }

    public function test_can_generate_stock_check_report(): void
    {
        $user = User::first();
        $stockCheck = WQSStockCheck::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('wqs.stock-checks.report', $stockCheck));

        $response->assertStatus(200);
    }
}
