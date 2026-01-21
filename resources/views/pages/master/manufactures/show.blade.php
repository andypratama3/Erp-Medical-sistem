@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Manufacture Detail" />

<div class="space-y-6">

    {{-- Manufacture Info Card --}}
    <x-common.component-card title="Manufacture Information">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            {{-- Code --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Code
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $manufacture->code }}
                </p>
            </div>

            {{-- Name --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Name
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $manufacture->name }}
                </p>
            </div>

            {{-- Country --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Country
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $manufacture->country ?? '-' }}
                </p>
            </div>

            {{-- City --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    City
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $manufacture->city ?? '-' }}
                </p>
            </div>

            {{-- Phone --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Phone
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $manufacture->phone ?? '-' }}
                </p>
            </div>

            {{-- Email --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Email
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $manufacture->email ?? '-' }}
                </p>
            </div>

            {{-- Website --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Website
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    @if($manufacture->website)
                        <a href="{{ $manufacture->website }}" target="_blank" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                            {{ $manufacture->website }}
                        </a>
                    @else
                        -
                    @endif
                </p>
            </div>

            {{-- Status --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Status
                </p>
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                    {{ $manufacture->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                    {{ ucfirst($manufacture->status) }}
                </span>
            </div>

            {{-- Address --}}
            @if($manufacture->address)
            <div class="sm:col-span-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Address
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $manufacture->address }}
                </p>
            </div>
            @endif

            {{-- Description --}}
            @if($manufacture->description)
            <div class="sm:col-span-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Description
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $manufacture->description }}
                </p>
            </div>
            @endif

            {{-- Created At --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Created At
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $manufacture->created_at->format('d M Y H:i') }}
                </p>
            </div>

            {{-- Updated At --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Last Updated
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $manufacture->updated_at->format('d M Y H:i') }}
                </p>
            </div>

        </div>
    </x-common.component-card>

    {{-- Products Card --}}
    <x-common.component-card title="Products">

        @if($manufacture->products->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">
                No products from this manufacture yet.
            </p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($manufacture->products as $product)
                    <div class="p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                        <p class="font-medium text-gray-800 dark:text-white">{{ $product->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->sku }}</p>
                    </div>
                @endforeach
            </div>
        @endif

    </x-common.component-card>

    {{-- Documents Card --}}
    <x-common.component-card title="Documents">

        @if($manufacture->documents->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">
                No documents for this manufacture yet.
            </p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($manufacture->documents as $document)
                    <div class="p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                        <p class="font-medium text-gray-800 dark:text-white">{{ $document->document_name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $document->document_type }}</p>
                    </div>
                @endforeach
            </div>
        @endif

    </x-common.component-card>

    {{-- Reg Alkes Cases Card --}}
    <x-common.component-card title="Reg Alkes Cases">

        @if($manufacture->regAlkesCases->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">
                No registration cases for this manufacture yet.
            </p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($manufacture->regAlkesCases as $case)
                    <div class="p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                        <p class="font-medium text-gray-800 dark:text-white">{{ $case->case_number }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $case->status }}</p>
                    </div>
                @endforeach
            </div>
        @endif

    </x-common.component-card>

    {{-- Actions --}}
    <div class="flex justify-end gap-3">

            <a href="{{ route('master.manufactures.index') }}"
            class="px-4 py-2 rounded-lg border
                   border-gray-300 text-gray-700
                   dark:border-gray-700 dark:text-white
                   hover:bg-gray-50 dark:hover:bg-white/[0.03]">
            Back
        </a>


        <a href="{{ route('master.manufactures.edit', $manufacture) }}"
            class="px-4 py-2 rounded-lg bg-blue-600 text-white
                   hover:bg-blue-700">
            Edit Manufacture
        </a>
    </div>

</div>

@endsection
