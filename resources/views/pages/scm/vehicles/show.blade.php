@extends('layouts.app')

@section('title', 'Vehicle Details')

@section('content')
<x-common.page-breadcrumb pageTitle="Vehicle Details" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card title="Vehicle Information">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Plate Number</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $vehicle->plate_number }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Brand & Model</p>
                <p class="text-gray-900 dark:text-white font-semibold">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Year</p>
                <p class="text-gray-900 dark:text-white">{{ $vehicle->year }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status</p>
                <span class="px-3 py-1 text-sm font-semibold rounded-full
                    {{ $vehicle->status == 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                    {{ $vehicle->status == 'maintenance' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                    {{ $vehicle->status == 'inactive' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400' : '' }}
                    {{ $vehicle->status == 'in_use' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}">
                    {{ ucfirst(str_replace('_', ' ', $vehicle->status)) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Capacity Weight</p>
                <p class="text-gray-900 dark:text-white">{{ number_format($vehicle->capacity_weight, 2) }} kg</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Fuel Type</p>
                <p class="text-gray-900 dark:text-white">{{ ucfirst($vehicle->fuel_type) }}</p>
            </div>
            @if($vehicle->driver)
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Assigned Driver</p>
                <p class="text-gray-900 dark:text-white font-semibold">{{ $vehicle->driver->name }}</p>
            </div>
            @endif
            @if($vehicle->odometer_reading)
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Odometer</p>
                <p class="text-gray-900 dark:text-white">{{ number_format($vehicle->odometer_reading) }} km</p>
            </div>
            @endif
        </div>
    </x-common.component-card>

    <x-common.component-card title="Documents & Maintenance">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Insurance</p>
                <p class="text-gray-900 dark:text-white">{{ $vehicle->insurance_number ?? '-' }}</p>
                @if($vehicle->insurance_expiry)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Expires: {{ $vehicle->insurance_expiry->format('d M Y') }}</p>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Tax Expiry</p>
                <p class="text-gray-900 dark:text-white">{{ $vehicle->tax_expiry?->format('d M Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Last Service</p>
                <p class="text-gray-900 dark:text-white">{{ $vehicle->last_service_date?->format('d M Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Next Service</p>
                <p class="text-gray-900 dark:text-white">{{ $vehicle->next_service_date?->format('d M Y') ?? '-' }}</p>
            </div>
        </div>
    </x-common.component-card>

    <div class="flex justify-end gap-3">
        <a href="{{ route('scm.vehicles.index') }}" 
            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03] transition text-sm">
            Back to List
        </a>
        <a href="{{ route('scm.vehicles.edit', $vehicle) }}" 
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
            Edit Vehicle
        </a>
    </div>
</div>
@endsection
