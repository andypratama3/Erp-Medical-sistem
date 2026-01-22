@extends('layouts.app')

@section('title', 'Task - ' . $taskBoard->task_type_label)

@section('content')
<div class="space-y-6">
    <x-common.page-breadcrumb pageTitle="Task Board {{ $taskBoard->task_type_label }}" />
    <!-- Header -->
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">{{ $taskBoard->task_type_label }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">DO: <span class="font-mono font-semibold">{{ $taskBoard->salesDO->do_code }}</span></p>
        </div>

        <div class="flex flex-col lg:flex-row gap-3 items-start lg:items-center">
            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-{{ $taskBoard->status_color }}-100 dark:bg-{{ $taskBoard->status_color }}-900 text-{{ $taskBoard->status_color }}-800 dark:text-{{ $taskBoard->status_color }}-200">
                {{ $taskBoard->status_label }}
            </span>
            @if($taskBoard->is_overdue)
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">⚠️ Overdue</span>
            @elseif($taskBoard->is_urgent)
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200">🔥 Urgent</span>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mb-6 flex flex-wrap gap-6">
        @if($taskBoard->canStart())
            <form method="POST" action="{{ route('wqs.task-board.start', $taskBoard) }}" style="display:inline;">
                @csrf
                <button class="px-4 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded-lg font-semibold transition inline-flex items-center gap-2">
                    ▶️ Start Task
                </button>
            </form>
        @endif

        @if($taskBoard->canComplete())
            <button onclick="openCompleteModal()" class="px-4 py-2 bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-800 text-white rounded-lg font-semibold transition inline-flex items-center gap-2">
                ✓ Complete Task
            </button>
        @endif

        @if($taskBoard->canHold())
            <button onclick="openHoldModal()" class="px-4 py-2 bg-yellow-600 dark:bg-yellow-700 hover:bg-yellow-700 dark:hover:bg-yellow-800 text-white rounded-lg font-semibold transition inline-flex items-center gap-2">
                ⏸️ Hold Task
            </button>
        @endif

        @if($taskBoard->canResume())
            <form method="POST" action="{{ route('wqs.task-board.resume', $taskBoard) }}" style="display:inline;">
                @csrf
                <button class="px-4 py-2 bg-purple-600 dark:bg-purple-700 hover:bg-purple-700 dark:hover:bg-purple-800 text-white rounded-lg font-semibold transition inline-flex items-center gap-2">
                    ▶️ Resume Task
                </button>
            </form>
        @endif

        <button onclick="openRejectModal()" class="px-4 py-2 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-800 text-white rounded-lg font-semibold transition inline-flex items-center gap-2">
            ✕ Reject
        </button>

        <a href="{{ route('wqs.task-board') }}" class="px-4 py-2 bg-gray-400 dark:bg-gray-700 hover:bg-gray-500 dark:hover:bg-gray-600 text-white rounded-lg font-semibold transition inline-flex items-center gap-2">
            ← Back
        </a>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- DO Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">📋 DO Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400 font-semibold">DO Code</label>
                        <p class="text-gray-900 dark:text-white font-mono mt-1">{{ $taskBoard->salesDO->do_code }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Customer</label>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $taskBoard->salesDO->customer?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Office</label>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $taskBoard->salesDO->office?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Grand Total</label>
                        <p class="text-gray-900 dark:text-white font-bold mt-1">Rp {{ number_format($taskBoard->salesDO->grand_total, 0, ',', '.') }}</p>
                    </div>
                    <div class="col-span-2">
                        <label class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Address</label>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $taskBoard->salesDO->shipping_address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Task Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">📝 Task Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Description</label>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $taskBoard->task_description }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Priority</label>
                            <span class="inline-block mt-1 px-3 py-1 bg-{{ $taskBoard->priority_color }}-100 dark:bg-{{ $taskBoard->priority_color }}-900 text-{{ $taskBoard->priority_color }}-800 dark:text-{{ $taskBoard->priority_color }}-200 rounded-full text-xs font-bold">
                                {{ $taskBoard->priority_label }}
                            </span>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Due Date</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $taskBoard->due_date?->format('d M Y') ?? '-' }}</p>
                            @if($taskBoard->days_until_due !== null)
                                @if($taskBoard->days_until_due < 0)
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ abs($taskBoard->days_until_due) }} days overdue</p>
                                @elseif($taskBoard->days_until_due === 0)
                                    <p class="text-xs text-orange-600 dark:text-orange-400">Today!</p>
                                @else
                                    <p class="text-xs text-green-600 dark:text-green-400">{{ $taskBoard->days_until_due }} days left</p>
                                @endif
                            @endif
                        </div>
                    </div>
                    @if($taskBoard->notes)
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <label class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Notes</label>
                            <p class="text-gray-900 dark:text-white mt-1 whitespace-pre-line text-sm">{{ $taskBoard->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stock Check -->
            @if($stockCheck)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">✓ Stock Check Details</h2>

                    <div class="mb-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-3">
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-bold">Status</p>
                            <span class="inline-block mt-1 px-2 py-1 bg-{{ $stockCheck->status_color }}-100 dark:bg-{{ $stockCheck->status_color }}-900 text-{{ $stockCheck->status_color }}-800 dark:text-{{ $stockCheck->status_color }}-200 rounded text-xs font-bold">
                                {{ $stockCheck->status_label }}
                            </span>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg p-3">
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-bold">Completion</p>
                            <p class="text-gray-900 dark:text-white font-bold text-lg mt-1">{{ $stockCheck->completion_percentage }}%</p>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-700 rounded-lg p-3">
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-bold">Total</p>
                            <p class="text-gray-900 dark:text-white font-bold text-lg mt-1">{{ $stockCheck->total_items }}</p>
                        </div>
                        <div class="bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 rounded-lg p-3">
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-bold">Available</p>
                            <p class="text-green-600 dark:text-green-400 font-bold text-lg mt-1">{{ $stockCheck->available_items }}</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300 font-bold">Product</th>
                                    <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300 font-bold">Status</th>
                                    <th class="px-4 py-2 text-right text-gray-700 dark:text-gray-300 font-bold">Qty</th>
                                    <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300 font-bold">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($stockCheck->items as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-4 py-2 text-gray-900 dark:text-white">
                                            <p class="font-medium">{{ $item->product->name }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $item->product->sku }}</p>
                                        </td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 bg-{{ $item->status_color }}-100 dark:bg-{{ $item->status_color }}-900 text-{{ $item->status_color }}-800 dark:text-{{ $item->status_color }}-200 rounded text-xs font-bold">
                                                {{ $item->stock_status_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-right text-gray-900 dark:text-white font-bold">{{ $item->available_qty }}</td>
                                        <td class="px-4 py-2 text-xs text-gray-600 dark:text-gray-400">{{ $item->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($stockCheck->getProblematicItems()->count() > 0)
                        <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg">
                            <p class="text-sm font-bold text-red-700 dark:text-red-400 mb-3">⚠️ Items with Issues:</p>
                            <ul class="space-y-1">
                                @foreach($stockCheck->getProblematicItems() as $item)
                                    <li class="text-xs text-red-700 dark:text-red-400">• {{ $item->product->name }} - {{ $item->stock_status_label }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 dark:text-white rounded-lg p-6">
                    <p class="text-yellow-700 dark:text-yellow-400 font-semibold mb-3">⚠️ No stock check created yet</p>
                    <a href="{{ route('wqs.stock-checks.create', ['sales_do_id' => $taskBoard->sales_do_id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 dark:bg-yellow-700 hover:bg-yellow-700 dark:hover:bg-yellow-800 text-white rounded-lg text-sm font-semibold">
                        Create Stock Check
                    </a>
                </div>
            @endif
        </div>

        <!-- Right: Sidebar -->
        <div class="space-y-6">
            <!-- Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">⏱️ Timeline</h2>
                <div class="space-y-3 text-sm">
                    <div class="pb-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-bold">Created</p>
                        <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $taskBoard->created_at->format('d M Y H:i') }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $taskBoard->createdBy?->name ?? '-' }}</p>
                    </div>

                    @if($taskBoard->started_at)
                        <div class="pb-3 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-bold">Started</p>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $taskBoard->started_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif

                    @if($taskBoard->completed_at)
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-bold">Completed</p>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $taskBoard->completed_at->format('d M Y H:i') }}</p>
                            @if($taskBoard->duration_in_hours)
                                <p class="text-xs text-gray-600 dark:text-gray-400">Duration: {{ $taskBoard->duration_in_hours }}h</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assignment -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">👤 Assignment</h2>
                <p class="text-xs text-gray-600 dark:text-gray-400 font-bold mb-2">Assigned To</p>
                <p class="text-gray-900 dark:text-white font-semibold mb-3">
                    @if($taskBoard->assignedUser)
                        {{ $taskBoard->assignedUser->name }}
                    @else
                        <span class="text-gray-600 dark:text-gray-400 italic">Unassigned</span>
                    @endif
                </p>

                @if(auth()->user()->can('process_wqs'))
                    <form method="POST" action="{{ route('wqs.task-board.assign', $taskBoard) }}" class="space-y-2">
                        @csrf
                        <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg text-sm">
                            <option value="">Select User</option>
                            @php $users = \App\Models\User::where('id', '!=', auth()->id())->get(); @endphp
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $taskBoard->assigned_to === $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full px-3 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded-lg text-sm font-bold transition">
                            Update
                        </button>
                    </form>
                @endif
            </div>

            <!-- Priority -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">⚡ Priority</h2>
                <span class="px-3 py-1 bg-{{ $taskBoard->priority_color }}-100 dark:bg-{{ $taskBoard->priority_color }}-900 text-{{ $taskBoard->priority_color }}-800 dark:text-{{ $taskBoard->priority_color }}-200 rounded-full text-sm font-bold">
                    {{ $taskBoard->priority_label }}
                </span>

                @if(auth()->user()->can('process_wqs'))
                    <form method="POST" action="{{ route('wqs.task-board.priority', $taskBoard) }}" class="mt-3 space-y-2">
                        @csrf
                        @method('PUT')
                        <select name="priority" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg text-sm">
                            <option value="low" {{ $taskBoard->priority === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ $taskBoard->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ $taskBoard->priority === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ $taskBoard->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        <button type="submit" class="w-full px-3 py-2 bg-orange-600 dark:bg-orange-700 hover:bg-orange-700 dark:hover:bg-orange-800 text-white rounded-lg text-sm font-bold transition">
                            Update Priority
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- MODALS -->

<!-- Complete Modal -->
<div id="completeModal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl dark:shadow-2xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">✓ Complete Task</h3>
        </div>

        <div class="p-6 space-y-4">
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg p-4">
                <p class="text-sm text-green-900 dark:text-green-200">
                    Apakah Anda yakin ingin menyelesaikan tugas ini? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <form method="POST" action="{{ route('wqs.task-board.complete', $taskBoard) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Catatan (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg text-sm" placeholder="Tambahkan catatan penyelesaian..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeCompleteModal()" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-800 text-white rounded-lg font-semibold transition">
                        Selesaikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hold Modal -->
<div id="holdModal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl dark:shadow-2xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">⏸️ Hold Task</h3>
        </div>

        <div class="p-6 space-y-4">
            <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                <p class="text-sm text-yellow-900 dark:text-yellow-200">
                    Tugas akan ditandai sebagai "On Hold" sampai Anda melanjutkannya kembali.
                </p>
            </div>

            <form method="POST" action="{{ route('wqs.task-board.hold', $taskBoard) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Alasan Penundaan *</label>
                    <textarea name="reason" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg text-sm" placeholder="Jelaskan mengapa tugas ini ditunda..." required></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeHoldModal()" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-yellow-600 dark:bg-yellow-700 hover:bg-yellow-700 dark:hover:bg-yellow-800 text-white rounded-lg font-semibold transition">
                        Tunda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl dark:shadow-2xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">✕ Reject Task</h3>
        </div>

        <div class="p-6 space-y-4">
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg p-4">
                <p class="text-sm text-red-900 dark:text-red-200">
                    ⚠️ Penolakan akan mengembalikan DO ke status "CRM to WQS" untuk ditinjau ulang.
                </p>
            </div>

            <form method="POST" action="{{ route('wqs.task-board.reject', $taskBoard) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">Alasan Penolakan *</label>
                    <textarea name="reason" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg text-sm" placeholder="Jelaskan mengapa tugas ditolak..." required></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-800 text-white rounded-lg font-semibold transition">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
// Complete Modal
function openCompleteModal() {
    document.getElementById('completeModal').classList.remove('hidden');
}
function closeCompleteModal() {
    document.getElementById('completeModal').classList.add('hidden');
}

// Hold Modal
function openHoldModal() {
    document.getElementById('holdModal').classList.remove('hidden');
}
function closeHoldModal() {
    document.getElementById('holdModal').classList.add('hidden');
}

// Reject Modal
function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Close modal when clicking outside
['completeModal', 'holdModal', 'rejectModal'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        ['completeModal', 'holdModal', 'rejectModal'].forEach(id => {
            document.getElementById(id)?.classList.add('hidden');
        });
    }
});
</script>
@endpush

@endsection
