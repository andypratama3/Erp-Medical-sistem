@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<x-common.page-breadcrumb pageTitle="Invoice {{ $invoice->invoice_number }}" />

<div class="space-y-6">
    <x-flash-message.flash />

    <!-- Header -->
    <x-common.component-card
        title="Invoice Detail"
        desc="Invoice {{ $invoice->invoice_number }}">

        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white font-mono">{{ $invoice->invoice_number }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    For Sales DO: <a href="{{ route('crm.sales-do.show', $invoice->salesDO) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">{{ $invoice->salesDO->do_code }}</a>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('act.invoices.index') }}"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03] text-sm">
                    Back
                </a>
            </div>
        </div>

        <!-- Status Badges -->
        <div class="flex gap-3 mb-2">
            @php
                $invStatus = match($invoice->invoice_status) {
                    'draft'     => ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200', 'label' => 'Draft'],
                    'issued'    => ['class' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200', 'label' => 'Issued'],
                    'unpaid'    => ['class' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200', 'label' => 'Unpaid'],
                    'partial'   => ['class' => 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200', 'label' => 'Partial'],
                    'paid'      => ['class' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200', 'label' => 'Paid'],
                    'completed' => ['class' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200', 'label' => 'Completed'],
                    default     => ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200', 'label' => ucwords($invoice->invoice_status ?? '-')],
                };
            @endphp
            <x-ui.badge size="md" variant="light" :class="$invStatus['class']">
                Invoice: {{ $invStatus['label'] }}
            </x-ui.badge>

            @php
                $doStatus = $invoice->salesDO->status;
                $doStatusBadge = match($doStatus) {
                    'act_tukar_faktur' => ['class' => 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200', 'label' => 'Tukar Faktur'],
                    'act_invoiced'     => ['class' => 'bg-sky-100 dark:bg-sky-900 text-sky-800 dark:text-sky-200', 'label' => 'Invoiced'],
                    'fin_on_collect'   => ['class' => 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200', 'label' => 'On Collection'],
                    'fin_paid'         => ['class' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200', 'label' => 'Paid'],
                    default            => ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200', 'label' => ucwords(str_replace('_', ' ', $doStatus))],
                };
            @endphp
            <x-ui.badge size="md" variant="light" :class="$doStatusBadge['class']">
                DO: {{ $doStatusBadge['label'] }}
            </x-ui.badge>
        </div>
    </x-common.component-card>

    <!-- Main Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Main Content -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Invoice Info -->
            <x-common.component-card title="Invoice Information">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Customer</p>
                        <p class="text-base font-medium text-gray-800 dark:text-white">
                            <a href="{{ route('master.customers.show', $invoice->salesDO->customer) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $invoice->salesDO->customer?->name ?? '-' }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Invoice Date</p>
                        <p class="text-base font-medium text-gray-800 dark:text-white">{{ $invoice->invoice_date?->format('d M Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Due Date</p>
                        <p class="text-base font-medium text-gray-800 dark:text-white">
                            {{ $invoice->due_date?->format('d M Y') ?? '-' }}
                            @if($invoice->days_overdue > 0)
                                <span class="ml-2 text-xs text-red-600 dark:text-red-400 font-bold">⚠️ {{ $invoice->days_overdue }} days overdue</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aging Category</p>
                        <p class="text-base font-medium text-gray-800 dark:text-white">{{ $invoice->aging_category }}</p>
                    </div>
                    @if($invoice->bank_name)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Bank</p>
                        <p class="text-base font-medium text-gray-800 dark:text-white">{{ $invoice->bank_name }}</p>
                    </div>
                    @endif
                    @if($invoice->account_number)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Account Number</p>
                        <p class="text-base font-medium text-gray-800 dark:text-white font-mono">{{ $invoice->account_number }}</p>
                    </div>
                    @endif
                </div>
            </x-common.component-card>

            <!-- Faktur Pajak -->
            <x-common.component-card title="Faktur Pajak">
                @if($invoice->faktur_pajak_number)
                    <div class="flex items-center gap-3 p-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg">
                        <span class="text-green-600 dark:text-green-400 text-lg">✓</span>
                        <div>
                            <p class="text-sm text-gray-900 dark:text-white font-semibold">Faktur Pajak: {{ $invoice->faktur_pajak_number }}</p>
                            @if($invoice->faktur_pajak_date)
                                <p class="text-xs text-gray-500 dark:text-gray-400">Date: {{ $invoice->faktur_pajak_date->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 mb-4">
                        <p class="text-sm text-yellow-800 dark:text-yellow-300">⚠️ Faktur Pajak belum diupload</p>
                    </div>

                    <form method="POST" action="{{ route('act.invoices.upload-faktur', $invoice) ?? '#' }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400">Faktur Number *</label>
                                <input type="text" name="faktur_number" required placeholder="e.g., FP-12345"
                                    class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400">Faktur Pajak File *</label>
                                <input type="file" name="faktur_pajak" accept=".pdf,.jpg,.png" required
                                    class="w-full text-xs text-gray-600 dark:text-gray-400 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-600">
                            </div>
                        </div>
                        <button type="submit"
                            class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-2 text-sm bg-orange-500 text-white shadow-theme-xs hover:bg-orange-600">
                            Upload Faktur Pajak
                        </button>
                    </form>
                @endif
            </x-common.component-card>

            <!-- Order Items -->
            <x-common.component-card title="Order Items">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">#</th>
                                <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Product</th>
                                <th class="px-4 py-3 text-right text-gray-700 dark:text-gray-300 font-bold">Qty</th>
                                <th class="px-4 py-3 text-right text-gray-700 dark:text-gray-300 font-bold">Unit Price</th>
                                <th class="px-4 py-3 text-right text-gray-700 dark:text-gray-300 font-bold">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($invoice->salesDO->items as $idx => $item)
                            <tr>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $item->product_name }}</td>
                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ $item->qty_ordered }}</td>
                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white font-semibold">Rp {{ number_format($item->line_total, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>

            @if($invoice->notes)
            <x-common.component-card title="Notes">
                <p class="text-gray-800 dark:text-white">{{ $invoice->notes }}</p>
            </x-common.component-card>
            @endif
        </div>

        <!-- Right: Sidebar -->
        <div class="space-y-6">

            <!-- Amount Summary -->
            <x-common.component-card title="Amount Summary">
                <div class="space-y-3">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>Subtotal:</span>
                        <span class="font-semibold">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>Tax:</span>
                        <span class="font-semibold">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                        <div class="flex justify-between text-lg font-bold text-gray-800 dark:text-white">
                            <span>Total:</span>
                            <span class="text-blue-600">Rp {{ $invoice->formatted_total }}</span>
                        </div>
                    </div>
                </div>
            </x-common.component-card>

            <!-- Payment Status -->
            <x-common.component-card title="Payment Status">
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Total Invoice:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                    </div>

                    @if($invoice->collections && $invoice->collections->count() > 0)
                        @php $totalCollected = $invoice->collections->sum('amount_collected'); @endphp
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Total Collected:</span>
                            <span class="font-semibold text-green-600 dark:text-green-400">Rp {{ number_format($totalCollected, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Outstanding:</span>
                            <span class="font-bold text-orange-600 dark:text-orange-400">Rp {{ number_format($invoice->total - $totalCollected, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <!-- Collect Button -->
                    @if($invoice->invoice_status !== 'paid' && $invoice->invoice_status !== 'completed')
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('fin.collections.create', ['invoice_id' => $invoice->id]) }}"
                                class="w-full inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-2.5 text-sm bg-green-500 text-white shadow-theme-xs hover:bg-green-600">
                                💰 Record Collection
                            </a>
                        </div>
                    @endif
                </div>
            </x-common.component-card>

            <!-- Collections History -->
            @if($invoice->collections && $invoice->collections->count() > 0)
            <x-common.component-card title="Collection History">
                <div class="space-y-3">
                    @foreach($invoice->collections as $collection)
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $collection->collection_number ?? 'COL-' . $collection->id }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $collection->collection_date?->format('d M Y') ?? '-' }} • {{ ucfirst($collection->payment_method ?? '-') }}</p>
                                </div>
                                <span class="text-sm font-bold text-green-600 dark:text-green-400">Rp {{ number_format($collection->amount_collected, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-common.component-card>
            @endif

            <!-- Documents -->
            @if($invoice->documents && $invoice->documents->count() > 0)
            <x-common.component-card title="Documents">
                <div class="space-y-2">
                    @foreach($invoice->documents as $doc)
                        <a href="{{ \Storage::url($doc->file_path) }}" target="_blank" class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <span class="text-blue-600 dark:text-blue-400">📄</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $doc->stage ?? 'Document' }}</span>
                        </a>
                    @endforeach
                </div>
            </x-common.component-card>
            @endif
        </div>
    </div>
</div>
@endsection
