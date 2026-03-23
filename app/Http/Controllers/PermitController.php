<?php

namespace App\Http\Controllers;

use App\Models\Permit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PermitController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'All Documents');

        $query = Permit::query();

        if ($filter && $filter !== 'All Documents') {
            $query->where('permit_type', $filter);
        }

        $permits = $query
            ->latest('created_at')
            ->get()
            ->map(function ($permit) {
                return [
                    'id' => $permit->id,
                    'permit_type' => $permit->permit_type,
                    'document_type' => $permit->document_type,
                    'permit_number' => $permit->permit_number,
                    'date_of_registration' => $permit->date_of_registration?->format('Y-m-d'),
                    'approved_date_of_registration' => $permit->approved_date_of_registration?->format('Y-m-d'),
                    'expiration_date_of_registration' => $permit->expiration_date_of_registration?->format('Y-m-d'),
                    'user' => $permit->user,
                    'tin' => $permit->tin,
                    'document_name' => $permit->document_name,
                    'status' => $permit->status,
                    'document_path' => $permit->document_path,
                ];
            });

        return response()->json($permits);
    }

    public function store(Request $request)
    {
        $request->validate([
            'permit_type' => 'required|string',
            'document_type' => 'required|string',
            'tin' => 'nullable|string',
            'date_of_registration' => 'nullable|date',
            'approved_date_of_registration' => 'nullable|date',
            'expiration_date_of_registration' => 'nullable|date',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $documentPath = null;
        $documentName = null;

        if ($request->hasFile('document')) {
            $file = $request->file('document');

            $originalName = $file->getClientOriginalName();
            $safeOriginalName = preg_replace('/[^A-Za-z0-9\-\_\.]/', '_', $originalName);
            $filename = time() . '_' . $safeOriginalName;

            $destinationPath = public_path('documents');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            $file->move($destinationPath, $filename);

            $documentName = $originalName;
            $documentPath = 'documents/' . $filename;
        }

        do {
            $permitNumber = 'PMT-' . now()->format('Y') . '-' . random_int(100000, 999999);
        } while (Permit::where('permit_number', $permitNumber)->exists());

        $permit = Permit::create([
            'permit_type' => $request->permit_type,
            'document_type' => $request->document_type,
            'permit_number' => $permitNumber,
            'date_of_registration' => $request->date_of_registration ?: null,
            'approved_date_of_registration' => $request->approved_date_of_registration ?: null,
            'expiration_date_of_registration' => $request->expiration_date_of_registration ?: null,
            'user' => Auth::check() ? Auth::user()->name : 'System',
            'tin' => $request->tin,
            'document_name' => $documentName,
            'document_path' => $documentPath,
        ]);

        return response()->json([
            'message' => 'Permit saved successfully.',
            'permit' => [
                'id' => $permit->id,
                'permit_type' => $permit->permit_type,
                'document_type' => $permit->document_type,
                'permit_number' => $permit->permit_number,
                'date_of_registration' => $permit->date_of_registration?->format('Y-m-d'),
                'approved_date_of_registration' => $permit->approved_date_of_registration?->format('Y-m-d'),
                'expiration_date_of_registration' => $permit->expiration_date_of_registration?->format('Y-m-d'),
                'user' => $permit->user,
                'tin' => $permit->tin,
                'document_name' => $permit->document_name,
                'document_path' => $permit->document_path,
                'status' => $permit->status,
            ]
        ], 201);
    }
}