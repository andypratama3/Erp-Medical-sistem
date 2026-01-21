@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Vendor Detail" />

<div class="space-y-6">
    <x-common.component-card title="Vendor Information">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Code</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $vendor->code }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $vendor->name }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Legal Name</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $vendor->legal_name ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">NPWP</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $vendor->npwp ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $vendor->phone ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Mobile</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $vendor->mobile ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $vendor->email ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Vendor Type</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $vendor->vendor_type ? ucfirst(str_replace('_', ' ', $vendor->vendor_type)) : '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Contact Person</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $vendor->contact_person ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Contact Phone</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $vendor->contact_phone ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                    {{ $vendor->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : ($vendor->status === 'blocked' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300') }}">
                    {{ ucfirst($vendor->status) }}
                </span>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Created At</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $vendor->created_at->format('d M Y H:i') }}</p>
            </div>

            @if($vendor->notes)
            <div class="sm:col-span-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">Notes</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $vendor->notes }}</p>
            </div>
            @endif
        </div>
    </x-common.component-card>

    {{-- Payment Term --}}
    @if($vendor->paymentTerm)
    <x-common.component-card title="Payment Term">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Payment Term</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $vendor->paymentTerm->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Days</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $vendor->paymentTerm->days }} days</p>
            </div>
        </div>
    </x-common.component-card>
    @endif

    <div class="flex justify-end gap-3">
        <a href="{{ route('master.vendors.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03]">
            Back
        </a>
        <a href="{{ route('master.vendors.edit', $vendor) }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            Edit Vendor
        </a>
    </div>
</div>
@endsection
