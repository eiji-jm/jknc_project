<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorizedCapitalStock extends Model
{

protected $fillable = [

'gis_id',
'share_type',
'number_of_shares',
'par_value',
'amount'

];

}