@props([
    'data' => [],
    'columns' => [],
    'searchable' => true,
    'filterable' => true,
])

@php
    use App\Helpers\StatusBadgeHelper;

    // Get all status badge classes untuk Alpine.js
    $statusClasses = StatusBadgeHelper::getStatusBadgeClasses();
@endphp

@push('styles')
    <style>
        /* ================================
        SweetAlert2 FIX & THEME
        ================================ */

        /* FIX BUG: grid bikin tombol disabled */
        .swal2-popup {
            display: flex !important;
            flex-direction: column;
        }

        /* Pastikan tombol selalu sejajar */
        .swal2-actions {
            display: flex !important;
            gap: 0.75rem;
        }

        /* Hilangkan backdrop putih bawaan */
        div:where(.swal2-container).swal2-backdrop-show,
        div:where(.swal2-container).swal2-noanimation {
            background: transparent !important;
        }

        /* ================================
        DARK MODE
        ================================ */
        .dark .swal2-popup {
            background-color: #020617 !important; /* slate-950 */
            color: #e5e7eb !important;
            border-radius: 1rem;
        }

        .dark .swal2-title {
            color: #f9fafb !important;
        }

        .dark .swal2-html-container {
            color: #cbd5f5 !important;
        }

        /* Icon warning */
        .dark .swal2-icon.swal2-warning {
            border-color: #facc15 !important;
            color: #facc15 !important;
        }

        /* Buttons */
        .dark .swal2-confirm {
            background-color: #dc2626 !important;
            color: #fff !important;
            border-radius: 0.75rem;
        }

        .dark .swal2-confirm:hover {
            background-color: #b91c1c !important;
        }

        .dark .swal2-cancel {
            background-color: #1f2937 !important;
            color: #e5e7eb !important;
            border-radius: 0.75rem;
        }

        .dark .swal2-cancel:hover {
            background-color: #374151 !important;
        }

        /* Backdrop dark */
        .dark .swal2-backdrop-show {
            background: rgba(2, 6, 23, 0.85) !important;
        }

    </style>
@endpush

<div
    x-data="{
        items: @js($data),
        search: '',
        statusFilter: '',
        statusClasses: @js($statusClasses),

        get filteredItems() {
            let result = this.items;

            if (this.search) {
                const keyword = this.search.toLowerCase();
                result = result.filter(item =>
                    Object.values(item).some(val => {
                        if (val === null || val === undefined) return false;

                        if (typeof val === 'object') {
                            return Object.values(val).some(v =>
                                String(v).toLowerCase().includes(keyword)
                            );
                        }

                        return String(val).toLowerCase().includes(keyword);
                    })
                );
            }

            if (this.statusFilter) {
                result = result.filter(item => {
                    if (!item.status || typeof item.status !== 'object') return true;
                    return item.status.value === this.statusFilter;
                });
            }

            return result;
        },

        /**
         * Get badge class dari status label
         * Menggunakan StatusBadgeHelper configuration
         */
        getStatusClass(statusLabel) {
            return this.statusClasses[statusLabel] || '';
        }
    }"
    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
>

    <div class="max-w-full overflow-x-auto">
        <table class="w-full min-w-[800px]">
            <thead>
                <tr class="border-b dark:text-white border-gray-100 dark:border-gray-800">
                    @foreach($columns as $column)
                        <th class="px-6 py-4 text-left dark:text-white text-sm font-medium">
                            {{ $column['label'] }}
                        </th>
                    @endforeach
                    <th class="px-6 py-4 text-center text-sm font-medium">
                        Action
                    </th>
                </tr>
            </thead>

            <tbody>
                <template x-for="item in filteredItems" :key="item.id">
                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">

                        <template x-for="column in @js($columns)" :key="column.key">
                            <td class="px-6 py-4">

                                <template x-if="column.type === 'text'">
                                    <p class="text-sm text-gray-700 dark:text-white"
                                       x-text="item[column.key]"></p>
                                </template>

                                <template x-if="column.type === 'tag'">
                                    <span class="inline-block px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-500/15 text-blue-700 dark:text-blue-400 text-xs"
                                          x-text="item[column.key]"></span>
                                </template>

                                <template x-if="column.type === 'date'">
                                    <p class="text-sm text-gray-700 dark:text-white"
                                       x-text="item[column.key]"></p>
                                </template>


                                <template x-if="column.type === 'badge' && item[column.key]">
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium"
                                          :class="getStatusClass(item[column.key].color)"
                                          x-text="item[column.key].label"></span>
                                </template>

                            </td>
                        </template>

                        <td class="px-4 py-4 text-sm font-medium text-right whitespace-nowrap">
                            <div class="flex justify-center relative">
                                <x-common.table-dropdown>
                                    {{-- BUTTON --}}
                                    <x-slot name="button">
                                        <button
                                            type="button"
                                            class="text-gray-800 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors"
                                        >
                                            <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M5.99902 10.245C6.96552 10.245 7.74902 11.0285 7.74902 11.995V12.005C7.74902 12.9715 6.96552 13.755 5.99902 13.755C5.03253 13.755 4.24902 12.9715 4.24902 12.005V11.995C4.24902 11.0285 5.03253 10.245 5.99902 10.245ZM17.999 10.245C18.9655 10.245 19.749 11.0285 19.749 11.995V12.005C19.749 12.9715 18.9655 13.755 17.999 13.755C17.0325 13.755 16.249 12.9715 16.249 12.005V11.995C16.249 11.0285 17.0325 10.245 17.999 10.245ZM13.749 11.995C13.749 11.0285 12.9655 10.245 11.999 10.245C11.0325 10.245 10.249 11.0285 10.249 11.995V12.005C10.249 12.9715 11.0325 13.755 11.999 13.755C12.9655 13.755 13.749 12.9715 13.749 12.005V11.995Z" />
                                            </svg>
                                        </button>
                                    </x-slot>

                                    {{-- CONTENT --}}
                                    <x-slot name="content">
                                        {{-- SHOW --}}
                                        <template x-if="item.actions?.show">
                                            <a
                                                :href="item.actions.show"
                                                class="flex w-full px-3 py-2 text-theme-xs font-medium
                                                    text-gray-800 hover:bg-gray-100 hover:text-gray-700
                                                    dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300
                                                    transition-colors"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Lihat
                                            </a>
                                        </template>

                                        {{-- EDIT --}}
                                        <template x-if="item.actions?.edit && item.actions.edit !== null">
                                            <a
                                                :href="item.actions.edit"
                                                class="flex w-full px-3 py-2 text-theme-xs font-medium
                                                    text-gray-500 hover:bg-gray-100 hover:text-blue-600
                                                    dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-blue-400
                                                    transition-colors"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Edit
                                            </a>
                                        </template>

                                        {{-- DELETE --}}
                                        <template x-if="item.actions?.delete && item.actions.delete !== null">
                                            <button
                                                type="button"
                                                class="flex w-full px-3 py-2 text-theme-xs font-medium text-left
                                                    text-red-600 hover:bg-red-50 dark:text-red-500
                                                    dark:hover:bg-red-500/10 transition-colors
                                                    js-confirm-delete"
                                                :data-url="item.actions.delete"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Delete
                                            </button>
                                        </template>
                                    </x-slot>
                                </x-common.table-dropdown>
                            </div>
                        </td>

                    </tr>
                </template>

                {{-- Empty State --}}
                <template x-if="filteredItems.length === 0">
                    <tr>
                        <td :colspan="@js(count($columns) + 1)"
                            class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada data</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm">Coba ubah filter atau cari dengan kata kunci lain</p>
                            </div>
                        </td>
                    </tr>
                </template>

            </tbody>
        </table>
    </div>
</div>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
/**
 * SweetAlert Helper untuk delete confirmation
 */
window.swalConfirm = function ({
    title = 'Anda yakin?',
    text = 'Data yang sudah dihapus tidak dapat dikembalikan!',
    confirmText = 'Hapus!',
    cancelText = 'Batal',
    icon = 'warning',
    onConfirm = () => {}
}) {
    return Swal.fire({
        title,
        text,
        icon,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        reverseButtons: true,
        allowOutsideClick: false,
        allowEscapeKey: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        didOpen: () => {
            const popup = Swal.getPopup();
            popup && popup.offsetHeight;
        }
    }).then(result => {
        if (result.isConfirmed) {
            onConfirm();
        }
    });
};
</script>

<script>
/**
 * Handle delete button click
 */
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.js-confirm-delete');
    if (!btn) return;

    e.preventDefault();

    const url = btn.dataset.url;
    if (!url) return;

    swalConfirm({
        onConfirm: () => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
            `;

            document.body.appendChild(form);
            form.submit();
        }
    });
});
</script>

@endpush
