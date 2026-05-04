<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    private const KYC_STATUSES = [
        'Verified',
        'Pending Verification',
        'Not Submitted',
        'Rejected',
    ];

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $kycFilter = $request->string('kyc')->toString();
        $perPage = (int) $request->integer('per_page', 10);
        $owners = User::query()->select(['id', 'name'])->orderBy('name')->get();
        $defaultOwner = $request->user();

        if (! $defaultOwner && $owners->isNotEmpty()) {
            $defaultOwner = $owners->first();
        }

        if (! in_array($perPage, [5, 10, 25, 50], true)) {
            $perPage = 10;
        }

        $query = Contact::query();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        if (in_array($kycFilter, self::KYC_STATUSES, true)) {
            $query->where('kyc_status', $kycFilter);
        } else {
            $kycFilter = 'All';
        }

        $contacts = $query
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate($perPage)
            ->withQueryString();

        $statusCounts = [
            'Verified' => Contact::where('kyc_status', 'Verified')->count(),
            'Pending Verification' => Contact::where('kyc_status', 'Pending Verification')->count(),
            'Not Submitted' => Contact::where('kyc_status', 'Not Submitted')->count(),
            'Rejected' => Contact::where('kyc_status', 'Rejected')->count(),
        ];

        return view('contacts.index', [
            'contacts' => $contacts,
            'search' => $search,
            'kycFilter' => $kycFilter,
            'perPage' => $perPage,
            'statusCounts' => $statusCounts,
            'kycStatuses' => self::KYC_STATUSES,
            'owners' => $owners,
            'defaultOwnerId' => old('owner_id', $defaultOwner?->id),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'lead_source' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:2000'],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $selectedOwner = null;
        if (! empty($validated['owner_id'])) {
            $selectedOwner = User::query()->find($validated['owner_id']);
        }

        if (! $selectedOwner) {
            $selectedOwner = $request->user() ?: User::query()->first();
        }

        Contact::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'lead_source' => $validated['lead_source'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'description' => $validated['description'] ?? null,
            'kyc_status' => 'Not Submitted',
            'owner_name' => $selectedOwner?->name,
        ]);

        return redirect()
            ->route('contacts.index')
            ->with('success', 'Contact created successfully.');
    }
}
