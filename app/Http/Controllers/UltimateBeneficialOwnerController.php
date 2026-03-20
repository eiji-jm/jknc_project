<?php

namespace App\Http\Controllers;

use App\Models\UltimateBeneficialOwner;
use Illuminate\Http\Request;

class UltimateBeneficialOwnerController extends Controller
{
    public function index()
    {
        $owners = UltimateBeneficialOwner::latest()->get();

        return view('corporate.ubo-form', compact('owners'));
    }

    public function create()
    {
        return view('corporate.common.form', [
            'title' => 'Add Ultimate Beneficial Owner',
            'action' => route('corporate.ubo.store'),
            'method' => 'POST',
            'cancelRoute' => route('corporate.ubo'),
            'fields' => $this->fields(),
            'item' => new UltimateBeneficialOwner(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        UltimateBeneficialOwner::create($data);

        return redirect()->route('corporate.ubo')->with('success', 'UBO created.');
    }

    public function show(UltimateBeneficialOwner $ultimateBeneficialOwner)
    {
        return view('corporate.common.show', [
            'title' => 'UBO Details',
            'item' => $ultimateBeneficialOwner,
            'fields' => $this->fields(),
            'backRoute' => route('corporate.ubo'),
            'editRoute' => route('corporate.ubo.edit', $ultimateBeneficialOwner),
        ]);
    }

    public function edit(UltimateBeneficialOwner $ultimateBeneficialOwner)
    {
        return view('corporate.common.form', [
            'title' => 'Edit Ultimate Beneficial Owner',
            'action' => route('corporate.ubo.update', $ultimateBeneficialOwner),
            'method' => 'PUT',
            'cancelRoute' => route('corporate.ubo'),
            'fields' => $this->fields(),
            'item' => $ultimateBeneficialOwner,
        ]);
    }

    public function update(Request $request, UltimateBeneficialOwner $ultimateBeneficialOwner)
    {
        $data = $this->validateData($request);
        $ultimateBeneficialOwner->update($data);

        return redirect()->route('corporate.ubo')->with('success', 'UBO updated.');
    }

    public function destroy(UltimateBeneficialOwner $ultimateBeneficialOwner)
    {
        $ultimateBeneficialOwner->delete();

        return redirect()->route('corporate.ubo')->with('success', 'UBO deleted.');
    }

    private function fields(): array
    {
        return [
            ['name' => 'complete_name', 'label' => 'Complete Name', 'type' => 'text', 'required' => true],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ['name' => 'residential_address', 'label' => 'Specific Residential Address', 'type' => 'text'],
            ['name' => 'nationality', 'label' => 'Nationality', 'type' => 'text'],
            ['name' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date'],
            ['name' => 'tax_identification_no', 'label' => 'Tax Identification No.', 'type' => 'text'],
            ['name' => 'ownership_percentage', 'label' => 'Ownership %', 'type' => 'number', 'step' => '0.01'],
            ['name' => 'ownership_type', 'label' => 'Type', 'type' => 'text'],
            ['name' => 'ownership_category', 'label' => 'Category', 'type' => 'text'],
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'complete_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'residential_address' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'tax_identification_no' => ['nullable', 'string', 'max:255'],
            'ownership_percentage' => ['nullable', 'numeric'],
            'ownership_type' => ['nullable', 'string', 'max:255'],
            'ownership_category' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
