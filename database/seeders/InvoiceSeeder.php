<?php

namespace Database\Seeders;

use App\Models\ACTInvoice;
use App\Models\SalesDO;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $salesDOs = SalesDO::whereIn('status', ['scm_delivered', 'act_tukar_faktur', 'act_invoiced', 'fin_on_collect', 'fin_paid'])->get();
        $branch = Branch::first();

        if ($salesDOs->isEmpty() || !$branch) {
            $this->command->warn('Skipping InvoiceSeeder - missing required data');
            return;
        }

        foreach ($salesDOs->take(20) as $index => $salesDO) {
            $invoiceDate = now()->subDays(rand(1, 60));
            $dueDate = $invoiceDate->copy()->addDays($salesDO->paymentTerm->days ?? 30);

            ACTInvoice::create([
                'sales_do_id' => $salesDO->id,
                'branch_id' => $branch->id,
                'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'faktur_pajak_number' => rand(0, 1) ? 'FP-' . rand(100000, 999999) : null,
                'faktur_pajak_date' => rand(0, 1) ? $invoiceDate->copy()->addDays(rand(1, 7)) : null,
                'subtotal' => $salesDO->subtotal,
                'tax_amount' => $salesDO->tax_amount,
                'total' => $salesDO->grand_total,
                'invoice_status' => collect(['draft', 'issued', 'tukar_faktur', 'completed'])->random(),
                'tukar_faktur_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                'tukar_faktur_pic' => rand(0, 1) ? 'Finance Manager' : null,
                'notes' => rand(0, 1) ? 'Please process payment on time' : null,
            ]);
        }
    }
}
