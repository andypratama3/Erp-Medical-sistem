
@php
    $isEdit = isset($inventory);
@endphp



<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

    {{-- Product --}}
    <div class="md:col-span-4">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Product <span class="text-red-500">*</span>
        </label>
        <x-form.select.searchable-select
            name="product_id"
            :options="$products->map(fn($o) => ['value' => $o->id, 'label' => $o->name])->toArray()"
            :selected="old('product_id', $inventory->product_id ?? '')"
            placeholder="-- Select Product --"
            searchPlaceholder="Search product..."
            :required="true"
        />
    </div>


    <div class="md:col-span-4">
        <label class="block text-sm font-medium mb-1 dark:text-white">
          Quantity <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input type="number" name="quantity" min="0"
               value="{{ old('quantity', $inventory->quantity ?? 0) }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white">
        </div>
    </div>

    {{-- Inventory Type --}}
    <div class="md:col-span-4">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Inventory Type <span class="text-red-500">*</span>
        </label>
        <x-form.select.searchable-select
            name="type"
            :options="[
                ['value' => 'in', 'label' => 'In'],
                ['value' => 'out', 'label' => 'Out'],
                ['value' => 'other', 'label' => 'Other'],
            ]"
            :selected="old('type', $inventory->type ?? 'in')"
            placeholder="-- Select Type --"
            :required="true"
        />
    </div>


    {{-- notes --}}
    <div class="md:col-span-4">
        <label class="block text-sm font-medium mb-1 dark:text-white">Notes</label>
        <textarea name="notes" rows="3"
                  class="w-full rounded-lg border px-3 py-2 text-sm
                         bg-white text-gray-900
                         dark:bg-gray-800 dark:border-gray-700 dark:text-white
                         dark:placeholder-gray-400">{{ old('notes', $inventory->notes ?? '') }}</textarea>
    </div>



    {{-- Actions --}}
     <div class="sm:col-span-4 flex items-center justify-end gap-3 mt-4">
        <a href="{{ route('wqs.inventory.index') }}"
            class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-error-500 text-white shadow-theme-xs hover:bg-error-600">
            Cancel
        </a>
        <button type="submit"
            class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">

            {{ $isEdit ? 'Update' : 'Create' }}
        </button>
    </div>

</div>


