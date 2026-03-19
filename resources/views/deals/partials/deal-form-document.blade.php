@php
    $data = $dealFormData ?? [];
    $text = static fn (string $key, string $fallback = '-') => filled($data[$key] ?? null) ? (string) $data[$key] : $fallback;
    $list = static fn (string $key): array => collect($data[$key] ?? [])->filter(fn ($value) => filled($value))->values()->all();
    $statusMap = is_array($data['requirements_status_map'] ?? null) ? $data['requirements_status_map'] : [];
@endphp

<div class="mx-auto w-full max-w-6xl border border-gray-700 bg-white text-[11px] leading-tight text-black">
    <div class="border-b border-gray-700 px-4 py-2 text-center">
        <h1 class="text-2xl font-bold">Consulting & Deal Form</h1>
    </div>

    <div class="grid grid-cols-12 border-b border-gray-700 text-[10px]">
        <div class="col-span-3 border-r border-gray-700 p-2">Deal Name<div class="mt-1 min-h-5 text-[11px]">{{ $text('deal_name') }}</div></div>
        <div class="col-span-3 border-r border-gray-700 p-2">Stage<div class="mt-1 min-h-5 text-[11px]">{{ $text('stage') }}</div></div>
        <div class="col-span-3 border-r border-gray-700 p-2">Engagement Type<div class="mt-1 min-h-5 text-[11px]">{{ $text('engagement_type') }}</div></div>
        <div class="col-span-3 p-2">Total Value<div class="mt-1 min-h-5 text-[11px]">{{ $text('total_estimated_engagement_value') }}</div></div>
    </div>

    <div class="border-b border-gray-700 bg-blue-900 px-3 py-1 text-center text-[12px] font-semibold text-white">Contact Information</div>
    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">Salutation</div><div class="mt-1 min-h-5">{{ $text('salutation') }}</div></div>
        <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">First Name</div><div class="mt-1 min-h-5">{{ $text('first_name') }}</div></div>
        <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">Middle Initial</div><div class="mt-1 min-h-5">{{ $text('middle_initial') }}</div></div>
        <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">Last Name</div><div class="mt-1 min-h-5">{{ $text('last_name') }}</div></div>
        <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">Name Extension</div><div class="mt-1 min-h-5">{{ $text('name_extension') }}</div></div>
        <div class="col-span-2 p-2"><div class="text-[10px]">Sex</div><div class="mt-1 min-h-5">{{ $text('sex') }}</div></div>
    </div>
    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">Date of Birth</div><div class="mt-1 min-h-5">{{ $text('date_of_birth') }}</div></div>
        <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">Email Address</div><div class="mt-1 min-h-5">{{ $text('email') }}</div></div>
        <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">Mobile Number</div><div class="mt-1 min-h-5">{{ $text('mobile') }}</div></div>
        <div class="col-span-3 p-2"><div class="text-[10px]">Position / Designation</div><div class="mt-1 min-h-5">{{ $text('position') }}</div></div>
    </div>
    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-6 border-r border-gray-700 p-2"><div class="text-[10px]">Address</div><div class="mt-1 min-h-6">{{ $text('address') }}</div></div>
        <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">Company</div><div class="mt-1 min-h-6">{{ $text('company_name') }}</div></div>
        <div class="col-span-3 p-2"><div class="text-[10px]">Company Address</div><div class="mt-1 min-h-6">{{ $text('company_address') }}</div></div>
    </div>

    <div class="border-b border-gray-700 bg-blue-900 px-3 py-1 text-center text-[12px] font-semibold text-white">Service Identification</div>
    <div class="border-b border-gray-700 p-2">
        <div class="text-[10px] font-semibold">Service Area</div>
        <div class="mt-1 min-h-5">{{ $text('service_area') }}</div>
    </div>
    <div class="border-b border-gray-700 p-2">
        <div class="text-[10px] font-semibold">Services</div>
        <div class="mt-1 min-h-6">{{ $text('services') }}</div>
    </div>

    <div class="border-b border-gray-700 bg-blue-900 px-3 py-1 text-center text-[12px] font-semibold text-white">Products</div>
    <div class="border-b border-gray-700 p-2"><div class="text-[10px] font-semibold">Products / Deliverables</div><div class="mt-1 min-h-6">{{ $text('products') }}</div></div>
    <div class="border-b border-gray-700 p-2"><div class="text-[10px] font-semibold">Scope of Work</div><div class="mt-1 min-h-8">{{ $text('scope_of_work') }}</div></div>

    <div class="border-b border-gray-700 bg-blue-900 px-3 py-1 text-center text-[12px] font-semibold text-white">Client Requirements</div>
    <div class="grid grid-cols-12 border-b border-gray-700 text-[10px]">
        <div class="col-span-6 border-r border-gray-700 p-2 font-semibold">Requirement</div>
        <div class="col-span-3 border-r border-gray-700 p-2 text-center font-semibold">Provided</div>
        <div class="col-span-3 p-2 text-center font-semibold">Pending</div>
    </div>
    @foreach ([
        'client_contact_form' => 'Client Contact Form',
        'deal_form' => 'Deal Form',
        'business_information_form' => 'Business Information Form',
        'client_information_form' => 'Client Information Form',
        'service_task_activation_routing_tracker' => 'Service Task Activation & Routing Tracker (Start)',
        'others' => 'Others',
    ] as $key => $label)
        @php $status = strtolower((string) ($statusMap[$key] ?? '')); @endphp
        <div class="grid grid-cols-12 border-b border-gray-700 text-[10px]">
            <div class="col-span-6 border-r border-gray-700 p-2">{{ $label }}</div>
            <div class="col-span-3 border-r border-gray-700 p-2 text-center">{{ $status === 'provided' ? 'Yes' : '-' }}</div>
            <div class="col-span-3 p-2 text-center">{{ $status === 'pending' ? 'Yes' : '-' }}</div>
        </div>
    @endforeach

    <div class="border-b border-gray-700 bg-blue-900 px-3 py-1 text-center text-[12px] font-semibold text-white">Fees & Payment</div>
    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">Professional Fee</div><div class="mt-1 min-h-5">{{ $text('estimated_professional_fee') }}</div></div>
        <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">Government Fees</div><div class="mt-1 min-h-5">{{ $text('estimated_government_fees') }}</div></div>
        <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">Service Support Fee</div><div class="mt-1 min-h-5">{{ $text('estimated_service_support_fee') }}</div></div>
        <div class="col-span-3 p-2"><div class="text-[10px]">Total Value</div><div class="mt-1 min-h-5">{{ $text('total_estimated_engagement_value') }}</div></div>
    </div>
    <div class="border-b border-gray-700 p-2"><div class="text-[10px]">Payment Terms</div><div class="mt-1 min-h-5">{{ $text('payment_terms') }} {{ filled($data['payment_terms_other'] ?? null) ? '- '.$data['payment_terms_other'] : '' }}</div></div>

    <div class="border-b border-gray-700 bg-blue-900 px-3 py-1 text-center text-[12px] font-semibold text-white">Timeline & Assessment</div>
    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-4 border-r border-gray-700 p-2"><div class="text-[10px]">Planned Start Date</div><div class="mt-1 min-h-5">{{ $text('planned_start_date') }}</div></div>
        <div class="col-span-4 border-r border-gray-700 p-2"><div class="text-[10px]">Estimated Duration</div><div class="mt-1 min-h-5">{{ $text('estimated_duration') }}</div></div>
        <div class="col-span-4 p-2"><div class="text-[10px]">Estimated Completion Date</div><div class="mt-1 min-h-5">{{ $text('estimated_completion_date') }}</div></div>
    </div>
    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-6 border-r border-gray-700 p-2"><div class="text-[10px]">Client Preferred Completion Date</div><div class="mt-1 min-h-5">{{ $text('client_preferred_completion_date') }}</div></div>
        <div class="col-span-6 p-2"><div class="text-[10px]">Confirmed Delivery Date</div><div class="mt-1 min-h-5">{{ $text('confirmed_delivery_date') }}</div></div>
    </div>
    <div class="border-b border-gray-700 p-2"><div class="text-[10px]">Timeline Notes</div><div class="mt-1 min-h-6">{{ $text('timeline_notes') }}</div></div>
    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-4 border-r border-gray-700 p-2"><div class="text-[10px]">Service Complexity</div><div class="mt-1 min-h-5">{{ $text('service_complexity') }}</div></div>
        <div class="col-span-8 p-2"><div class="text-[10px]">Professional Support Required</div><div class="mt-1 min-h-5">{{ $text('support_required') }}</div></div>
    </div>
    <div class="border-b border-gray-700 p-2"><div class="text-[10px]">Notes / Explanation</div><div class="mt-1 min-h-6">{{ $text('complexity_notes') }}</div></div>

    <div class="border-b border-gray-700 bg-blue-900 px-3 py-1 text-center text-[12px] font-semibold text-white">Proposal & Internal Assignment</div>
    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-6 border-r border-gray-700 p-2"><div class="text-[10px]">Proposal Decision</div><div class="mt-1 min-h-5">{{ $text('proposal_decision') }}</div></div>
        <div class="col-span-6 p-2"><div class="text-[10px]">Decline Reason</div><div class="mt-1 min-h-5">{{ $text('decline_reason') }}</div></div>
    </div>
    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-4 border-r border-gray-700 p-2"><div class="text-[10px]">Assigned Consultant</div><div class="mt-1 min-h-5">{{ $text('assigned_consultant') }}</div></div>
        <div class="col-span-4 border-r border-gray-700 p-2"><div class="text-[10px]">Assigned Associate</div><div class="mt-1 min-h-5">{{ $text('assigned_associate') }}</div></div>
        <div class="col-span-4 p-2"><div class="text-[10px]">Service Department / Unit</div><div class="mt-1 min-h-5">{{ $text('service_department_unit') }}</div></div>
    </div>

    <div class="border-b border-gray-700 bg-blue-900 px-3 py-1 text-center text-[12px] font-semibold text-white">Notes & Approval</div>
    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-6 border-r border-gray-700 p-2"><div class="text-[10px]">Consultant Notes</div><div class="mt-1 min-h-8">{{ $text('consultant_notes') }}</div></div>
        <div class="col-span-6 p-2"><div class="text-[10px]">Associate Notes</div><div class="mt-1 min-h-8">{{ $text('associate_notes') }}</div></div>
    </div>
    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">Prepared By</div><div class="mt-1 min-h-5">{{ $text('prepared_by') }}</div></div>
        <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">Reviewed By</div><div class="mt-1 min-h-5">{{ $text('reviewed_by') }}</div></div>
        <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">Date</div><div class="mt-1 min-h-5">{{ $text('internal_date') }}</div></div>
        <div class="col-span-3 p-2"><div class="text-[10px]">Client Fullname & Signature</div><div class="mt-1 min-h-5">{{ $text('client_fullname_signature') }}</div></div>
    </div>
    <div class="grid grid-cols-12">
        <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">Referred / Closed By</div><div class="mt-1 min-h-5">{{ $text('referred_closed_by') }}</div></div>
        <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">Sales & Marketing</div><div class="mt-1 min-h-5">{{ $text('internal_sales_marketing') }}</div></div>
        <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">Lead Consultant</div><div class="mt-1 min-h-5">{{ $text('lead_consultant') }}</div></div>
        <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">Lead Associate</div><div class="mt-1 min-h-5">{{ $text('lead_associate_assigned') }}</div></div>
        <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">Finance</div><div class="mt-1 min-h-5">{{ $text('internal_finance') }}</div></div>
        <div class="col-span-2 p-2"><div class="text-[10px]">President</div><div class="mt-1 min-h-5">{{ $text('internal_president') }}</div></div>
    </div>
</div>
