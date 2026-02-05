@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div class="text-dark dark:text-white">
            <h2 class="text-title-md2 font-semibold text-black dark:text-white">
                {{ $currentBranch->name }} Dashboard
            </h2>
            <p class="text-sm text-body text-dark dark:text-white">Your branch performance overview</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-body text-dark dark:text-white">Current Branch</p>
            <p class="text-lg font-semibold text-gray-800 dark:text-amber-500">{{ $currentBranch->code }}</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-4 2xl:gap-7.5 mb-6">
        <!-- My Sales DO -->
        <div class="rounded-sm border border-stroke  px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
                <svg class="fill-primary dark:fill-white" width="22" height="22" viewBox="0 0 22 22">
                    <path d="M21.1063 18.0469L19.3875 3.23126C19.2157 1.71876 17.9438 0.584381 16.3969 0.584381H5.56878C4.05628 0.584381 2.78441 1.71876 2.57816 3.23126L0.859406 18.0469C0.756281 18.9063 1.03128 19.7313 1.61566 20.3844C2.20003 21.0375 3.02816 21.3813 3.91566 21.3813H18.05C18.9375 21.3813 19.7657 21.0031 20.35 20.3844C20.9688 19.7313 21.2094 18.9063 21.1063 18.0469Z"/>
                </svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ $stats['my_sales_do'] }}
                    </h4>
                    <span class="text-sm font-medium text-dark dark:text-white">My Sales DO</span>
                </div>
            </div>
        </div>

        <!-- My Tasks -->
        <div class="rounded-sm border border-strok px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
                <svg class="fill-primary dark:fill-white" width="22" height="22" viewBox="0 0 22 22">
                    <path d="M21.1063 18.0469L19.3875 3.23126C19.2157 1.71876 17.9438 0.584381 16.3969 0.584381H5.56878C4.05628 0.584381 2.78441 1.71876 2.57816 3.23126L0.859406 18.0469C0.756281 18.9063 1.03128 19.7313 1.61566 20.3844C2.20003 21.0375 3.02816 21.3813 3.91566 21.3813H18.05C18.9375 21.3813 19.7657 21.0031 20.35 20.3844C20.9688 19.7313 21.2094 18.9063 21.1063 18.0469Z"/>
                </svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ $stats['my_tasks'] }}
                    </h4>
                    <span class="text-sm font-medium text-dark dark:text-white">Assigned to Me</span>
                </div>
            </div>
        </div>

        <!-- Branch Revenue -->
        <div class="rounded-sm border border-strok px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
                <svg class="fill-primary dark:fill-white" width="22" height="18" viewBox="0 0 22 18">
                    <path d="M7.18418 8.03751C9.31543 8.03751 11.0686 6.35313 11.0686 4.25626C11.0686 2.15938 9.31543 0.475006 7.18418 0.475006C5.05293 0.475006 3.2998 2.15938 3.2998 4.25626C3.2998 6.35313 5.05293 8.03751 7.18418 8.03751Z"/>
                </svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        Rp {{ number_format($stats['branch_revenue'], 0, ',', '.') }}
                    </h4>
                    <span class="text-sm font-medium text-dark dark:text-white">Branch Revenue</span>
                </div>
            </div>
        </div>

        <!-- Pending Items -->
        <div class="rounded-sm border border-strok px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
                <svg class="fill-primary dark:fill-white" width="22" height="22" viewBox="0 0 22 22">
                    <path d="M21.1063 18.0469L19.3875 3.23126C19.2157 1.71876 17.9438 0.584381 16.3969 0.584381H5.56878C4.05628 0.584381 2.78441 1.71876 2.57816 3.23126L0.859406 18.0469C0.756281 18.9063 1.03128 19.7313 1.61566 20.3844C2.20003 21.0375 3.02816 21.3813 3.91566 21.3813H18.05C18.9375 21.3813 19.7657 21.0031 20.35 20.3844C20.9688 19.7313 21.2094 18.9063 21.1063 18.0469Z"/>
                </svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ $stats['pending_items'] }}
                    </h4>
                    <span class="text-sm font-medium text-dark dark:text-white">Pending Actions</span>
                </div>
            </div>
        </div>
    </div>

    <!-- My Tasks List -->
    <div class="rounded-sm border border-strok px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1 mb-6">
        <h4 class="mb-6 text-xl font-semibold text-black dark:text-white">
            My Tasks ({{ $myTasks->count() }})
        </h4>

        <div class="flex flex-col">
            @forelse($myTasks as $task)
            <div class="flex items-center gap-5 py-3 px-7.5 hover:bg-gray-3 dark:hover:bg-meta-4">
                <div class="relative h-14 w-14 rounded-full bg-{{ $task->priority_color }}-100 flex items-center justify-center">
                    <span class="text-{{ $task->priority_color }}-600 font-bold">{{ strtoupper(substr($task->module, 0, 3)) }}</span>
                </div>

                <div class="flex flex-1 items-center justify-between">
                    <div>
                        <h5 class="font-medium text-black dark:text-white">
                            {{ $task->task_description }}
                        </h5>
                        <p>
                            <span class="text-sm">{{ $task->task_type_label }}</span>
                            <span class="text-xs"> • Priority: {{ $task->priority_label }}</span>
                            <span class="text-xs"> • Due: {{ $task->due_date?->format('d M Y') ?? 'No deadline' }}</span>
                        </p>
                    </div>
                    <a href="{{ route('tasks.show', $task) }}" class="inline-flex items-center justify-center rounded-md bg-primary px-5 py-2 text-sm font-medium text-white hover:bg-opacity-90">
                        View
                    </a>
                </div>
            </div>
            @empty
            <div class="py-10 text-center">
                <p class="text-body dark:text-white">No tasks assigned to you</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="rounded-sm border border-strok px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
        <h4 class="mb-6 text-xl font-semibold text-black dark:text-white">
            Recent Sales DO
        </h4>

        <div class="flex flex-col">
            <div class="grid grid-cols-3 rounded-sm bg-gray-2 dark:bg-meta-4 sm:grid-cols-4">
                <div class="p-2.5 xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base dark:text-white">DO Code</h5>
                </div>
                <div class="p-2.5 text-center xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base dark:text-white">Customer</h5>
                </div>
                <div class="hidden p-2.5 text-center sm:block xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base dark:text-white">Amount</h5>
                </div>
                <div class="p-2.5 text-center xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base dark:text-white">Status</h5>
                </div>
            </div>

            @foreach($recentSalesDO as $salesDO)
            <div class="grid grid-cols-3 border-b border-stroke dark:border-strokedark sm:grid-cols-4">
                <div class="flex items-center gap-3 p-2.5 xl:p-5">
                    <p class="text-black dark:text-white">{{ $salesDO->do_code }}</p>
                </div>

                <div class="flex items-center justify-center p-2.5 xl:p-5">
                    <p class="text-black dark:text-white">{{ $salesDO->customer->name }}</p>
                </div>

                <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                    <p class="text-black dark:text-white">Rp {{ number_format($salesDO->grand_total, 0, ',', '.') }}</p>
                </div>

                <div class="flex items-center justify-center p-2.5 xl:p-5">
                    <p class="inline-flex rounded-full bg-opacity-10 py-1 px-3 text-sm font-medium {{ $salesDO->status_config['badge_class'] }}">
                        {{ $salesDO->status_label }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
