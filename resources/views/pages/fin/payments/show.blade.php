@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')
<x-common.page-breadcrumb pageTitle="Payment Details" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <!-- Payment Info -->
    <x-common.component-card title="Payment Information">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Payment Date</p>
                <p class="text-gray-900 dark:text-white font-semibold">{{ $payment->payment_date->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Amount</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($payment->payment_amount, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Payment Method</p>
                <p class="text-gray-900 dark:text-white font-semibold">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Reference Number</p>
                <p class="text-gray-900 dark:text-white font-mono">{{ $payment->reference_number ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Recorded By</p>
                <p class="text-gray-900 dark:text-white font-semibold">{{ $payment->recorder->name ?? '-' }}</p>
            </div>
            @if($payment->notes)
            <div class="md:col-span-2">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Notes</p>
                <p class="text-gray-900 dark:text-white">{{ $payment->notes }}</p>
            </div>
            @endif
        </div>
    </x-common.component-card>

    <!-- Invoice Details -->
    <x-common.component-card title="Related Invoice">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Invoice Number</p>
                <a href="{{ route('act.invoices.show', $payment->invoice) }}" 
                    class="text-blue-600 dark:text-blue-400 hover:underline font-mono font-semibold">
                    {{ $payment->invoice->invoice_number }}
                </a>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Customer</p>
                <p class="text-gray-900 dark:text-white font-semibold">{{ $payment->invoice->salesDO->customer->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Invoice Total</p>
                <p class="text-gray-900 dark:text-white font-bold">{{ $payment->invoice->formatted_total }}</p>
            </div>
        </div>
    </x-common.component-card>

    <!-- Payment Receipt -->
    @if($payment->payment_date)
    <x-common.component-card title="Payment Receipt">
        <div class="bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Payment Receipt</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $payment->payment_date->format('d F Y') }}</p>
            </div>
            <div class="space-y-3 max-w-md mx-auto">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Amount Paid:</span>
                    <span class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($payment->payment_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Payment Method:</span>
                    <span class="text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Reference:</span>
                    <span class="text-gray-900 dark:text-white font-mono">{{ $payment->reference_number ?? '-' }}</span>
                </div>
            </div>
            <div class="mt-6 text-center">
                <button onclick="window.print()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                    Print Receipt
                </button>
            </div>
        </div>
    </x-common.component-card>
    @endif

    <!-- Actions -->
    <div class="flex justify-end gap-3">
        <a href="{{ route('fin.payments.index') }}" 
            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03] transition text-sm">
            Back to List
        </a>
    </div>
</div>
@endsection
