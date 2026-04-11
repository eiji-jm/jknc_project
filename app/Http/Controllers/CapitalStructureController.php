<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuthorizedCapitalStock;
use App\Models\SubscribedCapital;
use App\Models\PaidUpCapital;

class CapitalStructureController extends Controller
{
    public function storeAuthorized(Request $request)
    {
        $request->validate([
            'gis_id'            => 'required|exists:gis_records,id',
            'share_type'        => 'required|string|max:255',
            'number_of_shares'  => 'required|integer|min:1',
            'par_value'         => 'required|numeric|min:0',
            'amount'            => 'required|numeric|min:0',
        ]);

        AuthorizedCapitalStock::create([
            'gis_id'           => $request->gis_id,
            'share_type'       => $request->share_type,
            'number_of_shares' => $request->number_of_shares,
            'par_value'        => $request->par_value,
            'amount'           => $request->amount,
        ]);

        return back()->with('success', 'Authorized Capital added successfully.');
    }

    public function storeSubscribed(Request $request)
    {
        $request->validate([
            'gis_id'        => 'required|exists:gis_records,id',
            'nationality'   => 'required|string|max:255',
            'stockholders'  => 'required|integer|min:0',
            'share_type'    => 'required|string|max:255',
            'shares'        => 'required|integer|min:0',
            'par_value'     => 'required|numeric|min:0',
            'amount'        => 'required|numeric|min:0',
            'ownership'     => 'required|numeric|min:0|max:100',
        ]);

        SubscribedCapital::create([
            'gis_id'               => $request->gis_id,
            'nationality'          => $request->nationality,
            'no_of_stockholders'   => $request->stockholders,
            'share_type'           => $request->share_type,
            'number_of_shares'     => $request->shares,
            'par_value'            => $request->par_value,
            'amount'               => $request->amount,
            'ownership_percentage' => $request->ownership,
        ]);

        return back()->with('success', 'Subscribed Capital added successfully.');
    }

    public function storePaidup(Request $request)
    {
        $request->validate([
            'gis_id'        => 'required|exists:gis_records,id',
            'nationality'   => 'required|string|max:255',
            'stockholders'  => 'required|integer|min:0',
            'share_type'    => 'required|string|max:255',
            'shares'        => 'required|integer|min:0',
            'par_value'     => 'required|numeric|min:0',
            'amount'        => 'required|numeric|min:0',
            'ownership'     => 'required|numeric|min:0|max:100',
        ]);

        PaidUpCapital::create([
            'gis_id'               => $request->gis_id,
            'nationality'          => $request->nationality,
            'no_of_stockholders'   => $request->stockholders,
            'share_type'           => $request->share_type,
            'number_of_shares'     => $request->shares,
            'par_value'            => $request->par_value,
            'amount'               => $request->amount,
            'ownership_percentage' => $request->ownership,
        ]);

        return back()->with('success', 'Paid-Up Capital added successfully.');
    }
}