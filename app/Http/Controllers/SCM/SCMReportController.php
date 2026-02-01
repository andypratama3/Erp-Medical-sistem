<?php

namespace App\Http\Controllers\SCM;

use App\Http\Controllers\Controller;
use App\Models\{SCMDelivery, SCMDriver, Vehicle};
use Illuminate\Http\Request;
use Carbon\Carbon;

class SCMReportController extends Controller
{
    public function deliveryPerformance(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now());

        $stats = [
            'total_deliveries' => SCMDelivery::whereBetween('created_at', [$startDate, $endDate])->count(),
            'on_time' => SCMDelivery::whereBetween('created_at', [$startDate, $endDate])
                ->where('delivery_status', 'delivered')
                ->whereRaw('delivered_at <= expected_delivery_date')
                ->count(),
            'delayed' => SCMDelivery::whereBetween('created_at', [$startDate, $endDate])
                ->where('delivery_status', 'delivered')
                ->whereRaw('delivered_at > expected_delivery_date')
                ->count(),
            'in_progress' => SCMDelivery::whereIn('delivery_status', ['scheduled', 'on_route'])->count(),
        ];

        $chartData = $this->getDeliveryChartData($startDate, $endDate);

        return view('pages.scm.reports.delivery-performance', compact('stats', 'chartData'));
    }

    public function driverPerformance(Request $request)
    {
        $drivers = SCMDriver::withCount([
            'deliveries as total_deliveries',
            'deliveries as completed_deliveries' => fn($q) => $q->where('delivery_status', 'delivered'),
            'deliveries as delayed_deliveries' => fn($q) => $q->where('delivery_status', 'delivered')
                ->whereRaw('delivered_at > expected_delivery_date'),
        ])
        ->having('total_deliveries', '>', 0)
        ->get();

        return view('pages.scm.reports.driver-performance', compact('drivers'));
    }

    public function deliverySummary(Request $request)
    {
        // Implementation for summary report
        return view('pages.scm.reports.delivery-summary');
    }

    public function export(Request $request)
    {
        // Implementation for export to Excel
    }

    private function getDeliveryChartData($startDate, $endDate)
    {
        $deliveries = SCMDelivery::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, delivery_status, COUNT(*) as count')
            ->groupBy('date', 'delivery_status')
            ->get();

        // Format data for charts
        return [
            'dates' => $deliveries->pluck('date')->unique()->values(),
            'series' => [
                'delivered' => $deliveries->where('delivery_status', 'delivered')->pluck('count'),
                'on_route' => $deliveries->where('delivery_status', 'on_route')->pluck('count'),
                'scheduled' => $deliveries->where('delivery_status', 'scheduled')->pluck('count'),
            ]
        ];
    }
}
