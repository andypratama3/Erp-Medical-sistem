@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Category" />

<div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
    <div class="mx-auto w-full">
        {{-- Header Section --}}
        <div class="mb-10">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                        Category Management
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base">
                        Kelola kategori produk
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <a href="{{ route('categories.create') }}"
                       class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 dark:text-white rounded-lg font-medium transition-all duration-300 flex items-center justify-center sm:justify-start gap-2 shadow-lg hover:shadow-blue-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Category</span>
                    </a>
                </div>
            </div>

            {{-- Search & Filter Section --}}
            <div class="bg-white dark:bg-white/[0.03] border border-gray-200 dark:border-gray-800 rounded-xl p-5 shadow-sm">
                <form method="GET" action="{{ route('categories.index') }}" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari category..."
                               class="w-full pl-12 pr-4 py-2.5 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    </div>
                    <select name="status"
                            class="px-4 py-2.5 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 min-w-[150px]">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        <x-flash-message.flash />

        {{-- Categories Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($categories as $category)
            <div class="group bg-white dark:bg-white/[0.03] border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden hover:shadow-xl dark:hover:shadow-2xl transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-700">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 via-purple-600 to-pink-600 rounded-lg flex items-center justify-center shadow-lg flex-shrink-0">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-gray-900 dark:text-white font-semibold text-lg truncate">{{ $category->name }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $category->code }}</p>
                            </div>
                        </div>
                    </div>

                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                        {{ $category->description ?? 'Tidak ada deskripsi' }}
                    </p>

                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200 dark:border-gray-800">
                        @if($category->status === 'active')
                            <span class="inline-flex items-center gap-2 px-3 py-1 bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 text-xs font-semibold rounded-full border border-green-200 dark:border-green-500/20">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="3"/></svg>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-50 dark:bg-gray-500/10 text-gray-700 dark:text-gray-400 text-xs font-semibold rounded-full border border-gray-200 dark:border-gray-500/20">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="3"/></svg>
                                Inactive
                            </span>
                        @endif
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $category->created_at->diffForHumans() }}
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('categories.edit', $category) }}"
                           class="flex-1 px-4 py-2 bg-blue-100 dark:bg-blue-500/10 hover:bg-blue-200 dark:hover:bg-blue-500/20 text-blue-700 dark:text-blue-400 text-sm font-medium rounded-lg transition-colors text-center">
                            Edit
                        </a>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus category ini?')"
                                    class="w-full px-4 py-2 bg-red-100 dark:bg-red-500/10 hover:bg-red-200 dark:hover:bg-red-500/20 text-red-700 dark:text-red-400 text-sm font-medium rounded-lg transition-colors">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada category</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai dengan menambahkan category baru.</p>
                <div class="mt-6">
                    <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Category
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($categories->hasPages())
        <div class="mt-12">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
