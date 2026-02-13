@php
    $isEdit = isset($discountPolicy);
@endphp

@push('styles')
    <style>
        
    </style>
@endpush

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

    {{-- Department Code --}}
    <div class="sm:col-span-1">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Department Code <span class="text-red-500">*</span>
        </label>
         <x-form.select.searchable-select
                    name="department_code"
                    :options="$departments->map(fn($o) => ['value' => $o->code, 'label' => $o->name])->toArray()"
                    :selected="old('department_code', $discountPolicy->department_code ?? '')"
                    placeholder="-- Select Department --"
                    searchPlaceholder="Search department..."
                    :required="true" />
        @error('department_code')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Level Name --}}
    <div class="sm:col-span-1">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Level Name <span class="text-red-500">*</span>
        </label>
        <input type="text" name="level_name" required
               value="{{ old('level_name', $discountPolicy->level_name ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="e.g., Manager, Staff">
        @error('level_name')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Segment --}}
    <div class="sm:col-span-1">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Segment <span class="text-red-500">*</span>
        </label>
        <input type="text" name="segment" required
               value="{{ old('segment', $discountPolicy->segment ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="e.g., Retail, Wholesale">
        @error('segment')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Max Discount Percent --}}
    <div class="sm:col-span-1">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Max Discount Percent <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input type="number" name="max_discount_percent" required
                   step="0.01" min="0" max="100"
                   value="{{ old('max_discount_percent', $discountPolicy->max_discount_percent ?? '') }}"
                   class="w-full h-11 rounded-lg border px-3 pr-10 text-sm
                          bg-white text-gray-900
                          focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                          dark:bg-gray-800 dark:border-gray-700 dark:text-white
                          dark:placeholder-gray-400"
                   placeholder="0.00">
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">%</span>
        </div>
        @error('max_discount_percent')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status --}}
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Status <span class="text-red-500">*</span>
        </label>
        <select name="status" required
                class="w-full h-11 rounded-lg border px-3 text-sm
                       bg-white text-gray-900
                       focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                       dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            <option value="active" @selected(old('status', $discountPolicy->status ?? 'active') == 'active')>Active</option>
            <option value="inactive" @selected(old('status', $discountPolicy->status ?? '') == 'inactive')>Inactive</option>
        </select>
        @error('status')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Notes --}}
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Notes
        </label>
        <textarea name="notes" rows="3"
                  class="w-full rounded-lg border px-3 py-2 text-sm
                         bg-white text-gray-900
                         focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                         dark:bg-gray-800 dark:border-gray-700 dark:text-white
                         dark:placeholder-gray-400"
                  placeholder="Additional notes...">{{ old('notes', $discountPolicy->notes ?? '') }}</textarea>
        @error('notes')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Actions --}}
    <div class="sm:col-span-2 flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ route('master.discount-policy.index') }}"
           class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-2.5 text-sm
                  bg-white text-gray-700 border border-gray-300
                  hover:bg-gray-50
                  dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Cancel
        </a>
        <button type="submit"
                class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-2.5 text-sm
                       bg-blue-600 text-white shadow-sm
                       hover:bg-blue-700
                       focus:ring-2 focus:ring-blue-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ $isEdit ? 'Update Policy' : 'Create Policy' }}
        </button>
    </div>

</div>