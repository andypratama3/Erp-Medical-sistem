@extends('layouts.app')

@section('title', 'RegAlkes Control Tower')

@section('content')
<x-common.page-breadcrumb pageTitle="RegAlkes Control Tower" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Total Cases</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Waiting NIE</p>
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $stats['waiting_nie'] }}</p>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">NIE Issued</p>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['nie_issued'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">SKU Active</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['sku_active'] }}</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">By Status</p>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-2">
                        @foreach($stats['by_status'] as $status => $count)
                            <span class="inline-block mr-2">{{ ucwords(str_replace('_', ' ', $status)) }}: <strong>{{ $count }}</strong></span>
                        @endforeach
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Cases -->
    <x-common.component-card
        title="Recent Cases"
        desc="Latest RegAlkes registration cases"
        link="{{ route('reg-alkes.cases.create') }}">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Case Number</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Manufacture</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Case Type</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Status</th>
                        <th class="px-6 py-3 text-center text-gray-700 dark:text-gray-300 font-bold">Total SKU</th>
                        <th class="px-6 py-3 text-center text-gray-700 dark:text-gray-300 font-bold">Active SKU</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">NIE Number</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Created</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentCases as $case)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4">
                            <a href="{{ route('reg-alkes.cases.show', $case) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-mono font-semibold">
                                {{ $case->case_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-900 dark:text-white font-medium">{{ $case->manufacture?->name ?? $case->manufacture_name ?? '-' }}</p>
                            @if($case->country_of_origin)
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $case->country_of_origin }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200 rounded text-xs font-bold">
                                {{ strtoupper($case->case_type ?? '-') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusBadge = match($case->case_status) {
                                    'case_draft'     => ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200', 'label' => 'Draft'],
                                    'case_submitted' => ['class' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200', 'label' => 'Submitted'],
                                    'waiting_nie'    => ['class' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200', 'label' => 'Waiting NIE'],
                                    'nie_issued'     => ['class' => 'bg-sky-100 dark:bg-sky-900 text-sky-800 dark:text-sky-200', 'label' => 'NIE Issued'],
                                    'sku_imported'   => ['class' => 'bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200', 'label' => 'SKU Imported'],
                                    'sku_active'     => ['class' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200', 'label' => 'SKU Active'],
                                    'cancelled'      => ['class' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200', 'label' => 'Cancelled'],
                                    default          => ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200', 'label' => $case->case_status],
                                };
                            @endphp
                            <span class="px-2 py-1 {{ $statusBadge['class'] }} rounded text-xs font-bold">
                                {{ $statusBadge['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-gray-900 dark:text-white font-semibold">{{ $case->total_skus ?? 0 }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-green-600 dark:text-green-400 font-semibold">{{ $case->active_skus ?? 0 }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-700 dark:text-gray-300 font-mono text-xs">{{ $case->nie_number ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-xs">
                            {{ $case->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('reg-alkes.cases.show', $case) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded text-xs font-bold transition">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            <p class="text-lg font-semibold">No cases found</p>
                            <p class="text-sm mt-1">Create a new case to get started</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Link to full list -->
        <div class="mt-4 text-right">
            <a href="{{ route('reg-alkes.cases.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-semibold">
                View All Cases →
            </a>
        </div>
    </x-common.component-card>
</div>
@endsection
