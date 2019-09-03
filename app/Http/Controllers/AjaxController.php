<?php


namespace App\Http\Controllers;

use App\Models\AppDetail;
use Request;

class AjaxController extends Controller
{

    public function getKeyInfo(Request $request)
    {
        if (Request::ajax()) {
            $keyId = Request::get('keyId');
            return AppDetail::where('id', $keyId)->select('id', 'expire_date', 'point')->first();
        }
    }


}
