<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Project;
use App\Models\ProjectSow;
use App\Models\ProjectSowReport;
use App\Models\ProjectStart;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProjectProvisioner
{
    public function createFromApprovedDeal(Deal $deal): ?Project
    {
        if (! Schema::hasTable('projects')) {
            return null;
        }

        $engagementType = Str::lower(trim((string) ($deal->engagement_type ?? '')));
        if (! Str::contains($engagementType, 'project') && ! Str::contains($engagementType, 'hybrid')) {
            return null;
        }

        $existingProject = Project::query()->where('deal_id', $deal->id)->first();
        if ($existingProject) {
            return $existingProject;
        }

        $contact = $deal->contact()->first();
        $company = $this->resolveDealCompany($deal, $contact);
        $clientName = trim(collect([
            $deal->first_name ?: $contact?->first_name,
            $deal->middle_name ?: $contact?->middle_name,
            $deal->last_name ?: $contact?->last_name,
        ])->filter()->implode(' '));

        $scopeItems = $this->defaultScopeItems($deal);
        $internalApproval = $this->defaultInternalApprovalPayload($deal);

        $project = Project::query()->create([
            'deal_id' => $deal->id,
            'contact_id' => $deal->contact_id,
            'company_id' => $company?->id,
            'name' => $deal->deal_code ?: ('Project for '.($deal->company_name ?: $clientName ?: 'Client')),
            'engagement_type' => $deal->engagement_type,
            'status' => 'Start',
            'current_phase' => 'Start',
            'current_step' => 'START Checklist',
            'planned_start_date' => $deal->planned_start_date,
            'target_completion_date' => $deal->estimated_completion_date,
            'client_preferred_completion_date' => $deal->client_preferred_completion_date,
            'assigned_project_manager' => $deal->assigned_consultant,
            'assigned_consultant' => $deal->assigned_consultant,
            'assigned_associate' => $deal->assigned_associate,
            'client_name' => $clientName,
            'business_name' => $deal->company_name ?: $contact?->company_name ?: $company?->company_name,
            'service_area' => $deal->service_area,
            'services' => $deal->services,
            'products' => $deal->products,
            'deal_value' => $deal->total_estimated_engagement_value,
            'scope_summary' => $deal->scope_of_work,
            'client_confirmation_name' => $clientName,
            'metadata' => [
                'created_from' => 'deal_approval',
                'deal_stage_at_creation' => $deal->stage,
            ],
            'opened_at' => now(),
        ]);

        ProjectStart::query()->create([
            'project_id' => $project->id,
            'date_started' => now()->toDateString(),
            'status' => 'pending',
            'checklist' => $this->defaultStartChecklist($deal, $contact, $company),
            'engagement_requirements' => $this->defaultStartEngagementRequirements($deal),
            'routing' => $this->defaultStartRouting(),
        ]);

        ProjectSow::query()->create([
            'project_id' => $project->id,
            'version_number' => '1.0',
            'date_prepared' => now()->toDateString(),
            'within_scope_items' => $scopeItems,
            'out_of_scope_items' => [],
            'client_confirmation_name' => $clientName,
            'internal_approval' => $internalApproval,
            'approval_status' => 'draft',
            'ntp_status' => 'pending',
        ]);

        ProjectSowReport::query()->create([
            'project_id' => $project->id,
            'version_number' => '1.0',
            'date_prepared' => now()->toDateString(),
            'within_scope_items' => $scopeItems,
            'out_of_scope_items' => [],
            'status_summary' => [
                'total_main_tasks' => count($scopeItems),
                'open' => count($scopeItems),
                'in_progress' => 0,
                'delayed' => 0,
                'completed' => 0,
                'on_hold' => 0,
            ],
            'project_completion_percentage' => 0,
            'key_issues' => null,
            'recommendations' => null,
            'way_forward' => null,
            'client_confirmation_name' => $clientName,
            'internal_approval' => $internalApproval,
        ]);

        return $project;
    }

    private function resolveDealCompany(Deal $deal, ?Contact $contact): ?Company
    {
        if (! Schema::hasTable('companies')) {
            return null;
        }

        $companyName = trim((string) ($deal->company_name ?: $contact?->company_name));
        if ($companyName === '') {
            return null;
        }

        return Company::query()
            ->where('company_name', $companyName)
            ->first();
    }

    private function defaultStartChecklist(Deal $deal, ?Contact $contact, ?Company $company): array
    {
        $contactApproved = strtolower((string) ($contact?->cif_status ?? '')) === 'approved';
        $bifApproved = Schema::hasTable('company_bifs')
            && strtolower((string) ($company?->latestBif?->status ?? '')) === 'approved';

        return [
            ['label' => 'Client Contact Form', 'status' => $deal->contact_id ? 'provided' : 'pending', 'auto_checked' => true],
            ['label' => 'Deal Form', 'status' => strtolower((string) $deal->deal_status) === 'approved' ? 'provided' : 'pending', 'auto_checked' => true],
            ['label' => 'Business Information Form', 'status' => $bifApproved ? 'provided' : 'pending', 'auto_checked' => true],
            ['label' => 'Client Information Form', 'status' => $contactApproved ? 'provided' : 'pending', 'auto_checked' => true],
            ['label' => 'Service Task Activation & Routing Tracker (Start)', 'status' => 'provided', 'auto_checked' => true],
            ['label' => 'Others', 'status' => 'pending', 'auto_checked' => false],
        ];
    }

    private function defaultStartEngagementRequirements(Deal $deal): array
    {
        $actions = collect($this->normalizeListValue($deal->required_actions))
            ->filter(fn ($item): bool => is_string($item) && trim($item) !== '')
            ->map(fn (string $item): array => [
                'requirement' => $item,
                'purpose' => 'Engagement-specific requirement',
                'assigned_to' => $deal->assigned_associate ?: $deal->assigned_consultant ?: 'Assigned team',
                'timeline' => 'To be scheduled',
            ])
            ->values()
            ->all();

        if ($actions !== []) {
            return $actions;
        }

        return [[
            'requirement' => 'Upload signed client confirmation file when available',
            'purpose' => 'Client confirmation',
            'assigned_to' => $deal->assigned_consultant ?: 'Sales & Marketing',
            'timeline' => 'Before NTP',
        ]];
    }

    private function defaultStartRouting(): array
    {
        return [
            ['role' => 'Admin', 'status' => 'pending'],
            ['role' => 'Lead Consultant', 'status' => 'pending'],
            ['role' => 'Lead Associate', 'status' => 'pending'],
            ['role' => 'Sales & Marketing', 'status' => 'pending'],
        ];
    }

    private function defaultScopeItems(Deal $deal): array
    {
        $services = collect($this->normalizeListValue($deal->services))
            ->merge($this->normalizeListValue($deal->products))
            ->filter(fn ($item): bool => is_string($item) && trim($item) !== '')
            ->values();

        if ($services->isEmpty() && filled($deal->scope_of_work)) {
            $services = collect(preg_split('/[\r\n,;]+/', (string) $deal->scope_of_work))
                ->filter(fn ($item): bool => is_string($item) && trim($item) !== '')
                ->values();
        }

        return $services
            ->take(5)
            ->map(fn ($item): array => [
                'main_task_description' => trim((string) $item),
                'sub_task_description' => 'To be defined during planning',
                'responsible' => $deal->assigned_consultant ?: 'Project Team',
                'duration' => $deal->estimated_duration ?: 'TBD',
                'start_date' => optional($deal->planned_start_date)->format('Y-m-d'),
                'end_date' => optional($deal->estimated_completion_date)->format('Y-m-d'),
                'status' => 'Pending',
                'remarks' => null,
            ])
            ->values()
            ->all();
    }

    private function defaultInternalApprovalPayload(Deal $deal): array
    {
        return [
            'prepared_by' => $deal->assigned_consultant,
            'reviewed_by' => 'Admin',
            'referred_by_closed_by' => null,
            'sales_marketing' => 'Sales & Marketing',
            'lead_consultant' => $deal->assigned_consultant,
            'lead_associate_assigned' => $deal->assigned_associate,
            'finance' => 'Finance',
            'president' => 'President',
            'record_custodian' => 'Record Custodian',
            'date_recorded' => now()->toDateString(),
            'date_signed' => null,
        ];
    }

    private function normalizeListValue(mixed $value): array
    {
        if (is_array($value)) {
            return collect($value)
                ->map(fn ($item): string => trim((string) $item))
                ->filter()
                ->values()
                ->all();
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        return collect(preg_split('/[,;\r\n]+/', $value))
            ->map(fn ($item): string => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }
}
