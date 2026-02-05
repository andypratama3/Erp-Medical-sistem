@extends('layouts.app')

@section('title', 'Payments')

@section('content')
<x-common.page-breadcrumb pageTitle="Payments" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg p-4">
            <p class="text-xs text-green-600 dark:text-green-400 font-bold">Total Received</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($stats['total_received'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
            <p class="text-xs text-blue-600 dark:text-blue-400 font-bold">This Month</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($stats['this_month'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-700 rounded-lg p-4">
            <p class="text-xs text-purple-600 dark:text-purple-400 font-bold">Pending</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['pending_count'] ?? 0 }}</p>
        </div>
        <div class="bg-orange-50 dark:bg-orange-900/30 border border-orange-200 dark:border-orange-700 rounded-lg p-4">
            <p class="text-xs text-orange-600 dark:text-orange-400 font-bold">Completed</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['completed_count'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Payments Table -->
    <x-common.component-card title="Payment Records" desc="View all payment transactions">
        <div class="mb-4 flex flex-col sm:flex-row gap-3">
            <input type="text" placeholder="Search by invoice number, customer..."
                class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition flex-1">
            <select class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="verified">Verified</option>
                <option value="completed">Completed</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Payment Date</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Invoice</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Customer</th>
                        <th class="px-4 py-3 text-right text-gray-700 dark:text-gray-300 font-bold">Amount</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Method</th>
                        <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Status</th>
                        <th class="px-4 py-3 text-center text-gray-700 dark:text-gray-300 font-bold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition">
                        <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $payment->payment_date->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-sm text-blue-600 dark:text-blue-400">{{ $payment->invoice->invoice_number }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $payment->invoice->salesDO->customer->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">Rp {{ number_format($payment->payment_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ ucfirst($payment->payment_method) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                Recorded
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('fin.payments.show', $payment) }}" 
                                class="text-blue-600 dark:text-blue-400 hover:underline text-sm">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            No payments found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="mt-4">
            {{ $payments->links() }}
        </div>
        @endif
    </x-common.component-card>
</div>
@endsection
