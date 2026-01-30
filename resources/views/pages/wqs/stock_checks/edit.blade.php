@extends('layouts.app')

@section('title', 'Edit Stock Check - ' . $stockCheck->salesDO->do_code)

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">✏️ Edit Stock Check</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">DO: <span class="font-mono font-semibold">{{ $stockCheck->salesDO->do_code }}</span> | Customer: <span class="font-semibold">{{ $stockCheck->salesDO->customer?->name }}</span></p>
    </div>

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('wqs.stock-checks.show', $stockCheck) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-400 dark:bg-gray-700 hover:bg-gray-500 dark:hover:bg-gray-600 text-white rounded-lg font-semibold transition">
            ← Back to Details
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-flash-message.flash />

        <!-- Left: Form (2 columns) -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('wqs.stock-checks.update', $stockCheck) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Check Information Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">ℹ️ Check Information</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Check Date</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $stockCheck->check_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Current Status</p>
                            <span class="inline-block mt-1 px-3 py-1 bg-{{ $stockCheck->status_color }}-100 dark:bg-{{ $stockCheck->status_color }}-900 text-{{ $stockCheck->status_color }}-800 dark:text-{{ $stockCheck->status_color }}-200 rounded text-sm font-bold">
                                {{ $stockCheck->status_label }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Check Notes -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                    <label class="block text-lg font-bold text-gray-900 dark:text-white mb-4">📝 Check Notes</label>
                    <textarea
                        name="check_notes"
                        rows="4"
                        placeholder="Tambahkan catatan pengecekan stock..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
                    >{{ old('check_notes', $stockCheck->check_notes) }}</textarea>
                </div>

                <!-- Items Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📦 Items to Update</h2>

                    <div id="itemsContainer" class="space-y-4">
                        @foreach($stockCheck->items as $index => $checkItem)
                            @php
                                $doItem = $stockCheck->salesDO->items->firstWhere('product_id', $checkItem->product_id);
                            @endphp
                            <div class="dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-4" data-item-index="{{ $index }}">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-bold text-gray-900 dark:text-white">{{ $checkItem->product->name }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">SKU: {{ $checkItem->product->sku }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Ordered Qty: <span class="font-bold">{{ $doItem?->qty_ordered ?? 0 }}</span> {{ $doItem?->unit ?? '' }}</p>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <input type="hidden" name="items[{{ $index }}][stock_check_item_id]" value="{{ $checkItem->id }}">
                                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $checkItem->product_id }}">

                                    <!-- Stock Status -->
                                    <div>
                                        <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Stock Status</label>
                                        <div class="grid grid-cols-3 gap-2">
                                            <label class="flex items-center p-2 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                                <input type="radio" name="items[{{ $index }}][stock_status]" value="available" {{ old("items.$index.stock_status", $checkItem->stock_status) === 'available' ? 'checked' : '' }} class="mr-2">
                                                <span class="text-sm text-gray-700 dark:text-gray-300 font-semibold">✓ Available</span>
                                            </label>
                                            <label class="flex items-center p-2 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                                <input type="radio" name="items[{{ $index }}][stock_status]" value="partial" {{ old("items.$index.stock_status", $checkItem->stock_status) === 'partial' ? 'checked' : '' }} class="mr-2">
                                                <span class="text-sm text-gray-700 dark:text-gray-300 font-semibold">⚠️ Partial</span>
                                            </label>
                                            <label class="flex items-center p-2 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                                <input type="radio" name="items[{{ $index }}][stock_status]" value="not_available" {{ old("items.$index.stock_status", $checkItem->stock_status) === 'not_available' ? 'checked' : '' }} class="mr-2">
                                                <span class="text-sm text-gray-700 dark:text-gray-300 font-semibold">✕ Not Available</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Available Qty -->
                                    <div class="mt-2">
                                        <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Available Qty</label>
                                        <input
                                            type="number"
                                            name="items[{{ $index }}][available_qty]"
                                            value="{{ old("items.$index.available_qty", $checkItem->available_qty) }}"
                                            min="0"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
                                            required
                                        >
                                    </div>

                                    <!-- Notes -->
                                    <div class="mt-2">
                                        <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Notes</label>
                                        <input
                                            type="text"
                                            name="items[{{ $index }}][notes]"
                                            value="{{ old("items.$index.notes", $checkItem->notes) }}"
                                            placeholder="e.g., Damaged, Expired, Location..."
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
                                        >
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <a href="{{ route('wqs.stock-checks.show', $stockCheck) }}" class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition text-center">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded-lg font-bold transition">
                        💾 Update Stock Check
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Sidebar -->
        <div class="space-y-6">
            <!-- Instructions -->
            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-6">
                <h3 class="font-bold text-blue-900 dark:text-blue-200 mb-3">ℹ️ Instructions</h3>
                <ul class="space-y-2 text-sm text-blue-800 dark:text-blue-300">
                    <li>✓ Review dan update status item</li>
                    <li>✓ Perbarui jumlah yang tersedia</li>
                    <li>✓ Tambahkan catatan tambahan</li>
                    <li>✓ Save untuk menyimpan perubahan</li>
                </ul>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-3">⚡ Quick Actions</h3>
                <div class="space-y-2">
                    <button type="button" onclick="setAllAvailable()" class="w-full px-3 py-2 bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-800 text-white rounded-lg text-sm font-bold transition">
                        Mark All Available
                    </button>
                    <button type="button" onclick="markAllZero()" class="w-full px-3 py-2 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-800 text-white rounded-lg text-sm font-bold transition">
                        Mark All Not Available
                    </button>
                </div>
            </div>

            <!-- Current Statistics -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-3">📊 Current Statistics</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Total Items</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $stockCheck->total_items }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Available</span>
                        <span class="font-bold text-green-600 dark:text-green-400">{{ $stockCheck->available_items }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Partial</span>
                        <span class="font-bold text-yellow-600 dark:text-yellow-400">{{ $stockCheck->partial_items }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Not Available</span>
                        <span class="font-bold text-red-600 dark:text-red-400">{{ $stockCheck->not_available_items }}</span>
                    </div>
                </div>
            </div>

            <!-- Status Legend -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-3">📊 Status Legend</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-white rounded text-xs font-bold">Available</span>
                        <span class="text-gray-600 dark:text-gray-400">Stock tersedia</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-white rounded text-xs font-bold">Partial</span>
                        <span class="text-gray-600 dark:text-gray-400">Stock tidak lengkap</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-white rounded text-xs font-bold">Not Available</span>
                        <span class="text-gray-600 dark:text-gray-400">Stock tidak ada</span>
                    </div>
                </div>
            </div>

            <!-- DO Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-3">📋 DO Information</h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">DO Code</p>
                        <p class="text-gray-900 dark:text-white font-mono font-semibold">{{ $stockCheck->salesDO->do_code }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Customer</p>
                        <p class="text-gray-900 dark:text-white font-semibold">{{ $stockCheck->salesDO->customer?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Grand Total</p>
                        <p class="text-gray-900 dark:text-white font-bold">Rp {{ number_format($stockCheck->salesDO->grand_total, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function setAllAvailable() {
    document.querySelectorAll('input[value="available"]').forEach(radio => {
        radio.checked = true;
    });
    document.querySelectorAll('input[name*="[available_qty]"]').forEach(input => {
        const container = input.closest('[data-item-index]');
        const orderedQtyText = container.querySelector('p:nth-of-type(3)')?.textContent;
        if (orderedQtyText) {
            const match = orderedQtyText.match(/Ordered Qty:\s*(\d+)/);
            if (match) {
                input.value = parseInt(match[1]);
            }
        }
    });
}

function markAllZero() {
    document.querySelectorAll('input[value="not_available"]').forEach(radio => {
        radio.checked = true;
    });
    document.querySelectorAll('input[name*="[available_qty]"]').forEach(input => {
        input.value = 0;
    });
}
</script>
@endsection

@endsection 
