<?php $__env->startSection('content'); ?>
<div class="bg-[#f5f6f8] min-h-screen p-6">

    <div class="max-w-[1400px] mx-auto flex gap-6">

        
        <div id="ack-scroll-container" class="w-[70%] h-[calc(100vh-80px)] overflow-y-auto pr-2">

            
            <div class="mb-4 flex justify-between items-center">
                <a href="<?php echo e(route('townhall')); ?>"
                   class="border border-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-100">
                    ← Back
                </a>

                <div class="flex items-center gap-2">
                    <?php if($communication->approval_status === 'Approved'): ?>
                        <a href="<?php echo e(route('townhall.download.pdf', $communication->id)); ?>"
                           class="inline-flex items-center gap-2 rounded-lg bg-red-600 hover:bg-red-700 text-white px-4 py-2 text-sm shadow">
                            <i class="fas fa-file-pdf"></i>
                            Download PDF
                        </a>
                    <?php endif; ?>

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
            </div>

            <?php if(
                $communication->approval_status === 'Needs Revision' &&
                $communication->approval_notes
            ): ?>
                <div class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                    <span class="font-semibold">Revision Note:</span> <?php echo e($communication->approval_notes); ?>

                </div>
            <?php endif; ?>

            
            <div class="memo-page">

                
                <div class="memo-page-header">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="shrink-0 pt-1">
                            <img src="<?php echo e(asset('images/jk-logo.png')); ?>" alt="JK Logo" class="h-[72px] w-auto object-contain">
                        </div>

                        <div class="flex-1 pt-1">
                            <p class="text-[12px] leading-[1.35] text-gray-700 font-serif m-0">
                                Atty. Jose B. Ogang, CPA, MMPSM · Jose Tamayo Rio,<br>
                                MM-BM, CPA · Lyndon Earl P. Rio, RN, CB · John Kelly Abalde,<br>
                                CLSSBB, CPM
                            </p>
                        </div>
                    </div>

                    <div class="memo-page-title">
                        <h2>MEMORANDUM</h2>
                    </div>

                    <div class="memo-page-meta memo-content-inset">
                        <p><strong>Memo NO.:</strong> <?php echo e($communication->ref_no); ?></p>
                        <p>
                            <strong>Date:</strong>
                            <?php echo e($communication->communication_date ? \Carbon\Carbon::parse($communication->communication_date)->format('F d, Y') : '—'); ?>

                        </p>
                        <p><strong><?php echo e($communication->recipient_label ?? 'To'); ?>:</strong> <?php echo e($communication->to_for ?: '—'); ?></p>
                        <p><strong>From:</strong> <?php echo e($communication->from_name ?: '—'); ?></p>
                        <p><strong>SUBJECT:</strong> <?php echo e($communication->subject ?: '—'); ?></p>
                    </div>

                    <div class="memo-page-divider memo-content-inset"></div>
                </div>

                
                <div class="memo-page-body memo-content-inset">
                    <?php echo $communication->message ?: '<p style="color:#9ca3af;">No memorandum body provided.</p>'; ?>

                </div>

                
                <div class="memo-page-footer">
                    <div class="issued-block memo-content-inset">
                        Issued this
                        <strong>
                            <?php echo e($communication->communication_date ? \Carbon\Carbon::parse($communication->communication_date)->format('jS \\d\\a\\y \\o\\f F, Y') : '______________'); ?>

                        </strong>
                        in Cebu City, Philippines.
                    </div>

                    <div class="prepared-block memo-content-inset">
                        <p class="prepared-label">Prepared by:</p>

                        <div class="signature-line"></div>
                        <p class="prepared-name"><?php echo e($communication->from_name ?: '—'); ?></p>
                        <p class="prepared-role">President/CEO</p>
                    </div>

                    <div class="memo-extra-details memo-content-inset">
                        <p><strong>CC:</strong> <?php echo e($communication->cc ?: '—'); ?></p>
                        <p><strong>Additional:</strong> <?php echo e($communication->additional ?: '—'); ?></p>
                    </div>

                    <div class="memo-footer-note memo-content-inset">
                        This Memorandum is an official corporate record of JK&amp;C INC. Unauthorized reproduction,
                        alteration, disclosure, or misuse of this Memorandum, in whole or in part, is strictly prohibited
                        and may result in administrative sanctions, termination of employment or engagement, and/or the
                        institution of appropriate civil, criminal, or regulatory actions, in accordance with applicable
                        laws and company policies.
                    </div>

                    <div class="memo-footer-address memo-content-inset">
                        JK&amp;C INC.<br>
                        3F Cebu Holdings Center Cebu Business Park, Cebu City, Philippines, 6000
                    </div>
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
                        <p><?php echo e($communication->communication_date ? \Carbon\Carbon::parse($communication->communication_date)->format('F d, Y') : '—'); ?></p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">From</p>
                        <p><?php echo e($communication->from_name ?: '—'); ?></p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs"><?php echo e($communication->recipient_label ?? 'To'); ?></p>
                        <p><?php echo e($communication->to_for ?: '—'); ?></p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Department</p>
                        <p><?php echo e($communication->department_stakeholder ?: '—'); ?></p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Priority</p>
                        <p><?php echo e($communication->priority ?: '—'); ?></p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Status</p>
                        <p><?php echo e($communication->approval_status ?: '—'); ?></p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Subject</p>
                        <p><?php echo e($communication->subject ?: '—'); ?></p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">CC</p>
                        <p><?php echo e($communication->cc ?: '—'); ?></p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Additional</p>
                        <p><?php echo e($communication->additional ?: '—'); ?></p>
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
                       class="block text-center bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
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

<?php $__env->startPush('styles'); ?>
<style>
    .memo-page {
        width: 100%;
        background: #fff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        padding: 50px 60px;
        box-sizing: border-box;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .memo-content-inset {
        margin-left: 40px;
        margin-right: 40px;
    }

    .memo-page-header {
        margin-bottom: 24px;
    }

    .memo-page-title {
        text-align: center;
        margin-bottom: 28px;
    }

    .memo-page-title h2 {
        font-size: 28px;
        font-weight: 600;
        letter-spacing: 0.04em;
        color: #555;
        font-family: "Times New Roman", Georgia, serif;
        margin: 0;
    }

    .memo-page-meta {
        margin-bottom: 10px;
        font-size: 14px;
        line-height: 1.35;
        color: #111827;
        font-family: "Times New Roman", Georgia, serif;
    }

    .memo-page-meta p {
        margin: 2px 0;
    }

    .memo-page-divider {
        border-bottom: 1px solid #6b7280;
        margin-top: 10px;
        margin-bottom: 24px;
    }

    .memo-page-body,
    .memo-page-body p,
    .memo-page-body li,
    .memo-page-body span,
    .memo-page-body div,
    .memo-page-body td,
    .memo-page-body th {
        font-family: "Times New Roman", Georgia, serif !important;
        color: #111827;
    }

    .memo-page-body {
        font-size: 14px;
        line-height: 1.3;
        text-align: justify;
        min-height: 420px;
    }

    .memo-page-body p,
    .memo-page-body li {
        text-align: justify;
    }

    .memo-page-body p {
        margin: 0 0 3px 0;
    }

    .memo-page-body ul,
    .memo-page-body ol {
        margin: 0 0 18px 24px;
        padding-left: 18px;
    }

    .memo-page-body table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        margin: 12px 0 16px 0;
    }

    .memo-page-body th,
    .memo-page-body td {
        border: 1px solid #94a3b8;
        padding: 10px 12px;
        vertical-align: top;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .memo-page-body th {
        background: #f8fafc;
        font-weight: 600;
    }

    .memo-page-footer {
        margin-top: 40px;
        font-family: "Times New Roman", Georgia, serif;
        color: #1f2937;
    }

    .issued-block {
        font-size: 14px;
        line-height: 1.7;
        margin-bottom: 32px;
    }

    .prepared-block {
        margin-top: 20px;
    }

    .prepared-label {
        margin: 0 0 36px 0;
        font-size: 14px;
    }

    .signature-line {
        width: 240px;
        border-bottom: 1px solid #374151;
        margin-bottom: 4px;
    }

    .prepared-name {
        margin: 0;
        font-weight: 600;
        line-height: 1.2;
    }

    .prepared-role {
        margin: 0;
        line-height: 1.2;
    }

    .memo-extra-details {
        margin-top: 24px;
        font-size: 13px;
        line-height: 1.5;
    }

    .memo-extra-details p {
        margin: 2px 0;
    }

    .memo-footer-note {
    margin-top: 56px;
    font-size: 11px;
    line-height: 1.45;
    text-align: justify;
}

    .memo-footer-address {
    margin-top: 24px;
    font-size: 11px;
    line-height: 1.45;
}
</style>
<?php $__env->stopPush(); ?>

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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\townhall\show.blade.php ENDPATH**/ ?>