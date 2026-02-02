
@extends('layouts.app')

@section('title', 'FIN Task Board')

@section('content')
<x-common.page-breadcrumb pageTitle="FIN Task Board" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card
        title=""
        desc="Manage all Tasks Board FIN">

        <!-- Statistics -->
        <div class="grid grid-cols-12 gap-6 align-items-center justify-center">
            <div class="col-span-2 md:col-span-6 xl:col-span-3 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Pending</p>
                <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $stats['pending'] ?? 0 }}</p>
            </div>

            <div class="col-span-2 md:col-span-6 xl:col-span-3 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">In Progress</p>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['in_progress'] ?? 0 }}</p>
            </div>

            <div class="col-span-2 md:col-span-6 xl:col-span-3 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Completed</p>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['completed'] ?? 0 }}</p>
            </div>

            <div class="col-span-2 md:col-span-6 xl:col-span-3 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Total</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ ($stats['pending'] ?? 0) + ($stats['in_progress'] ?? 0) + ($stats['completed'] ?? 0) }}</p>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 dark:bg-white/[0.03] mt-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="DO code, Customer..."
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Status</label>
                    <select name="status"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                        Apply
                    </button>
                    <a href="{{ route('fin.task-board.index') }}"
                        class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-red-500 text-white shadow-theme-xs hover:bg-red-600">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Tasks Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl overflow-hidden mt-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b dark:text-white border-gray-100 dark:border-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">DO Code</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Customer</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Invoice</th>
                            <th class="px-6 py-3 text-right text-gray-700 dark:text-gray-300 font-bold">Total</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Due Date</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Task Status</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">DO Status</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('crm.sales-do.show', $task->taskable) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:underline font-mono font-semibold">
                                    {{ $task->taskable->do_code }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900 dark:text-white font-medium">
                                    {{ $task->taskable->customer?->name ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $task->taskable->office?->name ?? '-' }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                @php $invoice = $task->taskable->invoice ?? null; @endphp
                                @if($invoice)
                                    <a href="{{ route('act.invoices.show', $invoice) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-mono text-xs">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400 text-xs italic">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-gray-900 dark:text-white font-semibold">
                                    Rp {{ number_format($task->taskable->grand_total, 0, ',', '.') }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                @if($invoice && $invoice->due_date)
                                    <p class="text-gray-900 dark:text-white text-sm">{{ $invoice->due_date->format('d M Y') }}</p>
                                    @if($invoice->days_overdue > 0)
                                        <p class="text-xs text-red-600 dark:text-red-400 font-bold">⚠️ {{ $invoice->days_overdue }}d overdue</p>
                                    @endif
                                @else
                                    <span class="text-gray-500 dark:text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $taskStatusBadge = match($task->task_status) {
                                        'pending'     => ['class' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200', 'label' => 'Pending'],
                                        'in_progress' => ['class' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200', 'label' => 'In Progress'],
                                        'completed'   => ['class' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200', 'label' => 'Completed'],
                                        default       => ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200', 'label' => $task->task_status],
                                    };
                                @endphp
                                <span class="px-2 py-1 {{ $taskStatusBadge['class'] }} rounded text-xs font-bold">
                                    {{ $taskStatusBadge['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $doStatus = $task->taskable->status;
                                    $finStatusBadge = match($doStatus) {
                                        'fin_on_collect' => ['class' => 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200', 'label' => 'On Collection'],
                                        'fin_paid'       => ['class' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200', 'label' => 'Paid'],
                                        'fin_overdue'    => ['class' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200', 'label' => 'Overdue'],
                                        default          => ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200', 'label' => ucwords(str_replace('_', ' ', $doStatus))],
                                    };
                                @endphp
                                <span class="px-2 py-1 {{ $finStatusBadge['class'] }} rounded text-xs font-bold">
                                    {{ $finStatusBadge['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    @if($invoice && $task->task_status !== 'completed')
                                        <a href="{{ route('fin.collections.create', ['invoice_id' => $invoice->id]) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-green-500 dark:bg-green-600 hover:bg-green-600 dark:hover:bg-green-700 text-white rounded text-xs font-bold transition">
                                            Collect
                                        </a>
                                    @endif
                                    <a href="{{ route('crm.sales-do.show', $task->taskable) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded text-xs font-bold transition">
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <p class="text-lg font-semibold">No tasks found</p>
                                <p class="text-sm mt-1">Tasks will appear after invoices are created in ACT</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($tasks->hasPages())
        <div class="mt-6">
            {{ $tasks->links() }}
        </div>
        @endif
    </x-common.component-card>
</div>
@endsection
