<?php

namespace Database\Seeders;

use App\Models\FINCollection;
use App\Models\ACTInvoice;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class CollectionSeeder extends Seeder
{
    public function run(): void
    {
        $invoices = ACTInvoice::whereIn('invoice_status', ['issued', 'tukar_faktur', 'completed'])->get();
        $users = User::all();
        $branch = Branch::first();

        foreach ($invoices->take(15) as $index => $invoice) {
            $startedAt = now()->subDays(rand(1, 45));
            
            FINCollection::create([
                'sales_do_id' => $invoice->sales_do_id,
                'invoice_id' => $invoice->id,
                'branch_id' => $branch->id,
                'collection_number' => 'COL-' . now()->format('Ymd') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'collection_status' => collect(['pending', 'in_progress', 'partial', 'completed', 'overdue'])->random(),
                'total_amount' => $invoice->total,
                'collected_amount' => rand(0, 1) ? $invoice->total * (rand(50, 100) / 100) : 0,
                'outstanding_amount' => $invoice->total,
                'started_at' => $startedAt,
                'completed_at' => rand(0, 1) ? $startedAt->copy()->addDays(rand(1, 30)) : null,
                'collector_id' => $users->random()->id,
                'notes' => rand(0, 1) ? 'Follow up required' : null,
                'last_followup_at' => rand(0, 1) ? now()->subDays(rand(1, 15)) : null,
                'next_followup_at' => rand(0, 1) ? now()->addDays(rand(1, 15)) : null,
            ]);
        }
    }
}
