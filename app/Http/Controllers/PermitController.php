<?php

namespace App\Http\Controllers;

use App\Models\Permit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermitController extends Controller
{
    // Get permits by type
    public function index($permitType)
    {
        $permits = Permit::where('permit_type', $permitType)
            ->orderBy('date', 'desc')
            ->get();
        return response()->json($permits);
    }

    // Store new permit
    public function store(Request $request)
    {
        $permit = Permit::create([
            'permit_type' => $request->permit_type,
            'date' => now()->toDateString(), // current date
            'user' => Auth::user()->name,    // logged-in user
            'client' => $request->client,
            'tin' => $request->tin,
            'registration_status' => $request->registration_status,
            'status' => $request->status,
        ]);

        return response()->json($permit);
    }
}