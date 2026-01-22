@extends('layouts.app')

@section('title', 'Stock Check - ' . $salesDo->do_code)

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">✓ Stock Check</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">DO: <span class="font-mono font-semibold">{{ $salesDo->do_code }}</span> | Customer: <span class="font-semibold">{{ $salesDo->customer?->name }}</span></p>
    </div>

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('wqs.task-board') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-400 dark:bg-gray-700 hover:bg-gray-500 dark:hover:bg-gray-600 text-white rounded-lg font-semibold transition">
            Back to Task Board
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('wqs.stock-checks.store') }}" class="space-y-6">
                @csrf

                <!-- DO Summary Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📋 DO Summary</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Items</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $salesDo->items->count() }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Grand Total</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($salesDo->grand_total, 0, ',', '.') }}</p>
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
                    ></textarea>
                </div>

                <!-- Items Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📦 Items to Check</h2>

                    <input type="hidden" name="sales_do_id" value="{{ $salesDo->id }}">

                    <div id="itemsContainer" class="space-y-4">
                        @foreach($salesDo->items as $index => $item)
                            <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-4" data-item-index="{{ $index }}">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-bold text-gray-900 dark:text-white">{{ $item->product->name }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">SKU: {{ $item->product->sku }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Ordered Qty: <span class="font-bold">{{ $item->qty_ordered }}</span> {{ $item->unit }}</p>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">

                                    <!-- Stock Status -->
                                    <div>
                                        <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Stock Status</label>
                                        <div class="grid grid-cols-3 gap-2">
                                            <label class="flex items-center p-2 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                                <input type="radio" name="items[{{ $index }}][stock_status]" value="available" checked class="mr-2">
                                                <span class="text-sm text-gray-700 dark:text-gray-300 font-semibold">✓ Available</span>
                                            </label>
                                            <label class="flex items-center p-2 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                                <input type="radio" name="items[{{ $index }}][stock_status]" value="partial" class="mr-2">
                                                <span class="text-sm text-gray-700 dark:text-gray-300 font-semibold">⚠️ Partial</span>
                                            </label>
                                            <label class="flex items-center p-2 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                                <input type="radio" name="items[{{ $index }}][stock_status]" value="not_available" class="mr-2">
                                                <span class="text-sm text-gray-700 dark:text-gray-300 font-semibold">✕ Not Available</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Available Qty -->
                                    <div>
                                        <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Available Qty</label>
                                        <input
                                            type="number"
                                            name="items[{{ $index }}][available_qty]"
                                            value="{{ $item->qty_ordered }}"
                                            min="0"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
                                            required
                                        >
                                    </div>

                                    <!-- Notes -->
                                    <div>
                                        <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Notes</label>
                                        <input
                                            type="text"
                                            name="items[{{ $index }}][notes]"
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
                    <a href="{{ route('wqs.task-board') }}" class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition text-center">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 px-4 py-3 bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-800 text-white rounded-lg font-bold transition">
                        ✓ Complete Stock Check
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
                    <li>✓ Check setiap item dari DO</li>
                    <li>✓ Tentukan status ketersediaan</li>
                    <li>✓ Input jumlah yang tersedia</li>
                    <li>✓ Tambahkan catatan jika perlu</li>
                    <li>✓ Submit untuk validasi</li>
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

            <!-- Status Legend -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-3">📊 Status Legend</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded text-xs font-bold">Available</span>
                        <span class="text-gray-600 dark:text-gray-400">Semua stock tersedia</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded text-xs font-bold">Partial</span>
                        <span class="text-gray-600 dark:text-gray-400">Stock tidak lengkap</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded text-xs font-bold">Not Available</span>
                        <span class="text-gray-600 dark:text-gray-400">Stock tidak ada</span>
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
        const originalQty = container.querySelector('p:nth-child(4)')?.textContent;
        if (originalQty) {
            const qty = parseInt(originalQty.match(/\d+/)[0]);
            input.value = qty;
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
