<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FertilizerTransaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $year = now()->year;

        // ================================
        // Total summary
        // ================================
        $totalTransactions = FertilizerTransaction::count();

        $totalFarmers = FertilizerTransaction::distinct('farmer_id')
            ->count('farmer_id');

        $totalFertilizer = FertilizerTransaction::selectRaw('
            COALESCE(SUM(urea),0) +
            COALESCE(SUM(npk),0) +
            COALESCE(SUM(sp36),0) +
            COALESCE(SUM(za),0) +
            COALESCE(SUM(npk_formula),0) +
            COALESCE(SUM(organic),0) +
            COALESCE(SUM(organic_liquid),0) as total
        ')->value('total');

        // ================================
        // Monthly transactions (Chart)
        // ================================
        $monthlyTransactions = FertilizerTransaction::selectRaw('
                MONTH(created_at) as month,
                COUNT(*) as total
            ')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Biar selalu 12 bulan
        $monthlyChartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyChartData[] = $monthlyTransactions[$i] ?? 0;
        }

        return view('pages.dashboard.index', compact(
            'totalTransactions',
            'totalFarmers',
            'totalFertilizer',
            'monthlyChartData'
        ));
    }
}
