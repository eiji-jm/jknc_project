<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DirectorOfficer;

class DirectorOfficerController extends Controller
{

public function store(Request $request)
{

DirectorOfficer::create([

'gis_id'=>$request->gis_id,
'officer_name'=>$request->officer_name,
'address'=>$request->address,
'gender'=>$request->gender,
'nationality'=>$request->nationality,
'incr'=>$request->incr,
'stockholder'=>$request->stockholder,
'board'=>$request->board,
'officer_type'=>$request->officer_type,
'committee'=>$request->committee,
'tin'=>$request->tin

]);

return back();

}

}