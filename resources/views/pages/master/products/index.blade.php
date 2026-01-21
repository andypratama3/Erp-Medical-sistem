@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Flash Message --}}
    <x-flash-message.flash />
    <!-- Header -->
    <x-common.page-breadcrumb pageTitle="Products" />
    <!-- Filters -->
    <form method="GET" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="SKU / nama / barcode / AKL"class="w-full h-11 rounded-lg border px-3 text-sm
                      bg-white text-gray-900
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Manufacture</label>
                <select name="manufacture" class="w-full h-11 rounded-lg border px-3 text-sm
                       bg-white text-gray-900
                       dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                    <option value="">All</option>
                    @foreach($manufactures as $manufacture)
                        <option value="{{ $manufacture->id }}" {{ request('manufacture') == $manufacture->id ? 'selected' : '' }}>{{ $manufacture->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full h-11 rounded-lg border px-3 text-sm
                       bg-white text-gray-900
                       dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                    <option value="">ALL</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>inactive</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                <select name="type" class="w-full h-11 rounded-lg border px-3 text-sm
                       bg-white text-gray-900
                       dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                    <option value="">ALL</option>
                    <option value="SINGLE" {{ request('type') == 'SINGLE' ? 'selected' : '' }}>SINGLE</option>
                    <option value="BUNDLE" {{ request('type') == 'BUNDLE' ? 'selected' : '' }}>BUNDLE</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex gap-2">
            <a href="{{ route('master.products.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">Reset</a>
            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">Apply</button>
        </div>
    </form>

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
