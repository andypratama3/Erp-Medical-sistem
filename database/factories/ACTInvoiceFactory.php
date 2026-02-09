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

