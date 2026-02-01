@php
    $isEdit = isset($product);
@endphp



<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

    {{-- SKU --}}
    <div>
        <label class="block text-sm font-medium mb-1 dark:text-white">
            SKU <span class="text-red-500">*</span>
        </label>
        <input type="text" name="sku" required
               value="{{ old('sku', $product->sku ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400">
    </div>

    {{-- Nama Produk --}}
    <div class="md:col-span-4">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Nama Produk <span class="text-red-500">*</span>
        </label>
        <input type="text" name="name" required
               value="{{ old('name', $product->name ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white">
    </div>

    {{-- Unit --}}
    <div>
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Unit <span class="text-red-500">*</span>
        </label>
        <input type="text" name="unit" required
               value="{{ old('unit', $product->unit ?? 'PCS') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white">
    </div>

    {{-- Unit Price --}}
    <div>
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Unit Price <span class="text-red-500">*</span>
        </label>
        <div class="relative">

            <input type="text" id="unit_price_display" required
                   value="{{ old('unit_price', isset($product) ? number_format($product->unit_price, 0, ',', '.') : '0') }}"
                     class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            <input type="hidden" name="unit_price" id="unit_price_value"
                   value="{{ old('unit_price', $product->unit_price ?? 0) }}">
        </div>
    </div>

    {{-- Cost Price --}}
    <div>
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Cost Price
        </label>
        <div class="relative">
            {{-- <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm dark:text-gray-400">Rp</span> --}}
            <input type="text" id="cost_price_display"
                   value="{{ old('cost_price', isset($product) ? number_format($product->cost_price ?? 0, 0, ',', '.') : '0') }}"
                     class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            <input type="hidden" name="cost_price" id="cost_price_value"
                   value="{{ old('cost_price', $product->cost_price ?? 0) }}">
        </div>
    </div>

    {{-- Barcode --}}
    <div class="md:col-span-4">
        <label class="block text-sm font-medium mb-1 dark:text-white">Barcode</label>
        <input type="text" name="barcode"
               value="{{ old('barcode', $product->barcode ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white">
    </div>

    {{-- ProductGroup --}}
    <div class="md:col-span-4">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Product Group <span class="text-red-500">*</span>
        </label>
        <select name="product_group_id" required
                class="w-full h-11 rounded-lg border px-3 text-sm
                       bg-white text-gray-900
                       dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            <option value="">-- Pilih --</option>
            @foreach($productGroups as $pg)
                <option value="{{ $pg->id }}"
                    @selected(old('product_group_id',$product->product_group_id ?? '')==$pg->id)>
                    {{ $pg->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Product Type --}}
    <div class="md:col-span-4">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Product Type <span class="text-red-500">*</span>
        </label>
        <select name="product_type" required
                class="w-full h-11 rounded-lg border px-3 text-sm
                       bg-white text-gray-900
                       dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            <option value="">-- Pilih --</option>
            <option value="medical_device" @selected(old('product_type',$product->product_type ?? 'medical_device')=='medical_device')>
                Medical Device
            </option>
            <option value="pharmaceutical" @selected(old('product_type',$product->product_type ?? 'medical_device')=='pharmaceutical')>
                Pharmaceutical
            </option>
            <option value="consumable" @selected(old('product_type',$product->product_type ?? 'medical_device')=='consumable')>
                Consumable
            </option>
            <option value="other" @selected(old('product_type',$product->product_type ?? 'medical_device')=='other')>
                Other
            </option>
        </select>
    </div>

    {{-- Manufacture --}}
    <div class="md:col-span-4">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Manufacture <span class="text-red-500">*</span>
        </label>
        <select name="manufacture_id" required
                class="w-full h-11 rounded-lg border px-3 text-sm
                       bg-white text-gray-900
                       dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            <option value="">-- Pilih --</option>
            @foreach($manufactures as $m)
                <option value="{{ $m->id }}"
                    @selected(old('manufacture_id',$product->manufacture_id ?? '')==$m->id)>
                    {{ $m->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Category --}}
    <div class="md:col-span-4">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Category <span class="text-red-500">*</span>
        </label>
        <x-form.select.searchable-select
            name="category_id"
            :options="$categories->map(fn($o) => ['value' => $o->id, 'label' => $o->name])->toArray()"
            :selected="old('category_id', $product->category_id ?? '')"
            placeholder="-- Select Category --"
            searchPlaceholder="Search category..."
            :required="true"
        />
    </div>

    {{-- Min Stock --}}
    <div>
        <label class="block text-sm font-medium mb-1 dark:text-white">Min Stock</label>
        <input type="number" name="min_stock" min="0"
               value="{{ old('min_stock', $product->min_stock ?? 0) }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white">
    </div>

    {{-- Max Stock --}}
    <div>
        <label class="block text-sm font-medium mb-1 dark:text-white">Max Stock</label>
        <input type="number" name="max_stock" min="0"
               value="{{ old('max_stock', $product->max_stock ?? 0) }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white">
    </div>

    {{-- Is Taxable (Radio Button) --}}
    <div class="md:col-span-4">
        <div class="flex items-center gap-3 h-11"
             x-data="{ isChecked: '{{ old('is_taxable', $product->is_taxable ?? true) ? '1' : '0' }}' }">
            <label class="text-sm font-medium text-gray-800 dark:text-white">
                Taxable:
            </label>
            <div class="flex items-center gap-4">
                <label :class="isChecked === '0' ? 'text-gray-700 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                       class="relative flex cursor-pointer items-center gap-2 text-sm font-medium select-none">
                    <input class="sr-only" type="radio" name="is_taxable" value="0"
                           @checked(old('is_taxable', $product->is_taxable ?? true) == false)
                           @change="isChecked = '0'">
                    <span :class="isChecked === '0' ? 'border-blue-500 bg-blue-500' : 'bg-transparent border-gray-300 dark:border-gray-700'"
                          class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px] transition-all">
                        <span :class="isChecked === '0' ? 'block' : 'hidden'"
                              class="h-2 w-2 rounded-full bg-white"></span>
                    </span>
                    No
                </label>
                <label :class="isChecked === '1' ? 'text-gray-700 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                       class="relative flex cursor-pointer items-center gap-2 text-sm font-medium select-none">
                    <input class="sr-only" type="radio" name="is_taxable" value="1"
                           @checked(old('is_taxable', $product->is_taxable ?? true) == true)
                           @change="isChecked = '1'">
                    <span :class="isChecked === '1' ? 'border-blue-500 bg-blue-500' : 'bg-transparent border-gray-300 dark:border-gray-700'"
                          class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px] transition-all">
                        <span :class="isChecked === '1' ? 'block' : 'hidden'"
                              class="h-2 w-2 rounded-full bg-white"></span>
                    </span>
                    Yes
                </label>
            </div>
        </div>
    </div>

    {{-- Is Importable (Radio Button) --}}
    <div class="md:col-span-4">
        <div class="flex items-center gap-3 h-11"
             x-data="{ is_importable: '{{ old('is_importable', $product->is_importable ?? false) ? '1' : '0' }}' }">
            <label class="text-sm font-medium text-gray-800 dark:text-white">
                Importable:
            </label>
            <div class="flex items-center gap-4">

                <label :class="is_importable === '0' ? 'text-gray-700 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                       class="relative flex cursor-pointer items-center gap-2 text-sm font-medium select-none">
                    <input class="sr-only" type="radio" name="is_importable" value="0"
                           @checked(old('is_importable', $product->is_importable ?? false) == false)
                           @change="is_importable = '0'">
                    <span :class="is_importable === '0' ? 'border-blue-500 bg-blue-500' : 'bg-transparent border-gray-300 dark:border-gray-700'"
                          class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px] transition-all">
                        <span :class="is_importable === '0' ? 'block' : 'hidden'"
                              class="h-2 w-2 rounded-full bg-white"></span>
                    </span>
                    No
                </label>
                <label :class="is_importable === '1' ? 'text-gray-700 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                       class="relative flex cursor-pointer items-center gap-2 text-sm font-medium select-none">
                    <input class="sr-only" type="radio" name="is_importable" value="1"
                           @checked(old('is_importable', $product->is_importable ?? false) == true)
                           @change="is_importable = '1'">
                    <span :class="is_importable === '1' ? 'border-blue-500 bg-blue-500' : 'bg-transparent border-gray-300 dark:border-gray-700'"
                          class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px] transition-all">
                        <span :class="is_importable === '1' ? 'block' : 'hidden'"
                              class="h-2 w-2 rounded-full bg-white"></span>
                    </span>
                    Yes
                </label>
            </div>
        </div>
    </div>

    {{-- Status --}}
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Status <span class="text-red-500">*</span>
        </label>
        <select name="status" required
                class="w-full h-11 rounded-lg border px-3 text-sm
                       bg-white text-gray-900
                       dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            <option value="inactive">Inactive</option>
            <option value="active" @selected(old('status',$product->status ?? 'active')=='active')>
                Active
            </option>
            <option value="discontinued" @selected(old('status',$product->status ?? '')=='discontinued')>
                Discontinued
            </option>
        </select>
    </div>

    {{-- Description --}}
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">Description</label>
        <textarea name="description" rows="3"
                  class="w-full rounded-lg border px-3 py-2 text-sm
                         bg-white text-gray-900
                         dark:bg-gray-800 dark:border-gray-700 dark:text-white
                         dark:placeholder-gray-400">{{ old('description', $product->description ?? '') }}</textarea>
    </div>


    <div class="sm:col-span-2">
        <x-form.input.dropzone
            title="Upload Foto Product"
            name="images"
        />

    </div>

    <div class="sm:col-span-2">
        <x-form.input.dropzone-video
            title="Upload Video Product"
            name="video"
            accept="video/mp4,video/mov"
        />
    </div>


    {{-- Actions --}}
     <div class="sm:col-span-2 flex items-center justify-end gap-3 mt-4">
        <a href="{{ route('master.products.index') }}"
            class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-error-500 text-white shadow-theme-xs hover:bg-error-600">
            Cancel
        </a>
        <button type="submit"
            class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">

            {{ $isEdit ? 'Update Product' : 'Create Product' }}
        </button>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format Rupiah function
    function formatRupiah(angka) {
        const number = parseInt(angka.toString().replace(/[^0-9]/g, ''));
        if (isNaN(number)) return '0';
        return number.toLocaleString('id-ID');
    }

    // Setup price input
    function setupPriceInput(displayId, valueId) {
        const displayInput = document.getElementById(displayId);
        const valueInput = document.getElementById(valueId);

        if (!displayInput || !valueInput) return;

        displayInput.addEventListener('input', function(e) {
            const rawValue = e.target.value.replace(/[^0-9]/g, '');
            valueInput.value = rawValue;
            e.target.value = formatRupiah(rawValue);
        });

        displayInput.addEventListener('blur', function(e) {
            if (e.target.value === '') {
                e.target.value = '0';
                valueInput.value = '0';
            }
        });

        displayInput.addEventListener('focus', function(e) {
            if (e.target.value === '0') {
                e.target.value = '';
            }
        });
    }

    // Initialize price inputs
    setupPriceInput('unit_price_display', 'unit_price_value');
    setupPriceInput('cost_price_display', 'cost_price_value');
});
</script>
@endpush
