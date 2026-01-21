@php
    $isEdit = isset($customer);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

    {{-- Code --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Code <span class="text-red-500">*</span>
        </label>
        <input type="text" name="code" maxlength="50"
            value="{{ old('code', $customer->code ?? '') }}" required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white placeholder:text-gray-400 dark:placeholder:text-white/30 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
        @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Name --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Name <span class="text-red-500">*</span>
        </label>
        <input type="text" name="name"
            value="{{ old('name', $customer->name ?? '') }}" required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Legal Name --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Legal Name</label>
        <input type="text" name="legal_name"
            value="{{ old('legal_name', $customer->legal_name ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- NPWP --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">NPWP</label>
        <input type="text" name="npwp"
            value="{{ old('npwp', $customer->npwp ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Phone --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Phone</label>
        <input type="text" name="phone"
            value="{{ old('phone', $customer->phone ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Mobile --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Mobile</label>
        <input type="text" name="mobile"
            value="{{ old('mobile', $customer->mobile ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Email --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Email</label>
        <input type="email" name="email"
            value="{{ old('email', $customer->email ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Contact Person --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Contact Person</label>
        <input type="text" name="contact_person"
            value="{{ old('contact_person', $customer->contact_person ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Contact Phone --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Contact Phone</label>
        <input type="text" name="contact_phone"
            value="{{ old('contact_phone', $customer->contact_phone ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Customer Type --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Customer Type</label>
        <select name="customer_type"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
            @foreach (['hospital','clinic','pharmacy','distributor','retail','government','other'] as $type)
                <option value="{{ $type }}"
                    {{ old('customer_type', $customer->customer_type ?? 'hospital') === $type ? 'selected' : '' }}>
                    {{ ucfirst($type) }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Status --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Status <span class="text-red-500">*</span>
        </label>
        <select name="status" required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
            <option value="active" {{ old('status', $customer->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $customer->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="blocked" {{ old('status', $customer->status ?? '') === 'blocked' ? 'selected' : '' }}>Blocked</option>
        </select>
    </div>

    {{-- Notes --}}
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Notes</label>
        <textarea name="notes" rows="3"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">{{ old('notes', $customer->notes ?? '') }}</textarea>
    </div>

    {{-- Action Buttons --}}
    <div class="sm:col-span-2 flex justify-end gap-3 pt-4">
        <a href="{{ route('master.departments.index') }}"
            class="px-5 py-2.5 rounded-lg border text-sm font-medium border-gray-300 text-gray-700 dark:text-white dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-white/[0.03]">
            Cancel
        </a>
        <button type="submit"
            class="px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
            {{ $isEdit ? 'Update Department' : 'Save Department' }}
        </button>
    </div>

</div>
