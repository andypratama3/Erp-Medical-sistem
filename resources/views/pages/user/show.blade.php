@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Detail User" />

<div class="space-y-6">

    {{-- User Info Card --}}
    <x-common.component-card title="Informasi User">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            {{-- Nama --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Nama
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $user->name }}
                </p>
            </div>

            {{-- Email --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Email
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $user->email }}
                </p>
            </div>

            {{-- Roles --}}
            <div class="sm:col-span-2">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                    Role
                </p>

                @if($user->roles->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        User ini belum memiliki role.
                    </p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach($user->roles as $role)
                            <span
                                class="inline-flex items-center px-3 py-1.5 rounded-lg
                                       bg-blue-50 text-blue-700
                                       dark:bg-blue-500/15 dark:text-blue-400
                                       text-sm font-medium">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Created --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Dibuat Pada
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $user->created_at->format('d M Y H:i') }}
                </p>
            </div>

            {{-- Updated --}}
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Terakhir Diubah
                </p>
                <p class="text-base font-medium text-gray-800 dark:text-white">
                    {{ $user->updated_at->format('d M Y H:i') }}
                </p>
            </div>

        </div>
    </x-common.component-card>

    {{-- Actions --}}
    <div class="flex justify-end gap-3">
        <a
            href="{{ route('management.users.index') }}"
            class="px-4 py-2 rounded-lg border
                   border-gray-300 text-gray-700
                   dark:border-gray-700 dark:text-white
                   hover:bg-gray-50 dark:hover:bg-white/[0.03]">
            Kembali
        </a>

        <a
            href="{{ route('management.users.edit', $user) }}"
            class="px-4 py-2 rounded-lg bg-blue-600 dark:text-white
                   hover:bg-blue-700">
            Edit User
        </a>
    </div>

</div>

@endsection
