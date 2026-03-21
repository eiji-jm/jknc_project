<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permit;

class PermitController extends Controller
{
    public function index($type)
    {
        $data = Permit::where('permit_type', $type)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'permit_type' => 'required|string',
            'user' => 'required|string',
            'client' => 'required|string',
            'tin' => 'nullable|string',
            'date_of_registration' => 'nullable|date',
            'approved_date_of_registration' => 'nullable|date',
            'expiration_date_of_registration' => 'nullable|date',
            'registration_status' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $permit = Permit::create([
            'permit_type' => $request->permit_type,
            'user' => $request->user,
            'client' => $request->client,
            'tin' => $request->tin,
            'date_of_registration' => $request->date_of_registration,
            'approved_date_of_registration' => $request->approved_date_of_registration,
            'expiration_date_of_registration' => $request->expiration_date_of_registration,
            'registration_status' => $request->registration_status,
            'status' => $request->status,
        ]);

        return response()->json($permit, 201);
    }
}