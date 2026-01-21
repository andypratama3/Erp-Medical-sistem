<?php

namespace App\Http\Controllers\RegAlkes;

use App\Http\Controllers\Controller;
use App\Models\RegAlkesCase;
use Illuminate\Http\Request;

class ControlTowerController extends Controller
{
    public function index(Request $request)
    {
        // Stats by status
        $stats = [
            'total' => RegAlkesCase::count(),
            'waiting_nie' => RegAlkesCase::where('status', 'waiting_nie')->count(),
            'nie_issued' => RegAlkesCase::where('status', 'nie_issued')->count(),
            'sku_active' => RegAlkesCase::where('status', 'sku_active')->count(),
            'by_status' => RegAlkesCase::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
        ];

        // Recent cases
        $recentCases = RegAlkesCase::with(['manufacture'])
            ->latest()
            ->limit(10)
            ->get();

        return view('pages.reg_alkes.control_tower.index', compact('stats', 'recentCases'));
    }
}
