@props([
    'recentSalesDO' => [],
])

<div class="col-span-12 xl:col-span-6">
    <div
        class="rounded-sm border border-stroke px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
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

            @forelse($recentSalesDO as $salesDO)
            <div class="grid grid-cols-3 border-b border-stroke dark:border-strokedark sm:grid-cols-5">
                <div class="flex items-center gap-3 p-2.5 xl:p-5">
                    <p class="dark:text-white text-black dark:text-white">{{ $salesDO->do_code ?? 'N/A' }}</p>
                </div>

                <div class="flex items-center justify-center p-2.5 xl:p-5">
                    <p class="dark:text-white text-black dark:text-white">
                        {{ $salesDO->customer->name ?? 'N/A' }}</p>
                </div>

                <div class="flex items-center justify-center p-2.5 xl:p-5">
                    <p class="dark:text-white text-meta-3">{{ $salesDO->branch->name ?? 'N/A' }}</p>
                </div>

                <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                    <p class="dark:text-white text-black dark:text-white">Rp
                        {{ number_format($salesDO->grand_total, 0, ',', '.') }}</p>
                </div>

                <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                    <p
                        class="dark:text-white inline-flex rounded-full bg-opacity-10 py-1 px-3 text-sm font-medium {{ $salesDO->status_config['badge_class'] ?? '' }}">
                        {{ $salesDO->status_label ?? 'N/A' }}
                    </p>
                </div>
            </div>
            @empty
            <div class="p-5 text-center text-gray-500 dark:text-gray-400">
                <p>No recent sales found</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
