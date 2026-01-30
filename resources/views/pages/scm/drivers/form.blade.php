@php
$isEdit = isset($driver);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    {{-- Code --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Code <span class="text-red-500">*</span>
        </label>
        <input type="text" name="code" value="{{ old('code', $driver->code ?? '') }}" required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white placeholder:text-gray-400 dark:placeholder:text-white/30 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
        @error('code')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Name --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Name <span class="text-red-500">*</span>
        </label>
        <input type="text" name="name" value="{{ old('name', $driver->name ?? '') }}" required
            placeholder="Enter driver name"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white placeholder:text-gray-400 dark:placeholder:text-white/30 focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
        @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            License Number <span class="text-red-500">*</span>
        </label>
        <input type="text" name="license_number" value="{{ old('license_number', $driver->license_number ?? '') }}"
            required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
        @error('license_number')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Email</label>
        <input type="email" name="email" value="{{ old('email', $driver->email ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Vehicle Number</label>
        <input type="text" name="vehicle_number" value="{{ old('vehicle_number', $driver->vehicle_number ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>


    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">
            Vehicle Type <span class="text-red-500">*</span>
        </label>
        <select name="vehicle_type" required
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
            <option value="car" {{ old('vehicle_type', $driver->vehicle_type ?? 'car') === 'car' ? 'selected' : '' }}>
                Car</option>
            <option value="motorcycle"
                {{ old('vehicle_type', $driver->vehicle_type ?? '') === 'motorcycle' ? 'selected' : '' }}>
                Motorcycle</option>
            <option value="truck" {{ old('vehicle_type', $driver->vehicle_type ?? '') === 'truck' ? 'selected' : '' }}>
                Truck</option>
            <option value="bus" {{ old('vehicle_type', $driver->vehicle_type ?? '') === 'bus' ? 'selected' : '' }}>
                Bus</option>
            <option value="van" {{ old('vehicle_type', $driver->vehicle_type ?? '') === 'van' ? 'selected' : '' }}>
                Van</option>
            <option value="pickup"
                {{ old('vehicle_type', $driver->vehicle_type ?? '') === 'pickup' ? 'selected' : '' }}>
                Pickup</option>
            <option value="suv" {{ old('vehicle_type', $driver->vehicle_type ?? '') === 'suv' ? 'selected' : '' }}>
                SUV</option>
            <option value="mpv" {{ old('vehicle_type', $driver->vehicle_type ?? '') === 'mpv' ? 'selected' : '' }}>
                MPV</option>
            <option value="sedan" {{ old('vehicle_type', $driver->vehicle_type ?? '') === 'sedan' ? 'selected' : '' }}>
                Sedan</option>
            <option value="hatchback"
                {{ old('vehicle_type', $driver->vehicle_type ?? '') === 'hatchback' ? 'selected' : '' }}>
                Hatchback</option>
            <option value="electric_car"
                {{ old('vehicle_type', $driver->vehicle_type ?? '') === 'electric_car' ? 'selected' : '' }}>
                Electric Car</option>
            <option value="hybrid_car"
                {{ old('vehicle_type', $driver->vehicle_type ?? '') === 'hybrid_car' ? 'selected' : '' }}>
                Hybrid Car</option>
            <option value="forklift"
                {{ old('vehicle_type', $driver->vehicle_type ?? '') === 'forklift' ? 'selected' : '' }}>
                Forklift</option>
            <option value="excavator"
                {{ old('vehicle_type', $driver->vehicle_type ?? '') === 'excavator' ? 'selected' : '' }}>
                Excavator</option>
            <option value="ambulance"
                {{ old('vehicle_type', $driver->vehicle_type ?? '') === 'ambulance' ? 'selected' : '' }}>
                Ambulance</option>
        </select>
    </div>

    {{-- Phone --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium dark:text-white">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $driver->phone ?? '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
    </div>



    {{-- Action Buttons --}}
    <div class="sm:col-span-2 flex justify-end gap-3 pt-4">
        <a href="{{ route('scm.drivers.index') }}"
            class="px-5 py-2.5 rounded-lg border text-sm font-medium border-gray-300 text-gray-700 dark:text-white dark:border-gray-700">
            Cancel
        </a>
        <button type="submit"
            class="px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
            {{ $isEdit ? 'Update Driver' : 'Save Driver' }}
        </button>
    </div>
</div>
