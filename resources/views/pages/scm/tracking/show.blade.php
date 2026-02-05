@extends('layouts.app')

@section('title', 'Delivery Tracking Details')

@section('content')
<x-common.page-breadcrumb pageTitle="Delivery Tracking" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <!-- Delivery Status -->
    <x-common.component-card title="Delivery Status">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">DO Code</p>
                <a href="{{ route('crm.sales-do.show', $delivery->salesDO) }}" 
                    class="text-blue-600 dark:text-blue-400 hover:underline font-mono font-semibold">
                    {{ $delivery->salesDO->do_code }}
                </a>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Customer</p>
                <p class="text-gray-900 dark:text-white font-semibold">{{ $delivery->salesDO->customer->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status</p>
                <span class="px-3 py-1 text-sm font-semibold rounded-full
                    {{ $delivery->delivery_status == 'scheduled' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                    {{ $delivery->delivery_status == 'on_route' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                    {{ $delivery->delivery_status == 'delivered' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}">
                    {{ ucfirst(str_replace('_', ' ', $delivery->delivery_status)) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Driver</p>
                <p class="text-gray-900 dark:text-white">{{ $delivery->driver->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Delivery Date</p>
                <p class="text-gray-900 dark:text-white">{{ $delivery->delivery_date->format('d F Y') }}</p>
            </div>
            @if($delivery->tracking_number)
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Tracking Number</p>
                <p class="text-gray-900 dark:text-white font-mono">{{ $delivery->tracking_number }}</p>
            </div>
            @endif
        </div>
    </x-common.component-card>

    <!-- Timeline -->
    <x-common.component-card title="Delivery Timeline">
        <div class="space-y-4">
            @if($delivery->departure_time)
            <div class="flex items-start gap-4">
                <div class="w-3 h-3 rounded-full bg-blue-600 mt-1"></div>
                <div class="flex-1">
                    <p class="text-gray-900 dark:text-white font-semibold">Departed</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $delivery->departure_time->format('d M Y, H:i') }}</p>
                </div>
            </div>
            @endif

            @if($delivery->arrival_time)
            <div class="flex items-start gap-4">
                <div class="w-3 h-3 rounded-full bg-green-600 mt-1"></div>
                <div class="flex-1">
                    <p class="text-gray-900 dark:text-white font-semibold">Arrived</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $delivery->arrival_time->format('d M Y, H:i') }}</p>
                    @if($delivery->delivery_duration)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Duration: {{ $delivery->delivery_duration }}</p>
                    @endif
                </div>
            </div>
            @endif

            @if($delivery->received_at)
            <div class="flex items-start gap-4">
                <div class="w-3 h-3 rounded-full bg-green-600 mt-1"></div>
                <div class="flex-1">
                    <p class="text-gray-900 dark:text-white font-semibold">Received</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $delivery->received_at->format('d M Y, H:i') }}</p>
                    @if($delivery->receiver_name)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Received by: {{ $delivery->receiver_name }} ({{ $delivery->receiver_position ?? '-' }})</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </x-common.component-card>

    <!-- Shipping Address -->
    <x-common.component-card title="Shipping Address">
        <p class="text-gray-900 dark:text-white">{{ $delivery->shipping_address }}</p>
        @if($delivery->delivery_notes)
        <div class="mt-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Delivery Notes</p>
            <p class="text-gray-900 dark:text-white">{{ $delivery->delivery_notes }}</p>
        </div>
        @endif
    </x-common.component-card>

    <div class="flex justify-end gap-3">
        <a href="{{ route('scm.tracking.index') }}" 
            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03] transition text-sm">
            Back to List
        </a>
    </div>
</div>
@endsection
