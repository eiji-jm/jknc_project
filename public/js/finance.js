(() => {
    const bootstrap = window.financeBootstrap || {};
    const csrfToken = bootstrap.csrfToken || '';
    const workflowFilters = ['all', 'Uploaded', 'Shared', 'Submitted', 'Accepted', 'Reverted', 'Archived'];

    const textField = (name, label, options = {}) => ({ name, label, type: 'text', ...options });
    const numberField = (name, label, options = {}) => ({ name, label, type: 'number', ...options });
    const dateField = (name, label, options = {}) => ({ name, label, type: 'date', ...options });
    const textareaField = (name, label, options = {}) => ({ name, label, type: 'textarea', rows: 3, fullWidth: true, ...options });
    const selectField = (name, label, options = {}) => ({ name, label, type: 'select', options: [], ...options });
    const checkboxField = (name, label, options = {}) => ({ name, label, type: 'checkbox', ...options });

    const friendlyFieldLabels = {
        'module_key': 'Finance section',
        'record_number': 'Record number',
        'record_title': 'Title',
        'record_date': 'Date',
        'amount': 'Amount',
        'status': 'Status',
        'data.completion_mode': 'Completion mode',
        'data.supplier_id': 'Supplier',
        'data.coa_id': 'Chart of account',
        'data.parent_account_id': 'Main account',
        'data.linked_coa_id': 'Linked chart of account',
        'data.linked_pr_id': 'Linked PR',
        'data.linked_ca_id': 'Linked CA',
        'data.linked_lr_id': 'Linked LR',
        'data.linked_po_id': 'Linked PO',
        'data.linked_dv_id': 'Linked DV',
        'data.bank_account_id': 'Bank account',
        'data.funding_bank_account_id': 'Funding bank account',
        'data.receiving_bank_account_id': 'Receiving bank / cash account',
        'data.source_bank_account_id': 'Source bank account',
        'data.destination_bank_account_id': 'Destination bank account',
        'data.source_document_type': 'Linked document type',
        'data.source_document_id': 'Linked source document',
        'data.master_item_type': 'Item type',
        'data.master_item_id': 'Item / service',
        'data.linked_item_type': 'Item type',
        'data.linked_item_id': 'Item / service',
        'data.payroll_expense_coa_id': 'Payroll expense account',
        'data.asset_coa_id': 'Asset account',
    };

    function friendlyLabelForError(fieldKey) {
        return friendlyFieldLabels[fieldKey] || fieldKey.replace(/^data\./, '').replace(/_/g, ' ');
    }

    function getFriendlyErrorMessage(message = '') {
        const match = String(message).match(/The\s+(.+?)\s+field is required/i);
        if (!match) return message;

        return `${friendlyLabelForError(match[1])} is required.`;
    }

    function showFinanceToast(message, type = 'info') {
        const host = $('financeToastStack');
        if (!host) {
            alert(message);
            return;
        }

        const palette = {
            info: 'border-sky-200 bg-sky-50 text-sky-800',
            success: 'border-green-200 bg-green-50 text-green-800',
            warning: 'border-amber-200 bg-amber-50 text-amber-800',
            error: 'border-red-200 bg-red-50 text-red-800',
        };

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto min-w-[280px] max-w-[360px] rounded-xl border px-4 py-3 shadow-lg backdrop-blur ${palette[type] || palette.info}`;
        toast.innerHTML = `
            <div class="flex items-start justify-between gap-3">
                <p class="text-sm leading-5 whitespace-pre-line">${escapeHtml(message)}</p>
                <button type="button" class="text-current/60 hover:text-current font-semibold" aria-label="Dismiss">&times;</button>
            </div>
        `;

        const close = () => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 180);
        };

        toast.querySelector('button')?.addEventListener('click', close);
        host.appendChild(toast);
        setTimeout(close, 4200);
    }

    let supplierCompletionMode = 'complete_internally';

    function isSupplierModule() {
        return currentModuleKey === 'supplier';
    }

    function isSendToSupplierMode() {
        return isSupplierModule() && supplierCompletionMode === 'send_to_supplier';
    }

    function renderSupplierModeTabs() {
        const target = $('supplierModeTabs');
        if (!target) return;

        if (!isSupplierModule()) {
            target.classList.add('hidden');
            target.innerHTML = '';
            return;
        }

        target.classList.remove('hidden');

        const activeComplete = supplierCompletionMode === 'complete_internally';
        const activeSend = supplierCompletionMode === 'send_to_supplier';

        target.innerHTML = `
            <div class="flex gap-2 rounded-xl border border-gray-200 bg-gray-50 p-1">
                <button type="button" onclick="window.financeModule.changeSupplierCompletionMode('complete_internally')"
                    class="flex-1 rounded-lg px-3 py-2 text-sm font-medium transition ${activeComplete ? 'bg-white text-blue-700 shadow-sm border border-blue-100' : 'text-gray-600 hover:text-gray-900'}">
                    Complete Internally
                </button>
                <button type="button" onclick="window.financeModule.changeSupplierCompletionMode('send_to_supplier')"
                    class="flex-1 rounded-lg px-3 py-2 text-sm font-medium transition ${activeSend ? 'bg-white text-blue-700 shadow-sm border border-blue-100' : 'text-gray-600 hover:text-gray-900'}">
                    Send to Supplier
                </button>
            </div>
            <p class="mt-2 text-xs text-gray-500">
                ${activeSend
                    ? 'Send a completion form to the supplier email address.'
                    : 'Fill out the supplier details internally and save the record.'}
            </p>
        `;
    }

    function setSupplierFormLayout() {
        const isSend = isSendToSupplierMode();
        [
            'recordCoreFields',
            'recordMetaFields',
            'statusField',
            'attachmentsSection',
        ].forEach((id) => {
            const el = $(id);
            if (el) {
                el.classList.toggle('hidden', isSend);
            }
        });

        const dynamicFields = $('dynamicFields');
        if (dynamicFields) {
            dynamicFields.classList.toggle('grid', !isSend);
            dynamicFields.classList.toggle('md:grid-cols-2', !isSend);
            dynamicFields.classList.toggle('gap-4', !isSend);
        }
    }

    const financeModules = {
        supplier: {
            label: 'Supplier',
            addLabel: 'Add Supplier',
            recordNumberLabel: 'Supplier Code / ID',
            recordTitleLabel: 'Business Name',
            recordDateLabel: 'Created Date',
            summaryKeys: ['business_name', 'representative_full_name', 'email_address', 'completion_mode'],
            fields: [
                selectField('completion_mode', 'Completion Mode', {
                    options: [
                        { value: 'complete_internally', label: 'Complete Internally' },
                        { value: 'send_to_supplier', label: 'Send to Supplier' },
                    ],
                    required: true,
                }),
                textField('trade_name', 'Trade Name'),
                textField('supplier_type', 'Supplier Type'),
                textField('representative_full_name', 'Representative Full Name', { required: true }),
                textField('designation', 'Designation'),
                textField('email_address', 'Email Address', { inputType: 'email', required: true }),
                textField('phone_number', 'Phone Number', { required: true }),
                textField('alternate_contact_number', 'Alternate Contact Number'),
                textareaField('business_address', 'Business Address'),
                textareaField('billing_address', 'Billing Address'),
                textField('tin', 'TIN'),
                selectField('vat_status', 'VAT / Non-VAT', {
                    options: [
                        { value: 'VAT', label: 'VAT' },
                        { value: 'Non-VAT', label: 'Non-VAT' },
                    ],
                }),
                textField('payment_terms', 'Payment Terms'),
                selectField('accreditation_status', 'Accreditation Status', {
                    options: [
                        { value: 'Pending', label: 'Pending' },
                        { value: 'For Accreditation', label: 'For Accreditation' },
                        { value: 'Accredited', label: 'Accredited' },
                        { value: 'Blacklisted', label: 'Blacklisted' },
                    ],
                }),
                textField('bank_name', 'Bank Name'),
                textField('bank_account_name', 'Bank Account Name'),
                textField('bank_account_number', 'Bank Account Number'),
                textareaField('remarks', 'Remarks'),
            ],
        },
        service: {
            label: 'Service',
            addLabel: 'Add Service',
            recordNumberLabel: 'Service Code / Item Code',
            recordTitleLabel: 'Service Name',
            recordDateLabel: 'Date',
            summaryKeys: ['supplier_id', 'coa_id', 'default_cost', 'category'],
            fields: [
                textareaField('service_description', 'Service Description'),
                selectField('supplier_id', 'Supplier', { source: 'supplier', required: true }),
                selectField('coa_id', 'Account', { source: 'chart_account', required: true }),
                textField('category', 'Category'),
                selectField('unit_of_measure', 'Unit of Measure', {
                    options: [
                        { value: 'per hour', label: 'per hour' },
                        { value: 'per day', label: 'per day' },
                        { value: 'per month', label: 'per month' },
                        { value: 'per service/job', label: 'per service/job' },
                        { value: 'per unit', label: 'per unit' },
                        { value: 'per session', label: 'per session' },
                        { value: 'per contract', label: 'per contract' },
                    ],
                }),
                numberField('default_cost', 'Default Cost'),
                selectField('tax_type', 'Tax Type', {
                    options: [
                        { value: 'VAT', label: 'VAT' },
                        { value: 'Non-VAT', label: 'Non-VAT' },
                        { value: 'Zero-Rated', label: 'Zero-Rated' },
                    ],
                }),
                selectField('service_status', 'Service Status', {
                    options: [
                        { value: 'Active', label: 'Active' },
                        { value: 'Inactive', label: 'Inactive' },
                    ],
                }),
                textareaField('remarks', 'Remarks'),
            ],
        },
        product: {
            label: 'Product',
            addLabel: 'Add Product',
            recordNumberLabel: 'Product Code / Item Code',
            recordTitleLabel: 'Product Name',
            recordDateLabel: 'Date',
            summaryKeys: ['supplier_id', 'coa_id', 'default_cost', 'category'],
            fields: [
                textareaField('product_description', 'Product Description'),
                selectField('supplier_id', 'Supplier', { source: 'supplier', required: true }),
                selectField('coa_id', 'Account', { source: 'chart_account', required: true }),
                textField('category', 'Category'),
                selectField('unit_of_measure', 'Unit of Measure', {
                    options: [
                        { value: 'per hour', label: 'per hour' },
                        { value: 'per day', label: 'per day' },
                        { value: 'per month', label: 'per month' },
                        { value: 'per service/job', label: 'per service/job' },
                        { value: 'per unit', label: 'per unit' },
                        { value: 'per session', label: 'per session' },
                        { value: 'per contract', label: 'per contract' },
                    ],
                }),
                numberField('default_cost', 'Default Cost'),
                selectField('tax_type', 'Tax Type', {
                    options: [
                        { value: 'VAT', label: 'VAT' },
                        { value: 'Non-VAT', label: 'Non-VAT' },
                        { value: 'Zero-Rated', label: 'Zero-Rated' },
                    ],
                }),
                selectField('product_status', 'Product Status', {
                    options: [
                        { value: 'Active', label: 'Active' },
                        { value: 'Inactive', label: 'Inactive' },
                    ],
                }),
                textareaField('remarks', 'Remarks'),
            ],
        },
        chart_account: {
            label: 'Chart of Accounts',
            addLabel: 'Add Chart of Account',
            recordNumberLabel: 'Account Code',
            recordTitleLabel: 'Account Name',
            recordDateLabel: 'Date Created',
            summaryKeys: ['account_type', 'account_group', 'parent_account_id', 'normal_balance'],
            fields: [
                textareaField('account_description', 'Account Description'),
                checkboxField('is_sub_account', 'Sub-Account'),
                selectField('parent_account_id', 'Main Account', { source: 'chart_account', dependsOnCheckbox: 'is_sub_account' }),
                selectField('account_type', 'Account Type', {
                    options: [
                        { value: 'Asset', label: 'Asset' },
                        { value: 'Liability', label: 'Liability' },
                        { value: 'Equity', label: 'Equity' },
                        { value: 'Income', label: 'Income' },
                        { value: 'Expense', label: 'Expense' },
                        { value: 'Contra', label: 'Contra' },
                    ],
                }),
                textField('account_group', 'Account Group'),
                selectField('normal_balance', 'Normal Balance', {
                    options: [
                        { value: 'Debit', label: 'Debit' },
                        { value: 'Credit', label: 'Credit' },
                    ],
                }),
                selectField('account_status', 'Status', {
                    options: [
                        { value: 'Active', label: 'Active' },
                        { value: 'Inactive', label: 'Inactive' },
                    ],
                }),
                textareaField('remarks', 'Remarks'),
            ],
        },
        bank_account: {
            label: 'Bank Accounts',
            addLabel: 'Add Bank Account',
            recordNumberLabel: 'Account Number',
            recordTitleLabel: 'Bank / Account Name',
            recordDateLabel: 'Date',
            summaryKeys: ['bank_name', 'branch', 'currency', 'linked_coa_id'],
            fields: [
                textField('bank_name', 'Bank Name', { required: true }),
                textField('branch', 'Branch'),
                selectField('currency', 'Currency', {
                    options: [
                        { value: 'PHP', label: 'PHP' },
                        { value: 'USD', label: 'USD' },
                        { value: 'EUR', label: 'EUR' },
                        { value: 'Other', label: 'Other' },
                    ],
                }),
                selectField('bank_status', 'Status', {
                    options: [
                        { value: 'Active', label: 'Active' },
                        { value: 'Inactive', label: 'Inactive' },
                    ],
                }),
                selectField('account_type', 'Account Type', {
                    options: [
                        { value: 'Savings', label: 'Savings' },
                        { value: 'Checking', label: 'Checking' },
                        { value: 'Current', label: 'Current' },
                        { value: 'Payroll', label: 'Payroll' },
                        { value: 'Cash', label: 'Cash' },
                    ],
                }),
                selectField('linked_coa_id', 'Linked Chart of Account', { source: 'chart_account' }),
                textareaField('signatory_notes', 'Signatory Notes'),
                textareaField('remarks', 'Remarks'),
            ],
        },
        pr: {
            label: 'Purchase Request',
            addLabel: 'Add PR',
            recordNumberLabel: 'PR Number',
            recordTitleLabel: 'Request Title',
            recordDateLabel: 'Date',
            summaryKeys: ['requesting_department', 'requestor', 'supplier_id', 'coa_id'],
            fields: [
                textField('requesting_department', 'Requesting Department / Unit', { required: true }),
                textField('requestor', 'Requestor', { required: true }),
                selectField('request_type', 'Type', {
                    options: [
                        { value: 'Service', label: 'Service' },
                        { value: 'Product', label: 'Product' },
                    ],
                    required: true,
                }),
                selectField('supplier_id', 'Supplier', { source: 'supplier' }),
                selectField('master_item_type', 'Master Item Type', {
                    options: [
                        { value: 'service', label: 'Service' },
                        { value: 'product', label: 'Product' },
                    ],
                }),
                selectField('master_item_id', 'Item / Service Selected', {
                    sourceMap: { service: 'service', product: 'product' },
                    sourceKey: 'master_item_type',
                }),
                textareaField('description_specification', 'Description / Specification'),
                numberField('quantity', 'Quantity'),
                numberField('unit_cost', 'Unit Cost'),
                numberField('estimated_total_cost', 'Estimated Total Cost'),
                selectField('coa_id', 'Account', { source: 'chart_account' }),
                textareaField('purpose', 'Purpose / Justification'),
                dateField('needed_date', 'Needed Date'),
                textareaField('remarks', 'Remarks'),
            ],
        },
        po: {
            label: 'Purchase Order',
            addLabel: 'Add PO',
            recordNumberLabel: 'PO Number',
            recordTitleLabel: 'Order Title',
            recordDateLabel: 'Date',
            summaryKeys: ['linked_pr_id', 'supplier_id', 'total_amount', 'expected_delivery_date'],
            fields: [
                selectField('linked_pr_id', 'Linked PR', { source: 'pr' }),
                selectField('supplier_id', 'Supplier', { source: 'supplier', required: true }),
                textareaField('delivery_address', 'Delivery Address'),
                textareaField('terms_and_conditions', 'Terms and Conditions'),
                selectField('linked_item_type', 'Items / Services Type', {
                    options: [
                        { value: 'service', label: 'Service' },
                        { value: 'product', label: 'Product' },
                    ],
                }),
                selectField('linked_item_id', 'Items / Services', {
                    sourceMap: { service: 'service', product: 'product' },
                    sourceKey: 'linked_item_type',
                }),
                numberField('quantity', 'Quantity'),
                numberField('unit_cost', 'Unit Cost'),
                numberField('total_amount', 'Total Amount'),
                selectField('coa_id', 'Account', { source: 'chart_account' }),
                dateField('expected_delivery_date', 'Expected Delivery Date'),
                textareaField('remarks', 'Remarks'),
            ],
        },
        ca: {
            label: 'Cash Advance',
            addLabel: 'Add CA',
            recordNumberLabel: 'CA Number',
            recordTitleLabel: 'Purpose',
            recordDateLabel: 'Date',
            summaryKeys: ['requestor', 'department', 'amount_requested', 'mode_of_release'],
            fields: [
                textField('requestor', 'Requestor', { required: true }),
                textField('department', 'Department'),
                textareaField('purpose', 'Purpose', { required: true }),
                numberField('amount_requested', 'Amount Requested', { required: true }),
                dateField('needed_date', 'Needed Date'),
                selectField('mode_of_release', 'Mode of Release', {
                    options: [
                        { value: 'Cash', label: 'Cash' },
                        { value: 'Bank Transfer', label: 'Bank Transfer' },
                        { value: 'Check', label: 'Check' },
                    ],
                }),
                selectField('bank_account_id', 'Bank Account / Cash Source', { source: 'bank_account' }),
                selectField('coa_id', 'Account from Chart of Accounts', { source: 'chart_account' }),
                textareaField('remarks', 'Remarks'),
            ],
        },
        lr: {
            label: 'Liquidation Report',
            addLabel: 'Add LR',
            recordNumberLabel: 'LR Number',
            recordTitleLabel: 'Liquidating Person',
            recordDateLabel: 'Date',
            summaryKeys: ['linked_dv_id', 'linked_ca_id', 'variance_indicator', 'actual_expenses', 'supplier_id'],
            summaryLookupSources: {
                linked_dv_id: 'dv_ca',
            },
            fields: [
                selectField('linked_dv_id', 'Linked DV', { source: 'dv_ca', required: true }),
                selectField('linked_ca_id', 'Linked CA', { source: 'ca' }),
                numberField('total_cash_advance', 'Total Cash Advance'),
                numberField('actual_expenses', 'Actual Expenses'),
                numberField('variance', 'Variance'),
                selectField('variance_indicator', 'If Shortage / If Overage', {
                    options: [
                        { value: 'Shortage', label: 'Shortage' },
                        { value: 'Overage', label: 'Overage' },
                        { value: 'Balanced', label: 'Balanced' },
                    ],
                }),
                textareaField('expense_line_items', 'Expense Line Items'),
                selectField('supplier_id', 'Supplier', { source: 'supplier' }),
                selectField('coa_id', 'Account', { source: 'chart_account' }),
                textField('official_receipt', 'Official Receipt / Reference'),
                textareaField('remarks', 'Remarks'),
            ],
        },
        err: {
            label: 'Expense Reimbursement Request',
            addLabel: 'Add ERR',
            recordNumberLabel: 'ERR Number',
            recordTitleLabel: 'Requestor',
            recordDateLabel: 'Date',
            summaryKeys: ['linked_lr_id', 'amount', 'supplier_id', 'bank_account_id'],
            fields: [
                selectField('linked_lr_id', 'Linked LR', { source: 'lr' }),
                textareaField('expense_details', 'Expense Details'),
                numberField('amount', 'Amount', { required: true }),
                selectField('supplier_id', 'Supplier', { source: 'supplier' }),
                selectField('coa_id', 'Account from Chart of Accounts', { source: 'chart_account' }),
                selectField('reimbursement_mode', 'Mode of Reimbursement', {
                    options: [
                        { value: 'Cash', label: 'Cash' },
                        { value: 'Bank Transfer', label: 'Bank Transfer' },
                        { value: 'Check', label: 'Check' },
                    ],
                }),
                selectField('bank_account_id', 'Bank Account', { source: 'bank_account' }),
                textareaField('remarks', 'Remarks'),
            ],
        },
        dv: {
            label: 'Disbursement Voucher',
            addLabel: 'Add DV',
            recordNumberLabel: 'DV Number',
            recordTitleLabel: 'Payee',
            recordDateLabel: 'Date',
            summaryKeys: ['source_document_type', 'source_document_id', 'amount', 'bank_account_id'],
            fields: [
                selectField('source_document_type', 'Linked Source Document Type', {
                    options: [
                        { value: 'pr', label: 'PR' },
                        { value: 'po', label: 'PO' },
                        { value: 'ca', label: 'CA' },
                        { value: 'lr', label: 'LR' },
                        { value: 'err', label: 'ERR' },
                        { value: 'pda', label: 'PDA' },
                        { value: 'crf', label: 'CRF' },
                        { value: 'ibtf', label: 'IBTF' },
                        { value: 'arf', label: 'ARF' },
                    ],
                }),
                selectField('source_document_id', 'Linked Source Document', {
                    sourceMap: {
                        pr: 'pr',
                        po: 'po',
                        ca: 'ca',
                        lr: 'lr',
                        err: 'err',
                        pda: 'pda',
                        crf: 'crf',
                        ibtf: 'ibtf',
                        arf: 'arf',
                    },
                    sourceKey: 'source_document_type',
                }),
                selectField('supplier_id', 'Supplier', { source: 'supplier' }),
                numberField('amount', 'Amount', { required: true }),
                selectField('payment_type', 'Payment Type', {
                    options: [
                        { value: 'Cash', label: 'Cash' },
                        { value: 'Check', label: 'Check' },
                        { value: 'Bank Transfer', label: 'Bank Transfer' },
                        { value: 'E-Wallet', label: 'E-Wallet' },
                    ],
                }),
                selectField('bank_account_id', 'Bank Account', { source: 'bank_account' }),
                selectField('coa_id', 'Account', { source: 'chart_account' }),
                textField('reference_number', 'Reference Number'),
                textareaField('purpose', 'Purpose'),
                dateField('payment_date', 'Payment Date'),
                textareaField('remarks', 'Remarks'),
            ],
        },
        pda: {
            label: 'Payroll Disbursement Authorization',
            addLabel: 'Add PDA',
            recordNumberLabel: 'PDA Number',
            recordTitleLabel: 'Payroll Period',
            recordDateLabel: 'Date',
            summaryKeys: ['total_payroll_amount', 'department', 'funding_bank_account_id', 'payroll_expense_coa_id'],
            fields: [
                numberField('total_payroll_amount', 'Total Payroll Amount', { required: true }),
                textField('department', 'Department / Coverage'),
                selectField('funding_bank_account_id', 'Funding Bank Account', { source: 'bank_account' }),
                selectField('payroll_expense_coa_id', 'Payroll Expense Account', { source: 'chart_account' }),
                textareaField('supporting_payroll_summary', 'Supporting Payroll Summary'),
                textareaField('remarks', 'Remarks'),
            ],
        },
        crf: {
            label: 'Cash Return Form',
            addLabel: 'Add CRF',
            recordNumberLabel: 'CRF Number',
            recordTitleLabel: 'Returnee',
            recordDateLabel: 'Date',
            summaryKeys: ['linked_lr_id', 'amount_returned', 'receiving_bank_account_id', 'coa_id'],
            fields: [
                selectField('linked_lr_id', 'Linked LR', { source: 'lr_overage', required: true }),
                numberField('amount_returned', 'Amount Returned', { required: true }),
                selectField('mode_of_return', 'Mode of Return', {
                    options: [
                        { value: 'Cash', label: 'Cash' },
                        { value: 'Bank Transfer', label: 'Bank Transfer' },
                        { value: 'Check', label: 'Check' },
                    ],
                }),
                selectField('receiving_bank_account_id', 'Receiving Bank / Cash Account', { source: 'bank_account' }),
                selectField('coa_id', 'Account from Chart of Accounts', { source: 'chart_account' }),
                textField('reference_number', 'Reference Number'),
                textareaField('remarks', 'Remarks'),
            ],
        },
        ibtf: {
            label: 'Interbank Fund Transfer Form',
            addLabel: 'Add IBTF',
            recordNumberLabel: 'IBTF Number',
            recordTitleLabel: 'Transfer Title',
            recordDateLabel: 'Date',
            summaryKeys: ['source_bank_account_id', 'destination_bank_account_id', 'amount', 'transfer_reference_number'],
            fields: [
                selectField('source_bank_account_id', 'Source Bank Account', { source: 'bank_account', required: true }),
                selectField('destination_bank_account_id', 'Destination Bank Account', { source: 'bank_account', required: true }),
                numberField('amount', 'Amount', { required: true }),
                textareaField('reason', 'Reason / Purpose'),
                textField('source_account_code', 'Source Account Code'),
                textField('destination_account_code', 'Destination Account Code'),
                textField('transfer_reference_number', 'Transfer Reference Number'),
                textareaField('remarks', 'Remarks'),
            ],
        },
        arf: {
            label: 'Asset Registration Form',
            addLabel: 'Add ARF',
            recordNumberLabel: 'ARF Number',
            recordTitleLabel: 'Asset Name',
            recordDateLabel: 'Date',
            summaryKeys: ['linked_po_id', 'linked_dv_id', 'acquisition_cost', 'asset_coa_id'],
            fields: [
                selectField('linked_po_id', 'Linked PO', { source: 'po' }),
                selectField('linked_dv_id', 'Linked DV', { source: 'dv' }),
                textareaField('asset_description', 'Asset Description'),
                textField('asset_category', 'Asset Category'),
                textField('serial_number', 'Serial Number'),
                textField('model', 'Model'),
                selectField('supplier_id', 'Supplier', { source: 'supplier' }),
                numberField('acquisition_cost', 'Acquisition Cost'),
                dateField('acquisition_date', 'Acquisition Date'),
                selectField('asset_coa_id', 'Asset Account from Chart of Accounts', { source: 'chart_account' }),
                textField('location', 'Location'),
                textField('custodian', 'Custodian'),
                textField('useful_life', 'Useful Life'),
                numberField('residual_value', 'Residual Value'),
                textareaField('remarks', 'Remarks'),
            ],
        },
    };

    const moduleKeys = Object.keys(financeModules);
    let financeRecords = Array.isArray(bootstrap.records) ? bootstrap.records.slice() : [];
    let financeLookupOptions = bootstrap.lookupOptions || {};
    let currentModuleKey = financeModules[bootstrap.currentModule] ? bootstrap.currentModule : 'supplier';
    let currentWorkflowFilter = workflowFilters.includes(bootstrap.currentWorkflowFilter) ? bootstrap.currentWorkflowFilter : 'all';
    let currentPreviewRecord = null;
    let currentEditRecordId = null;

    const $ = (id) => document.getElementById(id);

    function escapeHtml(value) {
        return String(value === null || value === undefined ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function formatCurrency(value) {
        if (value === null || value === undefined || value === '') return 'N/A';
        const num = Number(value);
        if (Number.isNaN(num)) return String(value);
        return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatDate(value) {
        return value || 'N/A';
    }

    function getModuleConfig(moduleKey) {
        return financeModules[moduleKey] || financeModules.supplier;
    }

    function getLookupLabel(moduleKey, id) {
        const options = financeLookupOptions[moduleKey] || [];
        const match = options.find((item) => String(item.id) === String(id));
        return match ? match.label : '';
    }

    function getFieldValue(record, fieldName) {
        if (!record) return '';
        if (Object.prototype.hasOwnProperty.call(record, fieldName)) {
            return record[fieldName];
        }
        return record.data ? record.data[fieldName] : '';
    }

    function buildSummary(record, config) {
        const keys = config.summaryKeys || [];
        const lookupMap = {
            supplier_id: 'supplier',
            coa_id: 'chart_account',
            parent_account_id: 'chart_account',
            linked_coa_id: 'chart_account',
            bank_account_id: 'bank_account',
            funding_bank_account_id: 'bank_account',
            receiving_bank_account_id: 'bank_account',
            source_bank_account_id: 'bank_account',
            destination_bank_account_id: 'bank_account',
            linked_pr_id: 'pr',
            linked_po_id: 'po',
            linked_ca_id: 'ca',
            linked_dv_id: 'dv',
            linked_lr_id: 'lr',
            master_item_id: (record.data && record.data.master_item_type) ? record.data.master_item_type : null,
            source_document_id: (record.data && record.data.source_document_type) ? record.data.source_document_type : null,
        };
        const values = keys.map((key) => {
            const value = getFieldValue(record, key);
            if (!value) return '';
            if (key.endsWith('_id')) {
                const lookup = (config.summaryLookupSources && config.summaryLookupSources[key]) || lookupMap[key] || key.replace('_id', '');
                return getLookupLabel(lookup, value) || String(value);
            }
            return String(value);
        }).filter(Boolean);

        return values.length ? values.join(' | ') : 'No additional summary available';
    }

    function workflowBadgeClass(value) {
        const v = (value || '').toLowerCase();
        if (v === 'uploaded') return 'text-orange-700';
        if (v === 'shared') return 'text-sky-700';
        if (v === 'submitted') return 'text-blue-700';
        if (v === 'accepted') return 'text-green-700';
        if (v === 'reverted') return 'text-yellow-700';
        if (v === 'archived') return 'text-gray-700';
        return 'text-gray-700';
    }

    function approvalBadgeClass(value) {
        const v = (value || '').toLowerCase();
        if (v === 'approved') return 'text-green-700';
        if (v === 'pending') return 'text-yellow-700';
        if (v === 'pending supplier completion') return 'text-sky-700';
        if (v === 'needs revision') return 'text-red-700';
        if (v === 'archived') return 'text-gray-700';
        return 'text-gray-700';
    }

    function workflowLabel(value) {
        return value || 'Uploaded';
    }

    function approvalLabel(value) {
        return value || 'Pending';
    }

    function filteredRecords() {
        return financeRecords.filter((record) => {
            if (record.module_key !== currentModuleKey) {
                return false;
            }
            if (currentWorkflowFilter === 'all') {
                return true;
            }
            return (record.workflow_status || 'Uploaded') === currentWorkflowFilter;
        });
    }

    function setStatusMessage() {
        const messageBox = $('statusMessage');
        const moduleLabel = getModuleConfig(currentModuleKey).label;

        if (currentWorkflowFilter === 'all') {
            messageBox.className = 'mt-1 mb-4 border border-blue-200 bg-blue-50 text-blue-700 text-[14px] px-4 py-3 rounded-md';
            messageBox.textContent = `${moduleLabel} records are ready for encoding, review, and submission.`;
            return;
        }

        const messages = {
            Uploaded: 'Draft records are saved locally and ready for submission.',
            Shared: 'These supplier records have been shared for external completion.',
            Submitted: 'These records are submitted and waiting for review.',
            Accepted: 'These records are approved and active for lookup flows.',
            Reverted: 'These records were reverted and can be corrected then resubmitted.',
            Archived: 'These records are archived.',
        };

        const palette = {
            Uploaded: 'border-blue-200 bg-blue-50 text-blue-700',
            Shared: 'border-sky-200 bg-sky-50 text-sky-700',
            Submitted: 'border-yellow-200 bg-yellow-50 text-yellow-700',
            Accepted: 'border-green-200 bg-green-50 text-green-700',
            Reverted: 'border-red-200 bg-red-50 text-red-700',
            Archived: 'border-gray-200 bg-gray-50 text-gray-700',
        };

        const tone = palette[currentWorkflowFilter] || palette.Uploaded;
        messageBox.className = `mt-1 mb-4 border text-[14px] px-4 py-3 rounded-md ${tone}`;
        messageBox.textContent = messages[currentWorkflowFilter] || messages.Uploaded;
    }

    function setActiveModuleTabs() {
        const container = $('moduleTabs');
        const previousScrollLeft = container ? container.scrollLeft : 0;
        const buttons = moduleKeys.map((key) => {
            const active = key === currentModuleKey;
            return `
                <button id="finance-tab-${key}" type="button" onclick="window.financeModule.changeModule('${key}')"
                    style="flex: 0 0 calc((100% - 2rem) / 5); min-width: 150px;"
                    class="snap-start px-4 py-3 text-sm font-medium border rounded-xl whitespace-nowrap text-center transition ${active ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'}">
                    ${escapeHtml(financeModules[key].label)}
                </button>
            `;
        });
        container.innerHTML = buttons.join('');
        container.scrollLeft = previousScrollLeft;
    }

    function setActiveWorkflowTabs() {
        const container = $('workflowTabs');
        const tabs = workflowFilters.map((status) => {
            const active = status === currentWorkflowFilter;
            const label = status === 'all' ? 'All' : status;
            return `
                <button type="button" onclick="window.financeModule.changeWorkflow('${status}')"
                    class="px-3 py-2 rounded-full border text-xs whitespace-nowrap transition ${active ? 'bg-gray-900 border-gray-900 text-white' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'}">
                    ${escapeHtml(label)}
                </button>
            `;
        });
        container.innerHTML = tabs.join('');
    }

    function renderTableHeader() {
        $('tableHeadRow').innerHTML = `
            <th class="w-36 p-3 text-left">Number</th>
            <th class="w-44 p-3 text-left">Title</th>
            <th class="p-3 text-left">Summary</th>
            <th class="w-32 p-3 text-left">Date</th>
            <th class="w-36 p-3 text-left">Workflow</th>
            <th class="w-36 p-3 text-left">Approval</th>
            <th class="w-32 p-3 text-left">Actions</th>
        `;
    }

    function renderTableRows() {
        const tableBody = $('tableBody');
        const rows = filteredRecords();
        const moduleConfig = getModuleConfig(currentModuleKey);

        tableBody.innerHTML = '';

        if (!rows.length) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="p-10 text-center text-gray-400 italic">No records found</td>
                </tr>
            `;
            return;
        }

        rows.forEach((item) => {
            tableBody.innerHTML += `
                <tr class="border-t hover:bg-blue-50 cursor-pointer" onclick="window.financeModule.openPreview(${item.id})">
                    <td class="p-3 break-words">${escapeHtml(item.record_number || '')}</td>
                    <td class="p-3 break-words">${escapeHtml(item.record_title || '')}</td>
                    <td class="p-3 text-gray-700">${escapeHtml(buildSummary(item, moduleConfig))}</td>
                    <td class="p-3">${escapeHtml(formatDate(item.record_date))}</td>
                    <td class="p-3 ${workflowBadgeClass(item.workflow_status)} font-medium">${escapeHtml(workflowLabel(item.workflow_status))}</td>
                    <td class="p-3 ${approvalBadgeClass(item.approval_status)} font-medium">${escapeHtml(approvalLabel(item.approval_status))}</td>
                    <td class="p-3">
                        <button type="button" onclick="event.stopPropagation(); window.financeModule.openPreview(${item.id})" class="text-blue-600 hover:underline">
                            View
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    function refreshFinanceView() {
        setActiveModuleTabs();
        setActiveWorkflowTabs();
        setStatusMessage();
        syncFinanceSidebarState();
        renderTableHeader();
        renderTableRows();
        updateAddButton();
    }

    function scrollFinanceModuleTabs(amount) {
        if (!moduleKeys.length) return;

        const container = $('moduleTabs');
        if (!container) return;

        const direction = amount > 0 ? 1 : -1;
        const distance = Math.max(container.clientWidth, 320);
        container.scrollBy({ left: distance * direction, behavior: 'smooth' });
    }

    function updateAddButton() {
        $('addButton').textContent = `+ ${getModuleConfig(currentModuleKey).addLabel}`;
    }

    function syncFinanceSidebarState() {
        const sidebarLinks = document.querySelectorAll('[data-finance-module]');
        sidebarLinks.forEach((link) => {
            const isActive = link.getAttribute('data-finance-module') === currentModuleKey;
            link.classList.toggle('bg-blue-50', isActive);
            link.classList.toggle('text-blue-700', isActive);
            link.classList.toggle('border', isActive);
            link.classList.toggle('border-blue-100', isActive);
            link.classList.toggle('font-semibold', isActive);
            link.classList.toggle('text-gray-700', !isActive);
            link.classList.toggle('hover:bg-gray-100', !isActive);
        });
    }

    function getFieldOptions(field, formValues = {}) {
        if (Array.isArray(field.options) && field.options.length) {
            return field.options;
        }

        if (field.source) {
            return financeLookupOptions[field.source] || [];
        }

        if (field.sourceMap && field.sourceKey) {
            const keyValue = formValues[`data[${field.sourceKey}]`] || formValues[field.sourceKey];
            const mappedModule = field.sourceMap[keyValue];
            return mappedModule ? (financeLookupOptions[mappedModule] || []) : [];
        }

        return [];
    }

    function renderDynamicField(field, value, formValues = {}) {
        const required = field.required ? 'required' : '';
        const label = escapeHtml(field.label);
        const hint = field.help ? `<p class="mt-1 text-xs text-gray-500">${escapeHtml(field.help)}</p>` : '';
        const fieldName = `data[${field.name}]`;
        const dependencyValue = field.dependsOnCheckbox ? formValues[`data[${field.dependsOnCheckbox}]`] : null;
        const disabledAttr = field.dependsOnCheckbox && !dependencyValue ? 'disabled' : '';
        const emptyOption = field.placeholder || `Select ${field.label}`;

        let control = '';

        if (field.type === 'textarea') {
            control = `<textarea name="${fieldName}" rows="${field.rows || 3}" class="w-full border rounded-md p-2">${escapeHtml(value)}</textarea>`;
        } else if (field.type === 'select') {
            const options = getFieldOptions(field, formValues)
                .map((option) => `<option value="${escapeHtml(option.id ?? option.value)}" ${String(value) === String(option.id ?? option.value) ? 'selected' : ''}>${escapeHtml(option.label ?? option.value)}</option>`)
                .join('');
            control = `
                <select name="${fieldName}" class="w-full border rounded-md p-2" ${disabledAttr} ${required}>
                    <option value="">${escapeHtml(emptyOption)}</option>
                    ${options}
                </select>
            `;
        } else if (field.type === 'checkbox') {
            control = `
                <label class="inline-flex items-center gap-2 mt-2">
                    <input type="checkbox" name="${fieldName}" value="1" ${value ? 'checked' : ''} class="rounded border-gray-300">
                    <span class="text-sm text-gray-700">${label}</span>
                </label>
            `;
        } else if (field.type === 'number') {
            control = `<input type="number" step="0.01" name="${fieldName}" value="${escapeHtml(value)}" class="w-full border rounded-md p-2" ${required}>`;
        } else if (field.type === 'date') {
            control = `<input type="date" name="${fieldName}" value="${escapeHtml(value)}" class="w-full border rounded-md p-2" ${required}>`;
        } else {
            control = `<input type="${field.inputType || 'text'}" name="${fieldName}" value="${escapeHtml(value)}" class="w-full border rounded-md p-2" ${required}>`;
        }

        return `
            <div class="${field.fullWidth ? 'md:col-span-2' : ''}">
                <label class="block text-sm font-medium mb-1">${label}${field.required ? ' <span class="text-red-500">*</span>' : ''}</label>
                ${control}
                ${hint}
            </div>
        `;
    }

    let financeFormValues = {};

    function getModuleFieldValue(record, field) {
        if (!record) return '';
        if (record.data && Object.prototype.hasOwnProperty.call(record.data, field.name)) {
            return record.data[field.name];
        }
        return '';
    }

    function getFormDisplayValue(field, value, formValues) {
        if (field.type === 'checkbox') {
            return value ? 'Yes' : 'No';
        }

        if (field.type === 'number') {
            return formatCurrency(value);
        }

        if (field.type === 'date') {
            return value || 'N/A';
        }

        if (field.type === 'select') {
            if (field.source) {
                return getLookupLabel(field.source, value) || value;
            }

            if (field.sourceMap && field.sourceKey) {
                const moduleKey = field.sourceMap[formValues[`data[${field.sourceKey}]`] || formValues[field.sourceKey]];
                return getLookupLabel(moduleKey, value) || value;
            }

            const option = (field.options || []).find((item) => String(item.value) === String(value));
            return option ? option.label : value;
        }

        return value;
    }

    function renderAttachmentList(attachments = []) {
        const target = $('existingAttachmentList');
        if (!attachments.length) {
            target.innerHTML = '<p class="text-xs text-gray-400 italic">No existing attachments.</p>';
            return;
        }

        target.innerHTML = attachments.map((attachment, index) => `
            <div class="flex items-center justify-between gap-3 border rounded-md px-3 py-2 text-sm">
                <div class="min-w-0">
                    <p class="font-medium text-gray-900 break-all">${escapeHtml(attachment.name || `Attachment ${index + 1}`)}</p>
                    <p class="text-xs text-gray-500 break-all">${escapeHtml(attachment.path || '')}</p>
                </div>
                <a href="/${String(attachment.path || '').replace(/^\//, '')}" target="_blank" class="text-blue-600 hover:underline shrink-0">Open</a>
            </div>
        `).join('');
    }

    function wireDynamicFieldEvents() {
        const form = $('financeForm');
        form.querySelectorAll('input, select, textarea').forEach((input) => {
            input.addEventListener('input', renderDrawerPreview);
            input.addEventListener('change', renderDrawerPreview);
        });
    }

    function renderFinanceForm(record = null) {
        const moduleConfig = getModuleConfig(currentModuleKey);
        const values = {};
        financeFormValues = values;
        if (currentModuleKey === 'supplier' && record?.data?.completion_mode) {
            supplierCompletionMode = record.data.completion_mode;
        } else if (currentModuleKey !== 'supplier') {
            supplierCompletionMode = 'complete_internally';
        }

        const recordNumberValue = record ? record.record_number || '' : '';
        const recordTitleValue = record ? record.record_title || '' : '';
        const recordDateValue = record ? record.record_date || '' : '';
        const amountValue = record ? record.amount || '' : '';
        const statusValue = record ? record.status || 'Active' : 'Active';
        const existingAttachments = record ? (record.attachments || []) : [];

        $('financeRecordId').value = record ? record.id : '';
        $('financeModuleKey').value = currentModuleKey;
        $('recordNumberLabel').textContent = moduleConfig.recordNumberLabel;
        $('recordTitleLabel').textContent = moduleConfig.recordTitleLabel;
        $('recordDateLabel').textContent = moduleConfig.recordDateLabel || 'Date';
        $('recordNumberInput').placeholder = moduleConfig.recordNumberLabel;
        $('recordTitleInput').placeholder = moduleConfig.recordTitleLabel;
        $('recordNumberInput').value = recordNumberValue;
        $('recordTitleInput').value = recordTitleValue;
        $('recordDateInput').value = recordDateValue;
        $('amountInput').value = amountValue;
        $('statusInput').value = statusValue;
        $('drawerTitle').textContent = record ? `Edit ${moduleConfig.label}` : `Add ${moduleConfig.label}`;
        $('drawerSubtitle').textContent = isSendToSupplierMode()
            ? 'Enter the supplier email address and send the completion form.'
            : (record
                ? `Update the ${moduleConfig.label.toLowerCase()} record and save changes.`
                : `Create a new ${moduleConfig.label.toLowerCase()} record.`);
        $('drawerPreviewTitle').textContent = record ? `Preview: ${record.display_label || record.record_title || moduleConfig.label}` : 'New Finance Record';
        $('drawerSaveButton').textContent = isSendToSupplierMode() ? 'Send Form' : (record ? 'Update' : 'Save');
        $('existingAttachmentsJson').value = JSON.stringify(existingAttachments);

        renderSupplierModeTabs();
        setSupplierFormLayout();

        const supplierFields = moduleConfig.fields.filter((field) => field.name !== 'completion_mode');
        const fieldsToRender = currentModuleKey === 'supplier' && isSendToSupplierMode()
            ? supplierFields.filter((field) => field.name === 'email_address')
            : supplierFields;

        const fieldsHtml = [
            currentModuleKey === 'supplier'
                ? `<input type="hidden" name="data[completion_mode]" value="${escapeHtml(supplierCompletionMode)}">`
                : '',
            ...fieldsToRender.map((field) => {
                const fieldValue = record ? getModuleFieldValue(record, field) : (values[`data[${field.name}]`] || '');
                values[field.name] = fieldValue;
                values[`data[${field.name}]`] = fieldValue;
                return renderDynamicField(field, fieldValue, values);
            }),
        ].join('');

        $('dynamicFields').innerHTML = fieldsHtml;
        renderAttachmentList(existingAttachments);
        wireDynamicFieldEvents();
        renderDrawerPreview();
    }

    function renderDrawerPreview() {
        const moduleConfig = getModuleConfig(currentModuleKey);
        const formValues = {};
        const formData = new FormData($('financeForm'));
        formData.forEach((value, key) => {
            formValues[key] = value;
        });
        const recordNumber = $('recordNumberInput').value.trim();
        const recordTitle = $('recordTitleInput').value.trim();
        const recordDate = $('recordDateInput').value;
        const amount = $('amountInput').value;

        const dataPairs = moduleConfig.fields.map((field) => {
            const value = formValues[`data[${field.name}]`];
            if (!value) {
                return '';
            }
            return `
                <div class="flex justify-between gap-4 py-2 border-b border-dashed border-gray-100">
                    <span class="text-gray-500">${escapeHtml(field.label)}</span>
                    <span class="text-right font-medium text-gray-900 break-words">${escapeHtml(getFormDisplayValue(field, value, formValues))}</span>
                </div>
            `;
        }).filter(Boolean).join('');

        $('drawerPreview').innerHTML = `
            <div class="space-y-4">
                <div class="rounded-xl border border-gray-200 p-4 bg-gray-50">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-gray-500">Number</p>
                            <p class="font-semibold text-gray-900">${escapeHtml(recordNumber || 'N/A')}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Title</p>
                            <p class="font-semibold text-gray-900 break-words">${escapeHtml(recordTitle || 'N/A')}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Date</p>
                            <p class="font-semibold text-gray-900">${escapeHtml(recordDate || 'N/A')}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Amount</p>
                            <p class="font-semibold text-gray-900">${escapeHtml(amount || '0.00')}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-gray-200 p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Details</h4>
                    <div class="space-y-1 text-sm">
                        ${dataPairs || '<p class="text-gray-400 italic">No module fields entered yet.</p>'}
                    </div>
                </div>
            </div>
        `;
    }

    function showOnlySection(sectionId) {
        $('tableSection').classList.add('hidden');
        $('previewSection').classList.add('hidden');
        $(sectionId).classList.remove('hidden');
    }

    function closePreview() {
        currentPreviewRecord = null;
        $('previewDocument').innerHTML = '';
        $('previewActions').innerHTML = '';
        showOnlySection('tableSection');
    }

    function openFinanceDrawer(record = null) {
        currentEditRecordId = record ? record.id : null;
        renderFinanceForm(record);
        const drawerSection = $('drawerSection');
        const drawerPanel = $('drawerPanel');
        drawerSection.classList.remove('hidden');
        requestAnimationFrame(() => drawerPanel.classList.remove('translate-x-full'));
    }

    function closeFinanceDrawer() {
        const drawerSection = $('drawerSection');
        const drawerPanel = $('drawerPanel');
        drawerPanel.classList.add('translate-x-full');
        setTimeout(() => {
            drawerSection.classList.add('hidden');
            currentEditRecordId = null;
        }, 300);
    }

    function changeSupplierCompletionMode(mode) {
        if (!isSupplierModule()) return;
        supplierCompletionMode = mode === 'send_to_supplier' ? 'send_to_supplier' : 'complete_internally';
        renderFinanceForm(currentEditRecordId ? getRecordById(currentEditRecordId) : null);
    }

    function getRecordById(id) {
        return financeRecords.find((record) => String(record.id) === String(id));
    }

    function renderPreviewDocument(record) {
        const moduleConfig = getModuleConfig(record.module_key);
        const entries = [
            ['Module', moduleConfig.label],
            ['Record Number', record.record_number],
            ['Record Title', record.record_title],
            ['Record Date', record.record_date],
            ['Amount', record.amount ? formatCurrency(record.amount) : 'N/A'],
            ['Status', record.status],
            ['Workflow', record.workflow_status],
            ['Approval', record.approval_status],
            ['Created By', record.user],
            ['Submitted At', record.submitted_at],
            ['Approved At', record.approved_at],
            ['Review Note', record.review_note || 'N/A'],
        ];

        const dynamicEntries = (moduleConfig.fields || []).map((field) => {
            const rawValue = getFieldValue(record, field.name);
            const value = getFormDisplayValue(field, rawValue, record.data || {});
            return [field.label, value || 'N/A'];
        });

        const attachments = (record.attachments || []).map((attachment) => `
            <a href="/${String(attachment.path || '').replace(/^\//, '')}" target="_blank" class="block text-blue-600 hover:underline break-all">${escapeHtml(attachment.name || attachment.path)}</a>
        `).join('');

        $('previewDocument').innerHTML = `
            <div class="max-w-4xl mx-auto">
                <div class="rounded-2xl border border-gray-200 p-6 bg-white shadow-sm">
                    <div class="flex items-start justify-between gap-4 border-b border-gray-100 pb-4 mb-6">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Finance Operations</p>
                            <h3 class="text-2xl font-semibold text-gray-900 mt-2">${escapeHtml(moduleConfig.label)}</h3>
                            <p class="text-sm text-gray-500 mt-1">${escapeHtml(record.display_label || '')}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Workflow</p>
                            <p class="font-semibold text-gray-900">${escapeHtml(record.workflow_status || '')}</p>
                            <p class="text-xs text-gray-500 mt-3">Approval</p>
                            <p class="font-semibold text-gray-900">${escapeHtml(record.approval_status || '')}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                        ${entries.map(([label, value]) => `
                            <div class="border border-gray-100 rounded-xl p-3">
                                <p class="text-xs uppercase tracking-wide text-gray-500">${escapeHtml(label)}</p>
                                <p class="mt-1 font-medium text-gray-900 break-words">${escapeHtml(value || 'N/A')}</p>
                            </div>
                        `).join('')}
                    </div>

                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Module Fields</h4>
                        <div class="space-y-2 text-sm">
                            ${dynamicEntries.map(([label, value]) => `
                                <div class="flex items-start justify-between gap-6 border-b border-dashed border-gray-100 py-2">
                                    <span class="text-gray-500">${escapeHtml(label)}</span>
                                    <span class="text-right font-medium text-gray-900 break-words">${escapeHtml(value || 'N/A')}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>

                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Attachments</h4>
                        <div class="space-y-2">
                            ${attachments || '<p class="text-gray-400 italic text-sm">No attachments uploaded.</p>'}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function renderPreviewActions(record) {
        const actions = [];
        actions.push(`<button type="button" onclick="window.financeModule.openFinanceDrawer(window.financeModule.getRecordById(${record.id}))" class="w-full border border-gray-300 rounded-md py-2 hover:bg-gray-50">Edit</button>`);
        actions.push(`<button type="button" onclick="window.financeModule.printFinanceRecord(${record.id})" class="w-full border border-gray-300 rounded-md py-2 hover:bg-gray-50">Print</button>`);

        if (record.can_submit) {
            actions.push(`<button type="button" onclick="window.financeModule.submitFinanceRecord(${record.id})" class="w-full bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700">Submit for Review</button>`);
        }

        if (record.can_share_supplier) {
            actions.push(`<button type="button" onclick="window.financeModule.shareSupplierRecord(${record.id})" class="w-full bg-sky-600 text-white rounded-md py-2 hover:bg-sky-700">Send to Supplier</button>`);
        }

        if (record.module_key === 'supplier' && record.data?.completion_mode === 'send_to_supplier' && (record.workflow_status === 'Shared' || record.share_token)) {
            actions.push(`<button type="button" onclick="window.financeModule.resendSupplierForm(${record.id})" class="w-full bg-sky-700 text-white rounded-md py-2 hover:bg-sky-800">Resend Completion Form</button>`);
            actions.push(`<button type="button" onclick="window.financeModule.changeSupplierEmailAndResend(${record.id})" class="w-full border border-sky-300 text-sky-700 rounded-md py-2 hover:bg-sky-50">Change Email &amp; Resend</button>`);
        }

        if (record.can_review) {
            actions.push(`<button type="button" onclick="window.financeModule.approveFinanceRecord(${record.id})" class="w-full bg-green-600 text-white rounded-md py-2 hover:bg-green-700">Approve</button>`);
            actions.push(`<button type="button" onclick="window.financeModule.revertFinanceRecord(${record.id})" class="w-full bg-amber-500 text-white rounded-md py-2 hover:bg-amber-600">Return for Revision</button>`);
            actions.push(`<button type="button" onclick="window.financeModule.archiveFinanceRecord(${record.id})" class="w-full bg-gray-700 text-white rounded-md py-2 hover:bg-gray-800">Archive</button>`);
        }

        if (record.supplier_completion_url) {
            actions.push(`<button type="button" onclick="window.financeModule.copySupplierLink('${escapeHtml(record.supplier_completion_url)}')" class="w-full border border-sky-300 text-sky-700 rounded-md py-2 hover:bg-sky-50">Copy Supplier Link</button>`);
        }

        $('previewActions').innerHTML = actions.join('');
    }

    function openPreview(id) {
        const record = getRecordById(id);
        if (!record) return;

        currentPreviewRecord = record;
        $('previewModuleTitle').textContent = record.module_label;
        $('previewRecordNumber').textContent = record.record_number || 'N/A';
        $('previewRecordTitle').textContent = record.record_title || 'N/A';
        $('previewRecordDate').textContent = record.record_date || 'N/A';
        $('previewAmount').textContent = record.amount ? formatCurrency(record.amount) : 'N/A';
        $('previewWorkflow').textContent = record.workflow_status || 'Uploaded';
        $('previewApproval').textContent = record.approval_status || 'Pending';
        $('previewStatus').textContent = record.status || 'Active';
        $('previewUser').textContent = record.user || bootstrap.currentUserName || '';
        $('previewReviewNote').textContent = record.review_note || 'N/A';
        renderPreviewDocument(record);
        renderPreviewActions(record);
        showOnlySection('previewSection');
    }

    function upsertFinanceRecord(record) {
        const index = financeRecords.findIndex((item) => String(item.id) === String(record.id));
        if (index >= 0) {
            financeRecords[index] = record;
        } else {
            financeRecords.unshift(record);
        }
        syncLookupOptions(record);
    }

    function syncLookupOptions(record) {
        const lookupKey = record.module_key;
        const eligible = ['Accepted'].includes(record.workflow_status) || ['Approved'].includes(record.approval_status);
        const options = financeLookupOptions[lookupKey] || [];
        const existingIndex = options.findIndex((option) => String(option.id) === String(record.id));

        if (eligible) {
            const newOption = {
                id: record.id,
                label: record.display_label || record.record_title || record.record_number || `${record.module_label} #${record.id}`,
            };

            if (existingIndex >= 0) {
                options[existingIndex] = newOption;
            } else {
                options.unshift(newOption);
            }

            financeLookupOptions[lookupKey] = options;
            return;
        }

        if (existingIndex >= 0) {
            financeLookupOptions[lookupKey] = options.filter((option) => String(option.id) !== String(record.id));
        }
    }

    async function saveFinanceRecord(event) {
        event.preventDefault();
        const form = $('financeForm');
        const formData = new FormData(form);
        const moduleConfig = getModuleConfig(currentModuleKey);
        const sendToSupplier = isSendToSupplierMode();

        formData.set('module_key', currentModuleKey);
        formData.set('data[completion_mode]', sendToSupplier ? 'send_to_supplier' : 'complete_internally');

        if (sendToSupplier && currentModuleKey === 'supplier') {
            const supplierEmail = String(formData.get('data[email_address]') || '').trim();
            if (!supplierEmail) {
                alert('Please enter the supplier email address.');
                return;
            }

            formData.set('record_number', $('recordNumberInput').value.trim() || `SUP-${Date.now()}`);
            formData.set('record_title', $('recordTitleInput').value.trim() || 'Supplier Completion');
            formData.set('record_date', $('recordDateInput').value || new Date().toISOString().slice(0, 10));
            formData.set('amount', $('amountInput').value || '');
            formData.set('status', $('statusInput').value || 'Active');
        } else {
            formData.set('record_number', $('recordNumberInput').value.trim());
            formData.set('record_title', $('recordTitleInput').value.trim());
            formData.set('record_date', $('recordDateInput').value);
            formData.set('amount', $('amountInput').value);
            formData.set('status', $('statusInput').value);

            if (!formData.get('record_number') || !formData.get('record_title') || !formData.get('record_date')) {
                alert('Please fill in the record number, title, and date.');
                return;
            }
        }

        const endpoint = currentEditRecordId ? `/finance/${currentEditRecordId}` : '/finance';
        if (currentEditRecordId) {
            formData.append('_method', 'PUT');
        }

        const res = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });

        let data = {};
        try {
            data = await res.json();
        } catch (error) {
            data = {};
        }

        if (!res.ok) {
            const firstErrorKey = data.errors ? Object.keys(data.errors)[0] : null;
            const firstErrorMessage = firstErrorKey && data.errors[firstErrorKey] ? data.errors[firstErrorKey][0] : '';
            const friendlyMessage = getFriendlyErrorMessage(firstErrorMessage || data.message || '');
            alert(friendlyMessage || `Please complete the required fields for ${moduleConfig.label.toLowerCase()}.`);
            return;
        }

        upsertFinanceRecord(data.data);
        closeFinanceDrawer();
        refreshFinanceView();
        openPreview(data.data.id);
    }

    async function submitFinanceRecord(id) {
        const res = await fetch(`/finance/${id}/submit`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        if (!res.ok) {
            alert(data.message || 'Unable to submit record.');
            return;
        }

        upsertFinanceRecord(data.data);
        refreshFinanceView();
        openPreview(data.data.id);
    }

    async function approveFinanceRecord(id) {
        const res = await fetch(`/finance/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        if (!res.ok) {
            alert(data.message || 'Unable to approve record.');
            return;
        }

        upsertFinanceRecord(data.data);
        refreshFinanceView();
        openPreview(data.data.id);
    }

    async function revertFinanceRecord(id) {
        const note = prompt('Enter a review note for reverting this record:');
        if (!note) return;

        const formData = new FormData();
        formData.append('review_note', note);

        const res = await fetch(`/finance/${id}/revert`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await res.json();
        if (!res.ok) {
            alert(data.message || 'Unable to revert record.');
            return;
        }

        upsertFinanceRecord(data.data);
        refreshFinanceView();
        openPreview(data.data.id);
    }

    async function archiveFinanceRecord(id) {
        const res = await fetch(`/finance/${id}/archive`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        if (!res.ok) {
            alert(data.message || 'Unable to archive record.');
            return;
        }

        upsertFinanceRecord(data.data);
        refreshFinanceView();
        openPreview(data.data.id);
    }

    async function shareSupplierRecord(id) {
        const res = await fetch(`/finance/${id}/share-supplier-link`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        if (!res.ok) {
            alert(data.message || 'Unable to create supplier link.');
            return;
        }

        upsertFinanceRecord(data.data);
        refreshFinanceView();
        openPreview(data.data.id);
        if (data.link) {
            await navigator.clipboard.writeText(data.link).catch(() => {});
            showFinanceToast('Supplier link has been emailed and copied to clipboard.', 'success');
        } else {
            showFinanceToast(data.message || 'Supplier link has been emailed.', 'success');
        }
    }

    async function resendSupplierForm(id) {
        const res = await fetch(`/finance/${id}/share-supplier-link`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        if (!res.ok) {
            alert(data.message || 'Unable to resend supplier form.');
            return;
        }

        upsertFinanceRecord(data.data);
        refreshFinanceView();
        openPreview(data.data.id);
        if (data.link) {
            await navigator.clipboard.writeText(data.link).catch(() => {});
            showFinanceToast('Supplier completion form resent and copied to clipboard.', 'success');
        } else {
            showFinanceToast(data.message || 'Supplier completion form resent.', 'success');
        }
    }

    async function changeSupplierEmailAndResend(id) {
        const record = getRecordById(id);
        if (!record) return;

        const currentEmail = record.data?.email_address || '';
        const email = prompt('Enter the new supplier email address:', currentEmail);
        if (!email) return;

        const formData = new FormData();
        formData.append('email_address', email.trim());

        const res = await fetch(`/finance/${id}/supplier-email`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await res.json();
        if (!res.ok) {
            const firstErrorKey = data.errors ? Object.keys(data.errors)[0] : null;
            const firstErrorMessage = firstErrorKey && data.errors[firstErrorKey] ? data.errors[firstErrorKey][0] : '';
            alert(firstErrorMessage || data.message || 'Unable to update supplier email.');
            return;
        }

        upsertFinanceRecord(data.data);
        refreshFinanceView();
        openPreview(data.data.id);
        if (data.link) {
            await navigator.clipboard.writeText(data.link).catch(() => {});
            showFinanceToast('Supplier email updated and completion form resent.', 'success');
        } else {
            showFinanceToast(data.message || 'Supplier email updated and completion form resent.', 'success');
        }
    }

    async function copySupplierLink(link) {
        if (!link) return;
        await navigator.clipboard.writeText(link).catch(() => {});
        showFinanceToast('Supplier link copied to clipboard.', 'success');
    }

    function printFinanceRecord(id) {
        const record = getRecordById(id);
        if (!record) return;

        const moduleConfig = getModuleConfig(record.module_key);
        const fields = moduleConfig.fields || [];
        const doc = window.open('', '_blank', 'width=1100,height=900');
        if (!doc) return;

        doc.document.write(`
            <html>
                <head>
                    <title>${escapeHtml(moduleConfig.label)} - ${escapeHtml(record.record_number || '')}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 32px; color: #111827; }
                        .card { border: 1px solid #e5e7eb; border-radius: 16px; padding: 24px; }
                        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
                        .item { border: 1px solid #f3f4f6; border-radius: 12px; padding: 12px; }
                        .label { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #6b7280; }
                        .value { margin-top: 6px; font-weight: 600; }
                        .section { margin-top: 20px; }
                        .row { display: flex; justify-content: space-between; gap: 16px; border-bottom: 1px dashed #e5e7eb; padding: 8px 0; }
                    </style>
                </head>
                <body>
                    <div class="card">
                        <div style="display:flex; justify-content:space-between; gap:16px; border-bottom:1px solid #f3f4f6; padding-bottom:16px; margin-bottom:20px;">
                            <div>
                                <div style="font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:.08em;">Finance Operations</div>
                                <h1 style="margin:8px 0 4px; font-size:28px;">${escapeHtml(moduleConfig.label)}</h1>
                                <div style="color:#6b7280;">${escapeHtml(record.display_label || '')}</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:11px; color:#6b7280;">Workflow</div>
                                <div style="font-weight:600;">${escapeHtml(record.workflow_status || '')}</div>
                                <div style="font-size:11px; color:#6b7280; margin-top:12px;">Approval</div>
                                <div style="font-weight:600;">${escapeHtml(record.approval_status || '')}</div>
                            </div>
                        </div>

                        <div class="grid">
                            <div class="item"><div class="label">Number</div><div class="value">${escapeHtml(record.record_number || 'N/A')}</div></div>
                            <div class="item"><div class="label">Title</div><div class="value">${escapeHtml(record.record_title || 'N/A')}</div></div>
                            <div class="item"><div class="label">Date</div><div class="value">${escapeHtml(record.record_date || 'N/A')}</div></div>
                            <div class="item"><div class="label">Amount</div><div class="value">${escapeHtml(record.amount ? formatCurrency(record.amount) : 'N/A')}</div></div>
                            <div class="item"><div class="label">Status</div><div class="value">${escapeHtml(record.status || 'Active')}</div></div>
                            <div class="item"><div class="label">Created By</div><div class="value">${escapeHtml(record.user || '')}</div></div>
                        </div>

                        <div class="section">
                            <h2>Module Fields</h2>
                            ${fields.map((field) => `
                                <div class="row">
                                    <div style="color:#6b7280;">${escapeHtml(field.label)}</div>
                                    <div style="font-weight:600; text-align:right; max-width:60%;">${escapeHtml(getFormDisplayValue(field, getFieldValue(record, field.name), record.data || {}))}</div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <script>window.onload = function(){ window.print(); };</script>
                </body>
            </html>
        `);
        doc.document.close();
    }

    function changeModule(moduleKey) {
        currentModuleKey = moduleKey;
        currentWorkflowFilter = 'all';
        supplierCompletionMode = moduleKey === 'supplier' ? 'complete_internally' : 'complete_internally';
        const url = new URL(window.location.href);
        url.searchParams.set('module', moduleKey);
        url.searchParams.delete('workflow_status');
        window.history.replaceState({}, '', url);
        refreshFinanceView();
        requestAnimationFrame(() => {
            const activeTab = document.getElementById(`finance-tab-${moduleKey}`);
            activeTab?.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        });
        closePreview();
    }

    function changeWorkflow(workflowStatus) {
        currentWorkflowFilter = workflowStatus;
        const url = new URL(window.location.href);
        url.searchParams.set('module', currentModuleKey);
        if (workflowStatus === 'all') {
            url.searchParams.delete('workflow_status');
        } else {
            url.searchParams.set('workflow_status', workflowStatus);
        }
        window.history.replaceState({}, '', url);
        refreshFinanceView();
        closePreview();
    }

    function initializeFinancePage() {
        const url = new URL(window.location.href);
        const moduleParam = url.searchParams.get('module');
        const workflowParam = url.searchParams.get('workflow_status');

        if (moduleParam && financeModules[moduleParam]) {
            currentModuleKey = moduleParam;
        }

        if (workflowParam && workflowFilters.includes(workflowParam)) {
            currentWorkflowFilter = workflowParam;
        }

        refreshFinanceView();
        renderTableHeader();
        renderTableRows();
        requestAnimationFrame(() => {
            const activeTab = document.getElementById(`finance-tab-${currentModuleKey}`);
            activeTab?.scrollIntoView({ behavior: 'auto', inline: 'center', block: 'nearest' });
        });
    }

    window.financeModule = {
        changeModule,
        changeWorkflow,
        changeSupplierCompletionMode,
        openFinanceDrawer,
        closeFinanceDrawer,
        openPreview,
        closePreview,
        getRecordById,
        saveFinanceRecord,
        submitFinanceRecord,
        approveFinanceRecord,
        revertFinanceRecord,
        archiveFinanceRecord,
        shareSupplierRecord,
        resendSupplierForm,
        changeSupplierEmailAndResend,
        copySupplierLink,
        printFinanceRecord,
        scrollFinanceModuleTabs,
    };

    window.openFinanceDrawer = () => window.financeModule.openFinanceDrawer();
    window.closeFinanceDrawer = () => window.financeModule.closeFinanceDrawer();
    window.closePreview = () => window.financeModule.closePreview();
    window.changeSupplierCompletionMode = (mode) => window.financeModule.changeSupplierCompletionMode(mode);
    window.saveFinanceRecord = (event) => window.financeModule.saveFinanceRecord(event);
    window.scrollFinanceModuleTabs = (amount) => window.financeModule.scrollFinanceModuleTabs(amount);

    $('recordNumberInput').addEventListener('input', renderDrawerPreview);
    $('recordTitleInput').addEventListener('input', renderDrawerPreview);
    $('recordDateInput').addEventListener('input', renderDrawerPreview);
    $('amountInput').addEventListener('input', renderDrawerPreview);
    $('attachmentsInput').addEventListener('change', renderDrawerPreview);

    initializeFinancePage();
})();
