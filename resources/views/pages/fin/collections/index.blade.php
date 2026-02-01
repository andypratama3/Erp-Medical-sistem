@extends('layouts.app')

@section('title', 'Collections')

@section('content')
<x-common.page-breadcrumb pageTitle="Collections" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <!-- Collections List -->
    <x-common.component-card
        title="Collections"
        desc="Manage all payment collections">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Collection No.</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Invoice</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Customer</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Collection Date</th>
                        <th class="px-6 py-3 text-right text-gray-700 dark:text-gray-300 font-bold">Amount</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Method</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Status</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($collections as $collection)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4">
                            <span class="text-gray-900 dark:text-white font-mono font-semibold text-sm">
                                {{ $collection->collection_number ?? 'COL-' . str_pad($collection->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($collection->invoice)
                                <a href="{{ route('act.invoices.show', $collection->invoice) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-mono text-xs">
                                    {{ $collection->invoice->invoice_number }}
                                </a>
                            @else
                                <span class="text-gray-500 dark:text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-900 dark:text-white font-medium">
                                {{ $collection->invoice?->salesDO?->customer?->name ?? '-' }}
                            </p>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                            {{ $collection->collection_date?->format('d M Y') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <p class="text-gray-900 dark:text-white font-bold">
                                {{ $collection->formatted_amount }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $methodBadge = match($collection->payment_method) {
                                    'cash'     => ['class' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200', 'label' => 'Cash'],
                                    'transfer' => ['class' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200', 'label' => 'Transfer'],
                                    'check'    => ['class' => 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200', 'label' => 'Check'],
                                    'giro'     => ['class' => 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200', 'label' => 'Giro'],
                                    default    => ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200', 'label' => ucfirst($collection->payment_method ?? '-')],
                                };
                            @endphp
                            <span class="px-2 py-1 {{ $methodBadge['class'] }} rounded text-xs font-bold">
                                {{ $methodBadge['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $collStatus = match($collection->collection_status) {
                                    'pending'   => ['class' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200', 'label' => 'Pending'],
                                    'completed' => ['class' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200', 'label' => 'Completed'],
                                    'failed'    => ['class' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200', 'label' => 'Failed'],
                                    default     => ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200', 'label' => ucfirst($collection->collection_status ?? '-')],
                                };
                            @endphp
                            <span class="px-2 py-1 {{ $collStatus['class'] }} rounded text-xs font-bold">
                                {{ $collStatus['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('fin.collections.show', $collection) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded text-xs font-bold transition">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            <p class="text-lg font-semibold">No collections found</p>
                            <p class="text-sm mt-1">Collections will appear after payments are recorded</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-common.component-card>

    <!-- Pagination -->
    @if($collections->hasPages())
        <div class="flex justify-start gap-2">
            {{ $collections->links() }}
        </div>
    @endif
</div>
@endsection
