<div x-data="branchSwitcher()"
    @click.away="open = false"
    class="relative">

    <!-- Main Button -->
    <button @click="open = !open"
        :disabled="switching || !hasAccessibleBranches"
        class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition disabled:opacity-50 disabled:cursor-not-allowed">

        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>

        <div class="text-left">
            <div class="text-xs text-gray-500 dark:text-gray-400">Current Branch</div>
            <div class="text-sm font-medium text-gray-800 dark:text-white">
                <span x-show="!switching" x-text="currentBranch?.name || 'No Branch'"></span>
                <span x-show="switching" class="flex items-center gap-1">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Switching...
                </span>
            </div>
        </div>

        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400 ml-2 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
        style="display: none;">

        <div class="p-2">
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                Switch Branch
            </div>

            <!-- Empty State -->
            <div x-show="!hasAccessibleBranches" class="px-3 py-4 text-sm text-red-600 dark:text-red-400 text-center">
                <svg class="w-5 h-5 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 105.11 2.623a.75.75 0 00-1.08.102A6 6 0 1013.477 14.89zm1.414-5.89a.75.75 0 11-1.06-1.06l2.5-2.5a.75.75 0 111.06 1.06l-2.5 2.5z" clip-rule="evenodd"/>
                </svg>
                You have no accessible branches
            </div>

            <!-- Branch List -->
            <template x-for="branch in userBranches" :key="branch.id">
                <button @click="switchBranch(branch.id)"
                    :disabled="switching || currentBranch?.id === branch.id"
                    class="w-full flex items-center gap-3 px-3 py-2 text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="currentBranch?.id === branch.id ? 'bg-amber-50 dark:bg-blue-900/20' : ''">

                    <div class="flex-shrink-0">
                        <svg x-show="currentBranch?.id === branch.id" class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <svg x-show="currentBranch?.id !== branch.id" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-800 dark:text-white truncate" x-text="branch.name"></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate" x-text="branch.city || 'No location'"></div>
                    </div>
                </button>
            </template>
        </div>

        <!-- Manage Link for Owners -->
        @if(auth()->user()->hasRole('owner'))
            <div class="border-t border-gray-200 dark:border-gray-700 p-2">
                <a href="{{ route('master.branches.index') }}"
                    class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Manage Branches
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function branchSwitcher() {
    return {
        open: false,
        switching: false,
        currentBranch: @json($currentBranch),
        userBranches: @json($userBranches),

        get hasAccessibleBranches() {
            return this.userBranches && this.userBranches.length > 0;
        },

        async switchBranch(branchId) {
            // Validate
            const branchIdInt = parseInt(branchId);
            if (!Number.isInteger(branchIdInt) || branchIdInt < 1) {
                this.showError('Invalid branch selected');
                return;
            }

            if (!this.userBranches.some(b => b.id === branchIdInt)) {
                this.showError('You do not have access to this branch');
                return;
            }

            if (this.currentBranch?.id === branchIdInt) {
                this.open = false;
                return;
            }

            this.switching = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const response = await fetch('{{ route("master.branches.switch") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ branch_id: branchIdInt })
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.message || 'Failed to switch branch');
                }

                // Update local state
                this.currentBranch = this.userBranches.find(b => b.id === branchIdInt);
                this.open = false;
                this.showSuccess('Branch switched successfully');

                // Reload page to get fresh branch-specific data
                setTimeout(() => window.location.reload(), 300);
            } catch (error) {
                console.error('Branch switch error:', error);
                this.showError(error.message || 'Failed to switch branch');
            } finally {
                this.switching = false;
            }
        },

        showError(message) {
            this.dispatchNotification(message, 'error');
        },

        showSuccess(message) {
            this.dispatchNotification(message, 'success');
        },

        dispatchNotification(message, type) {
            const event = new CustomEvent('show-notification', {
                detail: { message, type }
            });
            window.dispatchEvent(event);
        }
    }
}
</script>
