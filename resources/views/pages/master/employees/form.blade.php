@php
    $isEdit = isset($employee);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

    {{-- Employee Code --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Employee Code <span class="text-red-500">*</span>
        </label>
        <input type="text" name="employee_code" required
               value="{{ old('employee_code', $employee->employee_code ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="e.g., EMP001">
        @error('employee_code')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Employee Name --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Employee Name <span class="text-red-500">*</span>
        </label>
        <input type="text" name="employee_name" required
               value="{{ old('employee_name', $employee->employee_name ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="Full name">
        @error('employee_name')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- NIK --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            NIK (ID Card Number)
        </label>
        <input type="text" name="nik" maxlength="16"
               value="{{ old('nik', $employee->nik ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="16 digit NIK">
        @error('nik')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- NPWP --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            NPWP (Tax ID)
        </label>
        <input type="text" name="npwp" maxlength="20"
               value="{{ old('npwp', $employee->npwp ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="XX.XXX.XXX.X-XXX.XXX">
        @error('npwp')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Department --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Department <span class="text-red-500">*</span>
        </label>
         <x-form.select.searchable-select
                    name="dept_code"
                    :options="$departments->map(fn($o) => ['value' => $o->code, 'label' => $o->name])->toArray()"
                    :selected="old('dept_code', $employee->dept_code ?? '')"
                    placeholder="-- Select Department --"
                    searchPlaceholder="Search department..."
                    :required="true" />
        @error('dept_code')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Office --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Office <span class="text-red-500">*</span>
        </label>
        <x-form.select.searchable-select
                    name="office_code"
                    :options="$offices->map(fn($o) => ['value' => $o->code, 'label' => $o->name])->toArray()"
                    :selected="old('office_code', $employee->office_code ?? '')"
                    placeholder="-- Select Office --"
                    searchPlaceholder="Search office..."
                    :required="true" />
    </div>

    {{-- Job Title --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Job Title <span class="text-red-500">*</span>
        </label>
        <input type="text" name="job_title" required
               value="{{ old('job_title', $employee->job_title ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="e.g., Senior Manager">
        @error('job_title')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Level Type --}}
    <div class="sm:col-span-2 lg:col-span-1">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Level Type <span class="text-red-500">*</span>
        </label>
        <x-form.select.searchable-select
            name="level_type"
            :options="collect(\App\Models\Employee::LEVEL_TYPES)
                ->map(fn($label, $value) => [
                    'value' => $value,
                    'label' => $label
                ])
                ->values()
                ->toArray()"
            :selected="old('level_type', $employee->level_type ?? '')"
            placeholder="-- Select Level Type --"
            searchPlaceholder="Search level type..."
            :required="true"
        />

        @error('level_type')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Grade --}}
    <div class="sm:col-span-2 lg:col-span-1">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Grade
        </label>
        <input type="text" name="grade"
               value="{{ old('grade', $employee->grade ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="e.g., 1, 2, 3">
        @error('grade')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Payroll Status --}}
    <div class="sm:col-span-2 lg:col-span-1">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Payroll Status
        </label>
        <select name="payroll_status"
                class="w-full h-11 rounded-lg border px-3 text-sm
                       bg-white text-gray-900
                       focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                       dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            <option value="">-- Select --</option>
            <option value="permanent" @selected(old('payroll_status', $employee->payroll_status ?? '') == 'permanent')>Permanent</option>
            <option value="contract" @selected(old('payroll_status', $employee->payroll_status ?? '') == 'contract')>Contract</option>
            <option value="probation" @selected(old('payroll_status', $employee->payroll_status ?? '') == 'probation')>Probation</option>
        </select>
        @error('payroll_status')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Payroll Level --}}
    <div class="sm:col-span-2 lg:col-span-1">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Payroll Level
        </label>
        <input type="text" name="payroll_level"
               value="{{ old('payroll_level', $employee->payroll_level ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400">
        @error('payroll_level')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Education --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Education
        </label>
        <select name="education"
                class="w-full h-11 rounded-lg border px-3 text-sm
                       bg-white text-gray-900
                       focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                       dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            <option value="">-- Select --</option>
            <option value="SD" @selected(old('education', $employee->education ?? '') == 'SD')>SD</option>
            <option value="SMP" @selected(old('education', $employee->education ?? '') == 'SMP')>SMP</option>
            <option value="SMA/SMK" @selected(old('education', $employee->education ?? '') == 'SMA/SMK')>SMA/SMK</option>
            <option value="D3" @selected(old('education', $employee->education ?? '') == 'D3')>D3</option>
            <option value="S1" @selected(old('education', $employee->education ?? '') == 'S1')>S1</option>
            <option value="S2" @selected(old('education', $employee->education ?? '') == 'S2')>S2</option>
            <option value="S3" @selected(old('education', $employee->education ?? '') == 'S3')>S3</option>
        </select>
        @error('education')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Join Year --}}
    <div class="sm:col-span-2 lg:col-span-1">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Join Year
        </label>
        <input type="number" name="join_year" min="1900" max="2100"
               value="{{ old('join_year', $employee->join_year ?? date('Y')) }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="{{ date('Y') }}">
        @error('join_year')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Join Month --}}
    <div class="sm:col-span-2 lg:col-span-1">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Join Month
        </label>
        <select name="join_month"
                class="w-full h-11 rounded-lg border px-3 text-sm
                       bg-white text-gray-900
                       focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                       dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            <option value="">-- Select --</option>
            @for($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" @selected(old('join_month', $employee->join_month ?? '') == $i)>
                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                </option>
            @endfor
        </select>
        @error('join_month')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Phone --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Phone Number
        </label>
        <input type="text" name="phone"
               value="{{ old('phone', $employee->phone ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="08xxxxxxxxxx">
        @error('phone')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Email --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Email
        </label>
        <input type="email" name="email"
               value="{{ old('email', $employee->email ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="employee@company.com">
        @error('email')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- BPJS Ketenagakerjaan Number --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            BPJS Ketenagakerjaan No.
        </label>
        <input type="text" name="bpjs_tk_no"
               value="{{ old('bpjs_tk_no', $employee->bpjs_tk_no ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="BPJS TK Number">
        @error('bpjs_tk_no')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- BPJS Kesehatan Number --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            BPJS Kesehatan No.
        </label>
        <input type="text" name="bpjs_kes_no"
               value="{{ old('bpjs_kes_no', $employee->bpjs_kes_no ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="BPJS Kesehatan Number">
        @error('bpjs_kes_no')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Section: Bank Information --}}
    <div class="sm:col-span-4 mt-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
            Bank Information
        </h3>
    </div>

    {{-- Bank Name --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Bank Name
        </label>
        <input type="text" name="bank_name"
               value="{{ old('bank_name', $employee->bank_name ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="e.g., BCA, Mandiri, BNI">
        @error('bank_name')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Bank Branch --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Bank Branch
        </label>
        <input type="text" name="bank_branch"
               value="{{ old('bank_branch', $employee->bank_branch ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="Branch name">
        @error('bank_branch')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Bank Account Name --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Account Holder Name
        </label>
        <input type="text" name="bank_account_name"
               value="{{ old('bank_account_name', $employee->bank_account_name ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="Name as per bank account">
        @error('bank_account_name')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Bank Account Number --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Account Number
        </label>
        <input type="text" name="bank_account_number"
               value="{{ old('bank_account_number', $employee->bank_account_number ?? '') }}"
               class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white
                      dark:placeholder-gray-400"
               placeholder="Bank account number">
        @error('bank_account_number')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status --}}
    <div class="sm:col-span-2 lg:col-span-2">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Status <span class="text-red-500">*</span>
        </label>
        <select name="status" required
                class="w-full h-11 rounded-lg border px-3 text-sm
                       bg-white text-gray-900
                       focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                       dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            <option value="active" @selected(old('status', $employee->status ?? 'active') == 'active')>Active</option>
            <option value="inactive" @selected(old('status', $employee->status ?? '') == 'inactive')>Inactive</option>
            <option value="resigned" @selected(old('status', $employee->status ?? '') == 'resigned')>Resigned</option>
            <option value="terminated" @selected(old('status', $employee->status ?? '') == 'terminated')>Terminated</option>
        </select>
        @error('status')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Note --}}
    <div class="sm:col-span-4">
        <label class="block text-sm font-medium mb-1 dark:text-white">
            Note
        </label>
        <textarea name="note" rows="3"
                  class="w-full rounded-lg border px-3 py-2 text-sm
                         bg-white text-gray-900
                         focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                         dark:bg-gray-800 dark:border-gray-700 dark:text-white
                         dark:placeholder-gray-400"
                  placeholder="Additional notes...">{{ old('note', $employee->note ?? '') }}</textarea>
        @error('note')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Actions --}}
    <div class="sm:col-span-4 flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ route('master.employees.index') }}"
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
            {{ $isEdit ? 'Update Employee' : 'Create Employee' }}
        </button>
    </div>

</div>