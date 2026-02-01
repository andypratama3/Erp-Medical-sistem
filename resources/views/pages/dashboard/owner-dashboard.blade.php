@extends('layouts.app')

@section('title', 'Owner Dashboard')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-title-md2 font-semibold text-black dark:text-white">
            Owner Dashboard - Multi Branch Overview
        </h2>
        <p class="dark:text-white text-sm text-body">Monitoring semua cabang dalam satu tampilan</p>
    </div>

    <!-- Branch Selector -->
    <div class="mb-6 flex flex-wrap gap-3">
        <button onclick="filterBranch('all')" class="branch-filter active inline-flex items-center justify-center rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-white hover:bg-opacity-90">
            All Branches
        </button>
        @foreach($branches as $branch)
        <button onclick="filterBranch({{ $branch->id }})" class="branch-filter inline-flex items-center justify-center rounded-md border border-primary px-5 py-2.5 text-sm font-medium text-primary dark:text-white hover:bg-primary hover:text-white hover:bg-opacity-90">
            {{ $branch->name }}
        </button>
        @endforeach
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid sm:grid-cols-2 gap-4 mb-6">
        <!-- Total Sales DO -->
        <div class="rounded-sm border border-stroke bg-amber-800 px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4 p-5">
                <svg class="fill-primary dark:fill-white" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21.1063 18.0469L19.3875 3.23126C19.2157 1.71876 17.9438 0.584381 16.3969 0.584381H5.56878C4.05628 0.584381 2.78441 1.71876 2.57816 3.23126L0.859406 18.0469C0.756281 18.9063 1.03128 19.7313 1.61566 20.3844C2.20003 21.0375 3.02816 21.3813 3.91566 21.3813H18.05C18.9375 21.3813 19.7657 21.0031 20.35 20.3844C20.9688 19.7313 21.2094 18.9063 21.1063 18.0469ZM19.2157 19.3531C18.9407 19.6625 18.5625 19.8344 18.0157 19.8344H3.91566C3.40003 19.8344 3.02191 19.6625 2.74691 19.3531C2.47191 19.0438 2.33441 18.6313 2.40003 18.1844L4.11878 3.36876C4.19066 2.71563 4.73753 2.16876 5.56878 2.16876H16.4313C17.2282 2.16876 17.7751 2.71563 17.8469 3.36876L19.5657 18.1844C19.6313 18.6313 19.4938 19.0438 19.2157 19.3531Z"/>
                    <path d="M14.3345 5.29375C13.922 5.39688 13.647 5.80938 13.7501 6.22188C13.7845 6.42813 13.8189 6.63438 13.8189 6.80625C13.8189 8.35313 12.547 9.625 11.0001 9.625C9.45327 9.625 8.18140 8.35313 8.18140 6.80625C8.18140 6.6 8.21577 6.42813 8.25015 6.22188C8.35327 5.80938 8.07827 5.39688 7.66577 5.29375C7.25327 5.19063 6.84077 5.46563 6.73765 5.87813C6.66577 6.1875 6.62827 6.49688 6.62827 6.80625C6.62827 9.2125 8.5939 11.1781 11.0001 11.1781C13.4064 11.1781 15.372 9.2125 15.372 6.80625C15.372 6.49688 15.3345 6.1875 15.2626 5.87813C15.1595 5.46563 14.747 5.225 14.3345 5.29375Z"/>
                </svg>
            </div>

            <div class="mt-4 flex items-end justify-between p-5">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ $stats['total_sales_do'] }}
                    </h4>
                    <span class="text-sm font-medium text-dark dark:text-white">Total Sales DO</span>
                </div>

                <span class="flex items-center gap-1 text-sm font-medium text-meta-3 dark:text-white">
                    {{ $stats['sales_do_growth'] }}%
                    <svg class="fill-meta-3" width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.35716 2.47737L0.908974 5.82987L5.0443e-07 4.94612L5 0.0848689L10 4.94612L9.09103 5.82987L5.64284 2.47737L5.64284 10.0849L4.35716 10.0849L4.35716 2.47737Z"/>
                    </svg>
                </span>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="rounded-sm border border-stroke bg-amber-800 px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark p-5">
            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4 p-5">
                <svg class="fill-primary dark:fill-white" width="22" height="18" viewBox="0 0 22 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.18418 8.03751C9.31543 8.03751 11.0686 6.35313 11.0686 4.25626C11.0686 2.15938 9.31543 0.475006 7.18418 0.475006C5.05293 0.475006 3.2998 2.15938 3.2998 4.25626C3.2998 6.35313 5.05293 8.03751 7.18418 8.03751ZM7.18418 2.05626C8.45605 2.05626 9.52168 3.05313 9.52168 4.29063C9.52168 5.52813 8.49043 6.52501 7.18418 6.52501C5.87793 6.52501 4.84668 5.52813 4.84668 4.29063C4.84668 3.05313 5.9123 2.05626 7.18418 2.05626Z"/>
                    <path d="M15.8124 9.6875C17.6687 9.6875 19.1468 8.24375 19.1468 6.42188C19.1468 4.6 17.6343 3.15625 15.8124 3.15625C13.9905 3.15625 12.478 4.6 12.478 6.42188C12.478 8.24375 13.9905 9.6875 15.8124 9.6875ZM15.8124 4.7375C16.8093 4.7375 17.5999 5.49375 17.5999 6.45625C17.5999 7.41875 16.8093 8.175 15.8124 8.175C14.8155 8.175 14.0249 7.41875 14.0249 6.45625C14.0249 5.49375 14.8155 4.7375 15.8124 4.7375Z"/>
                    <path d="M15.9843 10.0313H15.6749C14.6437 10.0313 13.6468 10.3406 12.7874 10.8563C11.8593 9.61876 10.3812 8.79376 8.73115 8.79376H5.67178C2.85303 8.82814 0.618652 11.0625 0.618652 13.8469V16.3219C0.618652 16.975 1.13428 17.4906 1.7874 17.4906H20.2468C20.8999 17.4906 21.4499 16.9406 21.4499 16.2875V15.4625C21.4155 12.4719 18.9749 10.0313 15.9843 10.0313ZM2.16553 15.9438V13.8469C2.16553 11.9219 3.74678 10.3406 5.67178 10.3406H8.73115C10.6562 10.3406 12.2374 11.9219 12.2374 13.8469V15.9438H2.16553V15.9438ZM19.8687 15.9438H13.7499V13.8469C13.7499 13.2969 13.6468 12.7469 13.4749 12.2313C14.0937 11.7844 14.8499 11.5781 15.6405 11.5781H15.9499C18.0812 11.5781 19.8343 13.3313 19.8343 15.4625V15.9438H19.8687Z"/>
                </svg>
            </div>

            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                    </h4>
                    <span class="text-sm font-medium text-dark dark:text-white">Total Revenue</span>
                </div>

                <span class="flex items-center gap-1 text-sm font-medium text-meta-3 dark:text-white">
                    {{ $stats['revenue_growth'] }}%
                    <svg class="fill-meta-3" width="10" height="11" viewBox="0 0 10 11" fill="none">
                        <path d="M4.35716 2.47737L0.908974 5.82987L5.0443e-07 4.94612L5 0.0848689L10 4.94612L9.09103 5.82987L5.64284 2.47737L5.64284 10.0849L4.35716 10.0849L4.35716 2.47737Z"/>
                    </svg>
                </span>
            </div>
        </div>

        <!-- Pending Deliveries -->
        <div class="rounded-sm border border-stroke bg-amber-800 px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4 p-5">
                <svg class="fill-primary dark:fill-white" width="22" height="22" viewBox="0 0 22 22" fill="none">
                    <path d="M21.1063 18.0469L19.3875 3.23126C19.2157 1.71876 17.9438 0.584381 16.3969 0.584381H5.56878C4.05628 0.584381 2.78441 1.71876 2.57816 3.23126L0.859406 18.0469C0.756281 18.9063 1.03128 19.7313 1.61566 20.3844C2.20003 21.0375 3.02816 21.3813 3.91566 21.3813H18.05C18.9375 21.3813 19.7657 21.0031 20.35 20.3844C20.9688 19.7313 21.2094 18.9063 21.1063 18.0469Z"/>
                </svg>
            </div>

            <div class="mt-4 flex items-end justify-between p-5">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ $stats['pending_deliveries'] }}
                    </h4>
                    <span class="text-sm font-medium text-black dark:text-white">Pending Deliveries</span>
                </div>

                <span class="flex items-center gap-1 text-sm font-medium text-meta-5 text-black dark:text-white">
                    {{ $stats['deliveries_change'] }}
                    <svg class="fill-meta-5" width="10" height="11" viewBox="0 0 10 11" fill="none">
                        <path d="M5.64284 7.69237L9.09102 4.33987L10 5.22362L5 10.0849L-8.98488e-07 5.22362L0.908973 4.33987L4.35716 7.69237L4.35716 0.0848701L5.64284 0.0848704L5.64284 7.69237Z"/>
                    </svg>
                </span>
            </div>
        </div>

        <!-- Active Tasks -->
        <div class="rounded-sm border border-stroke bg-amber-800 px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark p-5">
            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4 p-5">
                <svg class="fill-primary dark:fill-white" width="22" height="22" viewBox="0 0 22 22" fill="none">
                    <path d="M21.1063 18.0469L19.3875 3.23126C19.2157 1.71876 17.9438 0.584381 16.3969 0.584381H5.56878C4.05628 0.584381 2.78441 1.71876 2.57816 3.23126L0.859406 18.0469C0.756281 18.9063 1.03128 19.7313 1.61566 20.3844C2.20003 21.0375 3.02816 21.3813 3.91566 21.3813H18.05C18.9375 21.3813 19.7657 21.0031 20.35 20.3844C20.9688 19.7313 21.2094 18.9063 21.1063 18.0469Z"/>
                </svg>
            </div>

            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ $stats['active_tasks'] }}
                    </h4>
                    <span class="text-sm font-medium text-black dark:text-white">Active Tasks</span>
                </div>

                <span class="flex items-center gap-1 text-sm font-medium text-meta-6 text-black dark:text-white">
                    {{ $stats['tasks_completion'] }}%
                </span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5 mb-6">
        <!-- Revenue Chart -->
        <div class="col-span-12 xl:col-span-8">
            <div class="rounded-sm border border-stroke bg-amber-800 px-5 pt-7.5 pb-5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
                <div class="flex flex-wrap items-start justify-between gap-3 sm:flex-nowrap mb-5">
                    <div class="flex w-full flex-wrap gap-3 sm:gap-5 py-5">
                        <div class="flex min-w-47.5 ">
                            <span class="mt-1 mr-2 flex h-4 w-full max-w-4 items-center justify-center rounded-full border border-primary">
                                <span class="block h-2.5 w-full max-w-2.5 rounded-full bg-primary"></span>
                            </span>
                            <div class="w-full">
                                <p class="dark:text-white font-semibold text-primary">This Month</p>
                                <p class="dark:text-white text-sm font-medium">Rp {{ number_format($chartData['current_month'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="flex min-w-47.5">
                            <span class="mt-1 mr-2 flex h-4 w-full max-w-4 items-center justify-center rounded-full border border-secondary">
                                <span class="block h-2.5 w-full max-w-2.5 rounded-full bg-secondary"></span>
                            </span>
                            <div class="w-full">
                                <p class="dark:text-white font-semibold text-secondary">Last Month</p>
                                <p class="dark:text-white text-sm font-medium">Rp {{ number_format($chartData['last_month'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div id="chartOne" class="-ml-5"></div>
                </div>
            </div>
        </div>

        <!-- Branch Performance -->
        <div class="col-span-12 xl:col-span-4">
            <div class="rounded-sm border border-stroke bg-amber-800 px-5 pt-7.5 pb-5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
                <div class="mb-3 justify-between gap-4 sm:flex mt-5">
                    <div>
                        <h5 class="text-xl font-semibold text-black dark:text-white">
                            Branch Performance
                        </h5>
                    </div>
                </div>

                <div class="mb-2">
                    <div id="chartThree" class="mx-auto flex justify-center"></div>
                </div>

                <div class="-mx-8 flex flex-wrap items-center justify-center gap-y-3">
                    @foreach($branchPerformance as $index => $branch)
                    <div class="w-full px-8 sm:w-1/2">
                        <div class="flex w-full items-center">
                            <span class="mr-2 block h-3 w-full max-w-3 rounded-full" style="background-color: {{ $colors[$index] }}"></span>
                            <p class="dark:text-white flex w-full justify-between text-sm font-medium text-black dark:text-white">
                                <span>{{ $branch['name'] }}</span>
                                <span>{{ $branch['percentage'] }}%</span>
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Tasks -->
    <div class="grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
        <!-- Recent Sales DO -->
        <div class="col-span-12 xl:col-span-6">
            <div class="rounded-sm border border-stroke bg-amber-800 px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
                <h4 class="mb-6 text-xl font-semibold text-black dark:text-white">
                    Recent Sales DO
                </h4>

                <div class="flex flex-col">
                    <div class="grid grid-cols-3 rounded-sm bg-gray-2 dark:bg-meta-4 sm:grid-cols-5 dark:text-white">
                        <div class="p-2.5 xl:p-5">
                            <h5 class="text-sm font-medium uppercase xsm:text-base">DO Code</h5>
                        </div>
                        <div class="p-2.5 text-center xl:p-5">
                            <h5 class="text-sm font-medium uppercase xsm:text-base">Customer</h5>
                        </div>
                        <div class="p-2.5 text-center xl:p-5">
                            <h5 class="text-sm font-medium uppercase xsm:text-base">Branch</h5>
                        </div>
                        <div class="hidden p-2.5 text-center sm:block xl:p-5">
                            <h5 class="text-sm font-medium uppercase xsm:text-base">Amount</h5>
                        </div>
                        <div class="hidden p-2.5 text-center sm:block xl:p-5">
                            <h5 class="text-sm font-medium uppercase xsm:text-base">Status</h5>
                        </div>
                    </div>

                    @foreach($recentSalesDO as $salesDO)
                    <div class="grid grid-cols-3 border-b border-stroke dark:border-strokedark sm:grid-cols-5">
                        <div class="flex items-center gap-3 p-2.5 xl:p-5">
                            <p class="dark:text-white text-black dark:text-white">{{ $salesDO->do_code ?? 'N/A' }}</p>
                        </div>

                        <div class="flex items-center justify-center p-2.5 xl:p-5">
                            <p class="dark:text-white text-black dark:text-white">{{ $salesDO->customer->name ?? 'N/A' }}</p>
                        </div>

                        <div class="flex items-center justify-center p-2.5 xl:p-5">
                            <p class="dark:text-white text-meta-3">{{ $salesDO->branch->name ?? 'N/A' }}</p>
                        </div>

                        <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                            <p class="dark:text-white text-black dark:text-white">Rp {{ number_format($salesDO->grand_total, 0, ',', '.') }}</p>
                        </div>

                        <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                            <p class="dark:text-white inline-flex rounded-full bg-opacity-10 py-1 px-3 text-sm font-medium {{ $salesDO->status_config['badge_class'] }}">
                                {{ $salesDO->status_label }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Pending Tasks -->
        <div class="col-span-12 xl:col-span-6">
            <div class="rounded-sm border border-stroke bg-amber-800 px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
                <h4 class="mb-6 text-xl font-semibold text-black dark:text-white">
                    Pending Tasks
                </h4>

                <div class="flex flex-col">
                    @foreach($pendingTasks as $task)
                    <div class="flex items-center gap-5 py-3 px-7.5 hover:bg-gray-3 dark:hover:bg-meta-4">
                        <div class="relative h-14 w-14 rounded-full">
                            <span class="absolute right-0 bottom-0 h-3.5 w-3.5 rounded-full border-2 border-white {{ $task->is_overdue ? 'bg-meta-1' : 'bg-meta-6' }}"></span>
                        </div>

                        <div class="flex flex-1 items-center justify-between">
                            <div>
                                <h5 class="font-medium text-black dark:text-white">
                                    {{ $task->task_description }}
                                </h5>
                                <p>
                                    <span class="text-sm text-black dark:text-white">{{ $task->module_label }}</span>
                                    <span class="text-xs"> • {{ $task->priority_label }}</span>
                                </p>
                            </div>
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-primary">
                                <span class="text-sm font-medium text-dark dark:text-white">{{ $task->sales_do_id }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// Revenue Chart
const chartOneOptions = {
    series: [{
        name: 'This Month',
        data: {!! json_encode($chartData['series_current']) !!}
    }, {
        name: 'Last Month',
        data: {!! json_encode($chartData['series_last']) !!}
    }],
    chart: {
        type: 'area',
        height: 350,
        toolbar: { show: false }
    },
    colors: ['#3C50E0', '#80CAEE'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    fill: {
        type: 'gradient',
        gradient: { opacityFrom: 0.55, opacityTo: 0 }
    },
    xaxis: {
        categories: {!! json_encode($chartData['categories']) !!}
    },
    yaxis: {
        labels: {
            formatter: function(val) {
                return 'Rp ' + (val/1000000).toFixed(1) + 'M';
            }
        }
    }
};

const chartOne = new ApexCharts(document.querySelector("#chartOne"), chartOneOptions);
chartOne.render();

// Branch Performance Donut
const chartThreeOptions = {
    series: {!! json_encode($branchPerformance->pluck('percentage')) !!},
    chart: {
        type: 'donut',
        height: 250
    },
    colors: ['#3C50E0', '#6577F3', '#8FD0EF', '#0FADCF', '#80CAEE'],
    labels: {!! json_encode($branchPerformance->pluck('name')) !!},
    legend: { show: false },
    plotOptions: {
        pie: {
            donut: {
                size: '65%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        showAlways: true,
                        label: 'Total',
                        fontSize: '16px',
                        fontWeight: '400'
                    },
                    value: {
                        show: true,
                        fontSize: '28px',
                        fontWeight: 'bold'
                    }
                }
            }
        }
    }
};


const chartThree = new ApexCharts(document.querySelector("#chartThree"), chartThreeOptions);
chartThree.render();

// Branch Filter
function filterBranch(branchId) {
    // Remove active class from all
    document.querySelectorAll('.branch-filter').forEach(btn => {
        btn.classList.remove('active', 'bg-brand-500', 'text-white');
        btn.classList.add('border', 'border-gray-300', 'text-primary');
    });

    // Add active to clicked
    event.target.classList.add('active', 'bg-primary', 'text-white');
    event.target.classList.remove('border', 'border-gray-300', 'text-primary');

    // Reload with filter
    if(branchId === 'all') {
        window.location.href = '{{ route("dashboard") }}';
    } else {
        window.location.href = '{{ route("dashboard") }}?branch=' + branchId;
    }
}
</script>
@endpush
@endsection
