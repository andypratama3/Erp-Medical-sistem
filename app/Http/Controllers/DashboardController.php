<?php

namespace App\Http\Controllers;

use App\Models\SalesDO;
use App\Models\ACTInvoice;
use Illuminate\Http\Request;
use App\Models\FINCollection;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {

        $stats = [
            'total_do' => SalesDO::count(),
            'pending_do' => SalesDO::whereIn('status', ['crm_to_wqs', 'wqs_ready'])->count(),
            'on_delivery' => SalesDO::where('status', 'scm_on_delivery')->count(),
            'pending_invoice' => ACTInvoice::where('invoice_status', 'draft')->count(),
            'pending_collection' => FINCollection::where('collection_status', 'pending')->count(),
            'overdue_invoices' => ACTInvoice::overdue()->count(),
        ];


        $recent_dos = SalesDO::with(['customer', 'office'])
            ->latest()
            ->take(10)
            ->get();

        return view('pages.dashboard.index', compact('stats', 'recent_dos'));
    }
}
