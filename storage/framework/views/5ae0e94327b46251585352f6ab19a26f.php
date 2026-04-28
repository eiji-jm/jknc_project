<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'id',
    'width' => 'sm:max-w-[640px] lg:max-w-[760px]',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'id',
    'width' => 'sm:max-w-[640px] lg:max-w-[760px]',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div id="<?php echo e($id); ?>" class="jknc-drawer fixed inset-0 z-[60] hidden" aria-hidden="true">
    <div data-drawer-overlay class="jknc-drawer-overlay absolute inset-0 bg-black/35 transition-opacity duration-300 ease-out"></div>

    <div class="absolute inset-y-0 right-0 flex max-w-full">
        <div class="jknc-drawer-panel flex h-full w-screen max-w-full <?php echo e($width); ?>">
            <div class="flex h-full flex-1 flex-col overflow-hidden border-l border-gray-200 bg-white shadow-xl">
                <?php echo e($slot); ?>

            </div>
        </div>
    </div>
</div>

<?php if (! $__env->hasRenderedOnce('05c10efe-eeb9-42a0-a53b-a8009e498b18')): $__env->markAsRenderedOnce('05c10efe-eeb9-42a0-a53b-a8009e498b18'); ?>
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
<?php endif; ?>
<?php /**PATH D:\School\ojt\jknc_work\jknc_project\resources\views/components/slide-over.blade.php ENDPATH**/ ?>