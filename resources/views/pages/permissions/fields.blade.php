@props([
'permission' => null, // model Permission (nullable)
'isEdit' => false,
])

@php
$getValue = fn ($field, $default = null) =>
old($field, $permission?->{$field} ?? $default);
@endphp

<div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

    {{-- Name --}}
    <div class="col-span-1 sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Nama Permission <span class="text-red-500">*</span>
        </label>
        <input type="text" name="name" required value="{{ $getValue('name') }}" placeholder="permission.create" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
    </div>

    {{-- Guard --}}
    <div class="col-span-1">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Guard <span class="text-red-500">*</span>
        </label>
        <select name="guard_name" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">Pilih Guard</option>
            @foreach (['web', 'api'] as $guard)
            <option value="{{ $guard }}" @selected($getValue('guard_name', 'web' )===$guard)>
                {{ strtoupper($guard) }}
            </option>
            @endforeach
        </select>
    </div>
</div>

</div>
