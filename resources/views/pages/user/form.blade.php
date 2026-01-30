@php
    $isEdit = isset($user);
@endphp

<div class="grid grid-cols-1 gap-6">

    {{-- Nama --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Nama <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $user->name ?? '') }}"
            required
            placeholder="Nama lengkap"
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
        <input
            type="email"
            name="email"
            value="{{ old('email', $user->email ?? '') }}"
            required
            placeholder="email@example.com"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
    </div>

    {{-- appenda Select multiple Branch --}}

    

    {{-- Password --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Password
            @if($isEdit)
                <span class="text-xs text-gray-400">(Opsional)</span>
            @else
                <span class="text-red-500">*</span>
            @endif
        </label>
        <input
            type="password"
            name="password"
            {{ $isEdit ? '' : 'required' }}
            placeholder="Minimal 6 karakter"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
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
        <input
            type="password"
            name="password_confirmation"
            placeholder="Ulangi password"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
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

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($roles as $role)
                <label
                    class="flex items-center gap-2 rounded-lg border px-3 py-2 cursor-pointer
                           border-gray-200 dark:border-gray-700
                           hover:bg-gray-50 dark:hover:bg-white/[0.03]">

                    <input
                        type="checkbox"
                        name="roles[]"
                        value="{{ $role->name }}"
                        class="rounded border-gray-300 dark:border-gray-600
                               text-blue-600 focus:ring-blue-500"
                        @checked(in_array(
                            $role->name,
                            old('roles', $selectedRoles ?? [])
                        ))
                    >

                    <span class="text-sm text-gray-700 dark:text-white">
                        {{ $role->name }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex justify-end gap-3 pt-4">
        <a
            href="{{ route('master.users.index') }}"
            class="px-5 py-2.5 rounded-lg border text-sm font-medium
                   border-gray-300 text-gray-700
                   dark:border-gray-700 dark:text-white
                   hover:bg-gray-50 dark:hover:bg-white/[0.03]">
            Batal
        </a>

        <button
            type="submit"
            class="px-5 py-2.5 rounded-lg bg-blue-600 dark:text-white
                   text-sm font-medium hover:bg-blue-700">
            {{ $isEdit ? 'Update User' : 'Simpan User' }}
        </button>
    </div>

</div>
