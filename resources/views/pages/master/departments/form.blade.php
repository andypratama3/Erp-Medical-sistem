@php
    $isEdit = isset($department);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    {{-- Code --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Code <span class="text-red-500">*</span>
        </label>
        <input type="text" name="code" value="{{ old('code', $department->code ?? '') }}" required
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
        <input type="text" name="name" value="{{ old('name', $department->name ?? '') }}" required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Head Name <span class="text-red-500">*</span>
        </label>
        <input type="text" name="head_name" value="{{ old('head_name', $department->head_name ?? '') }}" required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
        @error('head_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Office <span class="text-red-500">*</span>
        </label>
        <x-form.select.searchable-select name="office_id"
            :options="$offices->map(fn($o) => ['value' => $o->id, 'label' => $o->name])->toArray()"
            :selected="old('office_id', $department->office_id ?: '')"
            placeholder="-- Select Office --"
            searchPlaceholder="Search office..."
            :required="true" />
    </div>


    {{-- Phone --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $department->phone ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Email --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Email</label>
        <input type="email" name="email" value="{{ old('email', $department->email ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    {{-- Status --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Status <span class="text-red-500">*</span>
        </label>
        <select name="status" required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
            <option value="active" {{ old('status', $department->status ?? 'active') === 'active' ? 'selected' : '' }}>
                Active</option>
            <option value="inactive" {{ old('status', $department->status ?? '') === 'inactive' ? 'selected' : '' }}>
                Inactive</option>
        </select>
    </div>

    {{-- Action Buttons --}}
    <div class="sm:col-span-2 flex justify-end gap-3 pt-4">
        <a href="{{ route('master.departments.index') }}"
            class="px-5 py-2.5 rounded-lg border text-sm font-medium border-gray-300 text-gray-700 dark:text-white dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-white/[0.03]">
            Cancel
        </a>
        <button type="submit"
            class="px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
            {{ $isEdit ? 'Update department' : 'Save department' }}
        </button>
    </div>
</div>
