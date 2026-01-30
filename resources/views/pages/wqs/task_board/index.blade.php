@extends('layouts.app')

@section('title', 'WQS Task Board')

@section('content')
<x-common.page-breadcrumb pageTitle="WQS Task Board" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

      <x-common.component-card
        title=""
        desc="Manage all Tasks Board WQS">
        <!-- Statistics -->
        <div class="grid grid-cols-12 gap-6 align-items-center justify-center">
            <div class="col-span-2 md:col-span-6 xl:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Total Tasks</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total'] ?? 0 }}</p>
            </div>

            <div class="col-span-2 md:col-span-6 xl:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Pending</p>
                <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $stats['pending'] ?? 0 }}</p>
            </div>

            <div class="col-span-2 md:col-span-6 xl:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">In Progress</p>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['in_progress'] ?? 0 }}</p>
            </div>

            <div class="col-span-2 md:col-span-6 xl:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">On Hold</p>
                <p class="text-3xl font-bold text-orange-600 dark:text-orange-400 mt-1">{{ $stats['on_hold'] ?? 0 }}</p>
            </div>

            <!-- CARD TERAKHIR -->
            <div class="col-span-2 md:col-span-6 xl:col-span-4 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Overdue</p>
                <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $stats['overdue'] ?? 0 }}</p>
            </div>

        </div>

        <!-- Filters -->
        <form method="GET" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 dark:bg-white/[0.03]">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
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
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress
                        </option>
                        <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Priority</label>
                    <select name="priority"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
                        <option value="">All Priorities</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                        Apply
                    </button>
                    <a href="{{ route('wqs.task-board') }}"
                        class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-red-500 text-white shadow-theme-xs hover:bg-brand-600">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Tasks Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b dark:text-white border-gray-100 dark:border-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">DO Code</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Customer</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Task</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Priority</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Status</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Due Date</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Assigned</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-6 py-4">
                                <a href="{{ route('crm.sales-do.show', $task->salesDO) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:underline font-mono font-semibold">
                                    {{ $task->salesDO->do_code }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900 dark:text-white font-medium">
                                    {{ $task->salesDO->customer?->name ?? '-' }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $task->salesDO->office?->name ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-bold">
                                    {{ $task->task_type_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 bg-{{ $task->priority_color }}-100 dark:bg-{{ $task->priority_color }}-900 text-{{ $task->priority_color }}-800 dark:text-white rounded text-xs font-bold">
                                    {{ $task->priority_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 bg-{{ $task->status_color }}-100 dark:bg-{{ $task->status_color }}-900 text-{{ $task->status_color }}-800 dark:text-white rounded text-xs font-bold">
                                    {{ $task->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-gray-900 dark:text-white font-medium">
                                        {{ $task->due_date?->format('d M Y') ?? '-' }}</p>
                                    @if($task->is_overdue)
                                    <p class="text-xs text-red-600 dark:text-red-400 font-bold">⚠️ Overdue</p>
                                    @elseif($task->days_until_due !== null && $task->days_until_due <= 3) <p
                                        class="text-xs text-orange-600 dark:text-orange-400">{{ $task->days_until_due }}
                                        days left</p>
                                        @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900 dark:text-white font-medium">
                                    {{ $task->assignedUser?->name ?? 'Unassigned' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('wqs.task-board.show', $task) }}"
                                    class="inline-flex items-center gap-1 px-3 py-1 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded text-xs font-bold transition">
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <p class="text-lg font-semibold">No tasks found</p>
                                <p class="text-sm mt-1">Create a new Sales DO to start tasks</p>
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
</div>

@endsection
