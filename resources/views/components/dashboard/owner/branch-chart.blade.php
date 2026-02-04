@props([
    'branchPerformance' => [],
    'colors' => ['#3C50E0', '#6577F3', '#8FD0EF', '#0FADCF', '#80CAEE'],
])

<div class="col-span-1 lg:col-span-1">
    <div
        class="rounded-sm border border-stroke px-5 pt-7.5 pb-5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
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
                    <span class="mr-2 block h-3 w-full max-w-3 rounded-full"
                        style="background-color: {{ $colors[$index] ?? '#3C50E0' }}"></span>
                    <p
                        class="dark:text-white flex w-full justify-between text-sm font-medium text-black dark:text-white">
                        <span>{{ $branch['name'] }}</span>
                        <span>{{ $branch['percentage'] }}%</span>
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Branch Performance Donut
    const chartThreeOptions = {
        series: {
            !!json_encode($branchPerformance->pluck('percentage')) !!
        },
        chart: {
            type: 'donut',
            height: 250
        },
        colors: ['#3C50E0', '#6577F3', '#8FD0EF', '#0FADCF', '#80CAEE'],
        labels: {
            !!json_encode($branchPerformance->pluck('name')) !!
        },
        legend: {
            show: false
        },
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
</script>
@endpush
