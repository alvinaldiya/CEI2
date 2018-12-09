<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    public static function getMenu($id){
    	
    	$query = DB::table('mst_menu')
    			->where('id_mst_role', $id)
    			->get();

        return $query;
    
    }
}
