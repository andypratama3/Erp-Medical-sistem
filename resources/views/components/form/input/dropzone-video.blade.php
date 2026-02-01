@props([
    'title' => 'Dropzone Video',
    'name' => 'video',
    'accept' => 'video/mp4,video,mov/quicktime',
])

<x-common.component-card :title="$title">
    <div class="space-y-4">
        <!-- Hidden File Input - This is what gets submitted with the form -->
        <input
            type="file"
            id="{{ $name }}_input"
            name="{{ $name }}"
            accept="{{ $accept }}"
            class="hidden"
            onchange="handleVideoFileChange(this)"
        />

        <!-- Dropzone Area -->
        <div
            class="transition border-2 border-dashed rounded-xl cursor-pointer
                   border-gray-300 hover:border-brand-500
                   dark:border-gray-700 dark:hover:border-brand-500"
            ondrop="handleVideoDrop(event, '{{ $name }}_input')"
            ondragover="event.preventDefault(); this.classList.add('border-brand-500', 'bg-gray-100', 'dark:bg-gray-800');"
            ondragleave="this.classList.remove('border-brand-500', 'bg-gray-100', 'dark:bg-gray-800');"
            onclick="document.getElementById('{{ $name }}_input').click()"
        >
            <div class="rounded-xl p-7 lg:p-10">
                <div class="flex flex-col items-center">
                    <div class="mb-5 flex h-[68px] w-[68px] items-center justify-center rounded-full
                                bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-400">
                        <!-- Video Icon -->
                        <svg class="fill-current" width="29" height="28" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2 4a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V4zm3.5 6v2l5-3.5-5-3.5v3z"/>
                        </svg>
                    </div>

                    <h4 class="mb-2 font-semibold text-theme-xl text-gray-800 dark:text-white/90">
                        Drag & Drop Video Here
                    </h4>

                    <p class="mb-4 text-sm text-center text-gray-600 dark:text-gray-400">
                        MP4, MOV videos (Max 10MB)
                    </p>

                    <span class="font-medium underline text-brand-500 text-theme-sm">
                        Browse File
                    </span>
                </div>
            </div>
        </div>

        <!-- Preview -->
        <div id="{{ $name }}_preview" class="hidden space-y-3">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Selected Video
                </p>
            </div>
            <div id="{{ $name }}_list"></div>
        </div>

        <!-- Info Message -->
        <div id="{{ $name }}_info" class="hidden rounded-lg bg-blue-100 dark:bg-blue-900 px-4 py-3 text-sm">
            <span class="text-blue-800 dark:text-blue-200">
                ✓ Video selected. It will be uploaded when you submit the form.
            </span>
        </div>

        <!-- Error Message -->
        <div id="{{ $name }}_error" class="hidden rounded-lg bg-red-100 dark:bg-red-900 px-4 py-3 text-sm">
            <span id="{{ $name }}_error_text" class="text-red-800 dark:text-red-200"></span>
        </div>
    </div>
</x-common.component-card>
@push('scripts')
<script>
// ✅ Video file handling with validation

const VIDEO_MAX_SIZE = 10 * 1024 * 1024; // 10MB
const VIDEO_ALLOWED_TYPES = ['video/mp4', 'video/quicktime'];
const VIDEO_ALLOWED_EXTENSIONS = ['.mp4', '.mov', '.avi'];

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
}

function validateVideoFile(file) {
    const errors = [];

    // Check file type
    if (!VIDEO_ALLOWED_TYPES.includes(file.type)) {
        errors.push(`Invalid type. Allowed: MP4, MOV`);
    }

    // Check file extension
    const fileName = file.name.toLowerCase();
    const hasValidExtension = VIDEO_ALLOWED_EXTENSIONS.some(ext => fileName.endsWith(ext));
    if (!hasValidExtension) {
        errors.push(`Invalid extension. Allowed: ${VIDEO_ALLOWED_EXTENSIONS.join(', ')}`);
    }

    // Check file size
    if (file.size > VIDEO_MAX_SIZE) {
        errors.push(`File too large (${formatFileSize(file.size)} > 10MB)`);
    }

    // Check if file is empty
    if (file.size === 0) {
        errors.push(`File is empty`);
    }

    return errors;
}

function handleVideoFileChange(input) {
    const name = input.id.replace('_input', '');
    updateVideoPreview(name, input);
}

function handleVideoDrop(event, inputId) {
    event.preventDefault();
    event.stopPropagation();

    // Remove hover effect
    event.currentTarget.classList.remove('border-brand-500', 'bg-gray-100', 'dark:bg-gray-800');

    // ✅ CRITICAL: Get the file input and set files
    const fileInput = document.getElementById(inputId);
    const files = event.dataTransfer.files;

    if (files.length === 0) return;

    // For video, only take first valid file
    const dt = new DataTransfer();

    for (let file of files) {
        const errors = validateVideoFile(file);
        if (errors.length === 0) {
            dt.items.add(file);
            break; // Only one video
        }
    }

    fileInput.files = dt.files;

    const name = inputId.replace('_input', '');
    updateVideoPreview(name, fileInput);
}

function updateVideoPreview(name, fileInput) {
    const files = fileInput.files;
    const previewContainer = document.getElementById(name + '_preview');
    const fileList = document.getElementById(name + '_list');
    const infoDiv = document.getElementById(name + '_info');
    const errorDiv = document.getElementById(name + '_error');
    const errorText = document.getElementById(name + '_error_text');

    // Clear previous
    fileList.innerHTML = '';
    errorDiv.classList.add('hidden');

    if (files.length === 0) {
        previewContainer.classList.add('hidden');
        infoDiv.classList.add('hidden');
        return;
    }

    const file = files[0];
    const errors = validateVideoFile(file);

    if (errors.length > 0) {
        // Show error
        const li = document.createElement('li');
        li.className = 'flex items-center justify-between p-3 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800';

        li.innerHTML = `
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-red-700 dark:text-red-300 truncate font-medium" title="${file.name}">
                        ${file.name}
                    </p>
                    <ul class="text-xs text-red-600 dark:text-red-400 mt-1 space-y-0.5">
                        ${errors.map(e => `<li>• ${e}</li>`).join('')}
                    </ul>
                </div>
            </div>
        `;

        fileList.appendChild(li);
        previewContainer.classList.remove('hidden');
        infoDiv.classList.add('hidden');

        errorDiv.classList.remove('hidden');
        errorText.textContent = '⚠ Video validation failed. See details above.';

        // Clear the input
        fileInput.value = '';
    } else {
        // Show success
        previewContainer.classList.remove('hidden');
        infoDiv.classList.remove('hidden');

        const li = document.createElement('li');
        li.className = 'flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-800';

        li.innerHTML = `
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm text-gray-700 dark:text-gray-300 truncate" title="${file.name}">
                    ${file.name}
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">
                    (${formatFileSize(file.size)})
                </span>
            </div>
            <button type="button" class="ml-2 flex-shrink-0 text-red-500 hover:text-red-700 transition" onclick="removeVideo('${name}')">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        `;

        fileList.appendChild(li);

        console.log('✅ {{ $name }} Video ready for submission:', file.name);
    }
}

function removeVideo(name) {
    const fileInput = document.getElementById(name + '_input');
    fileInput.value = '';
    updateVideoPreview(name, fileInput);
}
</script>

@endpush
