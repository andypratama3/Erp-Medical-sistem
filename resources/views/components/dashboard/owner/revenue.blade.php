@props([
    'chartData' => [],
])

<div class="col-span-12 xl:col-span-8">
    <div
        class="rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6">
        <div class="flex flex-wrap items-start justify-between gap-3 sm:flex-nowrap mb-5">
            <div class="flex w-full flex-wrap gap-3 sm:gap-5 py-5">
                <div class="flex min-w-47.5 ">
                    <span
                        class="mt-1 mr-2 flex h-4 w-full max-w-4 items-center justify-center rounded-full border border-primary">
                        <span class="block h-2.5 w-full max-w-2.5 rounded-full bg-primary"></span>
                    </span>
                    <div class="w-full">
                        <p class="dark:text-white font-semibold text-primary">This Month</p>
                        <p class="dark:text-white text-sm font-medium">Rp
                            {{ number_format($chartData['current_month'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="flex min-w-47.5">
                    <span
                        class="mt-1 mr-2 flex h-4 w-full max-w-4 items-center justify-center rounded-full border border-secondary">
                        <span class="block h-2.5 w-full max-w-2.5 rounded-full bg-secondary"></span>
                    </span>
                    <div class="w-full">
                        <p class="dark:text-white font-semibold text-secondary">Last Month</p>
                        <p class="dark:text-white text-sm font-medium">Rp
                            {{ number_format($chartData['last_month'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div id="chartOne" class="-ml-5"></div>
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
            data: {
                !!json_encode($chartData['series_current'] ?? []) !!
            }
        }, {
            name: 'Last Month',
            data: {
                !!json_encode($chartData['series_last'] ?? []) !!
            }
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: false
            }
        },
        colors: ['#3C50E0', '#80CAEE'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                opacityFrom: 0.55,
                opacityTo: 0
            }
        },
        xaxis: {
            categories: {
                !!json_encode($chartData['categories'] ?? []) !!
            }
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
                }
            }
        }
    };

    const chartOne = new ApexCharts(document.querySelector("#chartOne"), chartOneOptions);
    chartOne.render();
</script>
@endpush
