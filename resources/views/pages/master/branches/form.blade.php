@php
$isEdit = isset($branch);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    {{-- Code --}}
    <div>
        <label class="block text-sm font-medium mb-1 dark:text-gray-300">
            Code <span class="text-red-500">*</span>
        </label>
        <input type="text" name="code" required value="{{ old('code', $branch->code ?? '') }}" class="h-11 w-full rounded-lg border px-4 text-sm
                            bg-white text-gray-900
                            dark:bg-gray-900 dark:border-gray-700 dark:text-white
                            @error('code') border-red-500 @enderror">
        @error('code')
        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Name --}}
    <div>
        <label class="block text-sm font-medium mb-1 dark:text-gray-300">
            Name <span class="text-red-500">*</span>
        </label>
        <input type="text" name="name" required value="{{ old('name', $branch->name ?? '') }}" class="h-11 w-full rounded-lg border px-4 text-sm
                            bg-white text-gray-900
                            dark:bg-gray-900 dark:border-gray-700 dark:text-white
                            @error('name') border-red-500 @enderror">
        @error('name')
        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- City --}}
    <div>
        <label class="block text-sm font-medium mb-1 dark:text-gray-300">
            City
        </label>
        <input type="text" name="city" value="{{ old('city', $branch->city ?? '') }}" class="h-11 w-full rounded-lg border px-4 text-sm
                            bg-white text-gray-900
                            dark:bg-gray-900 dark:border-gray-700 dark:text-white">
    </div>

    {{-- Province --}}
    <div>
        <label class="block text-sm font-medium mb-1 dark:text-gray-300">
            Province
        </label>
        <input type="text" name="province" value="{{ old('province', $branch->province ?? '') }}" class="h-11 w-full rounded-lg border px-4 text-sm
                            bg-white text-gray-900
                            dark:bg-gray-900 dark:border-gray-700 dark:text-white">
    </div>

    {{-- Phone --}}
    <div>
        <label class="block text-sm font-medium mb-1 dark:text-gray-300">
            Phone
        </label>
        <input type="text" name="phone" value="{{ old('phone', $branch->phone ?? '') }}" class="h-11 w-full rounded-lg border px-4 text-sm
                            bg-white text-gray-900
                            dark:bg-gray-900 dark:border-gray-700 dark:text-white">
    </div>

    {{-- Email --}}
    <div>
        <label class="block text-sm font-medium mb-1 dark:text-gray-300">
            Email
        </label>
        <input type="email" name="email" value="{{ old('email', $branch->email ?? '') }}" class="h-11 w-full rounded-lg border px-4 text-sm
                            bg-white text-gray-900
                            dark:bg-gray-900 dark:border-gray-700 dark:text-white">
    </div>

    {{-- Manager --}}
    <div>
        <label class="block text-sm font-medium mb-1 dark:text-gray-300">
            Manager
        </label>
        <x-form.select.searchable-select name="manager_id" :options="$managers->map(fn($m) => [
                        'value' => $m->id,
                        'label' => $m->name
                    ])" :selected="old('manager_id', $branch->manager_id ?? null)"
            placeholder="-- Select Manager --" />
    </div>

    {{-- Status --}}
    <div>
        <label class="block text-sm font-medium mb-1 dark:text-gray-300">
            Status <span class="text-red-500">*</span>
        </label>
        <select name="status" required class="h-11 w-full rounded-lg border px-4 text-sm
                            bg-white text-gray-900
                            dark:bg-gray-900 dark:border-gray-700 dark:text-white">
            <option value="active" {{ old('status', $branch->status ?? 'active') == 'active' ? 'selected' : '' }}>
                Active
            </option>
            <option value="inactive" {{ old('status', $branch->status ?? '') == 'inactive' ? 'selected' : '' }}>
                Inactive
            </option>
        </select>
    </div>

    {{-- Address --}}
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-gray-300">
            Address
        </label>
        <textarea name="address" rows="3"
            class="w-full rounded-lg border px-4 py-2 text-sm
            bg-white text-gray-900
            dark:bg-gray-900 dark:border-gray-700 dark:text-white">{{ old('address', $branch->address ?? '') }}</textarea>
    </div>


</div>

{{-- Actions --}}
<div class="flex items-center justify-end gap-4 mt-6">
    <a href="{{ route('master.branches.index') }}" class="px-6 py-3 text-sm font-medium rounded-lg
                        bg-error-500 text-white hover:bg-error-600">
        Cancel
    </a>
    <button type="submit" class="px-6 py-3 text-sm font-medium rounded-lg
                        bg-brand-500 text-white hover:bg-brand-600">
        {{ $isEdit ? 'Update Branch' : 'Save Branch' }}
    </button>
</div>
