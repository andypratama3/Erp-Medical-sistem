<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6">

    {{-- =================== CARD TOTAL PETANI =================== --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
            {{-- ICON --}}
            <svg class="fill-gray-800 dark:fill-white/90" width="24" height="24" viewBox="0 0 24 24">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M8.80443 5.60156C7.59109 5.60156 6.60749 6.58517 6.60749 7.79851C6.60749 9.01185 7.59109 9.99545 8.80443 9.99545C10.0178 9.99545 11.0014 9.01185 11.0014 7.79851C11.0014 6.58517 10.0178 5.60156 8.80443 5.60156Z" />
            </svg>
        </div>

        <div class="flex items-end justify-between mt-5">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Farmers</span>
                <h4 class="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">
                    {{ number_format($totalFarmers ?? 0) }}
                </h4>
            </div>
        </div>
    </div>

    {{-- =================== CARD TOTAL TRANSAKSI =================== --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
            {{-- ICON --}}
            <svg class="fill-gray-800 dark:fill-white/90" width="24" height="24" viewBox="0 0 24 24">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M11.665 3.75621C11.8762 3.65064 12.1247 3.65064 12.3358 3.75621Z" />
            </svg>
        </div>

        <div class="flex items-end justify-between mt-5">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Transactions</span>
                <h4 class="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">
                    {{ number_format($totalTransactions ?? 0) }}
                </h4>
            </div>
        </div>
    </div>

    {{-- =================== CARD TOTAL PUPUK =================== --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
            {{-- ICON --}}
            <svg class="fill-gray-800 dark:fill-white/90" width="24" height="24" viewBox="0 0 24 24">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M12 2L2 7l10 5 10-5-10-5Z" />
            </svg>
        </div>

        <div class="flex items-end justify-between mt-5">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Pupuk</span>
                <h4 class="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">
                    {{ number_format($totalFertilizer ?? 0) }}
                </h4>
            </div>
        </div>
    </div>

</div>
