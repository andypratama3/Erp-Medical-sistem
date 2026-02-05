@extends('layouts.app')

@section('title', 'Add New Vehicle')

@section('content')
<x-common.page-breadcrumb pageTitle="Add New Vehicle" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card title="Vehicle Information" desc="Register a new vehicle">
        <form method="POST" action="{{ route('scm.vehicles.store') }}" class="space-y-6">
            @csrf

            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Plate Number *</label>
                    <input type="text" name="plate_number" value="{{ old('plate_number') }}" required
                        placeholder="e.g., B 1234 XYZ"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                    @error('plate_number')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Brand *</label>
                    <input type="text" name="brand" value="{{ old('brand') }}" required
                        placeholder="e.g., Toyota, Mitsubishi"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Model *</label>
                    <input type="text" name="model" value="{{ old('model') }}" required
                        placeholder="e.g., Avanza, L300"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Year *</label>
                    <input type="number" name="year" value="{{ old('year', date('Y')) }}" required min="1900" max="{{ date('Y') + 1 }}"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Color</label>
                    <input type="text" name="color" value="{{ old('color') }}"
                        placeholder="e.g., White, Black"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Fuel Type *</label>
                    <select name="fuel_type" required
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                        <option value="gasoline">Gasoline</option>
                        <option value="diesel">Diesel</option>
                        <option value="electric">Electric</option>
                        <option value="hybrid">Hybrid</option>
                    </select>
                </div>
            </div>

            <!-- Capacity -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Capacity Weight (kg) *</label>
                    <input type="number" name="capacity_weight" value="{{ old('capacity_weight') }}" required min="0" step="0.01"
                        placeholder="1000"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Capacity Volume (m³)</label>
                    <input type="number" name="capacity_volume" value="{{ old('capacity_volume') }}" min="0" step="0.01"
                        placeholder="10"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                </div>
            </div>

            <!-- Documents -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Insurance Number</label>
                    <input type="text" name="insurance_number" value="{{ old('insurance_number') }}"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Insurance Expiry</label>
                    <input type="date" name="insurance_expiry" value="{{ old('insurance_expiry') }}"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Tax Expiry</label>
                    <input type="date" name="tax_expiry" value="{{ old('tax_expiry') }}"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Notes</label>
                <textarea name="notes" rows="3"
                    placeholder="Additional information..."
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 transition">{{ old('notes') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('scm.vehicles.index') }}"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03] transition text-sm">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-6 py-2.5 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                    Add Vehicle
                </button>
            </div>
        </form>
    </x-common.component-card>
</div>
@endsection
