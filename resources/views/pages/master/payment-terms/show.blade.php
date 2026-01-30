@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <x-common.page-breadcrumb pageTitle="Payment Term Details" />
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 dark:bg-white/[0.03]">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Code</label>
                <p class="text-gray-800 dark:text-white">{{ $paymentTerm->code }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Name</label>
                <p class="text-gray-800 dark:text-white">{{ $paymentTerm->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Days</label>
                <p class="text-gray-800 dark:text-white">{{ $paymentTerm->days }} days</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <p class="text-gray-800 dark:text-white">{{ ucfirst($paymentTerm->status) }}</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Description</label>
                <p class="text-gray-800 dark:text-white">{{ $paymentTerm->description ?? '-' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-4 mt-6">
            <a href="{{ route('master.payment-terms.edit', $paymentTerm) }}" class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-6 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">Edit</a>
            <a href="{{ route('master.payment-terms.index') }}" class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-6 py-3 text-sm bg-gray-500 text-white shadow-theme-xs hover:bg-gray-600">Back</a>
        </div>
    </div>
</div>
@endsection
