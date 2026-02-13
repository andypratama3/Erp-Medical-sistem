@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Discount Policy Details</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $discountPolicy->department_code }} - {{ $discountPolicy->level_name }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('master.discount-policy.edit', $discountPolicy) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('master.discount-policy.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Information --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Policy Details --}}
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Policy Details</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Department Code</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $discountPolicy->department_code }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Level Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $discountPolicy->level_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Segment</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $discountPolicy->segment }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Max Discount Percent</dt>
                            <dd class="mt-1 text-sm font-semibold text-blue-600 dark:text-blue-400">
                                {{ number_format($discountPolicy->max_discount_percent, 2) }}%
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Notes --}}
            @if($discountPolicy->notes)
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Notes</h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $discountPolicy->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Status Card --}}
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Status</h2>
                </div>
                <div class="p-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $discountPolicy->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                        {{ $discountPolicy->status === 'inactive' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400' : '' }}">
                        {{ ucfirst($discountPolicy->status) }}
                    </span>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h2>
                </div>
                <div class="p-6 space-y-2">
                    <a href="{{ route('master.discount-policy.edit', $discountPolicy) }}"
                       class="block w-full px-4 py-2 text-sm font-medium text-center text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50">
                        Edit Policy
                    </a>
                    <form action="{{ route('master.discount-policy.destroy', $discountPolicy) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this discount policy?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="block w-full px-4 py-2 text-sm font-medium text-center text-red-600 bg-red-50 rounded-lg hover:bg-red-100 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50">
                            Delete Policy
                        </button>
                    </form>
                </div>
            </div>

            {{-- Metadata --}}
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Metadata</h2>
                </div>
                <div class="p-6">
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $discountPolicy->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $discountPolicy->updated_at->format('M d, Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection