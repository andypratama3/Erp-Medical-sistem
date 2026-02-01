@extends('layouts.app')

@section('title', 'Edit Case ' . $case->case_number)

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Case {{ $case->case_number }}" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card
        title="Edit Registration Case"
        desc="Editing case {{ $case->case_number }}">

        <form method="POST" action="{{ route('reg-alkes.cases.update', $case) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Status Badge -->
            <div class="mb-2">
                @php
                    $statusBadge = match($case->case_status) {
                        'case_draft'     => ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200', 'label' => 'Draft'],
                        'case_submitted' => ['class' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200', 'label' => 'Submitted'],
                        default          => ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200', 'label' => $case->case_status],
                    };
                @endphp
                <span class="px-2 py-1 {{ $statusBadge['class'] }} rounded text-xs font-bold">
                    Current Status: {{ $statusBadge['label'] }}
                </span>
            </div>

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Manufacture *</label>
                    <select name="manufacture_id" required
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                        <option value="">Select Manufacture</option>
                        @foreach($manufactures as $manufacture)
                            <option value="{{ $manufacture->id }}" {{ old('manufacture_id', $case->manufacture_id) == $manufacture->id ? 'selected' : '' }}>
                                {{ $manufacture->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('manufacture_id')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Registration Type *</label>
                    <select name="registration_type" required
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                        <option value="">Select Type</option>
                        <option value="new" {{ old('registration_type', $case->registration_type ?? $case->case_type) === 'new' ? 'selected' : '' }}>New Registration</option>
                        <option value="renewal" {{ old('registration_type', $case->registration_type ?? $case->case_type) === 'renewal' ? 'selected' : '' }}>Renewal</option>
                        <option value="variation" {{ old('registration_type', $case->registration_type ?? $case->case_type) === 'variation' ? 'selected' : '' }}>Variation</option>
                    </select>
                    @error('registration_type')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Case Title *</label>
                    <input type="text" name="case_title" value="{{ old('case_title', $case->case_title) }}" required
                        placeholder="e.g., New Registration for Medical Device Series A"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    @error('case_title')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Total SKU *</label>
                    <input type="number" name="total_sku" value="{{ old('total_sku', $case->total_skus) }}" min="1" required
                        placeholder="Number of SKUs"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    @error('total_sku')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Existing Documents -->
            @if($case->documents && $case->documents->count() > 0)
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">📎 Existing Documents</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($case->documents as $doc)
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                            <span class="text-blue-600 dark:text-blue-400 text-sm">📄</span>
                            <span class="text-xs text-gray-700 dark:text-gray-300">{{ $doc->stage ?? 'Document' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Notes -->
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Notes (Optional)</label>
                <textarea name="notes" rows="3"
                    placeholder="Any additional notes or instructions..."
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">{{ old('notes', $case->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-2">
                    <a href="{{ route('reg-alkes.cases.show', $case) }}"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03] transition text-sm">
                        Cancel
                    </a>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-6 py-2.5 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </x-common.component-card>
</div>
@endsection
