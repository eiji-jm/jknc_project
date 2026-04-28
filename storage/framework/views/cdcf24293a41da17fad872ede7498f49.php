<div class="memo-preview bg-white border border-gray-300 shadow min-h-[1100px] px-[72px] py-[72px]">

    
    <div class="flex items-start justify-between border-b border-gray-300 pb-6 mb-8">
        <div>
            <h1 class="text-[22px] font-bold tracking-wide text-gray-900">JOHN KELLY & COMPANY</h1>
            <p class="text-[12px] text-gray-500 mt-1">Corporate Memorandum</p>
        </div>

        <div class="text-right text-[12px] text-gray-600 leading-5">
            <p>Ref No: <span class="font-semibold"><?php echo e($communication->ref_no); ?></span></p>
            <p>Date: <span class="font-semibold"><?php echo e($communication->communication_date); ?></span></p>
        </div>
    </div>

    
    <div class="text-center mb-8">
        <h2 class="text-[20px] font-bold tracking-[0.18em] text-gray-900">MEMORANDUM</h2>
    </div>

    
    <div class="space-y-3 text-[14px] text-gray-800 mb-10">
        <div class="grid grid-cols-[120px_1fr] gap-3">
            <p class="font-semibold uppercase"><?php echo e($communication->recipient_label ?? 'To'); ?></p>
            <p class="border-b border-dotted pb-1"><?php echo e($communication->to_for); ?></p>
        </div>

        <div class="grid grid-cols-[120px_1fr] gap-3">
            <p class="font-semibold uppercase">From</p>
            <p class="border-b border-dotted pb-1"><?php echo e($communication->from_name); ?></p>
        </div>

        <div class="grid grid-cols-[120px_1fr] gap-3">
            <p class="font-semibold uppercase">Department</p>
            <p class="border-b border-dotted pb-1"><?php echo e($communication->department_stakeholder); ?></p>
        </div>

        <div class="grid grid-cols-[120px_1fr] gap-3">
            <p class="font-semibold uppercase">Priority</p>
            <p class="border-b border-dotted pb-1"><?php echo e($communication->priority ?? 'Low'); ?></p>
        </div>

        <div class="grid grid-cols-[120px_1fr] gap-3">
            <p class="font-semibold uppercase">Subject</p>
            <p class="border-b border-dotted pb-1 font-semibold"><?php echo e($communication->subject); ?></p>
        </div>
    </div>

    
    <div class="text-[15px] leading-8 text-gray-900 min-h-[420px]">
        <?php echo $communication->message; ?>

    </div>

    
    <div class="mt-16 space-y-10 text-[14px] text-gray-800">
        <div>
            <p>Respectfully,</p>
            <div class="mt-12 border-b border-gray-400 w-[260px]"></div>
            <p class="mt-2 font-semibold"><?php echo e($communication->from_name); ?></p>
        </div>

        <div class="pt-6 border-t border-gray-200 space-y-2">
            <p><strong>CC:</strong> <?php echo e($communication->cc); ?></p>
            <p><strong>Additional:</strong> <?php echo e($communication->additional); ?></p>
        </div>
    </div>
</div>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\townhall\partials\memo.blade.php ENDPATH**/ ?>