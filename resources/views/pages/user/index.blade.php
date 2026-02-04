@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="User" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <form method="GET" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 dark:bg-white/[0.03]">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                <x-form.select.searchable-select
                    name="branch_id"
                    :options="$branchs->map(fn($o) => ['value' => $o->id, 'label' => $o->name])->toArray()"
                    :selected="old('branch_id', request('branch_id') ?: '')"
                    placeholder="-- Select Branch --"
                    searchPlaceholder="Search branch..."
                    :required="true" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <x-form.select.searchable-select
                    name="status"
                    :options="[
                        ['value' => 'active', 'label' => 'Active'],
                        ['value' => 'inactive', 'label' => 'Inactive'],
                    ]"
                    :selected="old('status', request('status') ?: '')"
                    placeholder="-- Select Status --"
                    searchPlaceholder="Search status..."
                    :required="true" />
            </div>

        </div>
        <div class="flex items-end gap-2 mt-4">
                <a href="{{ route('crm.sales-do.index') }}"
                class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-red-500 text-white shadow-theme-xs hover:bg-red-600">
                Reset
            </a>
            <button type="submit"
                class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                Apply
            </button>
        </div>
    </form>
    <x-common.component-card
        title="User List"
        desc="Manage all users in your system"
        link="{{ route('management.users.create') }}">
        {{-- <div class="flex-shrink-0">
            <a href="{{  }}" class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">Create Data</span>
                <span class="sm:hidden">+</span>
            </a>
        </div> --}}

        <x-table.table-component
            :data="$usersData"
            :columns="$columns"
            :searchable="true"
            :filterable="false"
            :pagination="$users" />
    </x-common.component-card>
</div>

@endsection
