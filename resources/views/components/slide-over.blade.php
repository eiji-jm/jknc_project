@props([
    'id',
    'width' => 'sm:max-w-[640px] lg:max-w-[760px]',
])

<div id="{{ $id }}" class="jknc-drawer fixed inset-0 z-[60] hidden" aria-hidden="true">
    <div data-drawer-overlay class="jknc-drawer-overlay absolute inset-0 bg-black/35 transition-opacity duration-300 ease-out"></div>

    <div class="absolute inset-y-0 right-0 flex max-w-full">
        <div class="jknc-drawer-panel flex h-full w-screen max-w-full {{ $width }}">
            <div class="flex h-full flex-1 flex-col overflow-hidden border-l border-gray-200 bg-white shadow-xl">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

@once
    <style>
        .jknc-drawer-overlay {
            opacity: 0;
        }

        .jknc-drawer-panel {
            transform: translateX(100%);
            transition: transform 0.3s ease-out;
        }

        .jknc-drawer.is-open .jknc-drawer-overlay {
            opacity: 1;
        }

        .jknc-drawer.is-open .jknc-drawer-panel {
            transform: translateX(0);
        }
    </style>

    <script>
        window.jkncSlideOver = window.jkncSlideOver || (() => {
            let openCount = 0;
            const transitionMs = 300;

            const syncBodyLock = () => {
                document.body.classList.toggle('overflow-hidden', openCount > 0);
            };

            const isOpen = (modal) => modal?.dataset.drawerState === 'open';

            const open = (modal) => {
                if (!modal || isOpen(modal)) {
                    return;
                }

                modal.dataset.drawerState = 'open';
                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
                openCount += 1;
                syncBodyLock();

                requestAnimationFrame(() => {
                    modal.classList.add('is-open');
                });
            };

            const close = (modal) => {
                if (!modal || !isOpen(modal)) {
                    return;
                }

                modal.dataset.drawerState = 'closed';
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                openCount = Math.max(0, openCount - 1);
                syncBodyLock();

                window.setTimeout(() => {
                    if (modal.dataset.drawerState === 'closed') {
                        modal.classList.add('hidden');
                    }
                }, transitionMs);
            };

            return {
                open,
                close,
                isOpen,
            };
        })();
    </script>
@endonce
