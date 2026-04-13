<form method="POST" action="{{ route('project.start.update', $project) }}" class="space-y-4">
    @csrf
    <section class="project-doc-shell">
        <div class="project-doc-topbar"></div>
        <div class="project-doc-header">
            <div class="project-doc-brand">
                <img src="{{ asset('images/imaglogo.png') }}" alt="John Kelly and Company">
                <div class="project-doc-title">
                    <h2>START</h2>
                    <p>Service Task Activation and Routing Tracker</p>
                </div>
            </div>
            <div class="flex items-start justify-between gap-4">
                <div class="grid flex-1 gap-3 md:grid-cols-4">
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Client Name</span><span class="project-doc-meta-value">{{ $project->client_name ?: '-' }}</span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Business Name</span><span class="project-doc-meta-value">{{ $project->business_name ?: '-' }}</span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Condeal Ref. No.</span><span class="project-doc-meta-value">{{ $project->deal?->deal_code ?: '-' }}</span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Engagement Type</span><span class="project-doc-meta-value">{{ $project->engagement_type ?: '-' }}</span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Product</span><span class="project-doc-meta-value">{{ $project->products ?: '-' }}</span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Services</span><span class="project-doc-meta-value">{{ $project->services ?: '-' }}</span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Date Started</span><span class="project-doc-meta-value">{{ optional($start?->date_started)->format('M d, Y') ?: '-' }}</span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Date Completed</span><span class="project-doc-meta-value">{{ optional($start?->date_completed)->format('M d, Y') ?: '-' }}</span></div>
                </div>
                <button type="submit" class="project-doc-primary whitespace-nowrap">Save START</button>
            </div>
        </div>
    </section>
    <section class="project-doc-section">
        <div class="project-doc-section-title">Tracking Details</div>
        <div class="project-doc-section-body grid gap-4 md:grid-cols-3 xl:grid-cols-4">
            <div><label class="project-doc-label">Date Started</label><input type="date" name="date_started" value="{{ old('date_started', optional($start?->date_started)->format('Y-m-d')) }}" class="project-doc-input"></div>
            <div><label class="project-doc-label">Date Completed</label><input type="date" name="date_completed" value="{{ old('date_completed', optional($start?->date_completed)->format('Y-m-d')) }}" class="project-doc-input"></div>
            <div><label class="project-doc-label">Status</label><select name="status" class="project-doc-select">@foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'completed' => 'Completed'] as $value => $label)<option value="{{ $value }}" @selected(old('status', $start?->status ?? 'pending') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div><label class="project-doc-label">Rejection Reason</label><input name="rejection_reason" value="{{ old('rejection_reason', $start?->rejection_reason) }}" class="project-doc-input"></div>
        </div>
    </section>
    <section class="project-doc-section">
        <div class="project-doc-section-title">Core Checklist</div>
        <div class="project-doc-section-body overflow-x-auto">
            <table class="project-doc-table text-sm">
                <thead>
                    <tr>
                        <th class="text-left">Requirement</th>
                        <th class="text-left">Provided</th>
                        <th class="text-left">Pending</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($startChecklist as $index => $item)
                        @php $status = old('checklist_status.'.$index, $item['status'] ?? 'pending'); @endphp
                        <tr>
                            <td>
                                <input type="text" name="checklist_label[]" value="{{ old('checklist_label.'.$index, $item['label'] ?? '') }}" class="project-doc-input">
                            </td>
                            <td class="text-center">
                                <input type="radio" name="checklist_status[{{ $index }}]" value="provided" {{ $status === 'provided' ? 'checked' : '' }} class="h-4 w-4 border-gray-300 text-blue-700 focus:ring-blue-600">
                            </td>
                            <td class="text-center">
                                <input type="radio" name="checklist_status[{{ $index }}]" value="pending" {{ $status !== 'provided' ? 'checked' : '' }} class="h-4 w-4 border-gray-300 text-blue-700 focus:ring-blue-600">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    <section class="project-doc-section">
        <div class="flex items-center justify-between gap-3">
            <div class="project-doc-section-title w-full">Engagement-Specific Requirements</div>
            <button type="button" class="project-doc-action mr-4 mt-3" data-add-row="start-requirements">Add Row</button>
        </div>
        <div class="project-doc-section-body overflow-x-auto">
            <table class="project-doc-table text-sm">
                <thead><tr><th class="text-left">Requirement / Document</th><th class="text-left">Purpose</th><th class="text-left">Assigned To</th><th class="text-left">Timeline</th></tr></thead>
                <tbody id="start-requirements" class="divide-y divide-gray-100">
                    @foreach ($startReqs as $index => $item)
                        <tr>
                            <td><input name="engagement_requirement[]" value="{{ old('engagement_requirement.'.$index, $item['requirement'] ?? '') }}" class="project-doc-input"></td>
                            <td><input name="engagement_purpose[]" value="{{ old('engagement_purpose.'.$index, $item['purpose'] ?? '') }}" class="project-doc-input"></td>
                            <td><input name="engagement_assigned_to[]" value="{{ old('engagement_assigned_to.'.$index, $item['assigned_to'] ?? '') }}" class="project-doc-input"></td>
                            <td><input name="engagement_timeline[]" value="{{ old('engagement_timeline.'.$index, $item['timeline'] ?? '') }}" class="project-doc-input"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    <section class="project-doc-section">
        <div class="flex items-center justify-between gap-3">
            <div class="project-doc-section-title w-full">Clearance Routing</div>
            <button type="button" class="project-doc-action mr-4 mt-3" data-add-row="start-routing">Add Signer</button>
        </div>
        <div class="project-doc-section-body grid gap-3">
            <div class="grid gap-3 md:grid-cols-2 text-xs uppercase tracking-wide text-gray-500"><div>Role</div><div>Status</div></div>
            <div id="start-routing" class="space-y-3">
                @foreach ($routing as $index => $item)
                    <div class="grid gap-3 md:grid-cols-2">
                        <input name="routing_role[]" value="{{ old('routing_role.'.$index, $item['role'] ?? '') }}" class="project-doc-input">
                        <select name="routing_status[]" class="project-doc-select">
                            @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('routing_status.'.$index, $item['status'] ?? 'pending') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</form>
