@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Detail Role" />

<div class="space-y-6">

    {{-- Role Info Card --}}
    <x-common.component-card title="Informasi Role">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            {{-- Role Name --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Nama Role
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $role->name }}
                </p>
            </div>

            {{-- Guard --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Guard Name
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $role->guard_name }}
                </p>
            </div>

            {{-- Created --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Dibuat Pada
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $role->created_at->format('d M Y H:i') }}
                </p>
            </div>

            {{-- Updated --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Terakhir Diubah
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $role->updated_at->format('d M Y H:i') }}
                </p>
            </div>

        </div>
    </x-common.component-card>

    {{-- Permissions Card --}}
    <x-common.component-card title="Permissions">

        @if($permissions->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Role ini belum memiliki permission.
            </p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($permissions as $permission)
                    <span
                        class="inline-flex items-center px-3 py-1.5 rounded-lg
                               bg-blue-50 text-blue-700
                               dark:bg-blue-500/15 dark:text-blue-400
                               text-sm font-medium">
                        {{ $permission->name }}
                    </span>
                @endforeach
            </div>
        @endif

    </x-common.component-card>

    {{-- Actions --}}
    <div class="flex justify-end gap-3">
        <a
            href="{{ route('management.roles.index') }}"
            class="px-4 py-2 rounded-lg border
                   border-gray-300 text-gray-700
                   dark:border-gray-700 dark:text-white
                   hover:bg-gray-50 dark:hover:bg-white/[0.03]">
            Kembali
        </a>

        <a
            href="{{ route('management.roles.edit', $role) }}"
            class="px-4 py-2 rounded-lg bg-blue-600 dark:text-white
                   hover:bg-blue-700">
            Edit Role
        </a>
    </div>

</div>

@endsection
