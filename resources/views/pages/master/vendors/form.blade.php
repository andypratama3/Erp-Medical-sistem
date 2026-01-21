@php
    $isEdit = isset($vendor);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

    {{-- Code --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Code <span class="text-red-500">*</span>
        </label>
        <input type="text" name="code" maxlength="50"
            value="{{ old('code', $vendor->code ?? '') }}" required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white placeholder:text-gray-400 dark:placeholder:text-white/30 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
        @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Name --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Name <span class="text-red-500">*</span>
        </label>
        <input type="text" name="name"
            value="{{ old('name', $vendor->name ?? '') }}" required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Legal Name --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Legal Name</label>
        <input type="text" name="legal_name"
            value="{{ old('legal_name', $vendor->legal_name ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- NPWP --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">NPWP</label>
        <input type="text" name="npwp"
            value="{{ old('npwp', $vendor->npwp ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Phone --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Phone</label>
        <input type="text" name="phone"
            value="{{ old('phone', $vendor->phone ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Mobile --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Mobile</label>
        <input type="text" name="mobile"
            value="{{ old('mobile', $vendor->mobile ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Email --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Email</label>
        <input type="email" name="email"
            value="{{ old('email', $vendor->email ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Contact Person --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Contact Person</label>
        <input type="text" name="contact_person"
            value="{{ old('contact_person', $vendor->contact_person ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Contact Phone --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Contact Phone</label>
        <input type="text" name="contact_phone"
            value="{{ old('contact_phone', $vendor->contact_phone ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Vendor Type --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Vendor Type</label>
        <select name="vendor_type"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
            @foreach (['manufacturer','distributor','supplier','wholesaler','service_provider','other'] as $type)
                <option value="{{ $type }}"
                    {{ old('vendor_type', $vendor->vendor_type ?? 'supplier') === $type ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Payment Term --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Payment Term</label>
        <select name="payment_term_id"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
            <option value="">Select Payment Term</option>
            @foreach($paymentTerms as $term)
                <option value="{{ $term->id }}"
                    {{ old('payment_term_id', $vendor->payment_term_id ?? '') == $term->id ? 'selected' : '' }}>
                    {{ $term->name }} ({{ $term->days }} days)
                </option>
            @endforeach
        </select>
        @error('payment_term_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Status --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Status <span class="text-red-500">*</span>
        </label>
        <select name="status" required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
            <option value="active" {{ old('status', $vendor->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $vendor->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="blocked" {{ old('status', $vendor->status ?? '') === 'blocked' ? 'selected' : '' }}>Blocked</option>
        </select>
    </div>

    {{-- Notes --}}
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Notes</label>
        <textarea name="notes" rows="3"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">{{ old('notes', $vendor->notes ?? '') }}</textarea>
    </div>

    {{-- Action Buttons --}}
    <div class="sm:col-span-2 flex justify-end gap-3 pt-4">
        <a href="{{ route('master.vendors.index') }}"
            class="px-5 py-2.5 rounded-lg border text-sm font-medium border-gray-300 text-gray-700 dark:text-white dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-white/[0.03]">
            Cancel
        </a>
        <button type="submit"
            class="px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
            {{ $isEdit ? 'Update Vendor' : 'Save Vendor' }}
        </button>
    </div>

</div>
