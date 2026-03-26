<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCustomField;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureSeededProducts();

        $search = trim((string) $request->query('search', ''));
        $statusFilter = (string) $request->query('active', 'All');
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50], true) ? $perPage : 10;

        $ownerMap = collect($this->ownerOptions())->keyBy('id');

        $customFields = ProductCustomField::query()
            ->orderBy('sort_order')
            ->orderBy('field_name')
            ->get();

        $productsQuery = Product::query()->orderByDesc('created_at');

        if ($search !== '') {
            $productsQuery->where(function ($query) use ($search) {
                $query
                    ->where('product_name', 'like', '%'.$search.'%')
                    ->orWhere('sku', 'like', '%'.$search.'%')
                    ->orWhere('product_type', 'like', '%'.$search.'%')
                    ->orWhere('category', 'like', '%'.$search.'%')
                    ->orWhere('created_by', 'like', '%'.$search.'%');
            });
        }

        if (in_array($statusFilter, ['Active', 'Inactive', 'Draft', 'Archived'], true)) {
            $productsQuery->where('status', $statusFilter);
        } else {
            $statusFilter = 'All';
        }

        $totalProducts = (clone $productsQuery)->count();
        $totalPages = max((int) ceil(max($totalProducts, 1) / $perPage), 1);
        $currentPage = min(max((int) $request->query('page', 1), 1), $totalPages);
        $products = $productsQuery
            ->forPage($currentPage, $perPage)
            ->get()
            ->map(function (Product $product) use ($ownerMap) {
                $product->owner_name = data_get($ownerMap->get($product->owner_id), 'name', $product->created_by);
                return $product;
            });

        $from = $totalProducts > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
        $to = min($from + $perPage - 1, $totalProducts);
        $owners = $this->ownerOptions();
        $defaultOwnerId = (int) old('owner_id', ($owners[0]['id'] ?? 1001));

        return view('products.index', [
            'products' => $products,
            'search' => $search,
            'activeFilter' => $statusFilter,
            'totalProducts' => $totalProducts,
            'perPage' => $perPage,
            'from' => $from,
            'to' => $to,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'owners' => $owners,
            'defaultOwnerId' => $defaultOwnerId,
            'customFields' => $customFields,
            'fieldTypes' => collect($this->fieldTypes()),
            'categoryOptions' => $this->categoryOptions(),
            'productTypeOptions' => $this->productTypeOptions(),
            'productAreaOptions' => $this->productAreaOptions(),
            'pricingTypeOptions' => $this->pricingTypeOptions(),
            'taxTypeOptions' => $this->taxTypeOptions(),
            'inventoryTypeOptions' => $this->inventoryTypeOptions(),
            'statusOptions' => $this->statusOptions(),
            'unitOptions' => $this->unitOptions(),
            'serviceOptions' => [],
            'drawerMeta' => [
                'createdBy' => $this->currentUserDisplay(),
                'createdAtDisplay' => now()->format('F j, Y g:i A'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $customFields = ProductCustomField::query()
            ->orderBy('sort_order')
            ->orderBy('field_name')
            ->get();

        $owner = collect($this->ownerOptions())->firstWhere('id', (int) $request->input('owner_id'));
        if (! $owner) {
            return back()->withErrors(['owner_id' => 'Please select a valid owner.'])->withInput();
        }

        $validated = $request->validate($this->validationRules($customFields));

        Validator::make([], [])->after(function ($validator) use ($validated) {
            if (in_array('Others', $validated['product_area'] ?? [], true) && blank($validated['product_area_other'] ?? null)) {
                $validator->errors()->add('product_area_other', 'Other Product Area is required when Others is selected.');
            }

            if (($validated['inventory_type'] ?? null) === 'Inventory' && blank($validated['stock_qty'] ?? null)) {
                $validator->errors()->add('stock_qty', 'Stock Quantity is required for inventory-managed products.');
            }
        })->validate();

        $product = Product::create([
            'product_id' => $this->generateProductId(),
            'product_name' => $validated['product_name'],
            'product_type' => $validated['product_type'],
            'linked_service_id' => $validated['linked_service_id'] ?? null,
            'deal_id' => $validated['deal_id'] ?? null,
            'product_area' => array_values($validated['product_area']),
            'product_area_other' => in_array('Others', $validated['product_area'], true) ? ($validated['product_area_other'] ?? null) : null,
            'product_description' => $validated['product_description'],
            'product_inclusions' => $this->normalizeBulletLines($validated['product_inclusions'] ?? null),
            'category' => $validated['category'],
            'pricing_type' => $validated['pricing_type'],
            'price' => $validated['price'],
            'cost' => $validated['cost'] ?? null,
            'is_discountable' => $request->boolean('is_discountable'),
            'tax_type' => $validated['tax_type'],
            'sku' => $validated['sku'] ?? null,
            'inventory_type' => $validated['inventory_type'],
            'stock_qty' => $validated['inventory_type'] === 'Inventory' ? ($validated['stock_qty'] ?? null) : null,
            'unit' => $validated['unit'] ?? null,
            'status' => $validated['status'],
            'owner_id' => (int) $validated['owner_id'],
            'created_by' => $this->currentUserDisplay(),
            'custom_field_values' => $this->extractCustomFieldValues($request, $customFields),
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Request $request, string $id): View
    {
        $ownerMap = collect($this->ownerOptions())->keyBy('id');
        $product = Product::query()->where('product_id', $id)->firstOrFail();
        $product->owner_name = data_get($ownerMap->get($product->owner_id), 'name', $product->created_by);

        $tab = strtolower((string) $request->query('tab', 'timeline'));
        $allowedTabs = ['timeline', 'pipelines', 'files', 'tasks'];
        if (! in_array($tab, $allowedTabs, true)) {
            $tab = 'timeline';
        }

        return view('products.show', [
            'product' => $product,
            'tab' => $tab,
            'tabs' => [
                'timeline' => 'Timeline',
                'pipelines' => 'Pipelines',
                'files' => 'Files',
                'tasks' => 'Tasks',
            ],
            'timeline' => $this->mockTimeline($product),
            'pipelines' => [],
            'files' => [],
            'tasks' => [],
            'lastModifiedLabel' => 'Last Modified on '.$product->updated_at?->format('M d, h:i A'),
            'customFields' => ProductCustomField::query()->orderBy('sort_order')->orderBy('field_name')->get(),
        ]);
    }

    public function changeOwner(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'selected_products' => ['required', 'array', 'min:1'],
            'selected_products.*' => ['required', 'string'],
            'owner_id' => ['required', 'integer'],
        ]);

        $owner = collect($this->ownerOptions())->firstWhere('id', (int) $validated['owner_id']);
        if (! $owner) {
            return redirect()
                ->route('products.index')
                ->withErrors(['owner_id' => 'Please select a valid owner.']);
        }

        Product::query()
            ->whereIn('product_id', $validated['selected_products'])
            ->update([
                'owner_id' => $owner['id'],
                'updated_at' => now(),
            ]);

        return redirect()->route('products.index')->with('success', 'Product owner updated successfully.');
    }

    public function storeCustomField(Request $request): RedirectResponse
    {
        $allowedTypes = collect($this->fieldTypes())->pluck('value')->all();

        $validated = $request->validate([
            'field_type' => ['required', 'string', 'in:'.implode(',', $allowedTypes)],
            'field_name' => ['required', 'string', 'max:80'],
            'default_value' => ['nullable', 'string', 'max:255'],
            'required' => ['nullable', 'boolean'],
            'lookup_module' => ['nullable', 'string', 'in:deals,company,contacts,products'],
            'options' => ['nullable', 'array'],
            'options.*' => ['nullable', 'string', 'max:100'],
        ]);

        $fieldName = trim((string) $validated['field_name']);
        $fieldType = (string) $validated['field_type'];

        if (ProductCustomField::query()->whereRaw('LOWER(field_name) = ?', [Str::lower($fieldName)])->exists()) {
            return back()->withErrors(['field_name' => 'Field name already exists for Products.'])->withInput();
        }

        $fieldKey = $this->uniqueCustomFieldKey($fieldName);
        $options = collect($validated['options'] ?? [])
            ->map(fn ($option) => trim((string) $option))
            ->filter()
            ->values()
            ->all();

        if ($fieldType === 'picklist' && count($options) === 0) {
            return back()->withErrors(['options' => 'Picklist fields need at least one option.'])->withInput();
        }

        ProductCustomField::create([
            'field_type' => $fieldType,
            'field_name' => $fieldName,
            'field_key' => $fieldKey,
            'is_required' => (bool) ($validated['required'] ?? false),
            'options' => $fieldType === 'picklist' ? $options : [],
            'lookup_module' => $fieldType === 'lookup' ? ($validated['lookup_module'] ?? null) : null,
            'default_value' => $this->normalizedDefaultValue($fieldType, $validated['default_value'] ?? null),
            'sort_order' => ((int) ProductCustomField::query()->max('sort_order')) + 1,
        ]);

        return redirect()->route('products.index')->with('success', 'Custom field created successfully.');
    }

    private function validationRules(Collection $customFields): array
    {
        $productTypeOptions = $this->productTypeOptions();
        $productAreaOptions = $this->productAreaOptions();
        $categoryOptions = $this->categoryOptions();
        $pricingTypeOptions = $this->pricingTypeOptions();
        $taxTypeOptions = $this->taxTypeOptions();
        $inventoryTypeOptions = $this->inventoryTypeOptions();
        $statusOptions = $this->statusOptions();
        $unitOptions = $this->unitOptions();

        $rules = [
            'product_name' => ['required', 'string', 'max:150'],
            'product_type' => ['required', 'string', Rule::in($productTypeOptions)],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')],
            'linked_service_id' => ['nullable', 'integer', 'exists:services,id'],
            'deal_id' => ['nullable', 'integer', 'exists:deals,id'],
            'product_area' => ['required', 'array', 'min:1'],
            'product_area.*' => ['required', 'string', Rule::in($productAreaOptions)],
            'product_area_other' => ['nullable', 'string', 'max:150'],
            'product_description' => ['required', 'string', 'max:5000'],
            'product_inclusions' => ['nullable', 'string', 'max:5000'],
            'category' => ['required', 'string', Rule::in($categoryOptions)],
            'pricing_type' => ['required', 'string', Rule::in($pricingTypeOptions)],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'is_discountable' => ['nullable', 'boolean'],
            'tax_type' => ['required', 'string', Rule::in($taxTypeOptions)],
            'inventory_type' => ['required', 'string', Rule::in($inventoryTypeOptions)],
            'stock_qty' => ['nullable', 'integer', 'min:0'],
            'unit' => ['nullable', 'string', Rule::in($unitOptions)],
            'status' => ['required', 'string', Rule::in($statusOptions)],
            'owner_id' => ['required', 'integer'],
        ];

        foreach ($customFields as $field) {
            $fieldKey = 'custom_fields.'.$field->field_key;
            $fieldRules = [$field->is_required ? 'required' : 'nullable'];

            switch ($field->field_type) {
                case 'numerical':
                case 'currency':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                default:
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:255';
                    break;
            }

            $rules[$fieldKey] = $fieldRules;
        }

        return $rules;
    }

    private function ownerOptions(): array
    {
        $users = User::query()
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get()
            ->map(fn (User $user): array => [
                'id' => (int) $user->id,
                'name' => $user->name,
                'email' => $user->email ?: strtolower(str_replace(' ', '.', $user->name)).'@example.com',
            ])
            ->values()
            ->all();

        if (! empty($users)) {
            return $users;
        }

        return [
            ['id' => 1001, 'name' => 'Shine Florence Padillo', 'email' => 'shinepadi@gmail.com'],
            ['id' => 1002, 'name' => 'John Admin', 'email' => 'john.admin@example.com'],
            ['id' => 1003, 'name' => 'Maria Santos', 'email' => 'maria.santos@example.com'],
            ['id' => 1004, 'name' => 'Juan Dela Cruz', 'email' => 'juan.delacruz@example.com'],
        ];
    }

    private function fieldTypes(): array
    {
        return [
            ['value' => 'picklist', 'label' => 'Picklist', 'icon' => 'fa-list'],
            ['value' => 'text', 'label' => 'Text', 'icon' => 'fa-font'],
            ['value' => 'numerical', 'label' => 'Numerical', 'icon' => 'fa-hashtag'],
            ['value' => 'currency', 'label' => 'Currency', 'icon' => 'fa-peso-sign'],
            ['value' => 'date', 'label' => 'Date', 'icon' => 'fa-calendar-days'],
            ['value' => 'checkbox', 'label' => 'Checkbox', 'icon' => 'fa-square-check'],
            ['value' => 'user', 'label' => 'User', 'icon' => 'fa-user'],
            ['value' => 'lookup', 'label' => 'Lookup', 'icon' => 'fa-link'],
        ];
    }

    private function normalizedDefaultValue(string $fieldType, mixed $defaultValue): string
    {
        $value = trim((string) ($defaultValue ?? ''));

        if ($fieldType === 'checkbox') {
            return in_array(Str::lower($value), ['1', 'yes', 'true', 'checked'], true) ? '1' : '0';
        }

        return $value;
    }

    private function extractCustomFieldValues(Request $request, Collection $customFields): array
    {
        $values = [];
        $submitted = (array) $request->input('custom_fields', []);

        foreach ($customFields as $field) {
            $value = $submitted[$field->field_key] ?? $field->default_value;
            if ($field->field_type === 'checkbox') {
                $value = in_array((string) $value, ['1', 'true', 'yes', 'on'], true) ? '1' : '0';
            }
            $values[$field->field_key] = $value;
        }

        return $values;
    }

    private function generateProductId(): string
    {
        do {
            $productId = (string) random_int(10000, 99999);
        } while (Product::query()->where('product_id', $productId)->exists());

        return $productId;
    }

    private function uniqueCustomFieldKey(string $fieldName): string
    {
        $keyBase = Str::slug($fieldName, '_');
        if ($keyBase === '') {
            $keyBase = 'custom_field';
        }

        $key = 'custom_'.$keyBase;
        $suffix = 1;

        while (ProductCustomField::query()->where('field_key', $key)->exists()) {
            $suffix++;
            $key = 'custom_'.$keyBase.'_'.$suffix;
        }

        return $key;
    }

    private function normalizeBulletLines(?string $value): ?string
    {
        $lines = collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->map(fn ($line) => Str::startsWith($line, '•') ? $line : '• '.$line)
            ->values();

        return $lines->isEmpty() ? null : $lines->implode(PHP_EOL);
    }

    private function productTypeOptions(): array
    {
        return ['Service', 'Bundle', 'Package', 'Physical Product', 'Digital Product'];
    }

    private function productAreaOptions(): array
    {
        return [
            'Corporate & Regulatory Advisory',
            'Governance & Policy Advisory',
            'People & Talent Solutions',
            'Strategic Situations Advisory',
            'Accounting & Compliance Advisory',
            'Business Strategy & Process Advisory',
            'Learning & Capability Development',
            'Others',
            'None',
        ];
    }

    private function categoryOptions(): array
    {
        return [
            'Professional Fees',
            'Consulting Revenue',
            'Accounting Services',
            'Tax Services',
            'Corporate Services',
            'HR Services',
            'Training & Development',
            'Other Income',
        ];
    }

    private function pricingTypeOptions(): array
    {
        return ['Fixed', 'Variable', 'Tiered', 'Subscription'];
    }

    private function taxTypeOptions(): array
    {
        return ['VAT', 'Non-VAT', 'Zero-rated', 'Exempt'];
    }

    private function inventoryTypeOptions(): array
    {
        return ['Non-Inventory', 'Inventory', 'Service'];
    }

    private function statusOptions(): array
    {
        return ['Draft', 'Active', 'Inactive', 'Archived'];
    }

    private function unitOptions(): array
    {
        return ['Unit', 'Package', 'Set', 'Hour', 'Project'];
    }

    private function currentUserDisplay(): string
    {
        return User::query()->orderBy('id')->value('name') ?: 'Admin User';
    }

    private function ensureSeededProducts(): void
    {
        if (! Schema::hasTable('products') || Product::query()->exists()) {
            return;
        }

        $owner = collect($this->ownerOptions())->first();

        Product::query()->create([
            'product_id' => $this->generateProductId(),
            'product_name' => 'Business Registration Package',
            'product_type' => 'Service',
            'linked_service_id' => null,
            'product_area' => ['None'],
            'product_area_other' => null,
            'product_description' => 'End-to-end business registration service for new clients.',
            'product_inclusions' => "• SEC/DTI filing assistance\n• Basic compliance checklist",
            'category' => 'Corporate Services',
            'pricing_type' => 'Fixed',
            'price' => 5000,
            'cost' => 2500,
            'is_discountable' => true,
            'tax_type' => 'VAT',
            'sku' => 'PRD-1001',
            'inventory_type' => 'Service',
            'stock_qty' => null,
            'unit' => 'Project',
            'status' => 'Active',
            'owner_id' => $owner['id'] ?? null,
            'created_by' => $owner['name'] ?? 'John Admin',
            'custom_field_values' => [],
        ]);

        Product::query()->create([
            'product_id' => $this->generateProductId(),
            'product_name' => 'Accounting Cleanup Package',
            'product_type' => 'Service',
            'linked_service_id' => null,
            'product_area' => ['Accounting & Compliance Advisory'],
            'product_area_other' => null,
            'product_description' => 'Accounting records cleanup and reconciliation support.',
            'product_inclusions' => "• Ledger review\n• Reconciliation summary",
            'category' => 'Accounting Services',
            'pricing_type' => 'Fixed',
            'price' => 2500,
            'cost' => 1200,
            'is_discountable' => false,
            'tax_type' => 'VAT',
            'sku' => 'PRD-1002',
            'inventory_type' => 'Service',
            'stock_qty' => null,
            'unit' => 'Project',
            'status' => 'Inactive',
            'owner_id' => $owner['id'] ?? null,
            'created_by' => $owner['name'] ?? 'John Admin',
            'custom_field_values' => [],
        ]);
    }

    private function mockTimeline(Product $product): array
    {
        $owner = $product->created_by ?: 'John Admin';

        return [
            [
                'icon' => 'fa-box-open',
                'title' => 'Product added',
                'description' => $product->product_name,
                'user_name' => $owner,
                'created_at' => optional($product->created_at)->format('M d, Y h:i A'),
            ],
            [
                'icon' => 'fa-pen',
                'title' => 'Product updated',
                'description' => 'Latest product configuration saved.',
                'user_name' => $owner,
                'created_at' => optional($product->updated_at)->format('M d, Y h:i A'),
            ],
        ];
    }
}
