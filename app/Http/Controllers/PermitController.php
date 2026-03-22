<?php

namespace App\Http\Controllers;

use App\Models\Permit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PermitController extends Controller
{
    public function index($permitType)
    {
        $permits = Permit::where('permit_type', $permitType)
            ->orderBy('expiration_date_of_registration', 'asc')
            ->get()
            ->map(function ($permit) {
                return [
                    'id' => $permit->id,
                    'permit_type' => $permit->permit_type,
                    'permit_number' => $permit->permit_number,
                    'date_of_registration' => $permit->date_of_registration?->format('Y-m-d'),
                    'approved_date_of_registration' => $permit->approved_date_of_registration?->format('Y-m-d'),
                    'expiration_date_of_registration' => $permit->expiration_date_of_registration?->format('Y-m-d'),
                    'user' => $permit->user,
                    'tin' => $permit->tin,
                    'status' => $permit->status,
                ];
            });

        return response()->json($permits);
    }

    public function store(Request $request)
    {
        $request->validate([
            'permit_type' => 'required|string',
            'tin' => 'nullable|string',
            'date_of_registration' => 'nullable|date',
            'approved_date_of_registration' => 'nullable|date',
            'expiration_date_of_registration' => 'nullable|date',
        ]);

        do {
            $permitNumber = 'PMT-' . now()->format('Y') . '-' . random_int(100000, 999999);
        } while (Permit::where('permit_number', $permitNumber)->exists());

        $permit = Permit::create([
            'permit_type' => $request->permit_type,
            'permit_number' => $permitNumber,
            'date_of_registration' => $request->date_of_registration,
            'approved_date_of_registration' => $request->approved_date_of_registration,
            'expiration_date_of_registration' => $request->expiration_date_of_registration,
            'user' => Auth::check() ? Auth::user()->name : 'System',
            'tin' => $request->tin,
        ]);

        return response()->json([
            'id' => $permit->id,
            'permit_type' => $permit->permit_type,
            'permit_number' => $permit->permit_number,
            'date_of_registration' => $permit->date_of_registration?->format('Y-m-d'),
            'approved_date_of_registration' => $permit->approved_date_of_registration?->format('Y-m-d'),
            'expiration_date_of_registration' => $permit->expiration_date_of_registration?->format('Y-m-d'),
            'user' => $permit->user,
            'tin' => $permit->tin,
            'status' => $permit->status,
        ], 201);
    }

    public function showMayorPermitTemplate($id)
    {
        $permit = Permit::findOrFail($id);

        $pdf = Pdf::loadView('permits.templates.mayors-permit', compact('permit'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('mayors-permit-' . $permit->permit_number . '.pdf');
    }

    public function showBarangayBusinessPermitTemplate($id)
    {
        $permit = Permit::findOrFail($id);

        $pdf = Pdf::loadView('permits.templates.barangay-business-permit', compact('permit'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('barangay-business-permit-' . $permit->permit_number . '.pdf');
    }

    public function showFirePermitTemplate($id)
    {
        $permit = Permit::findOrFail($id);

        $pdf = Pdf::loadView('permits.templates.fire-permit', compact('permit'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('fire-permit-' . $permit->permit_number . '.pdf');
    }

    public function showSanitaryPermitTemplate($id)
    {
        $permit = Permit::findOrFail($id);

        $pdf = Pdf::loadView('permits.templates.sanitary-permit', compact('permit'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('sanitary-permit-' . $permit->permit_number . '.pdf');
    }

    public function showOboPermitTemplate($id)
    {
        $permit = Permit::findOrFail($id);

        $pdf = Pdf::loadView('permits.templates.obo-permit', compact('permit'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('obo-permit-' . $permit->permit_number . '.pdf');
    }
}