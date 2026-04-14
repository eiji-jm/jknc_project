<?php $__env->startSection('content'); ?>
<div class="min-h-full bg-[#f8f8f8]">
    <div class="w-full px-6 py-4">

        <!-- Top bar -->
        <div class="flex items-center justify-between mb-3">
            <div>
                <h1 class="text-sm font-semibold text-gray-800">Policy</h1>
            </div>

            <button
                type="button"
                onclick="document.getElementById('addPolicyModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-3 rounded-full transition"
            >
                <span class="text-xl leading-none -mt-0.5">+</span>
                <span>Add Policy</span>
            </button>
        </div>

        <!-- Spreadsheet-like table wrapper -->
        <div class="overflow-x-auto border border-gray-300 bg-white">
            <table class="min-w-[1400px] w-full border-collapse text-sm text-gray-800">
                <thead>
                    <tr class="bg-[#f7f7f7]">
                        <th class="border border-gray-300 px-4 py-3 text-left font-semibold w-[140px]">Status</th>
                        <th class="border border-gray-300 px-4 py-3 text-left font-semibold w-[140px]">Code</th>
                        <th class="border border-gray-300 px-4 py-3 text-left font-semibold w-[100px]">Version</th>
                        <th class="border border-gray-300 px-4 py-3 text-left font-semibold w-[150px]">Effectivity Date</th>
                        <th class="border border-gray-300 px-4 py-3 text-left font-semibold w-[150px]">Prepared by</th>
                        <th class="border border-gray-300 px-4 py-3 text-left font-semibold w-[150px]">Reviewed by</th>
                        <th class="border border-gray-300 px-4 py-3 text-left font-semibold w-[150px]">Approved by</th>
                        <th class="border border-gray-300 px-4 py-3 text-left font-semibold w-[140px]">Review Cycle</th>
                        <th class="border border-gray-300 px-4 py-3 text-left font-semibold w-[140px]">Review Date</th>
                        <th class="border border-gray-300 px-4 py-3 text-left font-semibold w-[160px]">Classification</th>
                        <th class="border border-gray-300 px-4 py-3 text-center font-semibold w-[100px]">Action</th>
                    </tr>
                </thead>

                <tbody>
                    
                    <?php $__empty_1 = true; $__currentLoopData = ($policies ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $policy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-4 py-3"><?php echo e($policy->status ?? '-'); ?></td>
                            <td class="border border-gray-300 px-4 py-3"><?php echo e($policy->code ?? '-'); ?></td>
                            <td class="border border-gray-300 px-4 py-3"><?php echo e($policy->version ?? '-'); ?></td>
                            <td class="border border-gray-300 px-4 py-3"><?php echo e($policy->effectivity_date ?? '-'); ?></td>
                            <td class="border border-gray-300 px-4 py-3"><?php echo e($policy->prepared_by ?? '-'); ?></td>
                            <td class="border border-gray-300 px-4 py-3"><?php echo e($policy->reviewed_by ?? '-'); ?></td>
                            <td class="border border-gray-300 px-4 py-3"><?php echo e($policy->approved_by ?? '-'); ?></td>
                            <td class="border border-gray-300 px-4 py-3"><?php echo e($policy->review_cycle ?? '-'); ?></td>
                            <td class="border border-gray-300 px-4 py-3"><?php echo e($policy->review_date ?? '-'); ?></td>
                            <td class="border border-gray-300 px-4 py-3"><?php echo e($policy->classification ?? '-'); ?></td>
                            <td class="border border-gray-300 px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="px-2 py-1 text-xs border border-blue-300 text-blue-700 rounded hover:bg-blue-50">
                                        View
                                    </button>
                                    <button class="px-2 py-1 text-xs border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                                        Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <?php for($i = 0; $i < 10; $i++): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-4">&nbsp;</td>
                                <td class="border border-gray-300 px-4 py-4"></td>
                                <td class="border border-gray-300 px-4 py-4"></td>
                                <td class="border border-gray-300 px-4 py-4"></td>
                                <td class="border border-gray-300 px-4 py-4"></td>
                                <td class="border border-gray-300 px-4 py-4"></td>
                                <td class="border border-gray-300 px-4 py-4"></td>
                                <td class="border border-gray-300 px-4 py-4"></td>
                                <td class="border border-gray-300 px-4 py-4"></td>
                                <td class="border border-gray-300 px-4 py-4"></td>
                                <td class="border border-gray-300 px-4 py-4"></td>
                            </tr>
                        <?php endfor; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Policy Modal -->
<div id="addPolicyModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('addPolicyModal').classList.add('hidden')"></div>

    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">
        <div class="w-full max-w-4xl bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Add Policy</h2>
                <button
                    type="button"
                    onclick="document.getElementById('addPolicyModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-3 rounded-full transition"
                >
                    <span class="text-xl leading-none -mt-0.5">+</span>
                    <span>Add Policy</span>
                </button>
            </div>

            <form action="<?php echo e(route('policies.store')); ?>" method="POST" class="p-6">
                <?php echo csrf_field(); ?>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                            <option value="">Select status</option>
                            <option value="Active">Active</option>
                            <option value="Draft">Draft</option>
                            <option value="Archived">Archived</option>
                            <option value="Obsolete">Obsolete</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                        <input type="text" name="code" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none" placeholder="Enter code">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Version</label>
                        <input type="text" name="version" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none" placeholder="Enter version">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Effectivity Date</label>
                        <input type="date" name="effectivity_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prepared by</label>
                        <input type="text" name="prepared_by" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none" placeholder="Enter name">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reviewed by</label>
                        <input type="text" name="reviewed_by" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none" placeholder="Enter name">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Approved by</label>
                        <input type="text" name="approved_by" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none" placeholder="Enter name">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Review Cycle</label>
                        <input type="text" name="review_cycle" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none" placeholder="e.g. Annual">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Review Date</label>
                        <input type="date" name="review_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                    </div>

                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Classification</label>
                        <input type="text" name="classification" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none" placeholder="Enter classification">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button
                        type="button"
                        onclick="document.getElementById('addPolicyModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition"
                    >
                        Save Policy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/policies/policies.blade.php ENDPATH**/ ?>