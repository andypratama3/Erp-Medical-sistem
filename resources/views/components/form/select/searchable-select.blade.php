@props([
    'name',
    'id' => null,
    'options' => [],
    'selected' => null, // string | array
    'placeholder' => 'Select an option',
    'required' => false,
    'searchPlaceholder' => 'Search...',
    'allowClear' => true,
    'multiple' => false,
])

@php
    $id = $id ?? $name;
    $uniqueId = $id . '_' . uniqid();
@endphp

<div
    x-data="searchableSelect(
        '{{ $uniqueId }}',
        {{ json_encode($options) }},
        {{ json_encode($selected) }},
        '{{ $placeholder }}',
        '{{ $searchPlaceholder }}',
        {{ $allowClear ? 'true' : 'false' }},
        {{ $multiple ? 'true' : 'false' }}
    )"
    x-init="init()"
    class="relative w-full"
>
    {{-- Hidden inputs --}}
    <template x-if="!multiple">
        <input type="hidden"
               name="{{ $name }}"
               x-model="selectedValue"
               {{ $required ? 'required' : '' }}>
    </template>

    <template x-if="multiple">
        <template x-for="value in selectedValues" :key="value">
            <input type="hidden" name="{{ $name }}[]" :value="value">
        </template>
    </template>

    {{-- Trigger --}}
    <button type="button"
            @click="toggle()"
            @click.outside="close()"
            class="h-11 w-full flex items-center justify-between rounded-lg border px-4 text-sm
                   bg-transparent text-gray-800 dark:text-white
                   border-gray-300 dark:border-gray-700 dark:bg-gray-900
                   focus:ring-2 focus:ring-blue-500/20">

        <div class="flex flex-wrap gap-1 flex-1 text-left overflow-hidden">

            {{-- MULTIPLE --}}
            <template x-if="multiple && selectedLabels.length">
                <template x-for="(label, index) in selectedLabels" :key="index">
                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded
                                text-blue-800
                               dark:bg-blue-900 dark:text-white">
                        <span x-text="label"></span>
                        <button type="button"
                                @click.stop="remove(index)"
                                class="font-bold hover:text-red-500 text-error-500 font-bold size-1.5">
                            ×
                        </button>
                    </span>
                </template>
            </template>

            {{-- SINGLE --}}
            <template x-if="!multiple">
                <span x-text="selectedLabel || placeholder"
                      :class="{'text-gray-400': !selectedLabel}"
                      class="truncate"></span>
            </template>

            {{-- Placeholder MULTIPLE --}}
            <template x-if="multiple && !selectedLabels.length">
                <span class="text-gray-400 truncate" x-text="placeholder"></span>
            </template>
        </div>

        {{-- Arrow --}}
        <svg class="w-5 h-5 text-gray-500 transition-transform"
             :class="{ 'rotate-180': isOpen }"
             fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-width="1.5" d="M6 9l6 6 6-6"/>
        </svg>
    </button>

    {{-- Dropdown --}}
    <div x-show="isOpen"
         x-transition
         class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-900
                border border-gray-300 dark:border-gray-700
                rounded-lg shadow-lg max-h-64 overflow-hidden"
         style="display:none">

        {{-- Search --}}
        <div class="p-2 border-b border-gray-200 dark:border-gray-700">
            <input type="text"
                   x-model="searchQuery"
                   @input="filterOptions()"
                   placeholder="{{ $searchPlaceholder }}"
                   class="w-full h-9 rounded border px-3 text-sm
                          bg-transparent text-gray-800 dark:text-white
                          border-gray-300 dark:border-gray-700
                          focus:ring-2 focus:ring-blue-500/20"
                   @click.stop>
        </div>

        {{-- Options --}}
        <div class="max-h-48 overflow-y-auto">

            {{-- Clear --}}
            <template x-if="allowClear && !multiple && selectedValue">
                <button type="button"
                        @click="clearSingle()"
                        class="w-full text-left px-4 py-2 text-sm text-gray-500
                               hover:bg-gray-100 dark:hover:bg-gray-800">
                    <em>Clear selection</em>
                </button>
            </template>

            <template x-for="option in filteredOptions" :key="option.value">
                <button type="button"
                        @click="select(option)"
                        class="w-full text-left px-4 py-2 text-sm transition
                               hover:bg-blue-50 dark:hover:bg-gray-800"
                        :class="{
                            'bg-blue-100 dark:bg-gray-700 font-medium':
                            isSelected(option.value)
                        }">
                    <span :class="isSelected(option.value) ? 'text-white' : 'text-gray-800 dark:text-gray-200'" x-text="option.label"></span>
                </button>
            </template>

            <div x-show="filteredOptions.length === 0"
                 class="px-4 py-3 text-sm text-gray-500 text-center">
                No results found
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
function searchableSelect(id, options, selected, placeholder, searchPlaceholder, allowClear, multiple) {
    return {
        id,
        isOpen: false,
        searchQuery: '',
        placeholder,
        searchPlaceholder,
        allowClear,
        multiple,

        options,
        filteredOptions: options,

        // SINGLE
        selectedValue: '',
        selectedLabel: '',

        // MULTIPLE
        selectedValues: [],
        selectedLabels: [],

        init() {
            if (this.multiple) {
                this.selectedValues = Array.isArray(selected) ? selected : [];
                this.syncLabels();
            } else {
                this.selectedValue = selected ?? '';
                const opt = this.options.find(o => o.value == this.selectedValue);
                if (opt) this.selectedLabel = opt.label;
            }
        },

        toggle() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.$el.querySelector('input[type="text"]')?.focus();
                });
            }
        },

        close() {
            this.isOpen = false;
            this.searchQuery = '';
            this.filteredOptions = this.options;
        },

        select(option) {
            if (this.multiple) {
                const idx = this.selectedValues.indexOf(option.value);
                idx === -1
                    ? this.selectedValues.push(option.value)
                    : this.selectedValues.splice(idx, 1);
                this.syncLabels();
            } else {
                this.selectedValue = option.value;
                this.selectedLabel = option.label;
                this.close();
            }
        },

        remove(index) {
            this.selectedValues.splice(index, 1);
            this.syncLabels();
        },

        clearSingle() {
            this.selectedValue = '';
            this.selectedLabel = '';
            this.close();
        },

        isSelected(value) {
            return this.multiple
                ? this.selectedValues.includes(value)
                : this.selectedValue == value;
        },

        syncLabels() {
            this.selectedLabels = this.options
                .filter(o => this.selectedValues.includes(o.value))
                .map(o => o.label);
        },

        filterOptions() {
            const q = this.searchQuery.toLowerCase();
            this.filteredOptions = this.options.filter(o =>
                o.label.toLowerCase().includes(q)
            );
        }
    }
}
</script>
@endpush
@endonce
