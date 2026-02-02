{{-- Notification Dropdown Component with Real-time Updates --}}
<div class="relative"
     x-data="{
         dropdownOpen: false,
         notifying: {{ auth()->user()->unreadNotificationsCount > 0 ? 'true' : 'false' }},
         unreadCount: {{ auth()->user()->unreadNotificationsCount }},
         notifications: [],
         loading: false,

         init() {
             this.fetchNotifications();
             // Auto-refresh every 30 seconds
             setInterval(() => {
                 this.fetchNotifications();
             }, 30000);

             // Listen for custom refresh event
             window.addEventListener('refresh-notifications', () => {
                 this.fetchNotifications();
             });
         },

         async fetchNotifications() {
             try {
                 const response = await fetch('/api/notifications/recent');
                 const data = await response.json();
                 this.notifications = data.notifications || [];
                 this.unreadCount = data.unread_count || 0;
                 this.notifying = this.unreadCount > 0;
             } catch (error) {
                 console.error('Failed to fetch notifications:', error);
             }
         },

         toggleDropdown() {
             this.dropdownOpen = !this.dropdownOpen;
             if (this.dropdownOpen && this.notifications.length === 0) {
                 this.fetchNotifications();
             }
         },

         closeDropdown() {
             this.dropdownOpen = false;
         },

         async markAsRead(notificationId) {
             try {
                 await fetch(`/api/notifications/${notificationId}/read`, { method: 'POST' });
                 this.fetchNotifications();
             } catch (error) {
                 console.error('Failed to mark notification as read:', error);
             }
         },

         async markAllAsRead() {
             if (!confirm('Mark all notifications as read?')) return;

             try {
                 await fetch('/api/notifications/mark-all-read', { method: 'POST' });
                 this.fetchNotifications();
             } catch (error) {
                 console.error('Failed to mark all as read:', error);
             }
         },

         handleItemClick(notification) {
             if (!notification.read_at) {
                 this.markAsRead(notification.id);
             }
             window.location.href = notification.url;
         },

         getNotificationIcon(type) {
             const icons = {
                 'sales_do_submitted': '📋',
                 'stock_check_completed': '✅',
                 'delivery_dispatched': '🚚',
                 'delivery_completed': '📦',
                 'invoice_created': '🧾',
                 'invoice_approved': '✔️',
                 'payment_received': '💰',
                 'stock_low': '⚠️',
                 'stock_out': '❌',
             };
             return icons[type] || '🔔';
         },

         getTimeAgo(date) {
             const seconds = Math.floor((new Date() - new Date(date)) / 1000);

             let interval = seconds / 31536000;
             if (interval > 1) return Math.floor(interval) + ' years ago';

             interval = seconds / 2592000;
             if (interval > 1) return Math.floor(interval) + ' months ago';

             interval = seconds / 86400;
             if (interval > 1) return Math.floor(interval) + ' days ago';

             interval = seconds / 3600;
             if (interval > 1) return Math.floor(interval) + ' hours ago';

             interval = seconds / 60;
             if (interval > 1) return Math.floor(interval) + ' minutes ago';

             return 'Just now';
         }
     }"
     @click.away="closeDropdown()">

    <!-- Notification Button -->
    <button
        class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-gray-900 h-11 w-11 hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
        @click="toggleDropdown()"
        type="button"
        aria-label="Notifications"
    >
        <!-- Notification Badge -->
        <span
            x-show="notifying"
            x-text="unreadCount"
            class="absolute right-0 flex items-center justify-center w-5 h-5 text-xs font-medium text-white rounded-full top-0.5 z-1 bg-orange-500"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-50"
            x-transition:enter-end="opacity-100 scale-100"
        >
        </span>

        <!-- Bell Icon -->
        <svg
            class="fill-current"
            width="20"
            height="20"
            viewBox="0 0 20 20"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z"
                fill=""
            />
        </svg>
    </button>

    <!-- Dropdown Panel -->
    <div
        x-show="dropdownOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute -right-[240px] mt-[17px] flex h-[480px] w-[350px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark sm:w-[361px] lg:right-0 z-9999"
        style="display: none;"
        @click.stop
    >
        <!-- Dropdown Header -->
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 dark:border-gray-800">
            <h5 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Notifications
                <span x-show="unreadCount > 0" class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                    (<span x-text="unreadCount"></span> new)
                </span>
            </h5>
            <div class="flex items-center gap-2">
                <!-- Mark All as Read Button -->
                <button
                    x-show="unreadCount > 0"
                    @click="markAllAsRead()"
                    class="text-xs text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300"
                    type="button"
                    title="Mark all as read"
                >
                    Mark all read
                </button>

                <!-- Close Button -->
                <button
                    @click="closeDropdown()"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                    type="button"
                    aria-label="Close"
                >
                    <svg
                        class="fill-current"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z"
                            fill=""
                        />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Notification List -->
        <ul class="flex flex-col h-auto overflow-y-auto custom-scrollbar">
            <!-- Loading State -->
            <li x-show="loading" class="p-8 text-center">
                <svg class="w-8 h-8 mx-auto text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Loading...</p>
            </li>

            <!-- Empty State -->
            <li x-show="!loading && notifications.length === 0" class="p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">No notifications yet</p>
            </li>

            <!-- Notification Items -->
            <template x-for="notification in notifications" :key="notification.id">
                <li>
                    <a
                        @click.prevent="handleItemClick(notification)"
                        :class="!notification.read_at ? 'bg-brand-50 dark:bg-brand-500/10' : ''"
                        class="flex gap-3 rounded-lg border-b border-gray-100 p-3 px-4.5 py-3 cursor-pointer transition hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-white/5"
                        href="#"
                    >
                        <!-- Icon -->
                        <span class="flex items-center justify-center flex-shrink-0 w-10 h-10 text-xl rounded-full bg-gray-100 dark:bg-gray-800"
                              x-text="getNotificationIcon(notification.type)">
                        </span>

                        <!-- Content -->
                        <span class="flex-1 block">
                            <span class="mb-1.5 flex items-start justify-between">
                                <span class="font-medium text-theme-sm text-gray-800 dark:text-white/90"
                                      x-text="notification.title">
                                </span>
                                <span x-show="!notification.read_at"
                                      class="flex-shrink-0 w-2 h-2 ml-2 rounded-full bg-brand-500">
                                </span>
                            </span>

                            <p class="mb-2 text-theme-sm text-gray-600 dark:text-gray-400 line-clamp-2"
                               x-text="notification.message">
                            </p>

                            <span class="flex items-center gap-2 text-gray-500 text-theme-xs dark:text-gray-400">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                <span x-text="getTimeAgo(notification.created_at)"></span>
                            </span>
                        </span>
                    </a>
                </li>
            </template>
        </ul>

        <!-- View All Button -->
        <a
            {{-- href="{{ route('notifications.index') }}" --}}
            class="mt-3 flex justify-center rounded-lg border border-gray-300 bg-white p-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
            @click="closeDropdown()"
        >
            View All Notifications
        </a>
    </div>
</div>

{{-- Add CSRF token meta tag if not already present --}}
@push('scripts')
<script>
    // Configure fetch to include CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
        window.fetch = new Proxy(window.fetch, {
            apply(target, thisArg, args) {
                const [url, config = {}] = args;
                if (config.method && config.method.toUpperCase() === 'POST') {
                    config.headers = {
                        ...config.headers,
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    };
                }
                return Reflect.apply(target, thisArg, [url, config]);
            }
        });
    }
</script>
@endpush
