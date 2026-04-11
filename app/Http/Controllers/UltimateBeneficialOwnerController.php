<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\GisRecord;
use App\Models\UltimateBeneficialOwner;

class UltimateBeneficialOwnerController extends Controller
{
    private function canApproveCorporate(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user && $user->hasPermission('approve_corporate');
    }

    private function canEditRecord(GisRecord $gis): bool
    {
        if ($this->canApproveCorporate()) {
            return true;
        }

        return (int) $gis->submitted_by === (int) Auth::id()
            && in_array($gis->workflow_status, ['Uploaded', 'Reverted']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'gis_id' => 'required|exists:gis_records,id',
            'complete_name' => 'required|string|max:255',
            'specific_residential_address' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'tax_identification_no' => 'nullable|string|max:255',
            'ownership_voting_rights' => 'nullable|numeric|min:0|max:100',
            'beneficial_owner_type' => 'nullable|in:D,I',
            'beneficial_ownership_category' => 'nullable|in:A,B,C,D,E,F,G,H,I',
        ]);

        $gis = GisRecord::findOrFail($request->gis_id);

        if (!$this->canEditRecord($gis)) {
            abort(403, 'This record can no longer be edited.');
        }

        UltimateBeneficialOwner::create([
            'gis_id' => $request->gis_id,
            'complete_name' => $request->complete_name,
            'specific_residential_address' => $request->specific_residential_address,
            'nationality' => $request->nationality,
            'date_of_birth' => $request->date_of_birth,
            'tax_identification_no' => $request->tax_identification_no,
            'ownership_voting_rights' => $request->ownership_voting_rights,
            'beneficial_owner_type' => $request->beneficial_owner_type,
            'beneficial_ownership_category' => $request->beneficial_ownership_category,
        ]);

        return back()->with('success', 'UBO added successfully.');
    }
}
