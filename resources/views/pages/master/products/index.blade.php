@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Flash Message --}}
    <x-flash-message.flash />
    <!-- Header -->
    <x-common.page-breadcrumb pageTitle="Products" />
    <!-- Filters -->
    <form method="GET" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 dark:bg-white/[0.03]">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="SKU / nama / barcode / AKL"class="h-11 w-full flex items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Manufacture</label>
                <x-form.select.searchable-select
                    name="manufacture_id"
                    :options="$manufactures->map(fn($o) => ['value' => $o->id, 'label' => $o->name])->toArray()"
                    :selected="old('manufacture_id', request('manufacture_id') ?: '')"
                    placeholder="-- Select Manufacture --"
                    searchPlaceholder="Search manufacture..."
                    :required="true" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="h-11 w-full flex items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    <option value="">ALL</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>inactive</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                <select name="type" class="h-11 w-full flex items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    <option value="">ALL</option>
                    <option value="SINGLE" {{ request('type') == 'SINGLE' ? 'selected' : '' }}>SINGLE</option>
                    <option value="BUNDLE" {{ request('type') == 'BUNDLE' ? 'selected' : '' }}>BUNDLE</option>
                </select>
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

    {{-- Modal --}}


    <!-- Table -->
   <x-common.component-card
        title="Product List"
        desc="Manage all products in your system"
        link="{{ route('master.products.create') }}">

        <x-table.table-component
            :data="$productsData"
            :columns="$columns"
            :searchable="true"
            :filterable="true" />
    </x-common.component-card>
</div>

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
    {{ session('success') }}
</div>
@endif
@endsection
