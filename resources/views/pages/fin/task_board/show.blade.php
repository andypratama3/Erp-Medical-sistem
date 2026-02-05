@extends('layouts.app')

@section('title', 'FIN Task Details')

@section('content')
<x-common.page-breadcrumb pageTitle="FIN Task Board Details" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card title="Task Information">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Task Type</p>
                <p class="text-gray-900 dark:text-white font-semibold">{{ $task->task_type_label }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status</p>
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-{{ $task->status_color }}-100 text-{{ $task->status_color }}-800 dark:bg-{{ $task->status_color }}-900/30 dark:text-{{ $task->status_color }}-400">
                    {{ $task->status_label }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Priority</p>
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-{{ $task->priority_color }}-100 text-{{ $task->priority_color }}-800 dark:bg-{{ $task->priority_color }}-900/30 dark:text-{{ $task->priority_color }}-400">
                    {{ $task->priority_label }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Due Date</p>
                <p class="text-gray-900 dark:text-white">{{ $task->due_date?->format('d F Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Assigned To</p>
                <p class="text-gray-900 dark:text-white">{{ $task->assignedUser->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Sales DO</p>
                <a href="{{ route('crm.sales-do.show', $task->salesDO) }}" 
                    class="text-blue-600 dark:text-blue-400 hover:underline font-mono">
                    {{ $task->salesDO->do_code }}
                </a>
            </div>
            @if($task->task_description)
            <div class="md:col-span-2">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Description</p>
                <p class="text-gray-900 dark:text-white">{{ $task->task_description }}</p>
            </div>
            @endif
            @if($task->notes)
            <div class="md:col-span-2">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Notes</p>
                <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $task->notes }}</p>
            </div>
            @endif
        </div>
    </x-common.component-card>

    <div class="flex justify-end gap-3">
        <a href="{{ route('fin.task-board') }}" 
            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03] transition text-sm">
            Back to Task Board
        </a>
        @if($task->canStart())
        <form method="POST" action="{{ route('fin.task-board.start', $task) }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                Start Task
            </button>
        </form>
        @endif
        @if($task->canComplete())
        <form method="POST" action="{{ route('fin.task-board.complete', $task) }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                Complete Task
            </button>
        </form>
        @endif
    </div>
</div>
@endsection
