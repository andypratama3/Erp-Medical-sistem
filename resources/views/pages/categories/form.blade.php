@php
    $isEdit = isset($category);
@endphp

<div class="row g-3">

    {{-- Name --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Nama Category <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $category->name ?? '') }}"
            required
            placeholder="BMHP, Alat Kesehatan"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
        @error('name')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Code --}}
    <div class="col-md-3">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Kode Category <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="code"
            value="{{ old('code', $category->code ?? '') }}"
            required
            placeholder="BMHP, ALKES"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
        @error('code')
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
            <option value="active" @selected(old('status', $category->status ?? 'active') == 'active')>Active</option>
            <option value="inactive" @selected(old('status', $category->status ?? '') == 'inactive')>Inactive</option>
        </select>
        @error('status')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Description --}}
    <div class="col-md-12">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Deskripsi
        </label>
        <textarea
            name="description"
            rows="3"
            placeholder="Deskripsi category"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">{{ old('description', $category->description ?? '') }}</textarea>
        @error('description')
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    {{-- Action Buttons --}}
    <div class="col-md-12">
        <div class="flex justify-end gap-3 pt-4">
            <a
                href="{{ route('categories.index') }}"
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
                {{ $isEdit ? 'Update Category' : 'Simpan Category' }}
            </button>
        </div>
    </div>

</div>
