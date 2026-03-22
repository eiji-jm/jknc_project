<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stockholder;

class StockholderController extends Controller
{

public function store(Request $request)
{

Stockholder::create([

'gis_id' => $request->gis_id,
'stockholder_name' => $request->stockholder_name,
'address' => $request->address,
'gender' => $request->gender,
'nationality' => $request->nationality,
'incr' => $request->incr,
'share_type' => $request->share_type,
'shares' => $request->shares,
'amount' => $request->amount,
'ownership_percentage' => $request->ownership_percentage,
'amount_paid' => $request->amount_paid,
'tin' => $request->tin

]);

return back();

}

}