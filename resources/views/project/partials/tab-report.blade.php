@php
    $reports = $project->sowReports()->latest('date_prepared')->latest()->get();
    $statusClasses = [
        'Sent to Client' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        'Generated' => 'bg-blue-50 text-blue-700 border border-blue-200',
    ];
@endphp

<div class="space-y-5">
    <section class="project-top-card rounded-2xl px-6 py-5">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Report Registry</p>
                <h2 class="mt-2 text-3xl font-semibold text-gray-900">SOW Reports</h2>
                <p class="mt-2 text-sm text-slate-500">Generated scope of work reports are recorded here from the Scope of Work tab.</p>
            </div>
            <div class="flex flex-wrap gap-3 text-sm">
                <span class="project-pill"><span class="text-slate-400">Total Reports</span> {{ $reports->count() }}</span>
                <span class="project-pill"><span class="text-slate-400">Latest Report</span> {{ $reports->first()?->report_number ?: '-' }}</span>
            </div>
        </div>
    </section>

    <section class="project-linked-card overflow-hidden rounded-2xl">
        <div class="border-b border-slate-200 px-6 py-4">
            <div class="relative max-w-md">
                <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input
                    id="projectReportSearch"
                    type="text"
                    placeholder="Search report number or status..."
                    autocomplete="off"
                    class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-9 pr-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">
                    <tr>
                        <th class="px-6 py-4 text-left">Report No.</th>
                        <th class="px-6 py-4 text-left">Date of Reporting</th>
                        <th class="px-6 py-4 text-left">Date Sent to Client</th>
                        <th class="px-6 py-4 text-left">Status</th>
                    </tr>
                </thead>
                <tbody id="projectReportTableBody" class="divide-y divide-slate-100 bg-white">
                    @forelse ($reports as $item)
                        @php
                            $statusLabel = 'Sent to Client';
                            $previewUrl = route('project.report.preview', ['project' => $project->id, 'report' => $item->id]);
                        @endphp
                        <tr
                            class="cursor-pointer text-slate-700 transition hover:bg-slate-50"
                            data-report-search="{{ \Illuminate\Support\Str::lower(implode(' ', array_filter([$item->report_number, $statusLabel, optional($item->date_prepared)->format('M d, Y')])) ) }}"
                            onclick="window.location='{{ $previewUrl }}'"
                        >
                            <td class="px-6 py-4">
                                <span class="font-semibold text-blue-700 hover:text-blue-800">{{ $item->report_number ?: 'Report-'.$item->id }}</span>
                            </td>
                            <td class="px-6 py-4">{{ optional($item->date_prepared)->format('M d, Y') ?: '-' }}</td>
                            <td class="px-6 py-4">{{ optional($item->created_at)->format('M d, Y') ?: '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses[$statusLabel] ?? 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">
                                No generated SOW reports yet. Use <span class="font-semibold text-slate-700">Generate SOW Report</span> in the Scope of Work tab.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
    (() => {
        const searchInput = document.getElementById('projectReportSearch');
        const rows = Array.from(document.querySelectorAll('#projectReportTableBody tr[data-report-search]'));

        if (!searchInput || rows.length === 0) {
            return;
        }

        searchInput.addEventListener('input', () => {
            const keyword = String(searchInput.value || '').trim().toLowerCase();

            rows.forEach((row) => {
                const blob = String(row.dataset.reportSearch || '').toLowerCase();
                row.classList.toggle('hidden', keyword !== '' && !blob.includes(keyword));
            });
        });
    })();
</script>
