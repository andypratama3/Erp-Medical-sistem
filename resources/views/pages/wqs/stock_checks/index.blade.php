@extends('layouts.app')

@section('title', 'Stock Checks')

@section('content')
<x-common.page-breadcrumb pageTitle="Stock Checks" />

<div class="space-y-6 sm:space-y-7">
    <!-- Header -->
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">✓ Stock Checks</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Manage all stock verification records</p>
        </div>

        <a href="{{ route('wqs.task-board.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded-lg font-semibold transition">
            Back to Task Board
        </a>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-12 gap-6 align-items-center justify-center">
        <div
            class="col-span-2 md:col-span-6 xl:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Pending</p>
            <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $stats['pending'] ?? 0 }}</p>
        </div>

        <div
            class="col-span-2 md:col-span-6 xl:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Checked</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['checked'] ?? 0 }}</p>
        </div>

        <div
            class="col-span-2 md:col-span-6 xl:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Completed</p>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['completed'] ?? 0 }}</p>
        </div>

        <div
            class="col-span-2 md:col-span-6 xl:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Failed</p>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $stats['failed'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 dark:bg-white/[0.03]">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Search DO</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="DO code or customer..."
                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
            </div>

            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Status</label>
                <select name="status"
                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="checked" {{ request('status') === 'checked' ? 'selected' : '' }}>Checked</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed
                    </option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
            </div>

            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                    class="flex-1 px-4 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded-lg font-bold transition">
                    🔍 Filter
                </button>
                <a href="{{ route('wqs.stock-checks.index') }}"
                    class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-bold transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Stock Checks Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class=" dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">DO Code</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Customer</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Check Date</th>
                        <th class="px-6 py-3 text-center text-gray-700 dark:text-white font-bold">Total Items</th>
                        <th class="px-6 py-3 text-center text-gray-700 dark:text-white font-bold">Available</th>
                        <th class="px-6 py-3 text-center text-gray-700 dark:text-white font-bold">Partial</th>
                        <th class="px-6 py-3 text-center text-gray-700 dark:text-white font-bold">Not Available</th>
                        <th class="px-6 py-3 text-center text-gray-700 dark:text-white font-bold">Completion %</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Status</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Checked By</th>
                        <th class="px-6 py-3 text-left text-gray-700 dark:text-white font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($stockChecks as $check)
                    <tr class="dark:hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4">
                            <a href="{{ route('crm.sales-do.show', $check->salesDO) }}"
                                class="text-blue-600 dark:text-blue-400 hover:underline font-mono font-semibold">
                                {{ $check->salesDO->do_code }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-900 dark:text-white font-medium">
                                {{ $check->salesDO->customer?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                {{ $check->salesDO->office?->name ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white">
                            {{ $check->check_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="inline-block px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-white rounded font-bold">
                                {{ $check->total_items }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="inline-block px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-white rounded font-bold">
                                {{ $check->available_items }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="inline-block px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-white rounded font-bold">
                                {{ $check->partial_items }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="inline-block px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-white rounded font-bold">
                                {{ $check->not_available_items }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <div class="w-12 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full"
                                        style="width: {{ $check->completion_percentage }}%"></div>
                                </div>
                                <span
                                    class="text-xs font-bold text-gray-900 dark:text-white w-8 text-right">{{ $check->completion_percentage }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="px-2 py-1 bg-{{ $check->status_color }}-100 dark:bg-{{ $check->status_color }}-900 text-black dark:text-white rounded text-xs font-bold">
                                {{ $check->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white">
                            <p class="font-medium">{{ $check->checkedBy?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                {{ $check->created_at->format('d M Y H:i') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('wqs.stock-checks.show', $check) }}"
                                    class="inline-flex items-center gap-1 px-2 py-1 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-black dark:text-white rounded text-xs font-bold transition">
                                    View
                                </a>

                                @if($check->overall_status !== 'completed')
                                <a href="{{ route('wqs.stock-checks.edit', $check) }}"
                                    class="inline-flex items-center gap-1 px-2 py-1 bg-orange-600 dark:bg-orange-700 hover:bg-orange-700 dark:hover:bg-orange-800 text-dark dark:text-white rounded text-xs font-bold transition">
                                    Edit
                                </a>
                                @endif

                                @if($check->overall_status === 'checked')
                                <button onclick="openMarkFailedModal({{ $check->id }})"
                                    class="inline-flex items-center gap-1 px-2 py-1 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-800 text-dark dark:text-white rounded text-xs font-bold transition">
                                    Failed
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            <p class="text-lg font-semibold">No stock checks found</p>
                            <p class="text-sm mt-1">Start by creating a stock check from the task board</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($stockChecks->hasPages())
    <div class="mt-6">
        {{ $stockChecks->links() }}
    </div>
    @endif
</div>

<!-- Mark Failed Modal -->
<div id="markFailedModal"
    class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl dark:shadow-2xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">✕ Mark Stock Check as Failed</h3>
        </div>

        <div class="p-6 space-y-4">
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg p-4">
                <p class="text-sm text-red-900 dark:text-red-200">
                    ⚠️ Marking this stock check as failed will require a new check to be performed.
                </p>
            </div>

            <form id="markFailedForm" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Reason for Failure
                        *</label>
                    <textarea name="reason" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400"
                        placeholder="Explain why this stock check failed..." required></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeMarkFailedModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-800 text-white rounded-lg font-semibold transition">
                        Mark Failed
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openMarkFailedModal(stockCheckId) {
        document.getElementById('markFailedModal').classList.remove('hidden');
        document.getElementById('markFailedForm').action = `/wqs/stock-checks/${stockCheckId}/mark-failed`;
    }

    function closeMarkFailedModal() {
        document.getElementById('markFailedModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('markFailedModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeMarkFailedModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeMarkFailedModal();
        }
    });
</script>
@endpush

@endsection
