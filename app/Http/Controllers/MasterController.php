<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Helper;
use App\Libraries\RapiClass;

class MasterController extends Controller
{
    public function getDataDosen(request $request)
    {
        $rapiClass = new RapiClass();

        if($request->input()){
          $like = $request->input('query');
          $array = $rapiClass->getDataDosen($like);
        }

        $data = array(
               'query'       => 'Unit',
               'suggestions' => $array
        );

        return response()->json($data);
    }

    public function getDataMhs($nim)
    {
        $rapiClass = new RapiClass();
        $data = $rapiClass->getDataMhs($nim);
        return $data;
    }
}
