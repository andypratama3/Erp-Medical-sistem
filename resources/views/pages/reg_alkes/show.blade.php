@extends('layouts.app')

@section('title', 'Case ' . $case->case_number)

@section('content')
<x-common.page-breadcrumb pageTitle="Case {{ $case->case_number }}" />

<div class="space-y-6">
    <x-flash-message.flash />

    <!-- Header -->
    <x-common.component-card
        title="RegAlkes Case Detail"
        desc="Case {{ $case->case_number }}">

        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $case->case_number }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $case->case_title ?? '-' }}</p>
            </div>
            <div class="flex gap-2">
                @if(in_array($case->case_status, ['case_draft', 'case_submitted']))
                    <a href="{{ route('reg-alkes.cases.edit', $case) }}"
                        class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-2.5 text-sm bg-orange-500 text-white shadow-theme-xs hover:bg-orange-600">
                        Edit
                    </a>
                @endif
                <a href="{{ route('reg-alkes.cases.index') }}"
                    class="px-4 py-2.5 rounded-lg border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.03] text-sm">
                    Back
                </a>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="mb-6">
            @php
                $statusBadge = match($case->case_status) {
                    'case_draft'     => ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200', 'label' => 'Draft'],
                    'case_submitted' => ['class' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200', 'label' => 'Submitted'],
                    'waiting_nie'    => ['class' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200', 'label' => 'Waiting NIE'],
                    'nie_issued'     => ['class' => 'bg-sky-100 dark:bg-sky-900 text-sky-800 dark:text-sky-200', 'label' => 'NIE Issued'],
                    'sku_imported'   => ['class' => 'bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200', 'label' => 'SKU Imported'],
                    'sku_active'     => ['class' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200', 'label' => 'SKU Active'],
                    'cancelled'      => ['class' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200', 'label' => 'Cancelled'],
                    default          => ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200', 'label' => $case->case_status],
                };
            @endphp
            <x-ui.badge size="md" variant="light" :class="$statusBadge['class']">
                {{ $statusBadge['label'] }}
            </x-ui.badge>
        </div>

        <!-- Status Progress -->
        <div class="flex items-center gap-1 flex-wrap mb-2">
            @php
                $statuses = ['case_draft', 'case_submitted', 'waiting_nie', 'nie_issued', 'sku_imported', 'sku_active'];
                $statusLabels = ['Draft', 'Submitted', 'Waiting NIE', 'NIE Issued', 'SKU Imported', 'SKU Active'];
                $currentIdx = array_search($case->case_status, $statuses);
                if ($currentIdx === false) $currentIdx = -1;
            @endphp
            @foreach($statuses as $idx => $status)
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold
                        @if($idx < $currentIdx) bg-green-500 text-white
                        @elseif($idx === $currentIdx) bg-brand-500 text-white
                        @else bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400
                        @endif">
                        @if($idx < $currentIdx) ✓ @else {{ $idx + 1 }} @endif
                    </div>
                    <span class="ml-1.5 text-xs text-gray-600 dark:text-gray-400 font-medium">{{ $statusLabels[$idx] }}</span>
                    @if($idx < count($statuses) - 1)
                        <div class="w-6 h-0.5 mx-1.5 @if($idx < $currentIdx) bg-green-500 @else bg-gray-200 dark:bg-gray-700 @endif"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </x-common.component-card>

    <!-- Main Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Main Info (2 cols) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Case Information -->
            <x-common.component-card title="Case Information">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Manufacture</p>
                        <p class="text-base font-medium text-gray-800 dark:text-white">{{ $case->manufacture?->name ?? $case->manufacture_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Country of Origin</p>
                        <p class="text-base font-medium text-gray-800 dark:text-white">{{ $case->country_of_origin ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Case Type</p>
                        <p class="text-base font-medium text-gray-800 dark:text-white">
                            <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200 rounded text-xs font-bold">
                                {{ strtoupper($case->case_type ?? '-') }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Submission Date</p>
                        <p class="text-base font-medium text-gray-800 dark:text-white">{{ $case->submission_date?->format('d M Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Target Date</p>
                        <p class="text-base font-medium text-gray-800 dark:text-white">{{ $case->target_date?->format('d M Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total SKUs</p>
                        <p class="text-base font-medium text-gray-800 dark:text-white">{{ $case->total_skus ?? 0 }}</p>
                    </div>
                </div>
            </x-common.component-card>

            <!-- NIE Information -->
            <x-common.component-card title="NIE Information">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">NIE Number</p>
                        <p class="text-base font-medium text-gray-800 dark:text-white font-mono">{{ $case->nie_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">NIE Issued Date</p>
                        <p class="text-base font-medium text-gray-800 dark:text-white">{{ $case->nie_issued_date?->format('d M Y') ?? '-' }}</p>
                    </div>
                </div>

                @if($case->case_status === 'waiting_nie' || $case->case_status === 'case_submitted')
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Upload NIE Document</h4>
                    <form method="POST" action="{{ route('reg-alkes.cases.upload-nie', $case) ?? '#' }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400">NIE Number *</label>
                                <input type="text" name="nie_number" required placeholder="e.g., NIE-12345"
                                    class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400">NIE Date *</label>
                                <input type="date" name="nie_date" required
                                    class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:text-white dark:border-gray-700 dark:bg-gray-900">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400">NIE Document *</label>
                                <input type="file" name="nie_document" accept=".pdf" required
                                    class="w-full text-xs text-gray-600 dark:text-gray-400 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-600">
                            </div>
                        </div>
                        <button type="submit"
                            class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-2 text-sm bg-sky-500 text-white shadow-theme-xs hover:bg-sky-600">
                            Upload NIE
                        </button>
                    </form>
                </div>
                @endif
            </x-common.component-card>

            <!-- SKU Items -->
            @if($case->caseItems && $case->caseItems->count() > 0)
            <x-common.component-card title="SKU Items">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">#</th>
                                <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Product</th>
                                <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Catalog No.</th>
                                <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">AKL/AKD Number</th>
                                <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Expiry</th>
                                <th class="px-4 py-3 text-left text-gray-700 dark:text-gray-300 font-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($case->caseItems as $idx => $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-gray-900 dark:text-white font-medium">{{ $item->product?->name ?? $item->product_name ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 font-mono text-xs">{{ $item->catalog_number ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 font-mono text-xs">{{ $item->akl_akd_number ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($item->akl_akd_expiry)
                                        <span class="@if($item->is_expired) text-red-600 dark:text-red-400 font-bold @else text-gray-700 dark:text-gray-300 @endif text-xs">
                                            {{ $item->akl_akd_expiry->format('d M Y') }}
                                        </span>
                                        @if($item->is_expired)
                                            <span class="ml-1 text-xs text-red-600 dark:text-red-400 font-bold">⚠️ Expired</span>
                                        @elseif($item->days_until_expiry !== null && $item->days_until_expiry <= 90)
                                            <span class="ml-1 text-xs text-yellow-600 dark:text-yellow-400">{{ $item->days_until_expiry }}d left</span>
                                        @endif
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $itemStatus = match($item->item_status) {
                                            'pending'  => ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200', 'label' => 'Pending'],
                                            'active'   => ['class' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200', 'label' => 'Active'],
                                            'expired'  => ['class' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200', 'label' => 'Expired'],
                                            'inactive' => ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400', 'label' => 'Inactive'],
                                            default    => ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200', 'label' => $item->item_status ?? '-'],
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 {{ $itemStatus['class'] }} rounded text-xs font-bold">
                                        {{ $itemStatus['label'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>
            @endif

            <!-- Notes -->
            @if($case->notes)
            <x-common.component-card title="Notes">
                <p class="text-gray-800 dark:text-white whitespace-pre-line">{{ $case->notes }}</p>
            </x-common.component-card>
            @endif
        </div>

        <!-- Right: Sidebar -->
        <div class="space-y-6">

            <!-- SKU Progress -->
            <x-common.component-card title="SKU Progress">
                <div class="space-y-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Progress</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ number_format($case->progress_percentage, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="bg-green-500 h-3 rounded-full transition-all" style="width: {{ $case->progress_percentage }}%"></div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Total SKUs</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $case->total_skus ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Imported</span>
                            <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $case->imported_skus ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Active</span>
                            <span class="font-semibold text-green-600 dark:text-green-400">{{ $case->active_skus ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </x-common.component-card>

            <!-- Documents -->
            <x-common.component-card title="Documents">
                <div class="space-y-2">
                    @if($case->documents && $case->documents->count() > 0)
                        @foreach($case->documents as $doc)
                            <a href="{{ \Storage::url($doc->file_path) }}" target="_blank" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                <span class="text-blue-600 dark:text-blue-400 text-lg">📄</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 dark:text-white font-medium truncate">{{ $doc->stage ?? 'Document' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $doc->created_at->format('d M Y') }}</p>
                                </div>
                                <span class="text-blue-600 dark:text-blue-400 text-xs">↓</span>
                            </a>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">No documents uploaded yet</p>
                    @endif
                </div>
            </x-common.component-card>

            <!-- Metadata -->
            <x-common.component-card title="Metadata">
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Created By</p>
                        <p class="text-gray-800 dark:text-white font-medium">{{ $case->creator?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Created At</p>
                        <p class="text-gray-800 dark:text-white font-medium">{{ $case->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Updated At</p>
                        <p class="text-gray-800 dark:text-white font-medium">{{ $case->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </x-common.component-card>
        </div>
    </div>
</div>
@endsection
