@extends('layouts.app')

@section('title', 'Stock Check Detail - ' . $stockCheck->salesDO->do_code)

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">📋 Stock Check Detail</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">DO: <span class="font-mono font-semibold">{{ $stockCheck->salesDO->do_code }}</span> | Customer: <span class="font-semibold">{{ $stockCheck->salesDO->customer?->name }}</span></p>
    </div>

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('wqs.stock-checks.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-400 dark:bg-gray-700 hover:bg-gray-500 dark:hover:bg-gray-600 text-white rounded-lg font-semibold transition">
            ← Back to Stock Checks
        </a>
    </div>

    <x-flash-message.flash />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left: Main Content (2 columns) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📊 Check Status</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        <span class="inline-block mt-1 px-3 py-1 bg-{{ $stockCheck->status_color }}-100 dark:text-white text-dark rounded text-sm font-bold">
                            {{ $stockCheck->status_label }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Check Date</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $stockCheck->check_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Items</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $stockCheck->total_items }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Completion</p>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full" style="width: {{ $stockCheck->completion_percentage }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $stockCheck->completion_percentage }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📈 Check Statistics</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/30 rounded-lg">
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stockCheck->available_items }}</p>
                        <p class="text-sm text-green-800 dark:text-green-300 mt-1 font-semibold">Available</p>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg">
                        <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stockCheck->partial_items }}</p>
                        <p class="text-sm text-yellow-800 dark:text-yellow-300 mt-1 font-semibold">Partial</p>
                    </div>
                    <div class="text-center p-4 bg-red-50 dark:bg-red-900/30 rounded-lg">
                        <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $stockCheck->not_available_items }}</p>
                        <p class="text-sm text-red-800 dark:text-red-300 mt-1 font-semibold">Not Available</p>
                    </div>
                </div>
            </div>

            <!-- Check Notes -->
            @if($stockCheck->check_notes)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📝 Check Notes</h2>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $stockCheck->check_notes }}</p>
                </div>
            </div>
            @endif

            <!-- Items Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📦 Checked Items</h2>

                <div class="space-y-4">
                    @foreach($stockCheck->items as $item)
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 {{ $item->stock_status !== 'available' ? ' dark:bg-amber' : 'bg-gray-50 dark:bg-black' }}">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white">{{ $item->product->name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">SKU: {{ $item->product->sku }}</p>
                                    @php
                                        $doItem = $stockCheck->salesDO->items->firstWhere('product_id', $item->product_id);
                                    @endphp
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Ordered: <span class="font-bold">{{ $doItem?->qty_ordered ?? 0 }}</span> {{ $doItem?->unit ?? '' }}</p>
                                </div>
                                <span class="px-3 py-1 bg-{{ $item->stock_status === 'available' ? 'green' : ($item->stock_status === 'partial' ? 'yellow' : 'red') }}-100 dark:bg-{{ $item->stock_status === 'available' ? 'green' : ($item->stock_status === 'partial' ? 'yellow' : 'red') }}-900/30 text-black dark:text-white rounded text-sm font-bold">
                                    {{ $item->stock_status_label }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Available Quantity</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $item->available_qty }}</p>
                                </div>
                                @if($item->notes)
                                <div class="col-span-2">
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Notes</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $item->notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Sidebar -->
        <div class="space-y-6">
            <!-- Actions Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">⚡ Actions</h3>
                <div class="space-y-2">
                    @if($stockCheck->overall_status !== 'completed')
                        <a href="{{ route('wqs.stock-checks.edit', $stockCheck) }}" class="block w-full px-4 py-2 bg-orange-600 dark:bg-orange-700 hover:bg-orange-700 dark:hover:bg-orange-800 text-white rounded-lg text-sm font-bold transition text-center">
                            ✏️ Edit Check
                        </a>
                    @endif

                    @if($stockCheck->overall_status === 'checked')
                        <button onclick="openMarkFailedModal({{ $stockCheck->id }})" class="block w-full px-4 py-2 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-800 text-white rounded-lg text-sm font-bold transition text-center">
                            ✕ Mark Failed
                        </button>
                    @endif

                    <a href="{{ route('crm.sales-do.show', $stockCheck->salesDO) }}" class="block w-full px-4 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded-lg text-sm font-bold transition text-center">
                        📄 View DO
                    </a>
                </div>
            </div>

            <!-- Check Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">ℹ️ Check Information</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Checked By</p>
                        <p class="text-gray-900 dark:text-white font-semibold">{{ $stockCheck->checkedBy?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Check Date</p>
                        <p class="text-gray-900 dark:text-white font-semibold">{{ $stockCheck->check_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Created At</p>
                        <p class="text-gray-900 dark:text-white font-semibold">{{ $stockCheck->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($stockCheck->updated_at != $stockCheck->created_at)
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Last Updated</p>
                        <p class="text-gray-900 dark:text-white font-semibold">{{ $stockCheck->updated_at->format('d M Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- DO Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">📋 DO Information</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">DO Code</p>
                        <p class="text-gray-900 dark:text-white font-mono font-semibold">{{ $stockCheck->salesDO->do_code }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Customer</p>
                        <p class="text-gray-900 dark:text-white font-semibold">{{ $stockCheck->salesDO->customer?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Office</p>
                        <p class="text-gray-900 dark:text-white font-semibold">{{ $stockCheck->salesDO->office?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Grand Total</p>
                        <p class="text-gray-900 dark:text-white font-bold">Rp {{ number_format($stockCheck->salesDO->grand_total, 0, ',', '.') }}</p>
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
        </div>
    </div>
</div>

<!-- Mark Failed Modal -->
<div id="markFailedModal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl dark:shadow-2xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">✕ Mark Stock Check as Failed</h3>
        </div>

        <div class="p-6 space-y-4">
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg p-4">
                <p class="text-sm text-red-900 dark:text-red-200">
                    ⚠️ Marking this stock check as failed will require a new check to be performed.
                </p>
            </div>

            <form id="markFailedForm" method="POST" action="{{ route('wqs.stock-checks.mark-failed', $stockCheck) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Reason for Failure *</label>
                    <textarea name="reason" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400" placeholder="Explain why this stock check failed..." required></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeMarkFailedModal()" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-800 text-white rounded-lg font-semibold transition">
                        Mark Failed
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openMarkFailedModal(stockCheckId) {
    document.getElementById('markFailedModal').classList.remove('hidden');
}

function closeMarkFailedModal() {
    document.getElementById('markFailedModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('markFailedModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeMarkFailedModal();
    }
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeMarkFailedModal();
    }
});
</script>
@endpush

@endsection
