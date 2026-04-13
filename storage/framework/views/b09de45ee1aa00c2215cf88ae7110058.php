<?php $__env->startSection('content'); ?>
<div class="bg-[#f5f6f8] min-h-screen p-6">

    <div class="max-w-[1400px] mx-auto flex gap-6">

        
        <div id="ack-scroll-container" class="w-[70%] h-[calc(100vh-80px)] overflow-y-auto pr-2">

            
            <div class="mb-4 flex justify-between items-center">
                <a href="<?php echo e(route('townhall')); ?>"
                   class="border border-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-100">
                    ← Back
                </a>

                <?php if(
                    $communication->approval_status === 'Needs Revision' &&
                    $communication->created_by === Auth::id() &&
                    Auth::user()->hasPermission('create_townhall')
                ): ?>
                    <a href="<?php echo e(route('townhall.edit', $communication->id)); ?>"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm shadow">
                        Edit Revision
                    </a>
                <?php endif; ?>
            </div>

            <?php if(
                $communication->approval_status === 'Needs Revision' &&
                $communication->approval_notes
            ): ?>
                <div class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                    <span class="font-semibold">Revision Note:</span> <?php echo e($communication->approval_notes); ?>

                </div>
            <?php endif; ?>

            
            <div class="memo-preview bg-white border border-gray-300 shadow px-[72px] py-[72px] mb-6">

                
                <div class="flex justify-between border-b pb-6 mb-8">
                    <div>
                        <h1 class="text-[22px] font-bold">JOHN KELLY & COMPANY</h1>
                        <p class="text-[12px] text-gray-500">Corporate Memorandum</p>
                    </div>

                    <div class="text-right text-sm">
                        <p>Ref No: <b><?php echo e($communication->ref_no); ?></b></p>
                        <p>Date: <b><?php echo e($communication->communication_date); ?></b></p>
                    </div>
                </div>

                
                <div class="text-center mb-8">
                    <h2 class="text-[20px] font-bold tracking-[0.2em]">MEMORANDUM</h2>
                </div>

                
                <div class="space-y-3 text-sm mb-10">
                    <div class="grid grid-cols-[120px_1fr]">
                        <b><?php echo e($communication->recipient_label ?? 'To'); ?></b>
                        <span class="border-b"><?php echo e($communication->to_for); ?></span>
                    </div>

                    <div class="grid grid-cols-[120px_1fr]">
                        <b>From</b>
                        <span class="border-b"><?php echo e($communication->from_name); ?></span>
                    </div>

                    <div class="grid grid-cols-[120px_1fr]">
                        <b>Department</b>
                        <span class="border-b"><?php echo e($communication->department_stakeholder); ?></span>
                    </div>

                    <div class="grid grid-cols-[120px_1fr]">
                        <b>Priority</b>
                        <span class="border-b"><?php echo e($communication->priority); ?></span>
                    </div>

                    <div class="grid grid-cols-[120px_1fr]">
                        <b>Subject</b>
                        <span class="border-b font-semibold"><?php echo e($communication->subject); ?></span>
                    </div>
                </div>

                
                <div class="text-[15px] leading-8 min-h-[300px]">
                    <?php echo $communication->message; ?>

                </div>

                
                <div class="mt-16">
                    <p>Respectfully,</p>
                    <div class="mt-10 border-b w-[250px]"></div>
                    <p class="mt-2 font-semibold"><?php echo e($communication->from_name); ?></p>
                </div>

                
                <div class="mt-10 border-t pt-4 text-sm">
                    <p><b>CC:</b> <?php echo e($communication->cc); ?></p>
                    <p><b>Additional:</b> <?php echo e($communication->additional); ?></p>
                </div>
            </div>

            
            <?php if($communication->attachment): ?>
            <div class="bg-white border rounded-xl shadow p-5 mb-6">
                <h3 class="font-semibold mb-3">Attachment Preview</h3>

                <div class="h-[500px] overflow-auto border rounded-lg">
                    <?php
                        $ext = strtolower(pathinfo($communication->attachment, PATHINFO_EXTENSION));
                    ?>

                    <?php if(in_array($ext, ['jpg','jpeg','png','gif','webp'])): ?>
                        <img src="<?php echo e(asset('storage/'.$communication->attachment)); ?>" class="w-full">
                    <?php elseif($ext === 'pdf'): ?>
                        <iframe src="<?php echo e(asset('storage/'.$communication->attachment)); ?>"
                                class="w-full h-[500px]"></iframe>
                    <?php else: ?>
                        <div class="p-4 text-center">
                            <a href="<?php echo e(asset('storage/'.$communication->attachment)); ?>"
                               target="_blank"
                               class="text-blue-600 underline">
                                Download Attachment
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if($communication->approval_status === 'Approved'): ?>
            <div class="bg-white border rounded-xl shadow p-5">

                <div class="flex justify-between mb-3">
                    <h3 class="font-semibold">Acknowledgement</h3>
                    <span><?php echo e($ackCount); ?>/<?php echo e($totalEmployees); ?></span>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                    <div class="bg-blue-600 h-3 rounded-full"
                         style="width: <?php echo e($progress); ?>%"></div>
                </div>

                <?php if($requiresAcknowledgement && !$hasAcknowledged): ?>
                    <form method="POST" action="<?php echo e(route('townhall.acknowledge', $communication->id)); ?>">
                        <?php echo csrf_field(); ?>

                        <button id="ack-btn"
                            type="submit"
                            disabled
                            class="bg-gray-400 text-white px-4 py-2 rounded-lg mb-4 cursor-not-allowed">
                            Acknowledge (Scroll + Wait 10s)
                        </button>

                        <p id="ack-status" class="text-xs text-gray-500">
                            Please scroll to the bottom and wait 10 seconds...
                        </p>
                    </form>
                <?php elseif($hasAcknowledged): ?>
                    <p class="text-sm text-green-600 font-medium mb-4">
                        ✔ You have acknowledged this communication.
                    </p>
                <?php endif; ?>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <b>✔ Acknowledged</b>
                        <?php $__empty_1 = true; $__currentLoopData = $acknowledgedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <p class="text-green-600"><?php echo e($user->name); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-gray-400">None yet</p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <b>Pending</b>
                        <?php $__empty_1 = true; $__currentLoopData = $notAcknowledgedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <p class="text-red-500"><?php echo e($user->name); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-gray-400">All acknowledged</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
            <?php endif; ?>

        </div>

        
        <div class="w-[30%]">
            <div class="bg-white border rounded-xl shadow p-5 sticky top-6 space-y-4">

                <h3 class="font-semibold text-lg">Communication Details</h3>

                <div class="text-sm space-y-3">

                    <div>
                        <p class="text-gray-500 text-xs">Ref</p>
                        <p><?php echo e($communication->ref_no); ?></p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Date</p>
                        <p><?php echo e($communication->communication_date); ?></p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">From</p>
                        <p><?php echo e($communication->from_name); ?></p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs"><?php echo e($communication->recipient_label ?? 'To'); ?></p>
                        <p><?php echo e($communication->to_for); ?></p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Department</p>
                        <p><?php echo e($communication->department_stakeholder); ?></p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Status</p>
                        <p><?php echo e($communication->approval_status); ?></p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Subject</p>
                        <p><?php echo e($communication->subject); ?></p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">CC</p>
                        <p><?php echo e($communication->cc); ?></p>
                    </div>

                    <?php if($communication->approval_notes): ?>
                    <div>
                        <p class="text-gray-500 text-xs">Approval Notes</p>
                        <p><?php echo e($communication->approval_notes); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if($communication->approval_status === 'Approved'): ?>
                    <a href="<?php echo e(route('townhall.download.pdf', $communication->id)); ?>"
                       class="block text-center bg-red-600 text-white py-2 rounded-lg">
                        Download PDF
                    </a>
                <?php endif; ?>

                <?php if(
                    $communication->approval_status === 'Needs Revision' &&
                    $communication->created_by === Auth::id() &&
                    Auth::user()->hasPermission('create_townhall')
                ): ?>
                    <a href="<?php echo e(route('townhall.edit', $communication->id)); ?>"
                       class="block text-center bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                        Edit and Resubmit
                    </a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let hasScrolledToBottom = false;
    let timerDone = false;
    let seconds = 10;

    const container = document.getElementById('ack-scroll-container');
    const button = document.getElementById('ack-btn');
    const statusText = document.getElementById('ack-status');

    if (!container || !button || !statusText) return;

    function updateButtonState() {
        if (hasScrolledToBottom && timerDone) {
            button.disabled = false;
            button.classList.remove('bg-gray-400', 'cursor-not-allowed');
            button.classList.add('bg-green-600', 'hover:bg-green-700');
            button.innerText = 'Acknowledge';
            statusText.innerText = 'You can now acknowledge.';
        } else if (hasScrolledToBottom && !timerDone) {
            statusText.innerText = `Scrolled to bottom. Please wait ${seconds}s...`;
        } else if (!hasScrolledToBottom && timerDone) {
            statusText.innerText = 'Timer finished. Please scroll to the bottom.';
        } else {
            statusText.innerText = `Please scroll to the bottom and wait ${seconds}s...`;
        }
    }

    const interval = setInterval(() => {
        seconds--;

        if (seconds <= 0) {
            timerDone = true;
            clearInterval(interval);
        }

        updateButtonState();
    }, 1000);

    container.addEventListener('scroll', function () {
        const isAtBottom =
            container.scrollTop + container.clientHeight >= container.scrollHeight - 10;

        if (isAtBottom) {
            hasScrolledToBottom = true;
            updateButtonState();
        }
    });

    updateButtonState();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/townhall/show.blade.php ENDPATH**/ ?>