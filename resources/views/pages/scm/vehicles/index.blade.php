@extends('layouts.app')

@section('content')
<!-- Stats Cards -->
<div class="mb-6 grid sm:grid-cols-2 gap-4">
    <div class="rounded-sm border border-stroke dark:text-white px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
            <svg class="fill-primary dark:fill-white" width="22" height="16" viewBox="0 0 22 16" fill="none">

            </svg>
        </div>
        <div class="mt-4 flex items-end justify-between">
            <div>
                <h4 class="text-title-md font-bold text-black dark:text-white">
                    {{ $vehicles->total() }}
                </h4>
                <span class="text-sm font-medium">Total Vehicles</span>
            </div>
        </div>
    </div>

    <div class="rounded-sm border border-stroke dark:text-white px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
            <svg class="fill-primary dark:fill-white" width="22" height="16" viewBox="0 0 22 16">
                <!-- Active Icon SVG -->
            </svg>
        </div>
        <div class="mt-4 flex items-end justify-between">
            <div>
                <h4 class="text-title-md font-bold text-black dark:text-white">
                    {{ $vehicles->where('status', 'active')->count() }}
                </h4>
                <span class="text-sm font-medium">Active</span>
            </div>
        </div>
    </div>

    <div class="rounded-sm border border-stroke dark:text-white px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
            <svg class="fill-primary dark:fill-white" width="22" height="16" viewBox="0 0 22 16">
                <!-- Maintenance Icon SVG -->
            </svg>
        </div>
        <div class="mt-4 flex items-end justify-between">
            <div>
                <h4 class="text-title-md font-bold text-black dark:text-white">
                    {{ $vehicles->where('status', 'maintenance')->count() }}
                </h4>
                <span class="text-sm font-medium">In Maintenance</span>
            </div>
        </div>
    </div>

    <div class="rounded-sm border border-stroke dark:text-white px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
            <svg class="fill-primary dark:fill-white" width="22" height="16" viewBox="0 0 22 16">
                <!-- Available Icon SVG -->
            </svg>
        </div>
        <div class="mt-4 flex items-end justify-between">
            <div>
                <h4 class="text-title-md font-bold text-black dark:text-white">
                    {{ $vehicles->where('driver_id', null)->count() }}
                </h4>
                <span class="text-sm font-medium">Available</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <!-- Card Header -->
    <div class="border-b border-stroke px-4 py-6 dark:border-strokedark md:px-6 xl:px-7.5">
        <div class="flex items-center justify-between">
            <h4 class="text-xl font-semibold text-black dark:text-white">
                All Vehicles
            </h4>
            <div class="flex gap-3">
                <!-- Search Form -->
                <form method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search vehicles..."
                        class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                    <button type="submit" class="flex items-center justify-center rounded bg-primary px-6 py-3 font-medium text-white hover:bg-opacity-90">
                        Search
                    </button>
                </form>

                @can('vehicle-create')
                <a href="{{ route('scm.vehicles.create') }}"
                    class="inline-flex items-center justify-center rounded-md bg-primary px-5 py-3 text-center font-medium text-white hover:bg-opacity-90">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Vehicle
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="p-4 md:p-6 xl:p-7.5">
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="min-w-[150px] px-4 py-4 font-medium text-black dark:text-white">Plate Number</th>
                        <th class="min-w-[200px] px-4 py-4 font-medium text-black dark:text-white">Brand/Model</th>
                        <th class="min-w-[120px] px-4 py-4 font-medium text-black dark:text-white">Year</th>
                        <th class="min-w-[150px] px-4 py-4 font-medium text-black dark:text-white">Driver</th>
                        <th class="min-w-[120px] px-4 py-4 font-medium text-black dark:text-white">Capacity</th>
                        <th class="min-w-[120px] px-4 py-4 font-medium text-black dark:text-white">Status</th>
                        <th class="px-4 py-4 font-medium text-black dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $vehicle)
                    <tr class="border-b border-[#eee] dark:border-strokedark">
                        <td class="px-4 py-5">
                            <p class="font-medium text-black dark:text-white">{{ $vehicle->plate_number }}</p>
                        </td>
                        <td class="px-4 py-5">
                            <p class="text-black dark:text-white">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                        </td>
                        <td class="px-4 py-5">
                            <p class="text-black dark:text-white">{{ $vehicle->year }}</p>
                        </td>
                        <td class="px-4 py-5">
                            @if($vehicle->driver)
                            <p class="text-black dark:text-white">{{ $vehicle->driver->name }}</p>
                            @else
                            <span class="text-sm text-meta-1">Unassigned</span>
                            @endif
                        </td>
                        <td class="px-4 py-5">
                            <p class="text-black dark:text-white">{{ number_format($vehicle->capacity_weight) }} kg</p>
                        </td>
                        <td class="px-4 py-5">
                            <span class="inline-flex rounded-full bg-opacity-10 px-3 py-1 text-sm font-medium
                                @if($vehicle->status == 'active') bg-success text-success
                                @elseif($vehicle->status == 'maintenance') bg-warning text-warning
                                @else bg-danger text-danger @endif">
                                {{ ucfirst($vehicle->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-5">
                            <div class="flex items-center space-x-3.5">
                                <a href="{{ route('scm.vehicles.show', $vehicle) }}"
                                    class="hover:text-primary" title="View">
                                    <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18">
                                        <!-- Eye Icon -->
                                    </svg>
                                </a>
                                @can('vehicle-edit')
                                <a href="{{ route('scm.vehicles.edit', $vehicle) }}"
                                    class="hover:text-primary" title="Edit">
                                    <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18">
                                        <!-- Edit Icon -->
                                    </svg>
                                </a>
                                @endcan
                                @can('vehicle-delete')
                                <form action="{{ route('scm.vehicles.destroy', $vehicle) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="hover:text-danger" title="Delete">
                                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18">
                                            <!-- Trash Icon -->
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-5 text-center">
                            <div class="py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No vehicles found</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding a new vehicle.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($vehicles->hasPages())
        <div class="mt-6">
            {{ $vehicles->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
