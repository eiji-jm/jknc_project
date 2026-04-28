<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        <?php echo $__env->make('company.partials.company-header', ['company' => $company], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <section class="bg-gray-50 p-4 min-h-[760px]">
            <div id="companyLguApp" class="bg-white rounded-xl border border-gray-200 flex flex-col min-h-[680px]" x-data="{ showSlideOver: false }">
                <div x-show="showSlideOver" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
                    <div class="absolute inset-0">
                        <div @click="showSlideOver = false" class="absolute inset-0 bg-gray-900 bg-opacity-50"></div>
                        <div class="absolute inset-y-0 right-0 flex max-w-full">
                            <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col h-full"
                                x-transition:enter="transform transition ease-in-out duration-300"
                                x-transition:enter-start="translate-x-full"
                                x-transition:enter-end="translate-x-0"
                                x-transition:leave="transform transition ease-in-out duration-300"
                                x-transition:leave-start="translate-x-0"
                                x-transition:leave-end="translate-x-full">
                                <div class="p-6 border-b flex justify-between">
                                    <div>
                                        <h2 id="lguDrawerTitle" class="font-bold text-lg">Add Permit Entry</h2>
                                        <p class="mt-1 text-sm text-gray-500">Records added here are automatically associated with <?php echo e($company->company_name); ?>.</p>
                                    </div>
                                    <button type="button" @click="showSlideOver = false" class="text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <form id="lguForm" method="POST" action="<?php echo e(route('company.lgu.store', $company->id)); ?>" class="flex min-h-0 flex-1 flex-col">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" id="lguFormMethod" name="_method" value="POST">

                                    <div class="p-6 space-y-4 flex-1 overflow-y-auto">
                                        <div>
                                            <label class="block text-sm font-medium">Permit Type</label>
                                            <select id="permitTypeInput" name="permit_type" class="w-full border rounded-md p-2 bg-white">
                                                <?php $__currentLoopData = $permitTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permitType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($permitType); ?>" <?php if($selectedPermit === $permitType): echo 'selected'; endif; ?>><?php echo e($permitType); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium">Client</label>
                                            <input class="w-full border rounded-md p-2 bg-gray-100 text-gray-600" value="<?php echo e($company->company_name); ?>" readonly>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium">Date</label>
                                            <input id="dateInput" name="date" type="date" class="w-full border rounded-md p-2" required>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium">Uploader</label>
                                            <input id="userInput" name="user" class="w-full border rounded-md p-2" placeholder="Uploader name" required>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium">TIN</label>
                                            <input id="tinInput" name="tin" class="w-full border rounded-md p-2" placeholder="TIN" required>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium">Registration Status</label>
                                            <select id="regInput" name="reg" class="w-full border rounded-md p-2 bg-white">
                                                <?php $__currentLoopData = ['Renewed', 'Pending', 'Expired', 'Active']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $registrationStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($registrationStatus); ?>"><?php echo e($registrationStatus); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium">Status</label>
                                            <select id="statusInput" name="status" class="w-full border rounded-md p-2 bg-white">
                                                <?php $__currentLoopData = ['Active', 'For Review', 'Overdue']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($status); ?>"><?php echo e($status); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="p-6 border-t flex gap-3">
                                        <button type="button" @click="showSlideOver = false" class="flex-1 border py-2 rounded">
                                            Cancel
                                        </button>
                                        <button type="submit" id="lguSubmitButton" class="flex-1 bg-blue-600 text-white py-2 rounded">
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between px-4 py-3 border-b shrink-0">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-gray-900">LGU</h2>
                        <p class="mt-1 text-sm text-gray-500">Manage local government unit records for this company.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <button id="permitDropdownBtn" class="flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-md font-medium hover:bg-gray-200">
                                <span id="selectedPermit"><?php echo e($selectedPermit); ?></span> <span>▾</span>
                            </button>
                            <div id="permitMenu" class="hidden absolute left-0 mt-2 w-56 bg-white border shadow-xl rounded-md z-50 py-1">
                                <?php $__currentLoopData = $permitTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permitType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(route('company.lgu', ['company' => $company->id, 'permit' => $permitType, 'status' => $selectedStatus])); ?>" class="block px-4 py-2 hover:bg-gray-100 cursor-pointer"><?php echo e($permitType); ?></a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <button @click="showSlideOver = true; resetLguForm()" class="bg-blue-600 text-white px-6 py-2 rounded text-sm">
                            + Add
                        </button>
                    </div>
                </div>

                <?php if(session('lgu_success')): ?>
                    <div class="mx-4 mt-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                        <?php echo e(session('lgu_success')); ?>

                    </div>
                <?php endif; ?>

                <div class="p-4 flex-grow overflow-hidden">
                    <div class="border rounded-md h-full overflow-auto">
                        <table class="w-full text-sm table-fixed border-collapse">
                            <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                                <tr>
                                    <th id="sortDate" class="w-32 p-3 text-left cursor-pointer select-none hover:text-blue-600">Date <span id="dateIcon">↓</span></th>
                                    <th id="sortUploader" class="w-32 p-3 text-left cursor-pointer select-none hover:text-blue-600">Uploader <span id="userIcon">↓</span></th>
                                    <th class="w-24 p-3 text-left">TIN</th>
                                    <th class="w-32 p-3 text-left">Reg Status</th>
                                    <th class="w-32 p-3 text-left relative overflow-visible">
                                        <button id="statusFilterBtn" class="flex items-center gap-1 hover:text-blue-600 font-bold">Status ▾</button>
                                        <div id="statusMenu" class="hidden absolute left-0 mt-2 w-36 bg-white border shadow-xl rounded-md z-50 py-1">
                                            <?php $__currentLoopData = ['all' => 'Show All', 'Active' => 'Active', 'For Review' => 'For Review', 'Overdue' => 'Overdue']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a href="<?php echo e(route('company.lgu', ['company' => $company->id, 'permit' => $selectedPermit, 'status' => $value])); ?>" class="block px-4 py-2 hover:bg-gray-100 cursor-pointer <?php echo e($value === 'Active' ? 'text-green-600' : ($value === 'For Review' ? 'text-yellow-600' : ($value === 'Overdue' ? 'text-red-600' : ''))); ?>"><?php echo e($label); ?></a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </th>
                                    <th class="w-36 p-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody" class="bg-white">
                                <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $statusClass = $record['status'] === 'Active' ? 'text-green-600' : ($record['status'] === 'Overdue' ? 'text-red-600' : 'text-yellow-600');
                                        $dotClass = $record['status'] === 'Active' ? 'bg-green-500' : ($record['status'] === 'Overdue' ? 'bg-red-500' : 'bg-yellow-500');
                                    ?>
                                    <tr class="border-t hover:bg-gray-50" data-record='<?php echo json_encode($record, 15, 512) ?>'>
                                        <td class="p-3"><?php echo e($record['date']); ?></td>
                                        <td class="p-3"><?php echo e($record['user']); ?></td>
                                        <td class="p-3"><?php echo e($record['tin']); ?></td>
                                        <td class="p-3"><?php echo e($record['reg']); ?></td>
                                        <td class="p-3">
                                            <span class="status-val flex items-center gap-1.5 <?php echo e($statusClass); ?>"><span class="w-2 h-2 <?php echo e($dotClass); ?> rounded-full"></span><?php echo e($record['status']); ?></span>
                                        </td>
                                        <td class="p-3">
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="button" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50" onclick="editLguRecord(<?php echo \Illuminate\Support\Js::from($record)->toHtml() ?>)">
                                                    Edit
                                                </button>
                                                <form method="POST" action="<?php echo e(route('company.lgu.destroy', [$company->id, $record['id']])); ?>" onsubmit="return confirm('Delete this LGU record?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <input type="hidden" name="permit" value="<?php echo e($selectedPermit); ?>">
                                                    <button type="submit" class="inline-flex h-8 items-center rounded-full border border-red-200 px-3 text-xs font-medium text-red-600 hover:bg-red-50">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="p-10 text-center text-gray-400 italic">
                                            No LGU records for this company yet.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const permitDropdownBtn = document.getElementById('permitDropdownBtn');
        const permitMenu = document.getElementById('permitMenu');
        const statusFilterBtn = document.getElementById('statusFilterBtn');
        const statusMenu = document.getElementById('statusMenu');
        const tableBody = document.getElementById('tableBody');
        const rows = Array.from(tableBody.querySelectorAll('tr[data-record]'));
        let sortDirs = { date: true, user: true };

        permitDropdownBtn?.addEventListener('click', function (event) {
            event.stopPropagation();
            permitMenu.classList.toggle('hidden');
        });

        statusFilterBtn?.addEventListener('click', function (event) {
            event.stopPropagation();
            statusMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', function () {
            permitMenu?.classList.add('hidden');
            statusMenu?.classList.add('hidden');
        });

        const sortRows = (key, iconId) => {
            const sorted = rows.sort((a, b) => {
                const recordA = JSON.parse(a.dataset.record);
                const recordB = JSON.parse(b.dataset.record);
                const first = String(recordA[key] ?? '');
                const second = String(recordB[key] ?? '');

                return sortDirs[key] ? first.localeCompare(second) : second.localeCompare(first);
            });

            sortDirs[key] = !sortDirs[key];
            document.getElementById(iconId).textContent = sortDirs[key] ? '↓' : '↑';
            sorted.forEach((row) => tableBody.appendChild(row));
        };

        document.getElementById('sortDate')?.addEventListener('click', function () {
            sortRows('date', 'dateIcon');
        });

        document.getElementById('sortUploader')?.addEventListener('click', function () {
            sortRows('user', 'userIcon');
        });

        <?php if($errors->any()): ?>
            document.getElementById('companyLguApp')?.__x?.$data && (document.getElementById('companyLguApp').__x.$data.showSlideOver = true);
        <?php endif; ?>
    });

    function resetLguForm() {
        const form = document.getElementById('lguForm');
        form.reset();
        form.action = <?php echo json_encode(route('company.lgu.store', $company->id), 512) ?>;
        document.getElementById('lguFormMethod').value = 'POST';
        document.getElementById('lguDrawerTitle').textContent = 'Add Permit Entry';
        document.getElementById('lguSubmitButton').textContent = 'Save';
        document.getElementById('permitTypeInput').value = <?php echo json_encode($selectedPermit, 15, 512) ?>;
    }

    function editLguRecord(record) {
        const container = document.getElementById('companyLguApp');
        resetLguForm();
        document.getElementById('lguForm').action = <?php echo json_encode(route('company.lgu.update', [$company->id, '__RECORD__'])) ?>.replace('__RECORD__', record.id);
        document.getElementById('lguFormMethod').value = 'PUT';
        document.getElementById('lguDrawerTitle').textContent = 'Edit Permit Entry';
        document.getElementById('lguSubmitButton').textContent = 'Update';
        document.getElementById('permitTypeInput').value = record.permit_type;
        document.getElementById('dateInput').value = record.date;
        document.getElementById('userInput').value = record.user;
        document.getElementById('tinInput').value = record.tin;
        document.getElementById('regInput').value = record.reg;
        document.getElementById('statusInput').value = record.status;
        if (container && container.__x) {
            container.__x.$data.showSlideOver = true;
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\company\lgu.blade.php ENDPATH**/ ?>