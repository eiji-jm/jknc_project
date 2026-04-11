<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DirectorOfficer;

class DirectorOfficerController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'gis_id'        => 'required|exists:gis_records,id',
            'officer_name'  => 'required|string|max:255',
            'address'       => 'required|string|max:255',
            'gender'        => 'required|in:M,F',
            'nationality'   => 'required|string|max:255',
            'incr'          => 'required|in:Y,N',
            'stockholder'   => 'required|in:Y,N',
            'board'         => 'required|in:C,M',
            'officer_type'  => 'required|string|max:255',
            'committee'     => 'required|string|max:255',
            'tin'           => 'required|string|max:255',
        ]);

        DirectorOfficer::create([
            'gis_id'       => $request->gis_id,
            'officer_name' => $request->officer_name,
            'address'      => $request->address,
            'gender'       => $request->gender,
            'nationality'  => $request->nationality,
            'incr'         => $request->incr === 'Y' ? 1 : 0,
            'stockholder'  => $request->stockholder === 'Y' ? 1 : 0,
            'board'        => $request->board,
            'officer_type' => $request->officer_type,
            'committee'    => $request->committee,
            'tin'          => $request->tin,
        ]);

        return back()->with('success', 'Director / Officer added successfully.');
    }
}