@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Sales DO Detail" />

<div class="space-y-6">
    <x-flash-message.flash />

    <!-- Header Section -->
    <x-common.component-card
        title="Sales DO List"
        desc="Detail Sales Delivery Order {{ $salesDo->do_code }}">

        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $salesDo->do_code }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tracking: {{ $salesDo->tracking_code }}</p>
            </div>
            <div class="flex gap-2">
                @if(in_array($salesDo->status, ['crm_to_wqs', 'wqs_on_hold']))
                    <a href="{{ route('crm.sales-do.edit', $salesDo) }}"
                        class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                        Edit
                    </a>
                @endif
                {{-- <a href="{{ route('crm.sales-do.exportPDF', $salesDo) }}"
                    class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-orange-400 text-white shadow-theme-xs hover:bg-orange-600">
                    Export PDF
                    <i class=""></i>
                </a> --}}
            </div>
        </div>

        <!-- Status Badge -->
        <div class="mb-6">
            <div class="mb-6">
                <x-ui.badge
                    size="md"
                    variant="light"
                    :class="$salesDo->status_config['badge_class']"
                >
                    {{ $salesDo->status_config['label'] }}
                </x-ui.badge>
            </div>

        </div>
    </x-common.component-card>

    <!-- Customer & Shipping Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-common.component-card title="Customer Information">
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Customer</p>
                    <p class="text-base font-medium text-gray-800 dark:text-white">
                        <a href="{{ route('master.customers.show', $salesDo->customer) }}" class="text-blue-600 hover:underline">
                            {{ $salesDo->customer->name }}
                        </a>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">PIC Customer</p>
                    <p class="text-base font-medium text-gray-800 dark:text-white">{{ $salesDo->pic_customer ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Office</p>
                    <p class="text-base font-medium text-gray-800 dark:text-white">{{ $salesDo->office->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">DO Date</p>
                    <p class="text-base font-medium text-gray-800 dark:text-white">
                        {{ $salesDo->do_date->format('d M Y') }}
                    </p>
                </div>
            </div>
        </x-common.component-card>

        <x-common.component-card title="Shipping Information">
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Shipping Address</p>
                    <p class="text-base font-medium text-gray-800 dark:text-white">{{ $salesDo->shipping_address }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Payment Term</p>
                    <p class="text-base font-medium text-gray-800 dark:text-white">
                        {{ $salesDo->paymentTerm->name ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tax</p>
                    <p class="text-base font-medium text-gray-800 dark:text-white">
                        {{ $salesDo->tax->name ?? 'No Tax' }}
                        @if($salesDo->tax)
                            ({{ $salesDo->tax->rate }}%)
                        @endif
                    </p>
                </div>
            </div>
        </x-common.component-card>
    </div>

    <!-- Items Table -->
    <x-common.component-card title="Order Items">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">#</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Product</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">SKU</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Qty Ordered</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Qty Delivered</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Unit Price</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Discount</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salesDo->items as $item)
                        <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ $item->line_number }}</td>
                            <td class="py-3 px-4 text-gray-800 dark:text-white font-medium">{{ $item->product_name }}</td>
                            <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ $item->product_sku }}</td>
                            <td class="py-3 px-4 text-right text-gray-800 dark:text-white">{{ $item->qty_ordered }}</td>
                            <td class="py-3 px-4 text-right text-gray-800 dark:text-white">{{ $item->qty_delivered }}</td>
                            <td class="py-3 px-4 text-right text-gray-800 dark:text-white">
                                Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right text-gray-800 dark:text-white">
                                {{ $item->discount_percent }}%
                                <span class="text-gray-500 dark:text-gray-400 text-xs">(Rp {{ number_format($item->discount_amount, 0, ',', '.') }})</span>
                            </td>
                            <td class="py-3 px-4 text-right text-gray-800 dark:text-white font-semibold">
                                Rp {{ number_format($item->line_total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-common.component-card>

    <!-- Summary Section -->
    <x-common.component-card title="Summary Sales DO">
        <div class="space-y-3 max-w-xs ml-auto">
            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($salesDo->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Tax:</span>
                <span>Rp {{ number_format($salesDo->tax_amount, 0, ',', '.') }}</span>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex justify-between text-lg font-semibold text-gray-800 dark:text-white">
                <span>Grand Total:</span>
                <span class="text-blue-600">Rp {{ number_format($salesDo->grand_total, 0, ',', '.') }}</span>
            </div>
        </div>
    </x-common.component-card>

    <!-- Additional Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if($salesDo->notes_crm)
            <x-common.component-card title="CRM Notes">
                <p class="text-gray-800 dark:text-white">{{ $salesDo->notes_crm }}</p>
            </x-common.component-card>
        @endif

        <x-common.component-card title="Metadata">
            <div class="space-y-4">
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Created By</p>
                    <p class="text-gray-800 dark:text-white font-medium">{{ $salesDo->createdBy->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Created At</p>
                    <p class="text-gray-800 dark:text-white font-medium">{{ $salesDo->created_at->format('d M Y H:i') }}</p>
                </div>
                @if($salesDo->updatedBy)
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Last Updated By</p>
                        <p class="text-gray-800 dark:text-white font-medium">{{ $salesDo->updatedBy->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Last Updated At</p>
                        <p class="text-gray-800 dark:text-white font-medium">{{ $salesDo->updated_at->format('d M Y H:i') }}</p>
                    </div>
                @endif
            </div>
        </x-common.component-card>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-3">
        <a href="{{ route('crm.sales-do.index') }}"
            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03]">
            Back
        </a>
        @if(in_array($salesDo->status, ['crm_to_wqs', 'wqs_on_hold']))
            <form method="POST" action="{{ route('crm.sales-do.destroy', $salesDo) }}" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Are you sure?')"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                    Delete
                </button>
            </form>

            <a href="{{ route('crm.sales-do.edit', $salesDo) }}"
                class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                Edit
            </a>
            <button type="button" onclick="openSubmitModal()" class="inline-flex items-center px-4 py-2 bg-green-600 text-black dark:text-white rounded-lg hover:bg-green-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Submit to WQS
            </button>
        @endif
    </div>

    <div id="submitModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Submit to WQS?</h3>
            </div>

            <div class="p-6 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-900">
                        <strong>Please confirm:</strong> You are about to submit this Sales DO to the WQS (Warehouse Quality System) module. This action will change the status from "CRM to WQS" to "WQS Ready" and cannot be undone.
                    </p>
                </div>

                <div class="space-y-2">
                    <h4 class="font-semibold text-gray-900 text-sm">Summary:</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• DO Code: <span class="font-mono font-semibold">{{ $salesDo->do_code }}</span></li>
                        <li>• Customer: <span class="font-semibold">{{ $salesDo->customer?->name ?? '-' }}</span></li>
                        <li>• Total Items: <span class="font-semibold">{{ $salesDo->items->count() }}</span></li>
                        <li>• Grand Total: <span class="font-semibold">Rp {{ number_format($salesDo->grand_total, 0, ',', '.') }}</span></li>
                    </ul>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex gap-3">
                <button type="button" onclick="closeSubmitModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition">
                    Cancel
                </button>
                <form id="submitForm" action="{{ route('crm.sales-do.submit', $salesDo) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                        Confirm Submit
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    function openSubmitModal() {
        document.getElementById('submitModal').classList.remove('hidden');
    }

    function closeSubmitModal() {
        document.getElementById('submitModal').classList.add('hidden');
    }

    function openDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('submitModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeSubmitModal();
    });

    document.getElementById('deleteModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
    </script>
@endpush
@endsection
