@extends('layouts.app')

@section('title', 'Delivery Detail - ' . $delivery->salesDo->do_code)

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">📦 Delivery Detail</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">DO: <span class="font-mono font-semibold">{{ $delivery->salesDo->do_code }}</span></p>
    </div>

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('scm.deliveries.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-400 dark:bg-gray-700 hover:bg-gray-500 dark:hover:bg-gray-600 text-white rounded-lg font-semibold transition">
            ← Back to Deliveries
        </a>
    </div>

    <x-flash-message.flash />

    <div class="grid sm:grid-cols-2 gap-6">
        <!-- Left: Main Content (2 columns) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Delivery Status -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📊 Delivery Status</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        @if($delivery->delivery_status === 'scheduled')
                            <span class="inline-block mt-1 px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-sm font-bold">
                                Scheduled
                            </span>
                        @elseif($delivery->delivery_status === 'on_delivery')
                            <span class="inline-block mt-1 px-3 py-1 bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 rounded text-sm font-bold">
                                On Delivery
                            </span>
                        @elseif($delivery->delivery_status === 'delivered')
                            <span class="inline-block mt-1 px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded text-sm font-bold">
                                Delivered
                            </span>
                        @elseif($delivery->delivery_status === 'failed')
                            <span class="inline-block mt-1 px-3 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded text-sm font-bold">
                                Failed
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Delivery Date</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                            {{ $delivery->delivery_date ? \Carbon\Carbon::parse($delivery->delivery_date)->format('d M Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Items</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $delivery->salesDo->items->count() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Grand Total</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($delivery->salesDo->grand_total, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📍 Delivery Information</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Delivery Address</p>
                        <p class="text-gray-900 dark:text-white">{{ $delivery->delivery_address }}</p>
                    </div>

                    @if($delivery->delivery_notes)
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Delivery Notes</p>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                            <p class="text-gray-900 dark:text-white">{{ $delivery->delivery_notes }}</p>
                        </div>
                    </div>
                    @endif

                    @if($delivery->received_by)
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Received By</p>
                            <p class="text-gray-900 dark:text-white font-semibold">{{ $delivery->received_by }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Received At</p>
                            <p class="text-gray-900 dark:text-white font-semibold">
                                {{ $delivery->received_at ? \Carbon\Carbon::parse($delivery->received_at)->format('d M Y H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Mark as Delivered Form (if not delivered yet) -->
            @if($delivery->delivery_status !== 'delivered')
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">✅ Mark as Delivered</h2>
                <form method="POST" action="{{ route('scm.deliveries.delivered', $delivery) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Received By *</label>
                            <input type="text" name="received_by" value="{{ old('received_by') }}" required
                                   placeholder="Name of receiver"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Received At *</label>
                            <input type="datetime-local" name="received_at" value="{{ old('received_at', now()->format('Y-m-d\TH:i')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Signature</label>
                        <input type="file" name="signature" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Upload recipient signature (max 2MB)</p>
                    </div>

                    <button type="submit" class="w-full px-4 py-3 bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-800 text-white rounded-lg font-bold transition">
                        ✅ Mark as Delivered
                    </button>
                </form>
            </div>
            @endif

            <!-- Delivery Items -->
            <div class="w-full dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📦 Delivery Items</h2>
                <div class="space-y-3">
                    @foreach($delivery->salesDo->items as $item)
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 dark:bg-gray-700/50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white">{{ $item->product->name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">SKU: {{ $item->product->sku }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Qty: <span class="font-bold text-gray-900 dark:text-white">{{ $item->qty_ordered }}</span> {{ $item->unit }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Unit Price</p>
                                    <p class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Subtotal</p>
                                    <p class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <p class="text-lg font-bold text-gray-900 dark:text-white">Grand Total</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($delivery->salesDo->grand_total, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            @if($delivery->documents && $delivery->documents->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📎 Uploaded Documents</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($delivery->documents as $doc)
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <span class="text-2xl">
                                @if(Str::contains($doc->stage, 'loading'))
                                    📸
                                @elseif(Str::contains($doc->stage, 'proof'))
                                    ✅
                                @elseif(Str::contains($doc->stage, 'signature'))
                                    ✍️
                                @else
                                    📄
                                @endif
                            </span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $doc->stage }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $doc->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right: Sidebar -->
        <div class="space-y-6">
            <!-- Customer Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">👤 Customer Information</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Name</p>
                        <p class="text-gray-900 dark:text-white font-semibold">{{ $delivery->salesDo->customer?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Address</p>
                        <p class="text-gray-900 dark:text-white">{{ $delivery->salesDo->customer?->address ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Phone</p>
                        <p class="text-gray-900 dark:text-white font-mono">{{ $delivery->salesDo->customer?->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Email</p>
                        <p class="text-gray-900 dark:text-white">{{ $delivery->salesDo->customer?->email ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Driver Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">🚗 Driver Information</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Driver Name</p>
                        <p class="text-gray-900 dark:text-white font-semibold">{{ $delivery->driver?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Phone</p>
                        <p class="text-gray-900 dark:text-white font-mono">{{ $delivery->driver?->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">License Number</p>
                        <p class="text-gray-900 dark:text-white font-mono">{{ $delivery->driver?->license_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Status</p>
                        @if($delivery->driver?->status === 'active')
                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded text-xs font-bold">
                                Active
                            </span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded text-xs font-bold">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- DO Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">📋 DO Information</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">DO Code</p>
                        <p class="text-gray-900 dark:text-white font-mono font-semibold">{{ $delivery->salesDo->do_code }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">DO Date</p>
                        <p class="text-gray-900 dark:text-white">{{ $delivery->salesDo->do_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Office</p>
                        <p class="text-gray-900 dark:text-white">{{ $delivery->salesDo->office?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Created By</p>
                        <p class="text-gray-900 dark:text-white">{{ $delivery->salesDo->createdBy?->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">⏱️ Timeline</h3>
                <div class="space-y-3">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-2 h-2 mt-1.5 rounded-full bg-blue-500"></div>
                        <div class="text-sm">
                            <p class="text-gray-600 dark:text-gray-400">Created</p>
                            <p class="text-gray-900 dark:text-white font-semibold">{{ $delivery->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    @if($delivery->delivery_date)
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-2 h-2 mt-1.5 rounded-full bg-orange-500"></div>
                        <div class="text-sm">
                            <p class="text-gray-600 dark:text-gray-400">Scheduled</p>
                            <p class="text-gray-900 dark:text-white font-semibold">{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('d M Y') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($delivery->received_at)
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-2 h-2 mt-1.5 rounded-full bg-green-500"></div>
                        <div class="text-sm">
                            <p class="text-gray-600 dark:text-gray-400">Delivered</p>
                            <p class="text-gray-900 dark:text-white font-semibold">{{ \Carbon\Carbon::parse($delivery->received_at)->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class=" dark:bg-gray-800 rounded-lg dark:text-white shadow dark:shadow-xl p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">⚡ Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('crm.sales-do.show', $delivery->salesDo) }}" class="block w-full px-4 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-dark dark:text-white rounded-lg text-sm font-bold transition text-center">
                        📄 View Full DO
                    </a>
                    <a href="{{ route('scm.task-board.show', $delivery->salesDo) }}" class="block w-full px-4 py-2 bg-gray-600 dark:bg-gray-700 dark:hover:bg-gray-700 dark:hover:bg-gray-600 text-dark dark:text-white rounded-lg text-sm font-bold transition text-center">
                        🚚 View Task
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
