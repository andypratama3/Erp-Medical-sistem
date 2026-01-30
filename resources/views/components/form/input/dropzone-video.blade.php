@props([
    'title' => 'Upload Video',
    'accept' => 'video/mp4,video/mov',
])

<x-common.component-card :title="$title">
    <div
        x-data="dropzoneComponent()"
        class="transition border border-dashed rounded-xl cursor-pointer
               border-gray-300 hover:border-brand-500
               dark:border-gray-700 dark:hover:border-brand-500"
    >
        <!-- Drop Area -->
        <div
            @drop.prevent="handleDrop($event)"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @click="$refs.fileInput.click()"
            :class="isDragging
                ? 'border-brand-500 bg-gray-100 dark:bg-gray-800'
                : 'bg-gray-50 dark:bg-gray-900'"
            class="rounded-xl p-7 lg:p-10 transition-colors"
        >
            <input
                x-ref="fileInput"
                type="file"
                name="video" {{-- ✅ FIX --}}
                class="hidden"
                multiple
                accept="{{ $accept }}"
                @change="handleFiles(Array.from($event.target.files)); $event.target.value = ''"
                @click.stop
            />

            <div class="flex flex-col items-center">
                <div class="mb-5 flex h-[68px] w-[68px] items-center justify-center rounded-full
                            bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    <svg class="fill-current" width="29" height="28" viewBox="0 0 29 28">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M14.5 3.9c-.2 0-.4.1-.5.3L8.6 9.5a.75.75 0 1 0 1.1 1.1l4-4v12.1a.75.75 0 0 0 1.5 0V6.5l4.1 4a.75.75 0 1 0 1.1-1.1l-5.3-5.3c-.1-.2-.3-.3-.6-.3Z" />
                    </svg>
                </div>

                <h4 class="mb-2 font-semibold text-theme-xl text-gray-800 dark:text-white/90">
                    <span x-show="!isDragging">Drag & Drop Files Here</span>
                    <span x-show="isDragging" x-cloak>Drop Files Here</span>
                </h4>

                <p class="mb-4 text-sm text-center text-gray-600 dark:text-gray-400">
                    MP4, MOV videos
                </p>

                <span class="font-medium underline text-brand-500 text-theme-sm">
                    Browse File
                </span>
            </div>
        </div>

        <!-- Preview -->
        <div x-show="files.length" x-cloak
             class="mt-4 p-4 border-t border-gray-200 dark:border-gray-700">
            <ul class="space-y-2">
                <template x-for="(file, index) in files" :key="index">
                    <li class="flex items-center justify-between p-3 rounded-lg
                               bg-gray-50 dark:bg-gray-800">
                        <span class="text-sm text-gray-700 dark:text-gray-300"
                              x-text="file.name"></span>

                        <button @click.stop="removeFile(index)"
                                class="text-red-500 hover:text-red-700">
                            ✕
                        </button>
                    </li>
                </template>
            </ul>
        </div>

        <!-- Success Notification -->
        <div x-show="successMessage" x-transition x-cloak
             class="m-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">
            <span x-text="successMessage"></span>
        </div>
    </div>
</x-common.component-card>

@push('scripts')
<script>
function dropzoneComponent() {
    return {
        files: [],
        isDragging: false,
        successMessage: '',

        handleFiles(selectedFiles) {
            selectedFiles.forEach(file => {
                if (
                    !file.type.startsWith('image/') &&
                    !file.type.startsWith('video/')
                ) {
                    return;
                }

                this.files.push(file);
            });
        },

        removeFile(index) {
            this.files.splice(index, 1);
        }
    }
}

</script>
@endpush
