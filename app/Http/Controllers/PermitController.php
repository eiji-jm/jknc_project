<?php

namespace App\Http\Controllers;

use App\Models\Permit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                    'date_of_registration' => $permit->date_of_registration?->format('Y-m-d'),
                    'approved_date_of_registration' => $permit->approved_date_of_registration?->format('Y-m-d'),
                    'expiration_date_of_registration' => $permit->expiration_date_of_registration?->format('Y-m-d'),
                    'user' => $permit->user,
                    'client' => $permit->client,
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
            'client' => 'required|string',
            'tin' => 'nullable|string',
            'date_of_registration' => 'nullable|date',
            'approved_date_of_registration' => 'nullable|date',
            'expiration_date_of_registration' => 'nullable|date',
        ]);

        $permit = Permit::create([
            'permit_type' => $request->permit_type,
            'date_of_registration' => $request->date_of_registration,
            'approved_date_of_registration' => $request->approved_date_of_registration,
            'expiration_date_of_registration' => $request->expiration_date_of_registration,
            'user' => Auth::user()->name,
            'client' => $request->client,
            'tin' => $request->tin,
        ]);

        return response()->json([
            'id' => $permit->id,
            'permit_type' => $permit->permit_type,
            'date_of_registration' => $permit->date_of_registration?->format('Y-m-d'),
            'approved_date_of_registration' => $permit->approved_date_of_registration?->format('Y-m-d'),
            'expiration_date_of_registration' => $permit->expiration_date_of_registration?->format('Y-m-d'),
            'user' => $permit->user,
            'client' => $permit->client,
            'tin' => $permit->tin,
            'status' => $permit->status,
        ]);
    }
}