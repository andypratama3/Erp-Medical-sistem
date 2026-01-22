@extends('layouts.app')

@section('title', 'Sales DO - ' . $salesDo->do_code)

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Sales DO</h1>
                <p class="text-gray-600 mt-1">DO Code: <span class="font-mono font-semibold">{{ $salesDo->do_code }}</span></p>
            </div>

            <!-- Status Badge -->
            <div class="flex items-center gap-3">
                @php
                    $statusConfig = [
                        'crm_to_wqs' => ['label' => 'CRM to WQS', 'color' => 'yellow'],
                        'wqs_ready' => ['label' => 'WQS Ready', 'color' => 'blue'],
                        'wqs_on_hold' => ['label' => 'WQS On Hold', 'color' => 'red'],
                        'scm_on_delivery' => ['label' => 'On Delivery', 'color' => 'indigo'],
                        'scm_delivered' => ['label' => 'Delivered', 'color' => 'green'],
                        'act_tukar_faktur' => ['label' => 'Tukar Faktur', 'color' => 'purple'],
                        'act_invoiced' => ['label' => 'Invoiced', 'color' => 'green'],
                        'fin_on_collect' => ['label' => 'On Collection', 'color' => 'orange'],
                        'fin_paid' => ['label' => 'Paid', 'color' => 'green'],
                        'fin_overdue' => ['label' => 'Overdue', 'color' => 'red'],
                    ];
                    $status = $statusConfig[$salesDo->status] ?? ['label' => 'Unknown', 'color' => 'gray'];
                @endphp

                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-{{ $status['color'] }}-100 text-{{ $status['color'] }}-800">
                    {{ $status['label'] }}
                </span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mb-6 flex gap-3">
        @if(auth()->user()->can('edit_sales_do') && in_array($salesDo->status, ['crm_to_wqs', 'wqs_on_hold']))
            <a href="{{ route('crm.sales-do.edit', $salesDo) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
        @endif

        @if(auth()->user()->can('submit_sales_do') && $salesDo->status === 'crm_to_wqs')
            <button type="button" onclick="openSubmitModal()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Submit to WQS
            </button>
        @endif

        @if(auth()->user()->can('delete_sales_do') && $salesDo->status === 'crm_to_wqs')
            <button type="button" onclick="openDeleteModal()" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete
            </button>
        @endif

        <a href="{{ route('crm.sales-do.exportPDF', $salesDo) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8m0 8l-9-2m9 2l9-2m-9-8l9-2m-9 2l-9-2m9 2V9m0 8h.01"/>
            </svg>
            Export PDF
        </a>

        <a href="{{ route('crm.sales-do.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition">
            Back
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Document Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Document Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Document Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600 font-semibold">DO Date</label>
                        <p class="text-gray-900 font-medium">{{ $salesDo->do_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 font-semibold">Customer</label>
                        <p class="text-gray-900 font-medium">{{ $salesDo->customer?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 font-semibold">Office</label>
                        <p class="text-gray-900 font-medium">{{ $salesDo->office?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 font-semibold">Payment Term</label>
                        <p class="text-gray-900 font-medium">{{ $salesDo->paymentTerm?->name ?? '-' }}</p>
                    </div>
                    <div class="col-span-2">
                        <label class="text-sm text-gray-600 font-semibold">Shipping Address</label>
                        <p class="text-gray-900">{{ $salesDo->shipping_address }}</p>
                    </div>
                    @if($salesDo->pic_customer)
                        <div class="col-span-2">
                            <label class="text-sm text-gray-600 font-semibold">PIC Customer</label>
                            <p class="text-gray-900">{{ $salesDo->pic_customer }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Order Items</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Product</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">Qty</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">Unit Price</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">Discount</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($salesDo->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                                            <p class="text-xs text-gray-600">SKU: {{ $item->product_sku }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">{{ $item->qty_ordered }} {{ $item->unit }}</td>
                                    <td class="px-6 py-4 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        @if($item->discount_percent > 0)
                                            {{ $item->discount_percent }}% (Rp {{ number_format($item->discount_amount, 0, ',', '.') }})
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold text-gray-900">
                                        Rp {{ number_format($item->line_total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No items found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            @if($salesDo->notes_crm)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">CRM Notes</h2>
                    <p class="text-gray-700 whitespace-pre-line">{{ $salesDo->notes_crm }}</p>
                </div>
            @endif
        </div>

        <!-- Right Column: Summary & Metadata -->
        <div class="space-y-6">
            <!-- Financial Summary -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Summary</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center border-b pb-3">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-semibold">Rp {{ number_format($salesDo->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($salesDo->tax_id)
                        <div class="flex justify-between items-center border-b pb-3">
                            <span class="text-gray-600">Tax ({{ $salesDo->tax?->rate ?? 0 }}%):</span>
                            <span class="font-semibold">Rp {{ number_format($salesDo->tax_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center pt-3">
                        <span class="text-lg font-bold text-gray-900">Grand Total:</span>
                        <span class="text-lg font-bold text-green-600">Rp {{ number_format($salesDo->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Metadata -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Metadata</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <label class="text-gray-600 font-semibold">Created By:</label>
                        <p class="text-gray-900">{{ $salesDo->createdBy?->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $salesDo->created_at?->format('d M Y H:i') }}</p>
                    </div>

                    @if($salesDo->updated_by)
                        <div class="border-t pt-3">
                            <label class="text-gray-600 font-semibold">Last Updated By:</label>
                            <p class="text-gray-900">{{ $salesDo->updatedBy?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $salesDo->updated_at?->format('d M Y H:i') }}</p>
                        </div>
                    @endif

                    @if($salesDo->submitted_at)
                        <div class="border-t pt-3">
                            <label class="text-gray-600 font-semibold">Submitted By:</label>
                            <p class="text-gray-900">{{ $salesDo->submittedBy?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $salesDo->submitted_at?->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status Timeline (Optional) -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Workflow Status</h2>
                <div class="text-sm text-gray-600">
                    <div class="mb-2">
                        <span class="font-semibold">Current Status:</span>
                        <span class="ml-2 px-2 py-1 rounded bg-{{ $status['color'] }}-100 text-{{ $status['color'] }}-800">
                            {{ $status['label'] }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Next Step: Submit to WQS for quality check</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div id="submitModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Submit to WQS?</h3>
        </div>

        <div class="p-6 space-y-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-900">
                    <strong>Please confirm:</strong> You are about to submit this Sales DO to the WQS (Warehouse Quality System) module. This action will change the status from "CRM to WQS" to "WQS Ready" and cannot be undone.
                </p>
            </div>

            <div class="space-y-2">
                <h4 class="font-semibold text-gray-900 text-sm">Summary:</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• DO Code: <span class="font-mono font-semibold">{{ $salesDo->do_code }}</span></li>
                    <li>• Customer: <span class="font-semibold">{{ $salesDo->customer?->name ?? '-' }}</span></li>
                    <li>• Total Items: <span class="font-semibold">{{ $salesDo->items->count() }}</span></li>
                    <li>• Grand Total: <span class="font-semibold">Rp {{ number_format($salesDo->grand_total, 0, ',', '.') }}</span></li>
                </ul>
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 flex gap-3">
            <button type="button" onclick="closeSubmitModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition">
                Cancel
            </button>
            <form id="submitForm" action="{{ route('crm.sales-do.submit', $salesDo) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                    Confirm Submit
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-red-600">Delete Sales DO?</h3>
        </div>

        <div class="p-6 space-y-4">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-sm text-red-900">
                    <strong>Warning:</strong> This action will permanently delete this Sales DO. This cannot be undone.
                </p>
            </div>

            <div class="space-y-2">
                <h4 class="font-semibold text-gray-900 text-sm">Deleting:</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• DO Code: <span class="font-mono font-semibold">{{ $salesDo->do_code }}</span></li>
                    <li>• Customer: <span class="font-semibold">{{ $salesDo->customer?->name ?? '-' }}</span></li>
                </ul>
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 flex gap-3">
            <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition">
                Cancel
            </button>
            <form id="deleteForm" action="{{ route('crm.sales-do.destroy', $salesDo) }}" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
function openSubmitModal() {
    document.getElementById('submitModal').classList.remove('hidden');
}

function closeSubmitModal() {
    document.getElementById('submitModal').classList.add('hidden');
}

function openDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('submitModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeSubmitModal();
});

document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endsection
@endsection
