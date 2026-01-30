@extends('layouts.app')

@section('title', 'Delivery Task - ' . $salesDo->do_code)

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">🚚 Delivery Task</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">DO: <span class="font-mono font-semibold">{{ $salesDo->do_code }}</span> | Customer: <span class="font-semibold">{{ $salesDo->customer?->name }}</span></p>
    </div>

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('scm.task-board.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-400 dark:bg-gray-700 hover:bg-gray-500 dark:hover:bg-gray-600 text-white rounded-lg font-semibold transition">
            ← Back to Task Board
        </a>
    </div>

    <x-flash-message.flash />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left: Main Content (2 columns) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- DO Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📋 DO Summary</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">DO Date</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $salesDo->do_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Items</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $salesDo->items->count() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Grand Total</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($salesDo->grand_total, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        @if($salesDo->status === 'wqs_ready')
                            <span class="inline-block mt-1 px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-sm font-bold">
                                Ready
                            </span>
                        @elseif($salesDo->status === 'scm_on_delivery')
                            <span class="inline-block mt-1 px-3 py-1 bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 rounded text-sm font-bold">
                                On Delivery
                            </span>
                        @elseif($salesDo->status === 'scm_delivered')
                            <span class="inline-block mt-1 px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded text-sm font-bold">
                                Delivered
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Assign Driver Form (if not assigned or ready status) -->
            @if($salesDo->status === 'wqs_ready' || !$salesDo->delivery)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">👤 Assign Driver</h2>
                <form method="POST" action="{{ route('scm.task-board.assign-driver', $salesDo) }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Driver *</label>
                            <select name="driver_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                                <option value="">Select Driver</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ old('driver_id', $salesDo->delivery?->driver_id) == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }} - {{ $driver->phone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Vehicle Plate *</label>
                            <input type="text" name="vehicle_plate" value="{{ old('vehicle_plate', $salesDo->delivery?->vehicle_plate) }}" required
                                   placeholder="e.g., B 1234 XYZ"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Scheduled Date *</label>
                            <input type="date" name="scheduled_date" value="{{ old('scheduled_date', $salesDo->delivery?->scheduled_date) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Route Notes</label>
                        <textarea name="route_notes" rows="3" placeholder="Special instructions, route details, etc..."
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">{{ old('route_notes', $salesDo->delivery?->route_notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full px-4 py-3 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded-lg font-bold transition">
                        {{ $salesDo->delivery ? '🔄 Update Driver Assignment' : '✓ Assign Driver' }}
                    </button>
                </form>
            </div>
            @endif

            <!-- Start Delivery Form (if driver assigned but not started) -->
            @if($salesDo->delivery && $salesDo->status === 'wqs_ready')
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">🚀 Start Delivery</h2>
                <form method="POST" action="{{ route('scm.task-board.start-delivery', $salesDo) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Actual Departure *</label>
                            <input type="datetime-local" name="actual_departure" value="{{ old('actual_departure', now()->format('Y-m-d\TH:i')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Loading Photos</label>
                        <input type="file" name="loading_photos[]" multiple accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Upload photos of loaded goods (max 2MB each)</p>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Notes</label>
                        <textarea name="notes_scm" rows="3" placeholder="Additional notes for delivery..."
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">{{ old('notes_scm') }}</textarea>
                    </div>

                    <button type="submit" class="w-full px-4 py-3 bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-800 text-white rounded-lg font-bold transition">
                        🚀 Start Delivery
                    </button>
                </form>
            </div>
            @endif

            <!-- Complete Delivery Form (if on delivery) -->
            @if($salesDo->status === 'scm_on_delivery')
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">✅ Complete Delivery</h2>
                <form method="POST" action="{{ route('scm.task-board.complete-delivery', $salesDo) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Actual Arrival *</label>
                            <input type="datetime-local" name="actual_arrival" value="{{ old('actual_arrival', now()->format('Y-m-d\TH:i')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Received By *</label>
                            <input type="text" name="received_by" value="{{ old('received_by') }}" required
                                   placeholder="Name of receiver"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Received Position</label>
                            <input type="text" name="received_position" value="{{ old('received_position') }}"
                                   placeholder="e.g., Manager, Staff"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Delivery Photos</label>
                        <input type="file" name="delivery_photos[]" multiple accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Upload proof of delivery photos (max 2MB each)</p>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Signature *</label>
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-2 bg-white dark:bg-gray-700">
                            <canvas id="signatureCanvas" class="w-full h-40 border border-gray-200 dark:border-gray-600 rounded cursor-crosshair bg-white"></canvas>
                            <div class="flex gap-2 mt-2">
                                <button type="button" onclick="clearSignature()" class="px-3 py-1 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-900 dark:text-white rounded text-sm font-semibold transition">
                                    Clear
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="signature_data" id="signatureData" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">GPS Latitude</label>
                            <input type="number" step="any" name="gps_latitude" id="gps_latitude" value="{{ old('gps_latitude') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">GPS Longitude</label>
                            <input type="number" step="any" name="gps_longitude" id="gps_longitude" value="{{ old('gps_longitude') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        </div>
                    </div>
                    <button type="button" onclick="getLocation()" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        📍 Get Current Location
                    </button>

                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Delivery Notes</label>
                        <textarea name="delivery_notes" rows="3" placeholder="Any issues or special notes..."
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">{{ old('delivery_notes') }}</textarea>
                    </div>

                    <button type="submit" class="w-full px-4 py-3 bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-800 text-white rounded-lg font-bold transition">
                        ✅ Complete Delivery
                    </button>
                </form>
            </div>
            @endif

            <!-- Delivery Information (if completed) -->
            @if($salesDo->status === 'scm_delivered' && $salesDo->delivery)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">✅ Delivery Completed</h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Received By</p>
                            <p class="text-gray-900 dark:text-white font-semibold">{{ $salesDo->delivery->received_by }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Position</p>
                            <p class="text-gray-900 dark:text-white font-semibold">{{ $salesDo->delivery->received_position ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Actual Arrival</p>
                            <p class="text-gray-900 dark:text-white font-semibold">{{ $salesDo->delivery->actual_arrival ? \Carbon\Carbon::parse($salesDo->delivery->actual_arrival)->format('d M Y H:i') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Completed At</p>
                            <p class="text-gray-900 dark:text-white font-semibold">{{ $salesDo->delivery->completed_at?->format('d M Y H:i') ?? '-' }}</p>
                        </div>
                    </div>

                    @if($salesDo->delivery->delivery_notes)
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Delivery Notes</p>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                            <p class="text-gray-900 dark:text-white">{{ $salesDo->delivery->delivery_notes }}</p>
                        </div>
                    </div>
                    @endif

                    @if($salesDo->delivery->signature_path)
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Signature</p>
                        <img src="{{ Storage::url($salesDo->delivery->signature_path) }}" alt="Signature" class="border border-gray-300 dark:border-gray-600 rounded-lg max-w-xs">
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Items List -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📦 Delivery Items</h2>
                <div class="space-y-3">
                    @foreach($salesDo->items as $item)
                        <div class="border  dark:border-gray-600 rounded-lg p-4  dark:bg-gray-700/50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white">{{ $item->product->name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">SKU: {{ $item->product->sku }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Qty: <span class="font-bold text-gray-900 dark:text-white">{{ $item->qty_ordered }}</span> {{ $item->unit }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Price</p>
                                    <p class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Sidebar -->
        <div class="space-y-6">
            <!-- Customer Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">👤 Customer Information</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Name</p>
                        <p class="text-gray-900 dark:text-white font-semibold">{{ $salesDo->customer?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Address</p>
                        <p class="text-gray-900 dark:text-white">{{ $salesDo->customer?->address ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Phone</p>
                        <p class="text-gray-900 dark:text-white font-mono">{{ $salesDo->customer?->phone ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Driver Information -->
            @if($salesDo->delivery)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">🚗 Driver Information</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Driver</p>
                        <p class="text-gray-900 dark:text-white font-semibold">{{ $salesDo->delivery->driver?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Phone</p>
                        <p class="text-gray-900 dark:text-white font-mono">{{ $salesDo->delivery->driver?->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Vehicle</p>
                        <p class="text-gray-900 dark:text-white font-semibold">{{ $salesDo->delivery->vehicle_plate ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Scheduled</p>
                        <p class="text-gray-900 dark:text-white">{{ $salesDo->delivery->scheduled_date ? \Carbon\Carbon::parse($salesDo->delivery->scheduled_date)->format('d M Y') : '-' }}</p>
                    </div>
                    @if($salesDo->delivery->route_notes)
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Route Notes</p>
                        <p class="text-gray-900 dark:text-white text-xs">{{ $salesDo->delivery->route_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">⏱️ Timeline</h3>
                <div class="space-y-3">
                    @if($salesDo->delivery)
                        @if($salesDo->delivery->scheduled_date)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-2 h-2 mt-1.5 rounded-full bg-blue-500"></div>
                            <div class="text-sm">
                                <p class="text-gray-600 dark:text-gray-400">Scheduled</p>
                                <p class="text-gray-900 dark:text-white font-semibold">{{ \Carbon\Carbon::parse($salesDo->delivery->scheduled_date)->format('d M Y') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($salesDo->delivery->actual_departure)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-2 h-2 mt-1.5 rounded-full bg-orange-500"></div>
                            <div class="text-sm">
                                <p class="text-gray-600 dark:text-gray-400">Departed</p>
                                <p class="text-gray-900 dark:text-white font-semibold">{{ \Carbon\Carbon::parse($salesDo->delivery->actual_departure)->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($salesDo->delivery->actual_arrival)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-2 h-2 mt-1.5 rounded-full bg-green-500"></div>
                            <div class="text-sm">
                                <p class="text-gray-600 dark:text-gray-400">Arrived</p>
                                <p class="text-gray-900 dark:text-white font-semibold">{{ \Carbon\Carbon::parse($salesDo->delivery->actual_arrival)->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">No timeline available yet</p>
                    @endif
                </div>
            </div>

            <!-- Documents -->
            @if($salesDo->documents->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">📎 Documents</h3>
                <div class="space-y-2">
                    @foreach($salesDo->documents as $doc)
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <span class="text-blue-600 dark:text-blue-400">📄</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $doc->stage }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
// Signature Pad
let canvas = document.getElementById('signatureCanvas');
let signatureData = document.getElementById('signatureData');
let ctx = canvas ? canvas.getContext('2d') : null;
let isDrawing = false;

if (canvas && ctx) {
    // Set canvas size
    canvas.width = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;

    // Set drawing style
    ctx.strokeStyle = '#000000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';

    // Mouse events
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    // Touch events for mobile
    canvas.addEventListener('touchstart', handleTouchStart);
    canvas.addEventListener('touchmove', handleTouchMove);
    canvas.addEventListener('touchend', stopDrawing);

    function startDrawing(e) {
        isDrawing = true;
        ctx.beginPath();
        ctx.moveTo(e.offsetX, e.offsetY);
    }

    function draw(e) {
        if (!isDrawing) return;
        ctx.lineTo(e.offsetX, e.offsetY);
        ctx.stroke();
    }

    function stopDrawing() {
        if (isDrawing) {
            isDrawing = false;
            // Save signature as base64
            signatureData.value = canvas.toDataURL('image/png');
        }
    }

    function handleTouchStart(e) {
        e.preventDefault();
        const touch = e.touches[0];
        const rect = canvas.getBoundingClientRect();
        isDrawing = true;
        ctx.beginPath();
        ctx.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
    }

    function handleTouchMove(e) {
        e.preventDefault();
        if (!isDrawing) return;
        const touch = e.touches[0];
        const rect = canvas.getBoundingClientRect();
        ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
        ctx.stroke();
    }
}

function clearSignature() {
    if (ctx && canvas) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        signatureData.value = '';
    }
}

function getLocation() {
    if (navigator.geolocation) {
        // Show loading indicator
        const latInput = document.getElementById('gps_latitude');
        const lngInput = document.getElementById('gps_longitude');

        latInput.value = 'Getting location...';
        lngInput.value = 'Getting location...';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                // Success callback
                latInput.value = position.coords.latitude.toFixed(6);
                lngInput.value = position.coords.longitude.toFixed(6);

                // Show success message
                alert('Location captured successfully!');
            },
            function(error) {
                // Error callback
                latInput.value = '';
                lngInput.value = '';

                let errorMessage = 'Error getting location: ';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'User denied the request for Geolocation.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Location information is unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'The request to get user location timed out.';
                        break;
                    case error.UNKNOWN_ERROR:
                        errorMessage += 'An unknown error occurred.';
                        break;
                }
                alert(errorMessage);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

// Validate form before submit
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action*="complete-delivery"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const signature = signatureData.value;
            if (!signature || signature === '') {
                e.preventDefault();
                alert('Please provide a signature before submitting.');
                return false;
            }
        });
    }
});
</script>
@endpush
