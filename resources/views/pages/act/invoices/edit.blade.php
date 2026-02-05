@extends('layouts.app')

@section('title', 'Edit Invoice #' . $invoice->invoice_number)

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Invoice" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <!-- Invoice Summary Header -->
    <x-common.component-card title="Invoice Summary" desc="Editing invoice {{ $invoice->invoice_number }}">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                <p class="text-xs text-blue-600 dark:text-blue-400 font-bold">Invoice Number</p>
                <p class="text-gray-900 dark:text-white font-mono font-bold mt-1">{{ $invoice->invoice_number }}</p>
            </div>
            <div class="bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-700 rounded-lg p-4">
                <p class="text-xs text-purple-600 dark:text-purple-400 font-bold">Customer</p>
                <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $invoice->salesDO->customer?->name ?? '-' }}</p>
            </div>
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg p-4">
                <p class="text-xs text-green-600 dark:text-green-400 font-bold">Total</p>
                <p class="text-gray-900 dark:text-white font-bold mt-1">{{ $invoice->formatted_total }}</p>
            </div>
            <div class="bg-orange-50 dark:bg-orange-900/30 border border-orange-200 dark:border-orange-700 rounded-lg p-4">
                <p class="text-xs text-orange-600 dark:text-orange-400 font-bold">Status</p>
                <p class="text-gray-900 dark:text-white font-bold mt-1">{{ ucfirst($invoice->invoice_status) }}</p>
            </div>
        </div>
    </x-common.component-card>

    <!-- Invoice Form -->
    <x-common.component-card title="Invoice Details" desc="Update invoice information">
        <form method="POST" action="{{ route('act.invoices.update', $invoice) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Invoice Date *</label>
                    <input type="date" name="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    @error('invoice_date')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Due Date *</label>
                    <input type="date" name="due_date" value="{{ old('due_date', $invoice->due_date?->format('Y-m-d')) }}" required
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    @error('due_date')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Faktur Pajak -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Faktur Pajak Number</label>
                    <input type="text" name="faktur_pajak_number" value="{{ old('faktur_pajak_number', $invoice->faktur_pajak_number) }}"
                        placeholder="e.g., 010.000-XX.XXXXXXXX"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Faktur Pajak Date</label>
                    <input type="date" name="faktur_pajak_date" value="{{ old('faktur_pajak_date', $invoice->faktur_pajak_date?->format('Y-m-d')) }}"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                </div>
            </div>

            <!-- Tukar Faktur -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Tukar Faktur PIC</label>
                    <input type="text" name="tukar_faktur_pic" value="{{ old('tukar_faktur_pic', $invoice->tukar_faktur_pic) }}"
                        placeholder="Person in charge name"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Invoice Status</label>
                    <select name="invoice_status" required
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                        <option value="draft" {{ old('invoice_status', $invoice->invoice_status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="issued" {{ old('invoice_status', $invoice->invoice_status) == 'issued' ? 'selected' : '' }}>Issued</option>
                        <option value="tukar_faktur" {{ old('invoice_status', $invoice->invoice_status) == 'tukar_faktur' ? 'selected' : '' }}>Tukar Faktur</option>
                        <option value="completed" {{ old('invoice_status', $invoice->invoice_status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Notes (Optional)</label>
                <textarea name="notes" rows="3"
                    placeholder="Additional notes..."
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">{{ old('notes', $invoice->notes) }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('act.invoices.show', $invoice) }}"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03] transition text-sm">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-6 py-2.5 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                    Update Invoice
                </button>
            </div>
        </form>
    </x-common.component-card>
</div>
@endsection
