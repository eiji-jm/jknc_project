<?php
    $historyChips = [
        ['key' => 'all', 'label' => 'All Activities'],
        ['key' => 'profile', 'label' => 'Profile Changes'],
        ['key' => 'deals', 'label' => 'Deals'],
        ['key' => 'files', 'label' => 'Files'],
        ['key' => 'notes', 'label' => 'Notes'],
    ];

    $typeStyles = [
        'deals' => ['badge' => 'bg-amber-100 text-amber-600', 'icon' => 'fa-arrow-trend-up'],
        'notes' => ['badge' => 'bg-yellow-100 text-yellow-700', 'icon' => 'fa-note-sticky'],
        'profile' => ['badge' => 'bg-blue-100 text-blue-600', 'icon' => 'fa-pen'],
        'files' => ['badge' => 'bg-indigo-100 text-indigo-600', 'icon' => 'fa-file-arrow-up'],
    ];
?>

<section class="bg-gray-50 p-4 min-h-[760px]">
    <div class="rounded-md border border-gray-200 bg-white overflow-hidden">
        <div class="border-b border-gray-100 px-4 py-4">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">History</h2>
            <p class="mt-1 text-sm text-gray-500">View history logs of the company</p>
        </div>

        <div id="historyFeed" class="p-4">
            <div class="mb-4 flex flex-wrap items-center gap-2">
                <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50" aria-label="Filter">
                    <i class="fas fa-filter text-sm"></i>
                </button>
                <?php $__currentLoopData = $historyChips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button
                        type="button"
                        data-history-chip="<?php echo e($chip['key']); ?>"
                        class="history-chip rounded-lg border px-3 py-1.5 text-sm <?php echo e($chip['key'] === 'all' ? 'border-blue-200 bg-blue-700 text-white' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'); ?>"
                    >
                        <?php echo e($chip['label']); ?>

                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <span id="historyRecordCount" class="ml-auto text-sm text-gray-500"><?php echo e(count($historyItems)); ?> records</span>
            </div>

            <div class="relative space-y-4 pl-12 before:absolute before:bottom-2 before:left-4 before:top-2 before:w-px before:bg-gray-200">
                <?php $__currentLoopData = $historyItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $type = $item['type'] ?? 'profile';
                        $style = $typeStyles[$type] ?? $typeStyles['profile'];
                    ?>
                    <article data-history-item data-history-type="<?php echo e($type); ?>" class="relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <span class="absolute -left-12 top-6 z-10 flex h-9 w-9 items-center justify-center rounded-full <?php echo e($style['badge']); ?>">
                            <i class="fas <?php echo e($style['icon']); ?> text-xs"></i>
                        </span>

                        <h3 class="text-lg font-semibold leading-tight text-gray-900"><?php echo e($item['title']); ?></h3>
                        <p class="mt-1 text-sm text-gray-600"><?php echo e($item['description']); ?></p>

                        <?php if(!empty($item['extraLabel']) && !empty($item['extraValue'])): ?>
                            <div class="mt-3 rounded-md bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                <span class="font-medium text-gray-700"><?php echo e($item['extraLabel']); ?>:</span> <?php echo e($item['extraValue']); ?>

                            </div>
                        <?php endif; ?>

                        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 font-semibold text-blue-700"><?php echo e($item['initials']); ?></span>
                            <span><?php echo e($item['user']); ?></span>
                            <span><i class="far fa-clock mr-1"></i><?php echo e($item['datetime']); ?></span>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const feed = document.getElementById('historyFeed');
    if (!feed) return;

    const chips = Array.from(feed.querySelectorAll('[data-history-chip]'));
    const items = Array.from(feed.querySelectorAll('[data-history-item]'));
    const countLabel = document.getElementById('historyRecordCount');

    function setActiveChip(activeKey) {
        chips.forEach((chip) => {
            const isActive = chip.dataset.historyChip === activeKey;
            chip.classList.toggle('bg-blue-700', isActive);
            chip.classList.toggle('text-white', isActive);
            chip.classList.toggle('border-blue-200', isActive);
            chip.classList.toggle('bg-white', !isActive);
            chip.classList.toggle('text-gray-700', !isActive);
            chip.classList.toggle('border-gray-200', !isActive);
        });
    }

    function applyFilter(filterKey) {
        let visibleCount = 0;
        items.forEach((item) => {
            const visible = filterKey === 'all' || item.dataset.historyType === filterKey;
            item.classList.toggle('hidden', !visible);
            if (visible) visibleCount += 1;
        });
        countLabel.textContent = `${visibleCount} records`;
        setActiveChip(filterKey);
    }

    chips.forEach((chip) => chip.addEventListener('click', function () {
        applyFilter(chip.dataset.historyChip);
    }));

    applyFilter('all');
});
</script>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\company\partials\history-feed.blade.php ENDPATH**/ ?>