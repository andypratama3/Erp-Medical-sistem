@extends('layouts.app')

@section('title', 'Create Invoice for ' . $salesDo->do_code)

@section('content')
<x-common.page-breadcrumb pageTitle="Create Invoice" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <!-- DO Summary Header -->
    <x-common.component-card title="Sales DO Summary" desc="Creating invoice for {{ $salesDo->do_code }}">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                <p class="text-xs text-blue-600 dark:text-blue-400 font-bold">DO Code</p>
                <p class="text-gray-900 dark:text-white font-mono font-bold mt-1">{{ $salesDo->do_code }}</p>
            </div>
            <div class="bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-700 rounded-lg p-4">
                <p class="text-xs text-purple-600 dark:text-purple-400 font-bold">Customer</p>
                <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $salesDo->customer?->name ?? '-' }}</p>
            </div>
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg p-4">
                <p class="text-xs text-green-600 dark:text-green-400 font-bold">Subtotal</p>
                <p class="text-gray-900 dark:text-white font-bold mt-1">Rp {{ number_format($salesDo->subtotal, 0, ',', '.') }}</p>
            </div>
            <div class="bg-orange-50 dark:bg-orange-900/30 border border-orange-200 dark:border-orange-700 rounded-lg p-4">
                <p class="text-xs text-orange-600 dark:text-orange-400 font-bold">Items</p>
                <p class="text-gray-900 dark:text-white font-bold mt-1">{{ $salesDo->items->count() }} items</p>
            </div>
        </div>
    </x-common.component-card>

    <!-- Invoice Form -->
    <x-common.component-card
        title="Invoice Details"
        desc="Fill in the invoice information">

        <form method="POST" action="{{ route('act.invoices.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="sales_do_id" value="{{ $salesDo->id }}">

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Invoice Date *</label>
                    <input type="date" name="invoice_date" value="{{ old('invoice_date', now()->format('Y-m-d')) }}" required
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    @error('invoice_date')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Due Date *</label>
                    <input type="date" name="due_date" value="{{ old('due_date', now()->addDays($salesDo->paymentTerm?->days ?? 30)->format('Y-m-d')) }}" required
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    @error('due_date')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    @if($salesDo->paymentTerm)
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Payment term: {{ $salesDo->paymentTerm->name }}</p>
                    @endif
                </div>
            </div>

            <!-- Tax & Charges -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Tax Percentage (%)</label>
                    <input type="number" name="tax_percent" value="{{ old('tax_percent', 11) }}" step="0.1" min="0" max="100"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    @error('tax_percent')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Additional Charges (Rp)</label>
                    <input type="number" name="additional_charges" value="{{ old('additional_charges', 0) }}" min="0"
                        placeholder="0"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    @error('additional_charges')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Bank Information -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-4">🏦 Bank Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Bank Name</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                            placeholder="e.g., BCA, BRI, Mandiri"
                            class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Account Number</label>
                        <input type="text" name="account_number" value="{{ old('account_number') }}"
                            placeholder="e.g., 123-456-789"
                            class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Notes (Optional)</label>
                <textarea name="notes" rows="3"
                    placeholder="Special payment instructions or notes..."
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">{{ old('notes') }}</textarea>
            </div>

            <!-- Amount Preview -->
            <div class="bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-4">💰 Amount Summary</h3>
                <div class="space-y-3 max-w-sm ml-auto">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Subtotal:</span>
                        <span class="font-semibold">Rp {{ number_format($salesDo->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600 dark:text-gray-400" id="taxPreview">
                        <span>Tax (11%):</span>
                        <span class="font-semibold">Rp {{ number_format($salesDo->subtotal * 0.11, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600 dark:text-gray-400" id="chargesPreview">
                        <span>Additional Charges:</span>
                        <span class="font-semibold">Rp 0</span>
                    </div>
                    <div class="border-t border-gray-300 dark:border-gray-600 pt-3 flex justify-between text-lg font-bold text-gray-800 dark:text-white" id="totalPreview">
                        <span>Grand Total:</span>
                        <span class="text-blue-600">Rp {{ number_format($salesDo->subtotal * 1.11, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('act.task-board') }}"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03] transition text-sm">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-6 py-2.5 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                    Generate Invoice
                </button>
            </div>
        </form>
    </x-common.component-card>

    <!-- Order Items Reference -->
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
                    @foreach($salesDo->items as $idx => $item)
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
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const subtotal = {{ $salesDo->subtotal }};
    const taxInput = document.querySelector('input[name="tax_percent"]');
    const chargesInput = document.querySelector('input[name="additional_charges"]');

    function updatePreview() {
        const taxPercent = parseFloat(taxInput.value) || 0;
        const charges = parseFloat(chargesInput.value) || 0;
        const taxAmount = subtotal * (taxPercent / 100);
        const total = subtotal + taxAmount + charges;

        document.getElementById('taxPreview').querySelector('span:last-child').textContent =
            'Rp ' + total.toLocaleString('id-ID', {minimumFractionDigits: 0});
        document.getElementById('taxPreview').querySelector('span:first-child').textContent =
            'Tax (' + taxPercent + '%):';
        document.getElementById('taxPreview').querySelector('span:last-child').textContent =
            'Rp ' + taxAmount.toLocaleString('id-ID', {minimumFractionDigits: 0});
        document.getElementById('chargesPreview').querySelector('span:last-child').textContent =
            'Rp ' + charges.toLocaleString('id-ID', {minimumFractionDigits: 0});
        document.getElementById('totalPreview').querySelector('span:last-child').textContent =
            'Rp ' + total.toLocaleString('id-ID', {minimumFractionDigits: 0});
    }

    taxInput.addEventListener('input', updatePreview);
    chargesInput.addEventListener('input', updatePreview);
});
</script>
@endpush
@endsection
