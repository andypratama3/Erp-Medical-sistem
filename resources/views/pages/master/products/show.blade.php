@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Product Details: {{ $product->name }}</h2>
        <a href="{{ route('master.products.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">Back</a>
    </div>

    <!-- Product Information -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">SKU:</span>
            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $product->sku }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">Name:</span>
            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $product->name }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">Type:</span>
            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $product->type }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">Unit:</span>
            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $product->unit }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">Barcode:</span>
            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $product->barcode ?? '-' }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">Manufacture:</span>
            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $product->manufacture?->name ?? '-' }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">Category:</span>
            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $product->category?->name ?? '-' }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">Product Group:</span>
            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $product->productGroup?->name ?? '-' }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">Stock Qty:</span>
            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $product->stock_qty }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">Current Stock:</span>
            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $product->current_stock }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">AKL/AKD:</span>
            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $product->akl_akd ?? '-' }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">Status:</span>
            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $product->status == 'active' ? 'bg-green-500' : 'bg-gray-500' }} text-white">{{ $product->status }}</span>
        </div>

        @if($product->description)
        <div class="sm:col-span-2">
            <span class="text-sm text-gray-500 dark:text-gray-400">Description:</span>
            <p class="text-base text-gray-900 dark:text-white mt-1">{{ $product->description }}</p>
        </div>
        @endif
    </div>

    <!-- Photos -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Photos</h3>

        @php
            $images = $product->getMedia('product_images');
        @endphp

        @if($images->count())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($images as $image)
                    <div class="relative group">
                        <img
                            src="{{ $image->getUrl() }}"
                            alt="Product photo"
                            class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-90"
                            onclick="openImageModal('{{ $image->getUrl() }}')"
                        >
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded-lg"></div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">No photos uploaded</p>
        @endif
    </div>


    <!-- Videos -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Videos</h3>

        @php
            $videos = $product->getMedia('product_videos');
        @endphp

        @if($videos->count())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($videos as $video)
                    <div class="relative">
                        <video controls class="w-full h-64 rounded-lg bg-black">
                            <source src="{{ $video->getUrl() }}" type="{{ $video->mime_type }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">No videos uploaded</p>
        @endif
    </div>

</div>

<!-- Image Modal -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="closeImageModal()">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white bg-gray-800 bg-opacity-50 rounded-full p-2 hover:bg-opacity-75">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <img id="modalImage" src="" alt="Full size" class="max-w-full max-h-screen rounded-lg">
    </div>
</div>

<script>
function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}
</script>
@endsection
