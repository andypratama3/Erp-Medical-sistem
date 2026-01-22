@props([
    'name',
    'id' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => 'Select an option',
    'required' => false,
    'searchPlaceholder' => 'Search...',
    'allowClear' => true,
])

@php
    $id = $id ?? $name;
    $uniqueId = $id . '_' . uniqid();
@endphp

<div x-data="searchableSelect('{{ $uniqueId }}', {{ json_encode($options) }}, '{{ $selected }}', '{{ $placeholder }}', '{{ $searchPlaceholder }}', {{ $allowClear ? 'true' : 'false' }})"
     x-init="init()"
     class="relative w-full">

    <!-- Hidden input for form submission -->
    <input type="hidden" name="{{ $name }}" x-model="selectedValue" {{ $required ? 'required' : '' }}>

    <!-- Custom Select Trigger -->
    <button type="button"
            @click="toggle()"
            @click.outside="close()"
            class="h-11 w-full flex items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition"
            :class="{ 'border-blue-300 ring-3 ring-blue-500/10': isOpen }">
        <span x-text="selectedLabel || '{{ $placeholder }}'"
              :class="{ 'text-gray-400 dark:text-white': !selectedLabel }"
              class="truncate"></span>
        <svg class="stroke-current text-gray-500 dark:text-gray-400 transition-transform"
             :class="{ 'rotate-180': isOpen }"
             width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </button>

    <!-- Dropdown -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg max-h-60 overflow-hidden"
         style="display: none;">

        <!-- Search Input -->
        <div class="p-2 border-b border-gray-200 dark:border-gray-700">
            <input type="text"
                   x-model="searchQuery"
                   @input="filterOptions()"
                   placeholder="{{ $searchPlaceholder }}"
                   class="w-full h-9 rounded border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:text-white dark:border-gray-700 dark:bg-gray-800 focus:border-blue-300 focus:ring-2 focus:ring-blue-500/10 focus:outline-none"
                   @click.stop>
        </div>

        <!-- Options List -->
        <div class="overflow-y-auto max-h-48">
            <!-- Clear Option -->
            <template x-if="allowClear && selectedValue">
                <button type="button"
                        @click="select('', '{{ $placeholder }}')"
                        class="w-full text-left px-4 py-2 text-sm text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <em>Clear selection</em>
                </button>
            </template>

            <!-- Options -->
            <template x-for="option in filteredOptions" :key="option.value">
                <button type="button"
                        @click="select(option.value, option.label)"
                        class="w-full text-left px-4 py-2 text-sm text-gray-800 dark:text-white hover:bg-blue-50 dark:hover:bg-gray-800 transition"
                        :class="{ 'bg-blue-100 dark:bg-gray-700 font-medium': selectedValue == option.value }">
                    <span x-text="option.label"></span>
                </button>
            </template>

            <!-- No Results -->
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
    function searchableSelect(id, options, selected, placeholder, searchPlaceholder, allowClear) {
        return {
            id: id,
            isOpen: false,
            searchQuery: '',
            selectedValue: selected || '',
            selectedLabel: '',
            placeholder: placeholder,
            searchPlaceholder: searchPlaceholder,
            allowClear: allowClear,
            options: options,
            filteredOptions: options,

            init() {
                // Set initial selected label
                if (this.selectedValue) {
                    const option = this.options.find(opt => opt.value == this.selectedValue);
                    if (option) {
                        this.selectedLabel = option.label;
                    }
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

            select(value, label) {
                this.selectedValue = value;
                this.selectedLabel = label === this.placeholder ? '' : label;
                this.close();
            },

            filterOptions() {
                const query = this.searchQuery.toLowerCase();
                this.filteredOptions = this.options.filter(option =>
                    option.label.toLowerCase().includes(query)
                );
            }
        }
    }
</script>
@endpush
@endonce
