@php
    $isEdit = isset($role);
    $selectedPermissions = $selectedPermissions ?? [];
@endphp

<div class="grid grid-cols-1 gap-6">

    {{-- Role Name --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Nama Role <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $role->name ?? '') }}"
            required
            placeholder="Contoh: Admin"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
    </div>

    {{-- Guard Name --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Guard Name <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="guard_name"
            value="{{ old('guard_name', $role->guard_name ?? 'web') }}"
            required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
    </div>

    {{-- Permissions --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <label class="text-sm font-medium dark:text-white">
                Permissions
            </label>

            {{-- Select All --}}
            <label class="flex items-center gap-2 text-sm cursor-pointer dark:text-white">
                <input
                    type="checkbox"
                    id="select-all-permissions"
                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                Pilih Semua
            </label>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($permissions as $permission)
                <label
                    class="flex items-center gap-2 rounded-lg border px-3 py-2 cursor-pointer
                           border-gray-200 dark:border-gray-700
                           hover:bg-gray-50 dark:hover:bg-white/[0.03]">

                    <input
                        type="checkbox"
                        name="permissions[]"
                        value="{{ $permission->name }}"
                        class="permission-checkbox rounded border-gray-300 dark:border-gray-600
                               text-blue-600 focus:ring-blue-500"
                        @checked(in_array(
                            $permission->name,
                            old('permissions', $selectedPermissions)
                        ))
                    >

                    <span class="text-sm text-gray-700 dark:text-white">
                        {{ $permission->name }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex justify-end gap-3 pt-4">
        <a
            href="{{ route('management.roles.index') }}"
            class="px-5 py-2.5 rounded-lg border text-sm font-medium
                   border-gray-300 text-gray-700 dark:text-white
                   dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-white/[0.03]">
            Batal
        </a>

        <button
            type="submit"
            class="px-5 py-2.5 rounded-lg bg-blue-600 dark:text-white
                   text-sm font-medium hover:bg-blue-700">
            {{ $isEdit ? 'Update Role' : 'Simpan Role' }}
        </button>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const selectAll = document.getElementById('select-all-permissions');
    const checkboxes = document.querySelectorAll('.permission-checkbox');

    if (!selectAll || !checkboxes.length) return;

    selectAll.addEventListener('change', () => {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            selectAll.checked = [...checkboxes].every(c => c.checked);
        });
    });

    // Initial state (edit page)
    selectAll.checked = [...checkboxes].every(c => c.checked);
});
</script>
@endpush
