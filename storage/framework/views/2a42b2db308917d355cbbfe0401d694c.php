<?php ($currentStockTransferTab = $currentStockTransferTab ?? 'index'); ?>

<div class="px-4 border-b border-gray-200">
    <div class="flex items-center gap-2 overflow-x-auto whitespace-nowrap">
        <a href="<?php echo e(route('stock-transfer-book.index')); ?>" class="px-4 py-4 text-sm font-medium border-b-2 <?php echo e($currentStockTransferTab === 'index' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-700 hover:text-gray-900'); ?>">Index</a>
        <a href="<?php echo e(route('stock-transfer-book.journal')); ?>" class="px-4 py-4 text-sm font-medium border-b-2 <?php echo e($currentStockTransferTab === 'journal' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-700 hover:text-gray-900'); ?>">Journal</a>
        <a href="<?php echo e(route('stock-transfer-book.ledger')); ?>" class="px-4 py-4 text-sm font-medium border-b-2 <?php echo e($currentStockTransferTab === 'ledger' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-700 hover:text-gray-900'); ?>">Ledger</a>
        <a href="<?php echo e(route('stock-transfer-book.installment')); ?>" class="px-4 py-4 text-sm font-medium border-b-2 <?php echo e($currentStockTransferTab === 'installment' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-700 hover:text-gray-900'); ?>">Installment</a>
        <a href="<?php echo e(route('stock-transfer-book.certificates')); ?>" class="px-4 py-4 text-sm font-medium border-b-2 <?php echo e($currentStockTransferTab === 'certificates' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-700 hover:text-gray-900'); ?>">Certificates</a>
    </div>
</div>
<?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/corporate/stock-transfer-book/partials/section-tabs.blade.php ENDPATH**/ ?>