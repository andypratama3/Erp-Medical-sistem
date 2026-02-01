<?php

namespace App\Http\Controllers\SCM;

use App\Http\Controllers\Controller;
use App\Models\{SCMDelivery, DeliveryTracking, SalesDO};
use Illuminate\Http\Request;

class DeliveryTrackingController extends Controller
{
    public function index()
    {
        $deliveries = SCMDelivery::with(['salesDO', 'driver', 'vehicle'])
            ->whereIn('delivery_status', ['scheduled', 'on_route', 'arrived'])
            ->latest()
            ->paginate(20);

        return view('pages.scm.tracking.index', compact('deliveries'));
    }

    public function show(SalesDO $salesDo)
    {
        $delivery = $salesDo->delivery()->with(['driver', 'vehicle', 'trackingHistory'])->firstOrFail();

        return view('pages.scm.tracking.show', compact('delivery', 'salesDo'));
    }

    public function liveTracking(SalesDO $salesDo)
    {
        $delivery = $salesDo->delivery;
        $currentLocation = $delivery->trackingHistory()->latest()->first();

        return view('pages.scm.tracking.live', compact('delivery', 'currentLocation'));
    }

    public function history(SalesDO $salesDo)
    {
        $trackingHistory = $salesDo->delivery
            ->trackingHistory()
            ->with('updatedBy')
            ->latest()
            ->get();

        return response()->json($trackingHistory);
    }
}
