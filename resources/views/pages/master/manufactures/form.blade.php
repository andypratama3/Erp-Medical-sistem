@php
    $isEdit = isset($manufacture);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Role Name --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Name Manufactur <span class="text-red-500">*</span>
        </label>
        <input type="text" name="name" value="{{ old('name', $manufacture->name ?? '') }}" required
            placeholder="Example: "
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
    </div>

    {{-- Code --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Code Manufactur
        </label>
        <input type="text" name="code" value="{{ old('code', $manufacture->code ?? '') }}"
            placeholder="Example: "
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
    </div>

    {{-- Country --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Country
        </label>
        <input type="text" name="country" value="{{ old('country', $manufacture->country ?? '') }}"
            placeholder="Example: "
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
    </div>

    {{-- Email --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Email
        </label>
        <input type="email" name="email" value="{{ old('email', $manufacture->email ?? '') }}"
            placeholder="Example: @example.com"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-702 dark:bg-gray-900 dark:focus:border-blue-800">
    </div>

    {{-- Phone --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Phone
        </label>
        <input type="text" name="phone" value="{{ old('phone', $manufacture->phone ?? '') }}"
            placeholder="Example: "
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
    </div>

    {{-- Address --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Address
        </label>
        <input type="text" name="address" value="{{ old('address', $manufacture->address ?? '') }}"
            placeholder="Example: . Raya No. 1"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
    </div>

    {{-- Status --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Status
        </label>
        <select name="status"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                   text-gray-800 dark:text-white
                   placeholder:text-gray-400 dark:placeholder:text-white/30
                   focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10
                   dark:border-gray-700 dark:bg-gray-900 dark:focus:border-blue-800">
            <option value="active" {{ old('status', $manufacture->status ?? '') == 'active' ? 'selected' : '' }}>Active
            </option>
            <option value="inactive" {{ old('status', $manufacture->status ?? '') == 'inactive' ? 'selected' : '' }}>
                Inactive</option>
        </select>
    </div>

    <div class="flex justify-end gap-3 pt-4 md:col-span-2">
        <a href="{{ route('master.manufactures.index') }}"
            class="px-5 py-2.5 rounded-lg border text-sm font-medium
                   border-gray-300 text-gray-700 dark:text-white
                   dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-white/[0.03]">
            Batal
        </a>

        <button type="submit"
            class="px-5 py-2.5 rounded-lg bg-blue-600 dark:text-white
                   text-sm font-medium hover:bg-blue-700">
            {{ $isEdit ? 'Update Manufacture' : 'Simpan Manufacture' }}
        </button>
    </div>
</div>
</div>
