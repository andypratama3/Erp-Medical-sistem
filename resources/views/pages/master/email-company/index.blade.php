@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <x-flash-message.flash />
    <x-common.page-breadcrumb pageTitle="Products" />
    <form method="GET" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 dark:bg-white/[0.03]">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                <input type="text" name="name" value="{{ request('name') }}" placeholder="Name"class="h-11 w-full flex items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                <x-form.select.searchable-select
                    name="dept_code"
                    :options="$departments->map(fn($o) => ['value' => $o->code, 'label' => $o->name])->toArray()"
                    :selected="old('dept_code', request('dept_code') ?: '')"
                    placeholder="-- Select Department --"
                    searchPlaceholder="Search department..."
                    :required="true" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Level</label>
                <select name="level_type" class="h-11 w-full flex items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    <option value="">ALL</option>
                    <option value="active" {{ request('level_type') == 'active' ? 'selected' : '' }}>active</option>
                    <option value="inactive" {{ request('level_type') == 'inactive' ? 'selected' : '' }}>inactive</option>
                </select>
            </div>
        </div>
        <div class="flex items-end gap-2 mt-4">
                <a href="{{ route('master.employees.index') }}"
                class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-red-500 text-white shadow-theme-xs hover:bg-red-600">
                Reset
            </a>
            <button type="submit"
                class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                Apply
            </button>
        </div>
    </form>

    {{-- Modal --}}


    <!-- Table -->
   <x-common.component-card
        title="Employee List"
        desc="Manage all employees in your system"
        link="{{ route('master.employees.create') }}">

        <x-table.table-component
            :data="$employeesData"
            :columns="$columns"
            :searchable="true"
            :filterable="true"
            :pagination="$employees" />
    </x-common.component-card>
</div>
@endsection
