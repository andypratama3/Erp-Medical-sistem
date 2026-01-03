
<div
    class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pt-5 sm:px-6 sm:pt-6
           dark:border-gray-800 dark:bg-white/[0.03]"
>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            Monthly Transactions
        </h3>

        <x-common.dropdown-menu />
    </div>

    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <div
            id="chartTransaction"
            class="-ml-5 h-[320px] min-w-[690px] pl-2 xl:min-w-full"
        ></div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const isDark = document.documentElement.classList.contains('dark');

    const options = {
        chart: {
            type: 'area',
            height: 320,
            toolbar: { show: false }
        },

        series: [{
            name: 'Transactions',
            data: @json($data)
        }],

        colors: ['#2563eb'], // blue-600

        stroke: {
            curve: 'smooth',
            width: 3
        },

        dataLabels: {
            enabled: false
        },

        fill: {
            type: 'gradient',
            gradient: {
                opacityFrom: 0.45,
                opacityTo: 0.05
            }
        },

        xaxis: {
            categories: [
                'Jan','Feb','Mar','Apr','May','Jun',
                'Jul','Aug','Sep','Oct','Nov','Dec'
            ],
            labels: {
                style: {
                    colors: isDark ? '#9ca3af' : '#6b7280'
                }
            }
        },

        yaxis: {
            labels: {
                style: {
                    colors: isDark ? '#9ca3af' : '#6b7280'
                }
            }
        },

        grid: {
            borderColor: isDark ? '#1f2937' : '#e5e7eb',
            strokeDashArray: 4
        },

        tooltip: {
            theme: isDark ? 'dark' : 'light'
        }
    };

    const chart = new ApexCharts(
        document.querySelector("#chartTransaction"),
        options
    );

    chart.render();
});
</script>
@endpush
