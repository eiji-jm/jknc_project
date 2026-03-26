<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Service;
use App\Models\ServiceCustomField;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class CompanyServiceController extends Controller
{
    private const SERVICE_AREA_OPTIONS = [
        'Corporate & Regulatory Advisory',
        'Governance & Policy Advisory',
        'People & Talent Solutions',
        'Strategic Situations Advisory',
        'Accounting & Compliance Advisory',
        'Business Strategy & Process Advisory',
        'Learning & Capability Development',
        'Others',
    ];

    private const CATEGORY_OPTIONS = [
        'Professional Fees',
        'Consulting Revenue',
        'Accountancy Revenue',
        'Compliance Revenue',
        'Advisory Revenue',
    ];

    private const FREQUENCY_OPTIONS = [
        'One-time',
        'Daily',
        'Weekly',
        'Monthly',
        'Quarterly',
        'Annually',
        'Per Transaction',
        'Custom',
    ];

    private const REMINDER_OPTIONS = [
        'Same day',
        '1 day before',
        '3 days before',
        '7 days before',
    ];

    private const REQUIREMENT_CATEGORIES = [
        'SOLE / NATURAL PERSON / INDIVIDUAL',
        'JURIDICAL ENTITY (Corporation / OPC / Partnership / Cooperative)',
        'Other',
    ];

    private const ENGAGEMENT_OPTIONS = [
        'Project Engagement',
        'Regular (Retainer)',
        'Hybrid',
    ];

    private const UNIT_OPTIONS = [
        'Hour',
        'Day',
        'Week',
        'Month',
        'Project',
        'Fixed',
    ];

    private const ASSIGNED_UNIT_OPTIONS = [
        'Operations',
        'Accounting',
        'Tax',
        'Corporate',
        'Legal',
        'HR',
        'Admin',
        'Sales',
    ];

    private const STATUS_OPTIONS = [
        'Draft',
        'Active',
        'Inactive',
        'Archived',
    ];

    public function globalIndex(Request $request): View
    {
        $filters = $this->serviceFilters($request);
        $query = $this->serviceQuery($filters);
        $summary = $this->serviceSummary((clone $query)->get());
        $services = $query->paginate($filters['per_page'])->withQueryString();

        return view('services.index', $this->viewData($request, $services, null, $filters, $summary));
    }

    public function companyIndex(Request $request, int $company): View
    {
        $companyModel = Company::query()->findOrFail($company);
        $filters = $this->serviceFilters($request);
        $query = $this->serviceQuery($filters, $company);
        $summary = $this->serviceSummary((clone $query)->get());
        $services = $query->paginate($filters['per_page'])->withQueryString();

        return view('company.services', $this->viewData($request, $services, $companyModel, $filters, $summary));
    }

    public function storeGlobal(Request $request): RedirectResponse
    {
        $service = $this->persistService($request);

        return redirect()
            ->route('services.index')
            ->with('services_success', "Service {$service->service_name} created successfully.");
    }

    public function storeForCompany(Request $request, int $company): RedirectResponse
    {
        $companyModel = Company::query()->findOrFail($company);
        $service = $this->persistService($request, null, $companyModel->id);

        return redirect()
            ->route('company.services.index', $companyModel->id)
            ->with('services_success', "Service {$service->service_name} created for {$companyModel->company_name}.");
    }

    public function showGlobal(int $service): View
    {
        $serviceModel = $this->serviceWithRelations()->findOrFail($service);

        return view('services.show', [
            'service' => $serviceModel,
            'company' => $serviceModel->company,
            'customFields' => ServiceCustomField::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function showForCompany(int $company, int $service): View
    {
        $companyModel = Company::query()->findOrFail($company);
        $serviceModel = $this->serviceWithRelations()
            ->where('company_id', $companyModel->id)
            ->findOrFail($service);

        return view('company.service-show', [
            'company' => $companyModel,
            'service' => $serviceModel,
            'customFields' => ServiceCustomField::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function updateGlobal(Request $request, int $service): RedirectResponse
    {
        $serviceModel = Service::query()->findOrFail($service);
        $serviceModel = $this->persistService($request, $serviceModel);

        return redirect()
            ->route('services.index')
            ->with('services_success', "Service {$serviceModel->service_name} updated successfully.");
    }

    public function updateForCompany(Request $request, int $company, int $service): RedirectResponse
    {
        $companyModel = Company::query()->findOrFail($company);
        $serviceModel = Service::query()
            ->where('company_id', $companyModel->id)
            ->findOrFail($service);

        $serviceModel = $this->persistService($request, $serviceModel, $companyModel->id);

        return redirect()
            ->route('company.services.index', $companyModel->id)
            ->with('services_success', "Service {$serviceModel->service_name} updated successfully.");
    }

    public function destroyGlobal(int $service): RedirectResponse
    {
        $serviceModel = Service::query()->findOrFail($service);
        $serviceModel->delete();

        return redirect()
            ->route('services.index')
            ->with('services_success', 'Service removed successfully.');
    }

    public function destroyForCompany(int $company, int $service): RedirectResponse
    {
        $serviceModel = Service::query()
            ->where('company_id', $company)
            ->findOrFail($service);

        $serviceModel->delete();

        return redirect()
            ->route('company.services.index', $company)
            ->with('services_success', 'Service removed from this company successfully.');
    }

    public function storeCustomField(Request $request): RedirectResponse
    {
        $allowedTypes = collect($this->fieldTypes())->pluck('value')->all();

        $validated = $request->validate([
            'field_type' => ['required', 'string', Rule::in($allowedTypes)],
            'field_name' => ['required', 'string', 'max:80'],
            'default_value' => ['nullable', 'string', 'max:255'],
            'required' => ['nullable', 'boolean'],
            'lookup_module' => ['nullable', 'string', Rule::in(['deals', 'company', 'contacts', 'products'])],
            'options' => ['nullable', 'array'],
            'options.*' => ['nullable', 'string', 'max:100'],
        ]);

        $fieldName = trim((string) $validated['field_name']);
        $fieldType = (string) $validated['field_type'];
        $nameExists = ServiceCustomField::query()
            ->whereRaw('LOWER(field_name) = ?', [Str::lower($fieldName)])
            ->exists();

        if ($nameExists) {
            return back()->withErrors(['field_name' => 'Field name already exists for Services.'])->withInput();
        }

        $options = collect($validated['options'] ?? [])
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->values()
            ->all();

        if ($fieldType === 'picklist' && count($options) === 0) {
            return back()->withErrors(['options' => 'Picklist fields need at least one option.'])->withInput();
        }

        $keyBase = Str::slug($fieldName, '_');
        if ($keyBase === '') {
            $keyBase = 'custom_field';
        }

        $fieldKey = 'custom_'.$keyBase;
        $suffix = 1;
        while (ServiceCustomField::query()->where('field_key', $fieldKey)->exists()) {
            $suffix++;
            $fieldKey = 'custom_'.$keyBase.'_'.$suffix;
        }

        ServiceCustomField::query()->create([
            'field_name' => $fieldName,
            'field_key' => $fieldKey,
            'field_type' => $fieldType,
            'is_required' => (bool) ($validated['required'] ?? false),
            'options' => $fieldType === 'picklist' ? $options : [],
            'default_value' => $this->normalizedDefaultValue($fieldType, trim((string) ($validated['default_value'] ?? ''))),
            'lookup_module' => $fieldType === 'lookup' ? ($validated['lookup_module'] ?? null) : null,
            'sort_order' => ((int) ServiceCustomField::query()->max('sort_order')) + 1,
        ]);

        return redirect()->back()->with('services_success', 'Service custom field created successfully.');
    }

    private function viewData(Request $request, $services, ?Company $company, array $filters, array $summary): array
    {
        $customFields = ServiceCustomField::query()->orderBy('sort_order')->orderBy('id')->get();
        $users = User::query()->orderBy('name')->get(['id', 'name']);
        $companies = Company::query()->orderBy('company_name')->get(['id', 'company_name', 'owner_name']);

        return [
            'services' => $services,
            'company' => $company,
            'companies' => $companies,
            'customFields' => $customFields,
            'fieldTypes' => collect($this->fieldTypes()),
            'lookupModules' => $this->lookupModules(),
            'staffOptions' => $users->pluck('name')->values(),
            'users' => $users,
            'categories' => collect(self::CATEGORY_OPTIONS),
            'statusOptions' => collect(self::STATUS_OPTIONS),
            'frequencyOptions' => collect(self::FREQUENCY_OPTIONS),
            'serviceAreaOptions' => collect(self::SERVICE_AREA_OPTIONS),
            'reminderLeadTimes' => collect(self::REMINDER_OPTIONS),
            'requirementCategories' => collect(self::REQUIREMENT_CATEGORIES),
            'engagementOptions' => collect(self::ENGAGEMENT_OPTIONS),
            'unitOptions' => collect(self::UNIT_OPTIONS),
            'assignedUnitOptions' => collect(self::ASSIGNED_UNIT_OPTIONS),
            'filters' => $filters,
            'summary' => $summary,
        ];
    }

    private function serviceQuery(array $filters, ?int $companyId = null)
    {
        $query = $this->serviceWithRelations();

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        if ($filters['search'] !== '') {
            $term = '%'.$filters['search'].'%';
            $query->where(function ($builder) use ($term) {
                $builder
                    ->where('service_name', 'like', $term)
                    ->orWhere('service_id', 'like', $term)
                    ->orWhere('category', 'like', $term)
                    ->orWhere('frequency', 'like', $term)
                    ->orWhere('assigned_unit', 'like', $term)
                    ->orWhere('status', 'like', $term)
                    ->orWhereHas('company', fn ($companyQuery) => $companyQuery->where('company_name', 'like', $term))
                    ->orWhereHas('creator', fn ($userQuery) => $userQuery->where('name', 'like', $term));
            });
        }

        if ($filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if ($filters['category'] !== 'all') {
            $query->where('category', $filters['category']);
        }

        if ($filters['frequency'] !== 'all') {
            $query->where('frequency', $filters['frequency']);
        }

        if ($filters['assigned_unit'] !== 'all') {
            $query->where('assigned_unit', $filters['assigned_unit']);
        }

        if ($filters['engagement_type'] !== 'all') {
            $query->whereJsonContains('engagement_structure', $filters['engagement_type']);
        }

        if ($filters['service_area'] !== 'all') {
            $query->whereJsonContains('service_area', $filters['service_area']);
        }

        if ($filters['tab'] === 'active') {
            $query->where('status', 'Active');
        } elseif ($filters['tab'] === 'recurring') {
            $query->where('is_recurring', true);
        } elseif ($filters['tab'] === 'due_soon') {
            $query->whereNotNull('deadline')
                ->whereBetween('deadline', [now(), now()->addDays(7)]);
        }

        return $query->latest('updated_at');
    }

    private function serviceWithRelations()
    {
        return Service::query()->with(['company', 'creator', 'reviewer', 'approver']);
    }

    private function serviceFilters(Request $request): array
    {
        return [
            'search' => trim((string) $request->query('search', '')),
            'status' => trim((string) $request->query('status', 'all')),
            'category' => trim((string) $request->query('category', 'all')),
            'frequency' => trim((string) $request->query('frequency', 'all')),
            'engagement_type' => trim((string) $request->query('engagement_type', 'all')),
            'assigned_unit' => trim((string) $request->query('assigned_unit', 'all')),
            'service_area' => trim((string) $request->query('service_area', 'all')),
            'tab' => trim((string) $request->query('tab', 'all')),
            'per_page' => in_array((int) $request->query('per_page', 10), [5, 10, 25, 50], true)
                ? (int) $request->query('per_page', 10)
                : 10,
        ];
    }

    private function serviceSummary(Collection $services): array
    {
        return [
            'active' => $services->where('status', 'Active')->count(),
            'recurring' => $services->where('is_recurring', true)->count(),
            'due_soon' => $services->filter(function (Service $service): bool {
                return $service->deadline !== null
                    && $service->deadline->isFuture()
                    && $service->deadline->lte(now()->addDays(7));
            })->count(),
        ];
    }

    private function persistService(Request $request, ?Service $service = null, ?int $lockedCompanyId = null): Service
    {
        $customFields = ServiceCustomField::query()->orderBy('sort_order')->orderBy('id')->get();
        $validated = $this->validateService($request, $customFields, $lockedCompanyId !== null);
        $companyId = $lockedCompanyId ?? (int) $validated['company_id'];
        $engagementStructure = collect($validated['engagement_structure'] ?? [])->values()->all();
        $serviceArea = collect($validated['service_area'] ?? [])->values()->all();
        $requirements = $this->normalizeRequirements(
            $validated['requirement_category'] ?? null,
            $validated['requirements'] ?? ''
        );
        $customFieldValues = $this->normalizeCustomFieldValues($request, $customFields);
        $isRecurring = $this->isRecurring($engagementStructure);
        $userId = $request->user()?->id;

        if ($service === null) {
            $service = new Service();
            $service->service_id = $this->generateServiceId();
            $service->created_by = $userId;
        }

        $service->fill([
            'company_id' => $companyId,
            'service_name' => $validated['service_name'],
            'service_description' => $validated['service_description'],
            'service_activity_output' => $validated['service_activity_output'],
            'service_area' => $serviceArea,
            'service_area_other' => $validated['service_area_other'] ?? null,
            'category' => $validated['category'] ?? null,
            'frequency' => $validated['frequency'] ?? null,
            'schedule_rule' => $validated['schedule_rule'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
            'reminder_lead_time' => $validated['reminder_lead_time'] ?? null,
            'requirements' => $requirements,
            'requirement_category' => $validated['requirement_category'] ?? null,
            'engagement_structure' => $engagementStructure,
            'is_recurring' => $isRecurring,
            'unit' => $validated['unit'],
            'rate_per_unit' => $this->nullableDecimal($validated['rate_per_unit'] ?? null),
            'min_units' => $validated['min_units'] ?? null,
            'max_cap' => $this->nullableDecimal($validated['max_cap'] ?? null),
            'price_fee' => $this->nullableDecimal($validated['price_fee'] ?? null),
            'cost_of_service' => $this->nullableDecimal($validated['cost_of_service'] ?? null),
            'assigned_unit' => $validated['assigned_unit'] ?? null,
            'status' => $validated['status'],
            'reviewed_by' => $validated['reviewed_by'] ?? null,
            'reviewed_at' => $validated['reviewed_at'] ?? null,
            'approved_by' => $validated['approved_by'] ?? null,
            'approved_at' => $validated['approved_at'] ?? null,
            'custom_field_values' => $customFieldValues,
        ]);

        $service->save();

        return $service->fresh(['company', 'creator', 'reviewer', 'approver']);
    }

    private function validateService(Request $request, Collection $customFields, bool $companyLocked): array
    {
        $rules = [
            'service_name' => ['required', 'string', 'max:255'],
            'service_description' => ['required', 'string'],
            'service_activity_output' => ['required', 'string'],
            'service_area' => ['required', 'array', 'min:1'],
            'service_area.*' => ['string', Rule::in(self::SERVICE_AREA_OPTIONS)],
            'service_area_other' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', Rule::in(self::CATEGORY_OPTIONS)],
            'frequency' => ['nullable', 'string', Rule::in(self::FREQUENCY_OPTIONS)],
            'schedule_rule' => ['nullable', 'string', 'max:255'],
            'deadline' => ['nullable', 'date'],
            'reminder_lead_time' => ['nullable', 'string', Rule::in(self::REMINDER_OPTIONS)],
            'requirement_category' => ['nullable', 'string', Rule::in(self::REQUIREMENT_CATEGORIES)],
            'requirements' => ['nullable', 'string'],
            'engagement_structure' => ['required', 'array', 'min:1'],
            'engagement_structure.*' => ['string', Rule::in(self::ENGAGEMENT_OPTIONS)],
            'unit' => ['required', 'string', Rule::in(self::UNIT_OPTIONS)],
            'rate_per_unit' => ['nullable', 'numeric', 'min:0'],
            'min_units' => ['nullable', 'integer', 'min:1'],
            'max_cap' => ['nullable', 'numeric', 'min:0'],
            'price_fee' => ['nullable', 'numeric', 'min:0'],
            'cost_of_service' => ['nullable', 'numeric', 'min:0'],
            'assigned_unit' => ['nullable', 'string', Rule::in(self::ASSIGNED_UNIT_OPTIONS)],
            'status' => ['required', 'string', Rule::in(self::STATUS_OPTIONS)],
            'reviewed_by' => ['nullable', 'integer', 'exists:users,id'],
            'reviewed_at' => ['nullable', 'date'],
            'approved_by' => ['nullable', 'integer', 'exists:users,id'],
            'approved_at' => ['nullable', 'date'],
        ];

        if (! $companyLocked) {
            $companyRule = Schema::hasTable('companies')
                ? ['required', 'integer', 'exists:companies,id']
                : ['required', 'integer'];
            $rules['company_id'] = $companyRule;
        }

        foreach ($customFields as $field) {
            $key = 'custom_fields.'.$field->field_key;
            $fieldRules = [$field->is_required ? 'required' : 'nullable'];

            switch ($field->field_type) {
                case 'number':
                case 'currency':
                    $fieldRules[] = 'numeric';
                    break;
                case 'checkbox':
                    $fieldRules[] = 'nullable';
                    break;
                default:
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:255';
                    break;
            }

            if ($field->field_type === 'picklist' && filled($field->options)) {
                $fieldRules[] = Rule::in($field->options);
            }

            $rules[$key] = $fieldRules;
        }

        $validated = $request->validate($rules);

        validator($validated, [])->after(function (Validator $validator) use ($validated) {
            $serviceArea = collect($validated['service_area'] ?? [])->filter()->values();
            $engagementStructure = collect($validated['engagement_structure'] ?? [])->filter()->values();
            $isRecurring = $this->isRecurring($engagementStructure->all());
            $frequency = $validated['frequency'] ?? null;
            $requirements = trim((string) ($validated['requirements'] ?? ''));

            if ($serviceArea->contains('Others') && blank($validated['service_area_other'] ?? null)) {
                $validator->errors()->add('service_area_other', 'Other Service Area is required when Others is selected.');
            }

            if ($isRecurring && blank($validated['schedule_rule'] ?? null)) {
                $validator->errors()->add('schedule_rule', 'Schedule Rule is required for recurring services.');
            }

            if ($frequency === 'One-time' && blank($validated['deadline'] ?? null)) {
                $validator->errors()->add('deadline', 'Deadline is required for one-time services.');
            }

            if (blank($validated['rate_per_unit'] ?? null) && blank($validated['price_fee'] ?? null)) {
                $validator->errors()->add('rate_per_unit', 'Either Rate per Unit or Price / Fee is required.');
            }

            if ($requirements !== '' && blank($validated['requirement_category'] ?? null)) {
                $validator->errors()->add('requirement_category', 'Requirement Category is required when requirements are provided.');
            }
        })->validate();

        return $validated;
    }

    private function normalizeRequirements(?string $category, string $requirementsText): ?array
    {
        $lines = collect(preg_split('/\r\n|\r|\n/', $requirementsText) ?: [])
            ->map(fn ($line) => trim(Str::replaceFirst('•', '', $line)))
            ->filter()
            ->values()
            ->all();

        if ($category === null && count($lines) === 0) {
            return null;
        }

        return [
            'category' => $category,
            'items' => $lines,
        ];
    }

    private function normalizeCustomFieldValues(Request $request, Collection $customFields): array
    {
        $values = [];

        foreach ($customFields as $field) {
            $value = data_get($request->input('custom_fields', []), $field->field_key);

            if ($field->field_type === 'checkbox') {
                $values[$field->field_key] = $request->boolean('custom_fields.'.$field->field_key) ? '1' : '0';
                continue;
            }

            if (is_string($value)) {
                $value = trim($value);
            }

            if ($value === null || $value === '') {
                $default = $field->default_value;
                $values[$field->field_key] = $default === null ? '' : $default;
                continue;
            }

            $values[$field->field_key] = $value;
        }

        return $values;
    }

    private function nullableDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }

    private function isRecurring(array $engagementStructure): bool
    {
        return collect($engagementStructure)->contains(fn ($value) => in_array($value, ['Regular (Retainer)', 'Hybrid'], true));
    }

    private function generateServiceId(): string
    {
        do {
            $serviceId = (string) random_int(10000, 99999);
        } while (Service::query()->where('service_id', $serviceId)->exists());

        return $serviceId;
    }

    private function fieldTypes(): array
    {
        return [
            ['value' => 'text', 'label' => 'Single Line Text', 'icon' => 'fa-input-text'],
            ['value' => 'textarea', 'label' => 'Multi Line Text', 'icon' => 'fa-align-left'],
            ['value' => 'number', 'label' => 'Number', 'icon' => 'fa-hashtag'],
            ['value' => 'currency', 'label' => 'Currency', 'icon' => 'fa-money-bill-wave'],
            ['value' => 'picklist', 'label' => 'Picklist', 'icon' => 'fa-caret-square-down'],
            ['value' => 'checkbox', 'label' => 'Checkbox', 'icon' => 'fa-square-check'],
            ['value' => 'date', 'label' => 'Date', 'icon' => 'fa-calendar'],
            ['value' => 'lookup', 'label' => 'Lookup', 'icon' => 'fa-link'],
        ];
    }

    private function lookupModules(): array
    {
        return [
            ['value' => 'deals', 'label' => 'Deals'],
            ['value' => 'company', 'label' => 'Company'],
            ['value' => 'contacts', 'label' => 'Contacts'],
            ['value' => 'products', 'label' => 'Products'],
        ];
    }

    private function normalizedDefaultValue(string $fieldType, string $defaultValue): string
    {
        if ($fieldType === 'checkbox') {
            return in_array(Str::lower($defaultValue), ['1', 'yes', 'true', 'checked'], true) ? '1' : '0';
        }

        return $defaultValue;
    }
}
