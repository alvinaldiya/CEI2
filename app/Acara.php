<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use CyrildeWit\EloquentViewable\Viewable;


class Acara extends Model
{
    use Viewable;
    public static function getListMasterAcara()
    {
        $query = DB::table('mst_judul_acara')->where('active', 1)->get();

        return $query;
    }

    public static function getMasterAcaraDatatables()
    {
        DB::statement(DB::raw('set @rownum=0'));

        $query = DB::table('mst_judul_acara')
								->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),'id','judul_acara', 'active'])
								->where('active', 1)
								->get();

        return $query;
    }

    public static function getMasterAcaraAutoComplete($filter = null)
    {
        $query = DB::table('mst_judul_acara');

        $query->where('active', 1);

        if ($filter != null) {
            $query->where('judul_acara', 'LIKE', '%'.$filter.'%');
        }

        $result = $query->get();

        return $result;
    }


    public static function getDetailMasterAcara($id)
    {
        $query = DB::table('mst_judul_acara')
								->where([
								['active', '=', '1'],
								['id', '=', $id],
								])
								->first();

        return $query;
    }

    //SAVE FUNCTION

    public static function saveMasterAcara($data)
    {
        $query = DB::table('mst_judul_acara')->insert(
								['judul_acara' => $data]
								);
        $result = ($query) ? true : false;
        return $result;
    }

    public static function updateMasterAcara($data, $id)
    {
        $query = DB::table('mst_judul_acara')
								->where('id', $id)
								->update(['judul_acara' => $data]);
        $result = ($query) ? true : false;
        return $result;
    }

    public static function deleteMasterAcara($id)
    {
        $query = DB::table('mst_judul_acara')
								->where('id', $id)
								->update(['active' => 0]);
        $result = ($query) ? true : false;
        return $result;
    }
}
