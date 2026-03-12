<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $activeFilter = (string) $request->query('active', 'All');
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50], true) ? $perPage : 10;
        $customFields = collect($request->session()->get('products.custom_fields', []))
            ->values();

        $products = collect($request->session()->get('products.items', $this->defaultProducts()))
            ->map(function (array $product, int $index): array {
                if (blank($product['product_id'] ?? null)) {
                    $product['product_id'] = 'prd-legacy-'.($index + 1);
                }
                if (blank($product['product_owner_email'] ?? null) && ! blank($product['product_owner'] ?? null)) {
                    $product['product_owner_email'] = strtolower(str_replace(' ', '.', (string) $product['product_owner'])).'@example.com';
                }

                return $product;
            })
            ->map(fn (array $product): array => $this->applyCustomFieldDefaults($product, $customFields->all()))
            ->values();

        $request->session()->put('products.items', $products->all());

        if ($search !== '') {
            $products = $products->filter(function (array $product) use ($search): bool {
                $needle = mb_strtolower($search);
                $name = mb_strtolower((string) ($product['product_name'] ?? ''));
                $code = mb_strtolower((string) ($product['product_code'] ?? ''));
                $owner = mb_strtolower((string) ($product['product_owner'] ?? ''));

                return str_contains($name, $needle)
                    || str_contains($code, $needle)
                    || str_contains($owner, $needle);
            });
        }

        if (in_array($activeFilter, ['Active', 'Inactive'], true)) {
            $products = $products
                ->filter(fn (array $product): bool => ($product['product_active'] ?? 'Inactive') === $activeFilter)
                ->values();
        } else {
            $activeFilter = 'All';
        }

        $totalProducts = $products->count();
        $totalPages = max((int) ceil($totalProducts / $perPage), 1);
        $currentPage = min(max((int) $request->query('page', 1), 1), $totalPages);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedProducts = $products->slice($offset, $perPage)->values();
        $from = $totalProducts > 0 ? $offset + 1 : 0;
        $to = min($offset + $perPage, $totalProducts);
        $owners = $this->ownerOptions();
        $defaultOwnerId = (int) old('owner_id', ($owners[0]['id'] ?? 1001));

        return view('products.index', [
            'products' => $paginatedProducts,
            'search' => $search,
            'activeFilter' => $activeFilter,
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
            'categoryOptions' => [
                '-None-',
                'Hardware',
                'Software',
                'Service Package',
                'Subscription',
                'Compliance',
                'Other',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $ownerOptions = collect($this->ownerOptions())->keyBy('id');

        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:150'],
            'product_code' => ['required', 'string', 'max:100'],
            'product_category' => ['nullable', 'string', 'max:100'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'product_active' => ['nullable', 'boolean'],
            'owner_id' => ['required', 'integer'],
        ]);

        $owner = $ownerOptions->get((int) $validated['owner_id']);
        if (! $owner) {
            return back()->withErrors(['owner_id' => 'Please select a valid owner.'])->withInput();
        }

        $customFields = $request->session()->get('products.custom_fields', []);
        $items = collect($request->session()->get('products.items', $this->defaultProducts()));
        $items->prepend([
            'product_id' => 'prd-'.now()->format('YmdHisv').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'product_name' => $validated['product_name'],
            'product_code' => $validated['product_code'],
            'product_active' => ($validated['product_active'] ?? false) ? 'Active' : 'Inactive',
            'product_owner' => $owner['name'],
            'product_owner_email' => $owner['email'],
            'product_category' => $validated['product_category'] ?: '-None-',
            'unit_price' => isset($validated['unit_price']) ? (float) $validated['unit_price'] : null,
            'description' => $validated['description'] ?? null,
        ]);

        $request->session()->put(
            'products.items',
            $items
                ->map(fn (array $product): array => $this->applyCustomFieldDefaults($product, $customFields))
                ->values()
                ->all()
        );

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Request $request, string $id): View
    {
        $products = collect($request->session()->get('products.items', $this->defaultProducts()))
            ->map(function (array $product, int $index): array {
                if (blank($product['product_id'] ?? null)) {
                    $product['product_id'] = 'prd-legacy-'.($index + 1);
                }
                if (blank($product['product_owner_email'] ?? null) && ! blank($product['product_owner'] ?? null)) {
                    $product['product_owner_email'] = strtolower(str_replace(' ', '.', (string) $product['product_owner'])).'@example.com';
                }
                return $product;
            })
            ->values();

        $product = $products->firstWhere('product_id', $id);
        if (! $product) {
            abort(404);
        }

        $tab = strtolower((string) $request->query('tab', 'timeline'));
        $allowedTabs = ['timeline', 'pipelines', 'files', 'tasks'];
        if (! in_array($tab, $allowedTabs, true)) {
            $tab = 'timeline';
        }

        $timeline = $this->mockTimeline($product);
        $pipelines = $this->mockPipelines($product);
        $files = [];
        $tasks = [];

        return view('products.show', [
            'product' => $product,
            'tab' => $tab,
            'tabs' => [
                'timeline' => 'Timeline',
                'pipelines' => 'Pipelines',
                'files' => 'Files',
                'tasks' => 'Tasks',
            ],
            'timeline' => $timeline,
            'pipelines' => $pipelines,
            'files' => $files,
            'tasks' => $tasks,
            'lastModifiedLabel' => 'Last Modified on '.now()->format('M d, h:i A'),
        ]);
    }

    public function changeOwner(Request $request): RedirectResponse
    {
        $ownerOptions = collect($this->ownerOptions());

        $validated = $request->validate([
            'selected_products' => ['required', 'array', 'min:1'],
            'selected_products.*' => ['required', 'string'],
            'owner_id' => ['required', 'integer'],
        ]);

        $owner = $ownerOptions->firstWhere('id', (int) $validated['owner_id']);
        if (! $owner) {
            return redirect()
                ->route('products.index')
                ->withErrors(['owner_id' => 'Please select a valid owner.']);
        }

        $selected = collect($validated['selected_products'])
            ->filter()
            ->values()
            ->all();

        $items = collect($request->session()->get('products.items', $this->defaultProducts()))
            ->map(function (array $product) use ($selected, $owner): array {
                $productId = (string) ($product['product_id'] ?? '');
                if (in_array($productId, $selected, true)) {
                    $product['product_owner'] = $owner['name'];
                    $product['product_owner_email'] = $owner['email'];
                }

                return $product;
            })
            ->values()
            ->all();

        $request->session()->put('products.items', $items);

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
            'lookup_module' => ['nullable', 'string', 'in:deals,company,contacts'],
            'options' => ['nullable', 'array'],
            'options.*' => ['nullable', 'string', 'max:100'],
        ]);

        $fieldName = trim((string) $validated['field_name']);
        $fieldType = (string) $validated['field_type'];
        $customFields = collect($request->session()->get('products.custom_fields', []))->values();

        $nameExists = $customFields->contains(function (array $field) use ($fieldName): bool {
            return Str::lower((string) ($field['name'] ?? '')) === Str::lower($fieldName);
        });
        if ($nameExists) {
            return back()->withErrors(['field_name' => 'Field name already exists for Products.'])->withInput();
        }

        $keyBase = Str::slug($fieldName, '_');
        if ($keyBase === '') {
            $keyBase = 'custom_field';
        }
        $key = 'custom_'.$keyBase;
        $suffix = 1;
        $usedKeys = $customFields->pluck('key')->all();
        while (in_array($key, $usedKeys, true)) {
            $suffix++;
            $key = 'custom_'.$keyBase.'_'.$suffix;
        }

        $options = collect($validated['options'] ?? [])
            ->map(fn ($option) => trim((string) $option))
            ->filter()
            ->values()
            ->all();

        if ($fieldType === 'picklist' && count($options) === 0) {
            return back()->withErrors(['options' => 'Picklist fields need at least one option.'])->withInput();
        }

        if ($fieldType !== 'lookup') {
            $validated['lookup_module'] = null;
        }

        $defaultValue = trim((string) ($validated['default_value'] ?? ''));
        if ($fieldType === 'checkbox') {
            $defaultValue = in_array(Str::lower($defaultValue), ['1', 'yes', 'true', 'checked'], true) ? '1' : '0';
        }
        if ($fieldType === 'picklist' && $defaultValue !== '' && ! in_array($defaultValue, $options, true)) {
            return back()->withErrors(['default_value' => 'Default value must match one of the options.'])->withInput();
        }

        $field = [
            'id' => 'fld-'.now()->format('YmdHisv').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'type' => $fieldType,
            'name' => $fieldName,
            'key' => $key,
            'required' => (bool) ($validated['required'] ?? false),
            'options' => $fieldType === 'picklist' ? $options : [],
            'lookup_module' => $fieldType === 'lookup' ? (string) ($validated['lookup_module'] ?? '') : null,
            'default_value' => $this->normalizedDefaultValue($fieldType, $defaultValue),
        ];

        $customFields->push($field);
        $request->session()->put('products.custom_fields', $customFields->values()->all());

        $items = collect($request->session()->get('products.items', $this->defaultProducts()))
            ->map(fn (array $product): array => $this->applyCustomFieldDefaults($product, $customFields->all()))
            ->values()
            ->all();
        $request->session()->put('products.items', $items);

        return redirect()->route('products.index')->with('success', 'Custom field created successfully.');
    }

    private function defaultProducts(): array
    {
        return [
            [
                'product_id' => 'prd-1001',
                'product_name' => 'Name of the product',
                'product_code' => 'Product32',
                'product_active' => 'Active',
                'product_owner' => 'John Admin',
                'product_owner_email' => 'john.admin@example.com',
                'product_category' => 'Software',
                'unit_price' => 5000,
                'description' => null,
            ],
            [
                'product_id' => 'prd-1002',
                'product_name' => 'Name of the product2',
                'product_code' => 'Product32',
                'product_active' => 'Inactive',
                'product_owner' => 'John Admin',
                'product_owner_email' => 'john.admin@example.com',
                'product_category' => 'Hardware',
                'unit_price' => 2500,
                'description' => null,
            ],
        ];
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

    private function applyCustomFieldDefaults(array $product, array $customFields): array
    {
        foreach ($customFields as $field) {
            $key = (string) ($field['key'] ?? '');
            if ($key === '') {
                continue;
            }

            if (! array_key_exists($key, $product)) {
                $product[$key] = $field['default_value'] ?? $this->normalizedDefaultValue((string) ($field['type'] ?? 'text'), null);
            }
        }

        return $product;
    }

    private function normalizedDefaultValue(string $fieldType, ?string $defaultValue): string
    {
        $value = trim((string) ($defaultValue ?? ''));

        if ($fieldType === 'checkbox') {
            return in_array(Str::lower($value), ['1', 'yes', 'true', 'checked'], true) ? '1' : '0';
        }

        return $value;
    }

    private function mockTimeline(array $product): array
    {
        $owner = $product['product_owner'] ?? 'John Admin';
        $productName = $product['product_name'] ?? 'Product';
        $isActive = ($product['product_active'] ?? 'Inactive') === 'Active';

        return [
            [
                'icon' => 'fa-box-open',
                'title' => 'Product added',
                'description' => $productName,
                'user_name' => $owner,
                'created_at' => 'Mar 04, 2026 02:20 PM',
            ],
            [
                'icon' => 'fa-toggle-on',
                'title' => 'Product Active updated',
                'description' => $isActive ? "'false' to 'true'" : "'true' to 'false'",
                'user_name' => $owner,
                'created_at' => 'Mar 04, 2026 05:46 PM',
            ],
            [
                'icon' => 'fa-tag',
                'title' => 'Tag added',
                'description' => 'featured',
                'user_name' => $owner,
                'created_at' => 'Mar 04, 2026 05:54 PM',
            ],
            [
                'icon' => 'fa-pen',
                'title' => 'Product edited',
                'description' => 'Updated product details',
                'user_name' => $owner,
                'created_at' => 'Mar 05, 2026 10:08 AM',
            ],
            [
                'icon' => 'fa-user-pen',
                'title' => 'Owner changed',
                'description' => 'Assigned to '.$owner,
                'user_name' => $owner,
                'created_at' => 'Mar 06, 2026 09:10 AM',
            ],
        ];
    }

    private function mockPipelines(array $product): array
    {
        return [];
    }
}
