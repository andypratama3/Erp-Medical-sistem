@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Office Detail" />

<div class="space-y-6">
    <x-common.component-card title="Office Information">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Code</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $office->code }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $office->name }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">City</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $office->city ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Province</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $office->province ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $office->phone ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $office->email ?? '-' }}</p>
            </div>

            <div class="sm:col-span-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $office->address ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                    {{ $office->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-white-900 dark:text-white' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                    {{ ucfirst($office->status) }}
                </span>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Created At</p>
                <p class="text-base font-medium text-gray-800 dark:text-white">{{ $office->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </x-common.component-card>

    {{-- Departments --}}
    <x-common.component-card title="Departments">
        @if($office->departments->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">No departments in this office.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($office->departments as $dept)
                    <div class="p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                        <p class="font-medium text-gray-800 dark:text-white">{{ $dept->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $dept->code }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </x-common.component-card>

    <div class="flex justify-end gap-3">
        <a href="{{ route('master.offices.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03]">
            Back
        </a>
        <a href="{{ route('master.offices.edit', $office) }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            Edit Office
        </a>
    </div>
</div>
@endsection
