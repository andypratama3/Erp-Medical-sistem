<?php

namespace Database\Factories;

use App\Models\ACTInvoice;
use App\Models\SalesDO;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class ACTInvoiceFactory extends Factory
{
    protected $model = ACTInvoice::class;

    public function definition(): array
    {
        $invoiceDate = $this->faker->dateTimeBetween('-60 days', 'now');
        $dueDate = (clone $invoiceDate)->modify('+30 days');

        return [
            'sales_do_id' => SalesDO::factory(),
            'branch_id' => Branch::factory(),
            'invoice_number' => 'INV-' . $this->faker->unique()->numerify('######'),
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'faktur_pajak_number' => $this->faker->optional()->numerify('FP-######'),
            'faktur_pajak_date' => $this->faker->optional()->dateTimeBetween($invoiceDate, '+7 days'),
            'subtotal' => $subtotal = $this->faker->randomFloat(2, 1000000, 10000000),
            'tax_amount' => $taxAmount = $subtotal * 0.11,
            'total' => $subtotal + $taxAmount,
            'invoice_status' => $this->faker->randomElement(['draft', 'issued', 'tukar_faktur', 'completed']),
            'tukar_faktur_at' => $this->faker->optional()->dateTimeBetween($invoiceDate, 'now'),
            'tukar_faktur_pic' => $this->faker->optional()->name(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}

// FINCollectionFactory
namespace Database\Factories;

use App\Models\FINCollection;
use App\Models\SalesDO;
use App\Models\ACTInvoice;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FINCollectionFactory extends Factory
{
    protected $model = FINCollection::class;

    public function definition(): array
    {
        return [
            'sales_do_id' => SalesDO::factory(),
            'invoice_id' => ACTInvoice::factory(),
            'branch_id' => Branch::factory(),
            'collection_number' => 'COL-' . $this->faker->unique()->numerify('######'),
            'collection_status' => $this->faker->randomElement(['pending', 'in_progress', 'partial', 'completed', 'overdue']),
            'total_amount' => $totalAmount = $this->faker->randomFloat(2, 1000000, 10000000),
            'collected_amount' => $this->faker->randomFloat(2, 0, $totalAmount),
            'outstanding_amount' => $totalAmount,
            'started_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'completed_at' => $this->faker->optional()->dateTimeBetween('-15 days', 'now'),
            'collector_id' => User::factory(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}

// FINPaymentFactory
namespace Database\Factories;

use App\Models\FINPayment;
use App\Models\SalesDO;
use App\Models\FINCollection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FINPaymentFactory extends Factory
{
    protected $model = FINPayment::class;

    public function definition(): array
    {
        $method = $this->faker->randomElement(['cash', 'transfer', 'check', 'giro', 'other']);

        return [
            'sales_do_id' => SalesDO::factory(),
            'collection_id' => FINCollection::factory(),
            'payment_number' => 'PAY-' . $this->faker->unique()->numerify('######'),
            'payment_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'payment_amount' => $this->faker->randomFloat(2, 100000, 5000000),
            'payment_method' => $method,
            'bank_name' => in_array($method, ['transfer', 'check', 'giro']) ? $this->faker->randomElement(['BCA', 'Mandiri', 'BNI', 'BRI']) : null,
            'account_number' => in_array($method, ['transfer', 'check', 'giro']) ? $this->faker->numerify('##########') : null,
            'reference_number' => 'REF-' . $this->faker->numerify('######'),
            'notes' => $this->faker->optional()->sentence(),
            'recorded_by' => User::factory(),
        ];
    }
}

// WQSStockCheckFactory
namespace Database\Factories;

use App\Models\WQSStockCheck;
use App\Models\SalesDO;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class WQSStockCheckFactory extends Factory
{
    protected $model = WQSStockCheck::class;

    public function definition(): array
    {
        return [
            'sales_do_id' => SalesDO::factory(),
            'branch_id' => Branch::factory(),
            'check_code' => 'SC-' . $this->faker->unique()->numerify('######'),
            'check_date' => $this->faker->date(),
            'checker_id' => User::factory(),
            'check_status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'failed']),
            'total_items' => $total = $this->faker->numberBetween(5, 20),
            'checked_items' => $this->faker->numberBetween(0, $total),
            'problematic_items' => $this->faker->numberBetween(0, 5),
            'notes' => $this->faker->optional()->sentence(),
            'started_at' => $this->faker->optional()->dateTimeBetween('-7 days', 'now'),
            'completed_at' => $this->faker->optional()->dateTimeBetween('-3 days', 'now'),
        ];
    }
}

// SCMDeliveryFactory
namespace Database\Factories;

use App\Models\SCMDelivery;
use App\Models\SalesDO;
use App\Models\SCMDriver;
use App\Models\Vehicle;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class SCMDeliveryFactory extends Factory
{
    protected $model = SCMDelivery::class;

    public function definition(): array
    {
        return [
            'sales_do_id' => SalesDO::factory(),
            'branch_id' => Branch::factory(),
            'delivery_code' => 'DEL-' . $this->faker->unique()->numerify('######'),
            'driver_id' => SCMDriver::factory(),
            'vehicle_id' => Vehicle::factory(),
            'scheduled_date' => $this->faker->dateTimeBetween('now', '+7 days'),
            'departure_time' => $this->faker->optional()->dateTimeBetween('-2 days', 'now'),
            'arrival_time' => $this->faker->optional()->dateTimeBetween('-1 day', 'now'),
            'delivery_status' => $this->faker->randomElement(['scheduled', 'in_transit', 'delivered', 'failed']),
            'delivery_address' => $this->faker->address(),
            'recipient_name' => $this->faker->name(),
            'recipient_phone' => $this->faker->phoneNumber(),
            'delivery_notes' => $this->faker->optional()->sentence(),
        ];
    }
}

// TaskBoardFactory
namespace Database\Factories;

use App\Models\TaskBoard;
use App\Models\SalesDO;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskBoardFactory extends Factory
{
    protected $model = TaskBoard::class;

    public function definition(): array
    {
        return [
            'sales_do_id' => SalesDO::factory(),
            'module' => $this->faker->randomElement(['wqs', 'scm', 'act', 'fin']),
            'task_status' => $this->faker->randomElement(['pending', 'in_progress', 'on_hold', 'completed', 'rejected']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'assigned_to' => User::factory(),
            'assigned_by' => User::factory(),
            'assigned_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'started_at' => $this->faker->optional()->dateTimeBetween('-5 days', 'now'),
            'completed_at' => $this->faker->optional()->dateTimeBetween('-2 days', 'now'),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
