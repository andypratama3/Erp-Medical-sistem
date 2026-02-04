@props([
    'pendingTasks' => [],
])

<div class="col-span-12 xl:col-span-6">
    <div
        class="rounded-sm border border-stroke px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
        <h4 class="mb-6 text-xl font-semibold text-black dark:text-white">
            Pending Tasks
        </h4>

        <div class="flex flex-col">
            @forelse($pendingTasks as $task)
            <div class="flex items-center gap-5 py-3 px-7.5 hover:bg-gray-3 dark:hover:bg-meta-4">
                <div class="relative h-14 w-14 rounded-full">
                    <span
                        class="absolute right-0 bottom-0 h-3.5 w-3.5 rounded-full border-2 border-white {{ $task->is_overdue ? 'bg-meta-1' : 'bg-meta-6' }}"></span>
                </div>

                <div class="flex flex-1 items-center justify-between">
                    <div>
                        <h5 class="font-medium text-black dark:text-white">
                            {{ $task->task_description ?? 'No description' }}
                        </h5>
                        <p>
                            <span class="text-sm text-black dark:text-white">{{ $task->module_label ?? 'N/A' }}</span>
                            <span class="text-xs"> • {{ $task->priority_label ?? 'Normal' }}</span>
                        </p>
                    </div>
                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-primary">
                        <span
                            class="text-sm font-medium text-dark dark:text-white">{{ $task->sales_do_id ?? '-' }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-5 text-center text-gray-500 dark:text-gray-400">
                <p>No pending tasks</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
