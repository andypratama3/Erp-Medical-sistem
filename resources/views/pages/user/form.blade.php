@php
$isEdit = isset($user);
@endphp


<div class="grid grid-cols-1 gap-6">
    {{-- Nama --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Nama <span class="text-red-500">*</span>
        </label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required placeholder="Nama lengkap"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
    </div>

    {{-- Email --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Email <span class="text-red-500">*</span>
        </label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
            placeholder="email@example.com" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
    </div>

    {{-- Roles --}}
    <div>
        <label class="mb-2 block text-sm font-medium dark:text-white">
            Role <span class="text-red-500">*</span>
        </label>
        <x-form.select.searchable-select name="roles" :options="$roles->map(fn($o) => ['value' => $o->name, 'label' => $o->name])->toArray()" :selected="old('roles', $selectedRoles ?? [])"
            placeholder="-- Select Role --" searchPlaceholder="Search role..." :multiple=false :required="true" />
    </div>


    <div >
        <label class="mb-2 block text-sm font-medium dark:text-white">
            Branches <span class="text-red-500">*</span>
        </label>

        <x-form.select.searchable-select name="branches" :options="$branchs->map(fn($o) => [
                'value' => $o->id,
                'label' => $o->name
            ])->toArray()" :selected="old(
                'branches',
                isset($user) ? $user->branches->pluck('id')->toArray() : []
            )" placeholder="-- Select Branch --" searchPlaceholder="Search branch..." :multiple="true"
            :required="true" />
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Default Branch <span class="text-red-500">*</span>
        </label>
        <x-form.select.searchable-select name="default_branch_id"
            :options="$branchs->map(fn($o) => ['value' => $o->id, 'label' => $o->name])->toArray()"
            :selected="old('default_branch_id', $user->default_branch_id ?? '')" placeholder="-- Select Branch --"
            searchPlaceholder="Search branch..." :multiple=false :required="true" />
    </div>

    {{-- Password --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Password
            @if ($isEdit)
            <span class="text-xs text-gray-400">(Opsional)</span>
            @else
            <span class="text-red-500">*</span>
            @endif
        </label>
        <input type="password" name="password" {{ $isEdit ? '' : 'required' }} placeholder="Minimal 6 karakter" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
    </div>

    {{-- Konfirmasi Password --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Konfirmasi Password
        </label>
        <input type="password" name="password_confirmation" placeholder="Ulangi password" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
    </div>

    {{-- Action Buttons --}}
    <div class="flex justify-end gap-3 pt-4">
        <a href="{{ route('management.users.index') }}" class="px-5 py-2.5 rounded-lg border text-sm font-medium
                   border-gray-300 text-gray-700
                   dark:border-gray-700 dark:text-white
                   hover:bg-gray-50 dark:hover:bg-white/[0.03]">
            Batal
        </a>

        <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 dark:text-white
                   text-sm font-medium hover:bg-blue-700">
            {{ $isEdit ? 'Update User' : 'Simpan User' }}
        </button>
    </div>

</div>
