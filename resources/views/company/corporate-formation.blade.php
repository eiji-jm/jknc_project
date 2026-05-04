@extends('layouts.app')
@section('title', 'Corporate Formation')

@php
    $tabs = [
        'sec-coi' => [
            'label' => 'SEC-COI',
            'route' => route('company.corporate-formation.sec-coi', $company->id),
            'store' => route('company.corporate-formation.sec-coi.store', $company->id),
            'update' => route('company.corporate-formation.sec-coi.update', [$company->id, '__RECORD__']),
            'preview' => 'corporate.formation.show',
            'button' => 'SEC-COI',
            'title' => 'Add SEC-COI Record',
            'columns' => [
                ['label' => 'Date Upload', 'key' => 'date_upload'],
                ['label' => 'Date Created', 'render' => fn ($row) => optional($row->created_at)->format('M d, Y')],
                ['label' => 'Company Reg No.', 'key' => 'company_reg_no'],
                ['label' => 'Corporation Name', 'key' => 'corporate_name', 'class' => 'font-semibold text-gray-800'],
                ['label' => 'Issued On', 'key' => 'issued_on'],
                ['label' => 'Issued By', 'key' => 'issued_by'],
                ['label' => 'File Upload', 'render' => fn ($row) => $row->file_path ? basename($row->file_path) : '-', 'class' => 'text-blue-600 font-medium'],
            ],
            'fields' => [
                ['name' => 'corporate_name', 'label' => 'Corporate Name', 'type' => 'text', 'required' => true],
                ['name' => 'company_reg_no', 'label' => 'Company Reg No.', 'type' => 'text', 'required' => true],
                ['name' => 'issued_by', 'label' => 'Issued By', 'type' => 'text', 'required' => true],
                ['name' => 'issued_on', 'label' => 'Issued On', 'type' => 'date', 'required' => true, 'group' => 'dates'],
                ['name' => 'date_upload', 'label' => 'Date Upload', 'type' => 'date', 'required' => true, 'group' => 'dates'],
                ['name' => 'file_upload', 'label' => 'File Upload', 'type' => 'file'],
            ],
        ],
        'sec-aoi' => [
            'label' => 'SEC-AOI',
            'route' => route('company.corporate-formation.sec-aoi', $company->id),
            'store' => route('company.corporate-formation.sec-aoi.store', $company->id),
            'update' => route('company.corporate-formation.sec-aoi.update', [$company->id, '__RECORD__']),
            'preview' => 'corporate.sec_aoi.show',
            'button' => 'SEC-AOI',
            'title' => 'Add SEC-AOI Record',
            'columns' => [
                ['label' => 'Date Upload', 'key' => 'date_upload'],
                ['label' => 'Uploaded By', 'key' => 'uploaded_by'],
                ['label' => 'Company Reg No.', 'key' => 'company_reg_no'],
                ['label' => 'Corporation Name', 'key' => 'corporation_name'],
                ['label' => 'Principal Address', 'key' => 'principal_address'],
                ['label' => 'Par Value', 'key' => 'par_value'],
                ['label' => 'Authorized Capital Stock', 'key' => 'authorized_capital_stock'],
                ['label' => 'Number of Directors', 'key' => 'directors'],
                ['label' => 'Type of Formation', 'key' => 'type_of_formation'],
                ['label' => 'SEC-AOI Version', 'key' => 'aoi_version'],
                ['label' => 'Type of SEC-AOI Version', 'key' => 'aoi_type'],
            ],
            'fields' => [
                ['name' => 'corporation_name', 'label' => 'Corporation Name', 'type' => 'text', 'required' => true],
                ['name' => 'company_reg_no', 'label' => 'Company Reg No.', 'type' => 'text', 'required' => true],
                ['name' => 'principal_address', 'label' => 'Principal Address', 'type' => 'text'],
                ['name' => 'par_value', 'label' => 'Par Value', 'type' => 'text', 'group' => 'pair_a'],
                ['name' => 'directors', 'label' => 'No. of Directors', 'type' => 'number', 'group' => 'pair_a'],
                ['name' => 'authorized_capital_stock', 'label' => 'Authorized Capital Stock', 'type' => 'text'],
                ['name' => 'type_of_formation', 'label' => 'Type of Formation', 'type' => 'select', 'options' => ['Stock Corporation', 'Non-Stock Corporation'], 'group' => 'pair_b'],
                ['name' => 'aoi_version', 'label' => 'SEC-AOI Version', 'type' => 'text', 'group' => 'pair_b'],
                ['name' => 'aoi_type', 'label' => 'Type of SEC-AOI Version', 'type' => 'select', 'options' => ['Original', 'Amended', 'Revised']],
                ['name' => 'uploaded_by', 'label' => 'Uploaded By', 'type' => 'text', 'group' => 'pair_c'],
                ['name' => 'date_upload', 'label' => 'Date Upload', 'type' => 'date', 'required' => true, 'group' => 'pair_c'],
                ['name' => 'file_upload', 'label' => 'File Upload', 'type' => 'file'],
            ],
        ],
        'bylaws' => [
            'label' => 'bylaws',
            'route' => route('company.corporate-formation.bylaws', $company->id),
            'store' => route('company.corporate-formation.bylaws.store', $company->id),
            'update' => route('company.corporate-formation.bylaws.update', [$company->id, '__RECORD__']),
            'preview' => 'corporate.bylaws.show',
            'button' => 'SEC-BYLAWS',
            'title' => 'Add Bylaws Record',
            'columns' => [
                ['label' => 'Date Upload', 'key' => 'date_upload'],
                ['label' => 'Uploaded By', 'key' => 'uploaded_by'],
                ['label' => 'Company Reg No.', 'key' => 'company_reg_no'],
                ['label' => 'Corporation Name', 'key' => 'corporation_name'],
                ['label' => 'Type of Formation', 'key' => 'type_of_formation'],
                ['label' => 'SEC-AOI Version', 'key' => 'aoi_version'],
                ['label' => 'Type of Version', 'key' => 'aoi_type'],
                ['label' => 'Date of Version', 'key' => 'aoi_date'],
                ['label' => 'Regular ASM', 'key' => 'regular_asm'],
                ['label' => 'Notice Time', 'key' => 'asm_notice'],
                ['label' => 'Regular BODM', 'key' => 'regular_bodm'],
                ['label' => 'Notice Time', 'key' => 'bodm_notice'],
            ],
            'fields' => [
                ['name' => 'corporation_name', 'label' => 'Corporation Name', 'type' => 'text', 'required' => true],
                ['name' => 'company_reg_no', 'label' => 'Company Reg No.', 'type' => 'text', 'required' => true],
                ['name' => 'type_of_formation', 'label' => 'Type of Formation', 'type' => 'text'],
                ['name' => 'aoi_version', 'label' => 'SEC-AOI Version', 'type' => 'text'],
                ['name' => 'aoi_type', 'label' => 'Type of Version', 'type' => 'text'],
                ['name' => 'aoi_date', 'label' => 'Date of Version', 'type' => 'date'],
                ['name' => 'regular_asm', 'label' => 'Regular ASM', 'type' => 'text'],
                ['name' => 'asm_notice', 'label' => 'ASM Notice Time', 'type' => 'text'],
                ['name' => 'regular_bodm', 'label' => 'Regular BODM', 'type' => 'text'],
                ['name' => 'bodm_notice', 'label' => 'BODM Notice Time', 'type' => 'text'],
                ['name' => 'uploaded_by', 'label' => 'Uploaded By', 'type' => 'text'],
                ['name' => 'date_upload', 'label' => 'Date Upload', 'type' => 'date', 'required' => true],
                ['name' => 'file_upload', 'label' => 'File Upload', 'type' => 'file'],
            ],
        ],
        'gis' => [
            'label' => 'GIS',
            'route' => route('company.corporate-formation.gis', $company->id),
            'store' => route('company.corporate-formation.gis.store', $company->id),
            'update' => route('company.corporate-formation.gis.update', [$company->id, '__RECORD__']),
            'preview' => 'gis.show',
            'button' => 'SEC-GIS',
            'title' => 'Add GIS Record',
            'columns' => [
                ['label' => 'Date Upload', 'render' => fn ($row) => optional($row->created_at)->format('M d, Y')],
                ['label' => 'Uploaded By', 'key' => 'uploaded_by'],
                ['label' => 'Sec-Submission Status', 'key' => 'submission_status'],
                ['label' => 'Sec-Receive on', 'key' => 'receive_on'],
                ['label' => 'Sec-Period Date', 'key' => 'period_date'],
                ['label' => 'Company Reg No.', 'key' => 'company_reg_no'],
                ['label' => 'Corporation Name', 'key' => 'corporation_name'],
                ['label' => 'Date of Annual Meeting', 'key' => 'annual_meeting'],
                ['label' => 'Type of Meeting', 'key' => 'meeting_type'],
            ],
            'fields' => [
                ['name' => 'uploaded_by', 'label' => 'Uploaded By', 'type' => 'text'],
                ['name' => 'submission_status', 'label' => 'Submission Status', 'type' => 'select', 'options' => ['Submitted', 'Received', 'Pending']],
                ['name' => 'receive_on', 'label' => 'Receive On', 'type' => 'date'],
                ['name' => 'period_date', 'label' => 'Period Date', 'type' => 'text'],
                ['name' => 'company_reg_no', 'label' => 'Company Reg No.', 'type' => 'text', 'required' => true],
                ['name' => 'corporation_name', 'label' => 'Corporation Name', 'type' => 'text', 'required' => true],
                ['name' => 'annual_meeting', 'label' => 'Annual Meeting', 'type' => 'date'],
                ['name' => 'meeting_type', 'label' => 'Meeting Type', 'type' => 'select', 'options' => ['Regular Annual Meeting', 'Special Meeting']],
                ['name' => 'file', 'label' => 'File Upload', 'type' => 'file'],
            ],
        ],
    ];
    $module = $tabs[$activeTab];
    $recordsForJs = $records->map(fn ($record) => array_merge($record->toArray(), [
        'preview_url' => route($module['preview'], $record->id),
    ]));
@endphp

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8" x-data="{ openPanel: false }">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        @include('company.partials.company-header', ['company' => $company])

        <section class="bg-gray-50 p-4 min-h-[760px]">
            <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 bg-white">
                    <div class="flex items-center gap-0 overflow-x-auto">
                        @foreach ($tabs as $key => $tab)
                            <a href="{{ $tab['route'] }}" class="min-w-[118px] px-6 py-3 text-sm font-medium border {{ $activeTab === $key ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-800 hover:bg-gray-50' }} text-center {{ $loop->first ? '' : 'border-l-0' }}">
                                {{ $tab['label'] }}
                            </a>
                        @endforeach
                    </div>

                    <div class="flex-1"></div>

                    <div class="flex items-center gap-2">
                        <button class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                            <i class="fas fa-bars text-sm"></i>
                        </button>

                        <button class="w-9 h-9 rounded-full border border-gray-200 text-gray-500 flex items-center justify-center hover:bg-gray-50">
                            <i class="fas fa-table-cells-large text-sm"></i>
                        </button>

                        <div class="flex items-center">
                            <button type="button" id="openFormationDrawer" class="px-4 h-9 rounded-l-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                                <span class="text-base leading-none">+</span>
                                {{ $module['button'] }}
                            </button>

                            <button class="w-10 h-9 rounded-r-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center border-l border-white/20">
                                <i class="fas fa-caret-down text-xs"></i>
                            </button>
                        </div>

                        <button class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                            <i class="fas fa-ellipsis-v text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="bg-gray-50 min-h-[680px]">
                    <div class="p-3">
                        @if (session('corporate_formation_success'))
                            <div class="mb-3 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                                {{ session('corporate_formation_success') }}
                            </div>
                        @endif

                        <div class="overflow-x-auto border border-gray-200 rounded-md bg-white">
                            <table class="min-w-full text-[11px] text-left text-gray-700">
                                <thead class="bg-white border-b border-gray-200">
                                    <tr>
                                        @foreach ($module['columns'] as $column)
                                            <th class="px-3 py-2 font-semibold">{{ $column['label'] }}</th>
                                        @endforeach
                                        <th class="px-3 py-2 font-semibold text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($records as $row)
                                        <tr class="border-b border-gray-200 hover:bg-blue-50 transition">
                                            @foreach ($module['columns'] as $column)
                                                @php
                                                    $value = isset($column['render']) ? $column['render']($row) : data_get($row, $column['key']);
                                                @endphp
                                                <td class="px-3 py-2 {{ $column['class'] ?? '' }}">{{ $value }}</td>
                                            @endforeach
                                            <td class="px-3 py-2">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route($module['preview'], $row->id) }}" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                        View
                                                    </a>
                                                    <button type="button" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50" data-edit-record='@json(array_merge($row->toArray(), ['preview_url' => route($module['preview'], $row->id)]))'>
                                                        Edit
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($module['columns']) + 1 }}" class="px-4 py-12 text-center text-sm text-gray-500">
                                                No {{ strtolower($module['label']) }} records found for {{ $company->company_name }}.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div id="formationDrawerOverlay" class="fixed inset-0 z-[70] bg-black/35 hidden"></div>

    <div id="formationDrawer" class="fixed top-0 right-0 bottom-0 z-[80] w-[430px] bg-white border-l border-gray-300 shadow-2xl translate-x-full transition-transform duration-300 ease-out">
        <form id="formationForm" action="{{ $module['store'] }}" method="POST" enctype="multipart/form-data" class="h-full flex flex-col">
            @csrf
            <input type="hidden" id="formationFormMethod" name="_method" value="POST">

            <div class="h-16 px-6 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h2 id="formationDrawerTitle" class="text-[26px] font-semibold text-gray-900 leading-none">{{ $module['title'] }}</h2>
                    <p class="mt-1 text-xs text-gray-500">This record is automatically scoped to {{ $company->company_name }}.</p>
                </div>

                <button type="button" id="closeFormationDrawer" class="w-9 h-9 rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-800 flex items-center justify-center transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-6">
                <div class="space-y-5">
                    @php($groupedFields = collect($module['fields'])->groupBy(fn ($field) => $field['group'] ?? '__single__'))
                    @foreach ($groupedFields as $group => $fields)
                        @if ($group !== '__single__' && $fields->count() === 2)
                            <div class="grid grid-cols-2 gap-4">
                                @foreach ($fields as $field)
                                    <div>
                                        <label class="block text-[13px] font-medium text-gray-700 mb-2">{{ $field['label'] }}@if(!empty($field['required'])) <span class="text-red-500">*</span>@endif</label>
                                        @if ($field['type'] === 'select')
                                            <select name="{{ $field['name'] }}" id="field_{{ $field['name'] }}" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm bg-white">
                                                @foreach ($field['options'] as $option)
                                                    <option value="{{ $option }}">{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" id="field_{{ $field['name'] }}" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm" @if(!empty($field['required'])) required @endif>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            @foreach ($fields as $field)
                                <div>
                                    <label class="block text-[13px] font-medium text-gray-700 mb-2">{{ $field['label'] }}@if(!empty($field['required'])) <span class="text-red-500">*</span>@endif</label>
                                    @if ($field['type'] === 'select')
                                        <select name="{{ $field['name'] }}" id="field_{{ $field['name'] }}" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm bg-white">
                                            @foreach ($field['options'] as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    @elseif ($field['type'] === 'file')
                                        <label class="w-full min-h-[84px] border border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 flex flex-col items-center justify-center gap-2 px-4 cursor-pointer transition">
                                            <i class="far fa-file-alt text-[26px] text-gray-500"></i>
                                            <span class="text-[14px] text-blue-600 font-medium">Choose file to upload</span>
                                            <span class="text-[11px] text-gray-400">PDF, DOC, DOCX supported</span>
                                            <input type="file" name="{{ $field['name'] }}" id="field_{{ $field['name'] }}" class="hidden">
                                        </label>
                                    @else
                                        <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" id="field_{{ $field['name'] }}" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm" @if(!empty($field['required'])) required @endif>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" id="cancelFormationDrawer" class="min-w-[92px] px-6 py-2.5 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>

                <button type="submit" id="formationSubmitButton" class="min-w-[92px] px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const drawer = document.getElementById('formationDrawer');
        const overlay = document.getElementById('formationDrawerOverlay');
        const openButton = document.getElementById('openFormationDrawer');
        const closeButtons = [document.getElementById('closeFormationDrawer'), document.getElementById('cancelFormationDrawer')].filter(Boolean);
        const form = document.getElementById('formationForm');
        const formMethod = document.getElementById('formationFormMethod');
        const drawerTitle = document.getElementById('formationDrawerTitle');
        const submitButton = document.getElementById('formationSubmitButton');
        const editButtons = document.querySelectorAll('[data-edit-record]');
        const updateUrlTemplate = @json($module['update']);
        const defaultTitle = @json($module['title']);
        const records = @json($recordsForJs);

        const openDrawer = () => {
            overlay.classList.remove('hidden');
            drawer.classList.remove('translate-x-full');
        };

        const closeDrawer = () => {
            overlay.classList.add('hidden');
            drawer.classList.add('translate-x-full');
        };

        const resetForm = () => {
            form.reset();
            form.action = @json($module['store']);
            formMethod.value = 'POST';
            drawerTitle.textContent = defaultTitle;
            submitButton.textContent = 'Save';
        };

        const fillForm = (record) => {
            @foreach ($module['fields'] as $field)
                @if ($field['type'] !== 'file')
                    if (document.getElementById('field_{{ $field['name'] }}')) {
                        document.getElementById('field_{{ $field['name'] }}').value = record.{{ $field['name'] }} ?? '';
                    }
                @endif
            @endforeach
        };

        openButton?.addEventListener('click', function () {
            resetForm();
            openDrawer();
        });

        closeButtons.forEach((button) => button?.addEventListener('click', closeDrawer));
        overlay?.addEventListener('click', closeDrawer);

        editButtons.forEach((button) => {
            button.addEventListener('click', function () {
                const record = JSON.parse(this.dataset.editRecord);
                resetForm();
                form.action = updateUrlTemplate.replace('__RECORD__', record.id);
                formMethod.value = 'PUT';
                drawerTitle.textContent = defaultTitle.replace('Add', 'Edit');
                submitButton.textContent = 'Update';
                fillForm(record);
                openDrawer();
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeDrawer();
            }
        });

        @if ($errors->any())
            openDrawer();
        @endif
    });
</script>
@endsection
