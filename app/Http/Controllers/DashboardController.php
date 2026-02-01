<?php

namespace App\Http\Controllers;

use App\Models\{Branch, SalesDO, TaskBoard, SCMDelivery, ACTInvoice};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Check if owner or staff
        if ($user->isOwner()) {
            return $this->ownerDashboard($request);
        } else {
            return $this->staffDashboard($request);
        }
    }

    /**
     * Owner Dashboard - Multi Branch Analytics
     */
    private function ownerDashboard(Request $request)
    {
        $branchFilter = $request->get('branch');
        $branches = Branch::all();

        // Build query with optional branch filter
        $salesQuery = SalesDO::query();
        if ($branchFilter && $branchFilter !== 'all') {
            $salesQuery->where('branch_id', $branchFilter);
        }

        // Stats
        $stats = [
            'total_sales_do' => $salesQuery->count(),
            'sales_do_growth' => $this->calculateGrowth('sales_do', $branchFilter),
            'total_revenue' => $salesQuery->sum('grand_total'),
            'revenue_growth' => $this->calculateGrowth('revenue', $branchFilter),
            'pending_deliveries' => SCMDelivery::whereIn('delivery_status', ['pending', 'scheduled', 'on_route'])
                ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
                ->count(),
            'deliveries_change' => $this->calculateChange('deliveries', $branchFilter),
            'active_tasks' => TaskBoard::whereNotIn('task_status', ['completed', 'rejected'])
                ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
                ->count(),
            'tasks_completion' => $this->calculateTasksCompletion($branchFilter),
        ];

        // Chart Data (Last 7 days)
        $chartData = $this->getRevenueChartData($branchFilter);

        // Branch Performance
        $branchPerformance = Branch::withSum(['salesDo as revenue' => function($q) {
            $q->whereMonth('created_at', now()->month);
        }], 'grand_total')
        ->get()
        ->map(function($branch) use ($salesQuery) {
            $totalRevenue = $salesQuery->sum('grand_total');
            return [
                'name' => $branch->name,
                'revenue' => $branch->revenue ?? 0,
                'percentage' => $totalRevenue > 0 ? round(($branch->revenue / $totalRevenue) * 100, 1) : 0,
            ];
        });

        $colors = ['#3C50E0', '#6577F3', '#8FD0EF', '#0FADCF', '#80CAEE'];

        // Recent Sales DO
        $recentSalesDO = SalesDO::with(['customer', 'branch'])
            ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->latest()
            ->limit(5)
            ->get();

        // Pending Tasks
        $pendingTasks = TaskBoard::with('salesDO')
            ->whereNotIn('task_status', ['completed', 'rejected'])
            ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->latest()
            ->limit(5)
            ->get();

        return view('pages.dashboard.owner-dashboard', compact(
            'branches',
            'stats',
            'chartData',
            'branchPerformance',
            'colors',
            'recentSalesDO',
            'pendingTasks'
        ));
    }

    /**
     * Staff Dashboard - Single Branch View
     */
    private function staffDashboard(Request $request)
    {
        $user = auth()->user();
        $currentBranch = $user->currentBranch;

        // Stats for current branch only
        $stats = [
            'my_sales_do' => SalesDO::where('branch_id', $currentBranch->id)
                ->where('created_by', $user->id)
                ->count(),
            'my_tasks' => TaskBoard::where('branch_id', $currentBranch->id)
                ->where('assigned_to', $user->id)
                ->whereNotIn('task_status', ['completed', 'rejected'])
                ->count(),
            'branch_revenue' => SalesDO::where('branch_id', $currentBranch->id)
                ->whereMonth('created_at', now()->month)
                ->sum('grand_total'),
            'pending_items' => TaskBoard::where('branch_id', $currentBranch->id)
                ->where('assigned_to', $user->id)
                ->where('task_status', 'pending')
                ->count(),
        ];

        // My Tasks
        $myTasks = TaskBoard::with('salesDO')
            ->where('branch_id', $currentBranch->id)
            ->where('assigned_to', $user->id)
            ->whereNotIn('task_status', ['completed', 'rejected'])
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get();

        // Recent Sales DO from my branch
        $recentSalesDO = SalesDO::with('customer')
            ->where('branch_id', $currentBranch->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('pages.dashboard.staff-dashboard', compact(
            'currentBranch',
            'stats',
            'myTasks',
            'recentSalesDO'
        ));
    }

    /**
     * Calculate growth percentage
     */
    private function calculateGrowth($type, $branchFilter = null)
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        if ($type === 'sales_do') {
            $current = SalesDO::when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
                ->whereDate('created_at', '>=', $thisMonth)->count();
            $previous = SalesDO::when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
                ->whereBetween('created_at', [$lastMonth, $thisMonth])->count();
        } else {
            $current = SalesDO::when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
                ->whereDate('created_at', '>=', $thisMonth)->sum('grand_total');
            $previous = SalesDO::when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
                ->whereBetween('created_at', [$lastMonth, $thisMonth])->sum('grand_total');
        }

        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Calculate change
     */
    private function calculateChange($type, $branchFilter = null)
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $current = SCMDelivery::when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->whereDate('created_at', $today)->count();
        $previous = SCMDelivery::when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->whereDate('created_at', $yesterday)->count();

        return $current - $previous;
    }

    /**
     * Calculate tasks completion percentage
     */
    private function calculateTasksCompletion($branchFilter = null)
    {
        $total = TaskBoard::when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->whereMonth('created_at', now()->month)->count();
        $completed = TaskBoard::when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->whereMonth('created_at', now()->month)
            ->where('task_status', 'completed')->count();

        if ($total == 0) return 0;
        return round(($completed / $total) * 100);
    }

    /**
     * Get revenue chart data (last 7 days)
     */
    private function getRevenueChartData($branchFilter = null)
    {
        $days = [];
        $currentData = [];
        $lastData = [];

        // Last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $lastMonthDate = Carbon::now()->subMonth()->subDays($i);

            $days[] = $date->format('d M');

            $currentData[] = SalesDO::when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
                ->whereDate('created_at', $date)
                ->sum('grand_total');

            $lastData[] = SalesDO::when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
                ->whereDate('created_at', $lastMonthDate)
                ->sum('grand_total');
        }

        return [
            'categories' => $days,
            'series_current' => $currentData,
            'series_last' => $lastData,
            'current_month' => array_sum($currentData),
            'last_month' => array_sum($lastData),
        ];
    }
}
