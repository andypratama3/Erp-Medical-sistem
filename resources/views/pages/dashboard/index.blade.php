@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Dashboard" />

<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Total DO --}}
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total DO</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ number_format($stats['total_do']) }}
                    </p>
                </div>
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-500/20">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pending DO --}}
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending DO</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ number_format($stats['pending_do']) }}
                    </p>
                </div>
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-500/20">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- On Delivery --}}
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">On Delivery</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ number_format($stats['on_delivery']) }}
                    </p>
                </div>
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-500/20">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pending Invoice --}}
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending Invoice</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ number_format($stats['pending_invoice']) }}
                    </p>
                </div>
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-500/20">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pending Collection --}}
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending Collection</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ number_format($stats['pending_collection']) }}
                    </p>
                </div>
                <div class="p-3 rounded-full bg-orange-100 dark:bg-orange-500/20">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Overdue Invoices --}}
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Overdue Invoices</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ number_format($stats['overdue_invoices']) }}
                    </p>
                </div>
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-500/20">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Sales DO --}}
    <x-common.component-card title="Recent Sales DO">
        @if($recent_dos->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">No recent sales orders.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3">DO Number</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Office</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent_dos as $do)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-3 font-medium">{{ $do->do_number }}</td>
                            <td class="px-4 py-3">{{ $do->customer->name }}</td>
                            <td class="px-4 py-3">{{ $do->office->name }}</td>
                            <td class="px-4 py-3">{{ $do->formatted_total }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    {{ $do->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $do->do_date->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-common.component-card>
</div>
@endsection
