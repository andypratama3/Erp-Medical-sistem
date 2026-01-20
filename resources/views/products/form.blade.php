@php
    $isEdit = isset($product);
@endphp

<div class="row g-3">

    {{-- SKU --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            SKU <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="sku"
            value="{{ old('sku', $product->sku ?? '') }}"
            required
            placeholder="ALK-001, OBT-002"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
        @error('sku')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Nama Produk --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Nama Produk <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $product->name ?? '') }}"
            required
            placeholder="Nama produk"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
        @error('name')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Type --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Tipe <span class="text-red-500">*</span>
        </label>
        <select
            name="type"
            required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
            <option value="">-- Pilih Tipe --</option>
            <option value="SINGLE" @selected(old('type', $product->type ?? '') == 'SINGLE')>SINGLE</option>
            <option value="BUNDLE" @selected(old('type', $product->type ?? '') == 'BUNDLE')>BUNDLE</option>
        </select>
        @error('type')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Unit --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Unit <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="unit"
            value="{{ old('unit', $product->unit ?? '') }}"
            required
            placeholder="pcs / box / unit"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
        @error('unit')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Barcode --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Barcode
        </label>
        <input
            type="text"
            name="barcode"
            value="{{ old('barcode', $product->barcode ?? '') }}"
            placeholder="8991234567890"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
        @error('barcode')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Manufacture --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Manufacture
        </label>
        <select
            name="manufacture_id"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
            <option value="">-- Pilih Manufacture --</option>
            @foreach($manufactures as $manufacture)
                <option value="{{ $manufacture->id }}" @selected(old('manufacture_id', $product->manufacture_id ?? '') == $manufacture->id)>
                    {{ $manufacture->name }}
                </option>
            @endforeach
        </select>
        @error('manufacture_id')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Category --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Category
        </label>
        <select
            name="category_id"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
            <option value="">-- Pilih Category --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Product Group --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Product Group
        </label>
        <select
            name="product_group_id"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
            <option value="">-- Pilih Product Group --</option>
            @foreach($productGroups as $group)
                <option value="{{ $group->id }}" @selected(old('product_group_id', $product->product_group_id ?? '') == $group->id)>
                    {{ $group->name }}
                </option>
            @endforeach
        </select>
        @error('product_group_id')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Stock Qty --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Stock Qty <span class="text-red-500">*</span>
        </label>
        <input
            type="number"
            name="stock_qty"
            value="{{ old('stock_qty', $product->stock_qty ?? 0) }}"
            required
            min="0"
            placeholder="0"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
        @error('stock_qty')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Current Stock --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Current Stock <span class="text-red-500">*</span>
        </label>
        <input
            type="number"
            name="current_stock"
            value="{{ old('current_stock', $product->current_stock ?? 0) }}"
            required
            min="0"
            placeholder="0"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
        @error('current_stock')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- AKL/AKD --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            No AKD/AKL (opsional)
        </label>
        <input
            type="text"
            name="akl_akd"
            value="{{ old('akl_akd', $product->akl_akd ?? '') }}"
            placeholder="AKL.1234567890"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
        @error('akl_akd')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- AKL Reg No --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            AKL Reg No (mirror, opsional)
        </label>
        <input
            type="text"
            name="akl_reg_no"
            value="{{ old('akl_reg_no', $product->akl_reg_no ?? '') }}"
            placeholder="REG-123456"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
        @error('akl_reg_no')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Expired Registration --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Expired Registrasi (opsional)
        </label>
        <input
            type="date"
            name="expired_registration"
            value="{{ old('expired_registration', $product->expired_registration ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
        @error('expired_registration')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- General Name --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            General Name
        </label>
        <input
            type="text"
            name="general_name"
            value="{{ old('general_name', $product->general_name ?? '') }}"
            placeholder="Nama umum produk"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
        @error('general_name')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Licence Number --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Licence Number
        </label>
        <input
            type="text"
            name="licence_number"
            value="{{ old('licence_number', $product->licence_number ?? '') }}"
            placeholder="Nomor lisensi"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
        @error('licence_number')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Listing Level --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Listing Level
        </label>
        <input
            type="text"
            name="listing_level"
            value="{{ old('listing_level', $product->listing_level ?? '') }}"
            placeholder="Level listing"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
        @error('listing_level')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Status --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Status <span class="text-red-500">*</span>
        </label>
        <select
            name="status"
            required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
            <option value="inactive" @selected(old('status', $product->status ?? 'inactive') == 'inactive')>Inactive</option>
            <option value="active" @selected(old('status', $product->status ?? '') == 'active')>Active</option>
        </select>
        @error('status')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Description --}}
    <div class="col-md-12">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Description
        </label>
        <textarea
            name="description"
            rows="3"
            placeholder="Deskripsi produk"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Photos (Full Width col-md-12) --}}
    <div class="col-md-12">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Foto Produk (Wajib untuk PQP)
            <span class="text-xs text-gray-400">(Max: 2MB per foto, Format: JPG, PNG, GIF)</span>
        </label>
        <input
            type="file"
            name="photos[]"
            multiple
            accept="image/jpeg,image/png,image/jpg,image/gif"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm
                   text-gray-800 dark:text-white
                   file:mr-4 file:py-2 file:px-4
                   file:rounded-lg file:border-0
                   file:text-sm file:font-medium
                   file:bg-blue-50 file:text-blue-700
                   hover:file:bg-blue-100
                   dark:file:bg-blue-900/30 dark:file:text-blue-400
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
        @error('photos')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
        @error('photos.*')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror

        @if($isEdit && !empty($product->photos))
            <div class="mt-3 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($product->photos as $photo)
                    <div class="relative group">
                        <img src="{{ asset('storage/' . $photo) }}" alt="Product Photo" class="w-full h-24 object-cover rounded-lg">
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Videos (Full Width col-md-12) --}}
    <div class="col-md-12">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Video Produk (Wajib untuk PQP)
            <span class="text-xs text-gray-400">(Max: 10MB per video, Format: MP4, AVI, MOV)</span>
        </label>
        <input
            type="file"
            name="videos[]"
            multiple
            accept="video/mp4,video/avi,video/quicktime"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm
                   text-gray-800 dark:text-white
                   file:mr-4 file:py-2 file:px-4
                   file:rounded-lg file:border-0
                   file:text-sm file:font-medium
                   file:bg-blue-50 file:text-blue-700
                   hover:file:bg-blue-100
                   dark:file:bg-blue-900/30 dark:file:text-blue-400
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
        @error('videos')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
        @error('videos.*')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror

        @if($isEdit && !empty($product->videos))
            <div class="mt-3 space-y-2">
                @foreach($product->videos as $index => $video)
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                        </svg>
                        <span>Video {{ $index + 1 }}: {{ basename($video) }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Action Buttons --}}
    <div class="col-md-12">
        <div class="flex justify-end gap-3 pt-4">
            <a
                href="{{ route('products.index') }}"
                class="px-5 py-2.5 rounded-lg border text-sm font-medium
                       border-gray-300 text-gray-700
                       dark:border-gray-700 dark:text-white
                       hover:bg-gray-50 dark:hover:bg-white/[0.03]">
                Batal
            </a>

            <button
                type="submit"
                class="px-5 py-2.5 rounded-lg bg-blue-600 text-white
                       text-sm font-medium hover:bg-blue-700">
                {{ $isEdit ? 'Update Product' : 'Simpan Product' }}
            </button>
        </div>
    </div>

</div>
