@php
    $isEdit = isset($office);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    {{-- Code --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Code <span class="text-red-500">*</span>
        </label>
        <input type="text" name="code" value="{{ old('code', $office->code ?? '') }}" required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white placeholder:text-gray-400 dark:placeholder:text-white/30 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
        @error('code')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Name --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Name <span class="text-red-500">*</span>
        </label>
        <input type="text" name="name" value="{{ old('name', $office->name ?? '') }}" required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Address --}}
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Address</label>
        <textarea name="address" rows="3"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">{{ old('address', $office->address ?? '') }}</textarea>
    </div>

    {{-- City --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">City</label>
        <input type="text" name="city" value="{{ old('city', $office->city ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Province --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Province</label>
        <input type="text" name="province" value="{{ old('province', $office->province ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Postal Code --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Postal Code</label>
        <input type="text" name="postal_code" value="{{ old('postal_code', $office->postal_code ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Phone --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $office->phone ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Email --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Email</label>
        <input type="email" name="email" value="{{ old('email', $office->email ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Status --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Status <span class="text-red-500">*</span>
        </label>
        <select name="status" required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
            <option value="active" {{ old('status', $office->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $office->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    {{-- Action Buttons --}}
    <div class="sm:col-span-2 flex justify-end gap-3 pt-4">
        <a href="{{ route('master.offices.index') }}"
            class="px-5 py-2.5 rounded-lg border text-sm font-medium border-gray-300 text-gray-700 dark:text-white dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-white/[0.03]">
            Cancel
        </a>
        <button type="submit"
            class="px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
            {{ $isEdit ? 'Update Office' : 'Save Office' }}
        </button>
    </div>
</div>
