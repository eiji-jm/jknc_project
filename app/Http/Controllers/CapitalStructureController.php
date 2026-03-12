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

'share_type' => 'required',
'number_of_shares' => 'required|numeric',
'par_value' => 'required|numeric',
'amount' => 'required|numeric'

]);

AuthorizedCapitalStock::create([

'gis_id'=>$request->gis_id,
'share_type'=>$request->share_type,
'number_of_shares'=>$request->number_of_shares,
'par_value'=>$request->par_value,
'amount'=>$request->amount

]);

return back();

}


public function storeSubscribed(Request $request)
{

SubscribedCapital::create([

'gis_id'=>$request->gis_id,
'nationality'=>$request->nationality,
'no_of_stockholders'=>$request->stockholders,
'share_type'=>$request->share_type,
'number_of_shares'=>$request->shares,
'par_value'=>$request->par_value,
'amount'=>$request->amount,
'ownership_percentage'=>$request->ownership

]);

return back();

}


public function storePaidup(Request $request)
{

PaidUpCapital::create([

'gis_id'=>$request->gis_id,
'nationality'=>$request->nationality,
'no_of_stockholders'=>$request->stockholders,
'share_type'=>$request->share_type,
'number_of_shares'=>$request->shares,
'par_value'=>$request->par_value,
'amount'=>$request->amount,
'ownership_percentage'=>$request->ownership

]);

return back();

}

}