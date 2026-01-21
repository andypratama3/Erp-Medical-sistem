<?php

namespace App\Http\Controllers\FIN;

use App\Http\Controllers\Controller;
use App\Models\ACTInvoice;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AgingController extends Controller
{
    public function index(Request $request)
    {
        $query = ACTInvoice::with(['salesDo.customer'])
            ->where('payment_status', '!=', 'paid')
            ->where('outstanding_amount', '>', 0);

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->whereHas('salesDo', function($q) use ($request) {
                $q->where('customer_id', $request->customer_id);
            });
        }

        // Filter by aging period
        if ($request->filled('aging')) {
            $now = Carbon::now();
            switch ($request->aging) {
                case 'current':
                    $query->where('due_date', '>=', $now);
                    break;
                case '1_30':
                    $query->whereBetween('due_date', [$now->copy()->subDays(30), $now->copy()->subDay()]);
                    break;
                case '31_60':
                    $query->whereBetween('due_date', [$now->copy()->subDays(60), $now->copy()->subDays(31)]);
                    break;
                case '61_90':
                    $query->whereBetween('due_date', [$now->copy()->subDays(90), $now->copy()->subDays(61)]);
                    break;
                case 'over_90':
                    $query->where('due_date', '<', $now->copy()->subDays(90));
                    break;
            }
        }

        $invoices = $query->latest('due_date')->paginate(20);

        // Calculate aging summary
        $agingSummary = $this->calculateAgingSummary();

        return view('pages.fin.aging.index', compact('invoices', 'agingSummary'));
    }

    protected function calculateAgingSummary()
    {
        $now = Carbon::now();
        $unpaidInvoices = ACTInvoice::where('payment_status', '!=', 'paid')
            ->where('outstanding_amount', '>', 0)
            ->get();

        return [
            'current' => 'Rp ' . number_format($unpaidInvoices->filter(fn($inv) => $inv->due_date >= $now)->sum('outstanding_amount'), 0, ',', '.'),
            '1_30' => 'Rp ' . number_format($unpaidInvoices->filter(fn($inv) => $inv->due_date->between($now->copy()->subDays(30), $now->copy()->subDay()))->sum('outstanding_amount'), 0, ',', '.'),
            '31_60' => 'Rp ' . number_format($unpaidInvoices->filter(fn($inv) => $inv->due_date->between($now->copy()->subDays(60), $now->copy()->subDays(31)))->sum('outstanding_amount'), 0, ',', '.'),
            '61_90' => 'Rp ' . number_format($unpaidInvoices->filter(fn($inv) => $inv->due_date->between($now->copy()->subDays(90), $now->copy()->subDays(61)))->sum('outstanding_amount'), 0, ',', '.'),
            'over_90' => 'Rp ' . number_format($unpaidInvoices->filter(fn($inv) => $inv->due_date < $now->copy()->subDays(90))->sum('outstanding_amount'), 0, ',', '.'),
        ];
    }

    public function export(Request $request)
    {
        // Implementation for exporting aging report to Excel/PDF
        // Use maatwebsite/excel or barryvdh/laravel-dompdf
    }
}
