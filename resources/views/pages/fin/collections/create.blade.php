@extends('layouts.app')

@section('title', 'Create Collection')

@section('content')
<x-common.page-breadcrumb pageTitle="Create Collection" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card title="Collection Details" desc="Record a new payment collection">
        <form method="POST" action="{{ route('fin.collections.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Invoice *</label>
                    <x-form.select.searchable-select
                        name="invoice_id"
                        :options="$invoices"
                        placeholder="Select invoice..."
                        required />
                    @error('invoice_id')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Collection Date *</label>
                    <input type="date" name="collection_date" value="{{ old('collection_date', now()->format('Y-m-d')) }}" required
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    @error('collection_date')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Amount Collected (Rp) *</label>
                    <input type="number" name="amount_collected" value="{{ old('amount_collected') }}" required min="0" step="0.01"
                        placeholder="0"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    @error('amount_collected')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Payment Method *</label>
                    <select name="payment_method" required
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                        <option value="">Select method...</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cash">Cash</option>
                        <option value="check">Check</option>
                        <option value="giro">Giro</option>
                    </select>
                    @error('payment_method')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Payment Reference</label>
                <input type="text" name="payment_reference" value="{{ old('payment_reference') }}"
                    placeholder="e.g., TRX123456"
                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Notes</label>
                <textarea name="notes" rows="3"
                    placeholder="Additional notes..."
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('fin.collections.index') }}"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03] transition text-sm">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-6 py-2.5 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                    Record Collection
                </button>
            </div>
        </form>
    </x-common.component-card>
</div>
@endsection
