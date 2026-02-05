@extends('layouts.app')

@section('title', 'Accounts Receivable Aging')

@section('content')
<x-common.page-breadcrumb pageTitle="AR Aging Report" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <!-- Aging Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg p-4">
            <p class="text-xs text-green-600 dark:text-green-400 font-semibold mb-1">Current</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $agingSummary['current'] }}</p>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
            <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold mb-1">1-30 Days</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $agingSummary['1_30'] }}</p>
        </div>
        <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
            <p class="text-xs text-yellow-600 dark:text-yellow-400 font-semibold mb-1">31-60 Days</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $agingSummary['31_60'] }}</p>
        </div>
        <div class="bg-orange-50 dark:bg-orange-900/30 border border-orange-200 dark:border-orange-700 rounded-lg p-4">
            <p class="text-xs text-orange-600 dark:text-orange-400 font-semibold mb-1">61-90 Days</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $agingSummary['61_90'] }}</p>
        </div>
        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg p-4">
            <p class="text-xs text-red-600 dark:text-red-400 font-semibold mb-1">Over 90 Days</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $agingSummary['over_90'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <x-common.component-card title="Filters">
        <form method="GET" action="{{ route('fin.aging.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Aging Period</label>
                <select name="aging" 
                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
                    <option value="">All Periods</option>
                    <option value="current" {{ request('aging') == 'current' ? 'selected' : '' }}>Current</option>
                    <option value="1_30" {{ request('aging') == '1_30' ? 'selected' : '' }}>1-30 Days</option>
                    <option value="31_60" {{ request('aging') == '31_60' ? 'selected' : '' }}>31-60 Days</option>
                    <option value="61_90" {{ request('aging') == '61_90' ? 'selected' : '' }}>61-90 Days</option>
                    <option value="over_90" {{ request('aging') == 'over_90' ? 'selected' : '' }}>Over 90 Days</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" 
                    class="h-10 px-4 rounded-lg bg-brand-500 text-white hover:bg-brand-600 transition text-sm font-medium">
                    Apply Filters
                </button>
                <a href="{{ route('fin.aging.index') }}" 
                    class="h-10 px-4 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03] transition text-sm font-medium flex items-center">
                    Reset
                </a>
            </div>
        </form>
    </x-common.component-card>

    <!-- Aging Report Table -->
    <x-common.component-card title="Unpaid Invoices" desc="Accounts receivable aging report">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Invoice #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Invoice Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Due Date</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Amount</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Days Overdue</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($invoices as $invoice)
                        @php
                            $daysOverdue = now()->diffInDays($invoice->due_date, false);
                            $daysOverdue = $daysOverdue < 0 ? abs($daysOverdue) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition">
                            <td class="px-4 py-3">
                                <span class="font-mono text-sm text-blue-600 dark:text-blue-400">{{ $invoice->invoice_number }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $invoice->salesDo?->customer?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $invoice->invoice_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $invoice->due_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($daysOverdue > 0)
                                    <span class="text-red-600 dark:text-red-400 font-semibold">{{ $daysOverdue }} days</span>
                                @else
                                    <span class="text-green-600 dark:text-green-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($daysOverdue == 0)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Current</span>
                                @elseif($daysOverdue <= 30)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">1-30 Days</span>
                                @elseif($daysOverdue <= 60)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">31-60 Days</span>
                                @elseif($daysOverdue <= 90)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">61-90 Days</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Over 90</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('act.invoices.show', $invoice) }}" 
                                    class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No unpaid invoices found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($invoices->hasPages())
            <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                {{ $invoices->links() }}
            </div>
        @endif
    </x-common.component-card>
</div>
@endsection
