@props([
    'type' => null, // success | error | null (auto)
])

@php
    $success = session('success');
    $error   = session('error');
    $errorsBag = $errors ?? null;

    if ($type === 'success' || (!$type && $success)) {
        $messages = [$success];
        $isSuccess = true;
    } elseif ($type === 'error' || (!$type && ($error || $errorsBag?->any()))) {
        $messages = $error
            ? [$error]
            : $errorsBag->all();
        $isSuccess = false;
    } else {
        $messages = [];
        $isSuccess = true;
    }
@endphp

@if(count($messages))
    <div
        class="rounded-lg border p-4 space-y-2
        {{ $isSuccess
            ? 'bg-green-50 border-green-200 dark:bg-green-500/15 dark:border-green-500/30'
            : 'bg-red-50 border-red-200 dark:bg-red-500/15 dark:border-red-500/30'
        }}"
    >
        @foreach($messages as $message)
            <div class="flex items-start gap-3">
                {{-- Icon --}}
                <svg
                    class="w-5 h-5 flex-shrink-0
                    {{ $isSuccess ? 'text-green-600 dark:text-green-500' : 'text-red-600 dark:text-red-500' }}"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                >
                    @if($isSuccess)
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"/>
                    @else
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                              clip-rule="evenodd"/>
                    @endif
                </svg>

                {{-- Message --}}
                <p class="text-sm font-medium
                    {{ $isSuccess ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                    {{ $message }}
                </p>
            </div>
        @endforeach
    </div>
@endif
