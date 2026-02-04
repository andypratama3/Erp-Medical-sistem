<?php

namespace Tests\Feature\WQS;

use App\Models\User;
use App\Models\TaskBoard;
use App\Models\SalesDO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskBoardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_view_task_board_index(): void
    {
        $user = User::first();
        
        $response = $this->actingAs($user)
            ->get(route('wqs.task-board.index'));

        $response->assertStatus(200);
    }

    public function test_can_start_task(): void
    {
        $user = User::first();
        $salesDO = SalesDO::factory()->create([
            'status' => 'crm_to_wqs',
        ]);

        $response = $this->actingAs($user)
            ->post(route('wqs.task-board.start', $salesDO));

        $response->assertRedirect();
        $salesDO->refresh();
        $this->assertEquals('wqs_ready', $salesDO->status);
    }

    public function test_can_hold_task(): void
    {
        $user = User::first();
        $salesDO = SalesDO::factory()->create([
            'status' => 'wqs_ready',
        ]);

        $response = $this->actingAs($user)
            ->post(route('wqs.task-board.hold', $salesDO), [
                'notes' => 'On hold for verification',
            ]);

        $response->assertRedirect();
        $salesDO->refresh();
        $this->assertEquals('wqs_on_hold', $salesDO->status);
    }

    public function test_can_complete_task(): void
    {
        $user = User::first();
        $salesDO = SalesDO::factory()->create([
            'status' => 'wqs_ready',
        ]);

        $response = $this->actingAs($user)
            ->post(route('wqs.task-board.complete', $salesDO));

        $response->assertRedirect();
        $salesDO->refresh();
        $this->assertContains($salesDO->status, ['wqs_ready', 'scm_on_delivery']);
    }

    public function test_can_reject_task(): void
    {
        $user = User::first();
        $salesDO = SalesDO::factory()->create([
            'status' => 'wqs_ready',
        ]);

        $response = $this->actingAs($user)
            ->post(route('wqs.task-board.reject', $salesDO), [
                'rejection_reason' => 'Quality issues',
            ]);

        $response->assertRedirect();
    }

    public function test_can_assign_task(): void
    {
        $user = User::first();
        $assignee = User::skip(1)->first();
        $taskBoard = TaskBoard::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('wqs.task-board.assign', $taskBoard), [
                'assigned_to' => $assignee->id,
            ]);

        $response->assertRedirect();
        $taskBoard->refresh();
        $this->assertEquals($assignee->id, $taskBoard->assigned_to);
    }

    public function test_can_update_priority(): void
    {
        $user = User::first();
        $taskBoard = TaskBoard::factory()->create();

        $response = $this->actingAs($user)
            ->put(route('wqs.task-board.priority', $taskBoard), [
                'priority' => 'high',
            ]);

        $response->assertRedirect();
        $taskBoard->refresh();
        $this->assertEquals('high', $taskBoard->priority);
    }
}
