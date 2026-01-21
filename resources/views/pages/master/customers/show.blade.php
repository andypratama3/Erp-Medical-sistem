@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Customer Detail" />

<div class="space-y-6">
    <x-common.component-card title="Customer Information">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Code</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $customer->code }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $customer->name }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">City</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $customer->city ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Province</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $customer->province ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $customer->phone ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $customer->email ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Contact Person</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $customer->contact_person ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Contact Phone</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $customer->contact_phone ?? '-' }}</p>
            </div>

            <div class="sm:col-span-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $customer->address ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Customer Type</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ ucfirst($customer->customer_type) }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                    {{ $customer->status === 'active'
                        ? 'bg-green-100 text-green-800 dark:bg-white-900 dark:text-white'
                        : ($customer->status === 'blocked'
                            ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300') }}">
                    {{ ucfirst($customer->status) }}
                </span>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Created At</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $customer->created_at->format('d M Y H:i') }}
                </p>
            </div>

            <div class="sm:col-span-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">Notes</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $customer->notes ?? '-' }}
                </p>
            </div>

        </div>
    </x-common.component-card>

    <div class="flex justify-end gap-3">
        <a href="{{ route('master.customers.index') }}"
            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03]">
            Back
        </a>
        <a href="{{ route('master.customers.edit', $customer) }}"
            class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            Edit Customer
        </a>
    </div>
</div>
@endsection
