<?php
    $activeTab = $activeTab ?? 'gis';
    $topButtonLabel = $topButtonLabel ?? 'SEC-GIS';

    $items = [
        ['key' => 'formation', 'label' => 'SEC-COI', 'href' => route('corporate.formation')],
        ['key' => 'sec_aoi', 'label' => 'SEC-AOI', 'href' => route('corporate.sec_aoi')],
        ['key' => 'bylaws', 'label' => 'Bylaws', 'href' => route('corporate.bylaws')],
        ['key' => 'gis', 'label' => 'GIS', 'href' => route('corporate.gis')],
        ['key' => 'notices', 'label' => 'Notices of Meeting...', 'href' => route('notices')],
        ['key' => 'minutes', 'label' => 'Minutes of Meeting...', 'href' => route('minutes')],
        ['key' => 'resolution', 'label' => 'Resolution', 'href' => route('resolutions')],
        ['key' => 'secretary', 'label' => 'Secretary...', 'href' => route('secretary-certificates')],
    ];

    $activeIndex = collect($items)->search(fn ($item) => $item['key'] === $activeTab);
    $initialScrollLeft = $activeIndex === false ? 0 : max(0, ($activeIndex - 1) * 180);
?>

<div x-data="{
        scrollStep() {
            const ribbon = this.$refs.ribbon;
            const card = ribbon?.querySelector('[data-ribbon-card]');
            return card ? card.getBoundingClientRect().width * 3 : 540;
        },
        prev() {
            this.$refs.ribbon?.scrollBy({ left: -this.scrollStep(), behavior: 'smooth' });
        },
        next() {
            this.$refs.ribbon?.scrollBy({ left: this.scrollStep(), behavior: 'smooth' });
        }
    }"
    class="flex items-center justify-between gap-3 w-full min-w-0">

    <div class="flex items-center gap-2 flex-1 min-w-0">
        <button type="button"
                class="h-9 w-9 shrink-0 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition flex items-center justify-center"
                @click="prev()"
                aria-label="Scroll ribbon left">
            <i class="fas fa-chevron-left text-xs"></i>
        </button>

        <div x-ref="ribbon"
             x-init="$nextTick(() => { $el.scrollLeft = <?php echo e($initialScrollLeft); ?>; })"
             class="min-w-0 flex-1 overflow-x-auto whitespace-nowrap scroll-smooth no-scrollbar">
            <div class="flex items-stretch min-w-max">
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($item['href']); ?>"
                       data-ribbon-card
                       class="shrink-0 w-[180px] px-4 py-3 text-sm font-medium text-center border-t border-b border-r border-gray-200 first:border-l <?php echo e($activeTab === $item['key'] ? 'bg-blue-50 text-blue-700 border-blue-500' : 'bg-white text-gray-800 hover:bg-gray-50'); ?>">
                        <span class="block truncate"><?php echo e($item['label']); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <button type="button"
                class="h-9 w-9 shrink-0 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition flex items-center justify-center"
                @click="next()"
                aria-label="Scroll ribbon right">
            <i class="fas fa-chevron-right text-xs"></i>
        </button>
    </div>

    <div class="flex items-center gap-2 shrink-0">
        <button class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
            <i class="fas fa-bars text-sm"></i>
        </button>

        <button class="w-9 h-9 rounded-full border border-gray-200 text-gray-500 flex items-center justify-center hover:bg-gray-50">
            <i class="fas fa-table-cells-large text-sm"></i>
        </button>

        <div class="flex items-center">
            <button @click="openPanel=true"
                    class="px-4 h-9 rounded-l-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                <span class="text-base leading-none">+</span>
                <?php echo e($topButtonLabel); ?>

            </button>

            <button class="w-10 h-9 rounded-r-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center border-l border-white/20">
                <i class="fas fa-caret-down text-xs"></i>
            </button>
        </div>

        <button class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
            <i class="fas fa-ellipsis-v text-sm"></i>
        </button>
    </div>
</div><?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/corporate/partials/section-ribbon.blade.php ENDPATH**/ ?>