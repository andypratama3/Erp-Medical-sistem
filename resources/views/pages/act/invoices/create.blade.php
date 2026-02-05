@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')
<x-common.page-breadcrumb pageTitle="Create Invoice" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <!-- Invoice Form -->
    <x-common.component-card
        title="Invoice Details"
        desc="Create a new invoice">

        <form method="POST" action="{{ route('act.invoices.store') }}" class="space-y-6" id="invoiceForm">
            @csrf

            <!-- Sales DO Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Sales DO *</label>
                    <select name="sales_do_id" id="sales_do_id" required
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                        <option value="">Select Sales DO...</option>
                        @foreach($salesOrders as $order)
                            <option value="{{ $order->id }}" 
                                data-customer="{{ $order->customer_id }}"
                                data-subtotal="{{ $order->subtotal }}"
                                data-payment-term="{{ $order->paymentTerm?->days ?? 30 }}">
                                {{ $order->do_code }} - {{ $order->customer?->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('sales_do_id')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Customer *</label>
                    <input type="text" id="customer_display" readonly
                        class="h-10 w-full rounded-lg border border-gray-300 bg-gray-50 dark:bg-gray-800 px-3 text-sm dark:text-white dark:border-gray-700 cursor-not-allowed"
                        placeholder="Select Sales DO first">
                    <input type="hidden" name="customer_id" id="customer_id">
                </div>
            </div>

            <!-- Invoice Number & Dates -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Invoice Number *</label>
                    <input type="text" name="invoice_number" value="{{ old('invoice_number', 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)) }}" required
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    @error('invoice_number')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

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
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" required
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    @error('due_date')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Amount & Tax -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Amount (Rp) *</label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="0" step="0.01"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    @error('amount')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Tax Amount (Rp)</label>
                    <input type="number" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', 0) }}" min="0" step="0.01"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    @error('tax_amount')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Total Display -->
            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Total Amount:</span>
                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="total_display">Rp 0</span>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Notes</label>
                <textarea name="notes" rows="3"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">{{ old('notes') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('act.invoices.index') }}"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03] transition text-sm">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-6 py-2.5 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                    Create Invoice
                </button>
            </div>
        </form>
    </x-common.component-card>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const salesDoSelect = document.getElementById('sales_do_id');
    const customerDisplay = document.getElementById('customer_display');
    const customerIdInput = document.getElementById('customer_id');
    const amountInput = document.getElementById('amount');
    const taxInput = document.getElementById('tax_amount');
    const dueDateInput = document.getElementById('due_date');
    const totalDisplay = document.getElementById('total_display');

    // Update customer and amount when Sales DO is selected
    salesDoSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (this.value) {
            const customerId = selectedOption.dataset.customer;
            const subtotal = parseFloat(selectedOption.dataset.subtotal) || 0;
            const paymentTermDays = parseInt(selectedOption.dataset.paymentTerm) || 30;

            // Find customer name
            const customerName = selectedOption.text.split(' - ')[1] || '';
            customerDisplay.value = customerName;
            customerIdInput.value = customerId;

            // Set amount
            amountInput.value = subtotal;

            // Calculate tax (11%)
            taxInput.value = (subtotal * 0.11).toFixed(2);

            // Set due date
            const invoiceDate = new Date();
            invoiceDate.setDate(invoiceDate.getDate() + paymentTermDays);
            dueDateInput.value = invoiceDate.toISOString().split('T')[0];

            updateTotal();
        } else {
            customerDisplay.value = '';
            customerIdInput.value = '';
            amountInput.value = '';
            taxInput.value = '0';
            dueDateInput.value = '';
            updateTotal();
        }
    });

    // Update total when amount or tax changes
    amountInput.addEventListener('input', updateTotal);
    taxInput.addEventListener('input', updateTotal);

    function updateTotal() {
        const amount = parseFloat(amountInput.value) || 0;
        const tax = parseFloat(taxInput.value) || 0;
        const total = amount + tax;
        totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }
});
</script>
@endsection
