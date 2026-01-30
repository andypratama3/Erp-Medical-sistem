<div x-data="{
    isOpen: false,
    popperInstance: null,
    init() {
        this.$nextTick(() => {
            if (typeof createPopper === 'undefined') {
                console.error('Popper.js is not loaded. Please include it via CDN or import.');
                return;
            }
            this.popperInstance = createPopper(this.$refs.button, this.$refs.content, {
                placement: 'bottom-end',
                strategy: 'fixed',
                modifiers: [{
                    name: 'offset',
                    options: {
                        offset: [0, 4],
                    },
                }, ],
            });
        });
    },
    toggle() {
        this.isOpen = !this.isOpen;
        if (this.popperInstance) {
            this.popperInstance.update();
        }
    },
    destroy() {
        if (this.popperInstance) {
            this.popperInstance.destroy();
            this.popperInstance = null;
        }
    }
}" @click.away="isOpen = false" x-init="init()" x-on:destroy="destroy()">
    <div @click="toggle()" x-ref="button" class="cursor-pointer">
        {{ $button }}
    </div>

    <div class="z-50 fixed" x-ref="content" x-show="isOpen" x-cloak>
        <div
            class="p-2 bg-white border border-gray-200 rounded-2xl shadow-lg dark:border-gray-800 dark:bg-gray-dark w-40">
            <div class="space-y-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                {{ $content }}
            </div>
        </div>
    </div>
</div>
