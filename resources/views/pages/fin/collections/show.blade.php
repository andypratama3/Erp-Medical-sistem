@extends('layouts.app')

@section('title', 'Collection Details')

@section('content')
<x-common.page-breadcrumb pageTitle="Collection Details" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card title="Collection Information">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Collection Date</p>
                <p class="text-gray-900 dark:text-white font-semibold">{{ $collection->collection_date->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Amount Collected</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($collection->amount_collected, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Payment Method</p>
                <p class="text-gray-900 dark:text-white font-semibold">{{ ucfirst(str_replace('_', ' ', $collection->payment_method)) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Payment Reference</p>
                <p class="text-gray-900 dark:text-white font-mono">{{ $collection->payment_reference ?? '-' }}</p>
            </div>
            @if($collection->notes)
            <div class="md:col-span-2">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Notes</p>
                <p class="text-gray-900 dark:text-white">{{ $collection->notes }}</p>
            </div>
            @endif
        </div>
    </x-common.component-card>

    <x-common.component-card title="Related Invoice">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Invoice Number</p>
                <a href="{{ route('act.invoices.show', $collection->invoice) }}" 
                    class="text-blue-600 dark:text-blue-400 hover:underline font-mono font-semibold">
                    {{ $collection->invoice->invoice_number }}
                </a>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Customer</p>
                <p class="text-gray-900 dark:text-white font-semibold">{{ $collection->invoice->salesDO->customer->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Invoice Total</p>
                <p class="text-gray-900 dark:text-white font-bold">{{ $collection->invoice->formatted_total }}</p>
            </div>
        </div>
    </x-common.component-card>

    <div class="flex justify-end gap-3">
        <a href="{{ route('fin.collections.index') }}" 
            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03] transition text-sm">
            Back to List
        </a>
    </div>
</div>
@endsection
