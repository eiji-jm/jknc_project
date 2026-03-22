<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stockholder;

class StockholderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'gis_id'                 => 'required|exists:gis_records,id',
            'stockholder_name'       => 'required|string|max:255',
            'address'                => 'required|string|max:255',
            'gender'                 => 'required|in:M,F',
            'nationality'            => 'required|string|max:255',
            'incr'                   => 'required|in:Y,N',
            'share_type'             => 'required|string|max:255',
            'shares'                 => 'required|integer|min:0',
            'amount'                 => 'required|numeric|min:0',
            'ownership_percentage'   => 'required|numeric|min:0|max:100',
            'amount_paid'            => 'required|numeric|min:0',
            'tin'                    => 'required|string|max:255',
        ]);

        Stockholder::create([
            'gis_id'               => $request->gis_id,
            'stockholder_name'     => $request->stockholder_name,
            'address'              => $request->address,
            'gender'               => $request->gender,
            'nationality'          => $request->nationality,
            'incr'                 => $request->incr === 'Y' ? 1 : 0,
            'share_type'           => $request->share_type,
            'shares'               => $request->shares,
            'amount'               => $request->amount,
            'ownership_percentage' => $request->ownership_percentage,
            'amount_paid'          => $request->amount_paid,
            'tin'                  => $request->tin,
        ]);

        return back()->with('success', 'Stockholder added successfully.');
    }
}