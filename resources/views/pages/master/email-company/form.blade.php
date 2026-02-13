@php
    $isEdit = isset($emailCompany);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

    {{-- Scope Type --}}
    <div class="sm:col-span-1">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Scope Type <span class="text-red-500">*</span>
        </label>
        <input type="text" name="scope_type" required
               value="{{ old('scope_type', $emailCompany->scope_type ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="e.g., department, office, company">
        @error('scope_type')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Department Code --}}
    <div class="sm:col-span-1">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Department Code
        </label>
        <input type="text" name="dept_code"
               value="{{ old('dept_code', $emailCompany->dept_code ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="Department code">
        @error('dept_code')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Office Code --}}
    <div class="sm:col-span-1">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Office Code
        </label>
        <input type="text" name="office_code"
               value="{{ old('office_code', $emailCompany->office_code ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="Office code">
        @error('office_code')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Email Local Part --}}
    <div class="sm:col-span-1">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Email Local Part <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input type="text" name="email_local" required
                   value="{{ old('email_local', $emailCompany->email_local ?? '') }}"
                   class="w-full h-11 rounded-lg border px-3 pr-8 text-sm
                          bg-white text-gray-900
                          focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                          dark:bg-gray-800 dark:border-gray-700 dark:text-white
                          dark:placeholder-gray-400"
                   placeholder="info">
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">@</span>
        </div>
        @error('email_local')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Email Domain --}}
    <div class="sm:col-span-1">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Email Domain <span class="text-red-500">*</span>
        </label>
        <input type="text" name="email_domain" required
               value="{{ old('email_domain', $emailCompany->email_domain ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="company.com">
        @error('email_domain')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Email Preview --}}
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Full Email Preview
        </label>
        <div class="w-full h-11 rounded-lg border px-3 text-sm flex items-center
                    bg-gray-50 text-gray-500
                    dark:bg-gray-800/50 dark:border-gray-700 dark:text-gray-400"
             x-data="{
                 local: '{{ old('email_local', $emailCompany->email_local ?? '') }}',
                 domain: '{{ old('email_domain', $emailCompany->email_domain ?? '') }}'
             }"
             x-init="
                 $watch('local', value => {
                     const input = document.querySelector('input[name=email_local]');
                     if (input) local = input.value;
                 });
                 $watch('domain', value => {
                     const input = document.querySelector('input[name=email_domain]');
                     if (input) domain = input.value;
                 });
                 document.querySelector('input[name=email_local]')?.addEventListener('input', (e) => local = e.target.value);
                 document.querySelector('input[name=email_domain]')?.addEventListener('input', (e) => domain = e.target.value);
             ">
            <span x-text="local || 'email'" class="font-mono"></span>
            <span class="mx-1">@</span>
            <span x-text="domain || 'domain.com'" class="font-mono"></span>
        </div>
    </div>

    {{-- Is Primary (Radio Button) --}}
    <div class="sm:col-span-2">
        <div class="flex items-center gap-3 h-11"
             x-data="{ isPrimary: '{{ old('is_primary', $emailCompany->is_primary ?? false) ? '1' : '0' }}' }">
            <label class="text-sm font-medium text-gray-800 dark:text-white">
                Primary Email: <span class="text-red-500">*</span>
            </label>
            <div class="flex items-center gap-4">
                <label :class="isPrimary === '0' ? 'text-gray-700 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                       class="relative flex cursor-pointer items-center gap-2 text-sm font-medium select-none">
                    <input class="sr-only" type="radio" name="is_primary" value="0"
                           @checked(old('is_primary', $emailCompany->is_primary ?? false) == false)
                           @change="isPrimary = '0'">
                    <span :class="isPrimary === '0' ? 'border-blue-500 bg-blue-500' : 'bg-transparent border-gray-300 dark:border-gray-700'"
                          class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px] transition-all">
                        <span :class="isPrimary === '0' ? 'block' : 'hidden'"
                              class="h-2 w-2 rounded-full bg-white"></span>
                    </span>
                    No
                </label>
                <label :class="isPrimary === '1' ? 'text-gray-700 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                       class="relative flex cursor-pointer items-center gap-2 text-sm font-medium select-none">
                    <input class="sr-only" type="radio" name="is_primary" value="1"
                           @checked(old('is_primary', $emailCompany->is_primary ?? false) == true)
                           @change="isPrimary = '1'">
                    <span :class="isPrimary === '1' ? 'border-blue-500 bg-blue-500' : 'bg-transparent border-gray-300 dark:border-gray-700'"
                          class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px] transition-all">
                        <span :class="isPrimary === '1' ? 'block' : 'hidden'"
                              class="h-2 w-2 rounded-full bg-white"></span>
                    </span>
                    Yes
                </label>
            </div>
        </div>
        @error('is_primary')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status --}}
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Status <span class="text-red-500">*</span>
        </label>
        <select name="status" required
                class="w-full h-11 rounded-lg border px-3 text-sm
                       bg-white text-gray-900
                       focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                       dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            <option value="active" @selected(old('status', $emailCompany->status ?? 'active') == 'active')>Active</option>
            <option value="inactive" @selected(old('status', $emailCompany->status ?? '') == 'inactive')>Inactive</option>
        </select>
        @error('status')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Note --}}
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Note
        </label>
        <textarea name="note" rows="3"
                  class="w-full rounded-lg border px-3 py-2 text-sm
                         bg-white text-gray-900
                         focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                         dark:bg-gray-800 dark:border-gray-700 dark:text-white
                         dark:placeholder-gray-400"
                  placeholder="Additional notes...">{{ old('note', $emailCompany->note ?? '') }}</textarea>
        @error('note')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Actions --}}
    <div class="sm:col-span-2 flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ route('master.email-company.index') }}"
           class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-2.5 text-sm
                  bg-white text-gray-700 border border-gray-300
                  hover:bg-gray-50
                  dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Cancel
        </a>
        <button type="submit"
                class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-2.5 text-sm
                       bg-blue-600 text-white shadow-sm
                       hover:bg-blue-700
                       focus:ring-2 focus:ring-blue-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ $isEdit ? 'Update Email' : 'Create Email' }}
        </button>
    </div>

</div>