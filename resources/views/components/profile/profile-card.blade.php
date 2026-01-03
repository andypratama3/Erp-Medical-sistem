<div x-data="{ saveProfile() { console.log('Saving profile...') } }">

    <div class="mb-6 rounded-2xl border border-gray-200 p-5 lg:p-6 dark:border-gray-800">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">

            <div class="flex w-full flex-col items-center gap-6 xl:flex-row">

                {{-- Avatar --}}
                {{-- <div class="h-20 w-20 overflow-hidden rounded-full border border-gray-200 dark:border-gray-700">
                    <img src="./images/user/owner.jpg" alt="user" class="h-full w-full object-cover" />
                </div> --}}

                {{-- Info --}}
                <div class="order-3 xl:order-2">
                    <h4 class="mb-2 text-center text-lg font-semibold text-gray-800 xl:text-left dark:text-white">
                        {{ Auth::user()->name ?? '-' }}
                    </h4>

                    <div class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ Auth::user()->email ?? '-' }}
                        </p>
                        <div class="hidden h-3.5 w-px bg-gray-300 xl:block dark:bg-gray-700"></div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Indonesia
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
