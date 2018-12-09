<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Kegiatan extends Model
{
    public static function getListJnsKegiatan()
    {
        $query = DB::table('mst_jns_kegiatan')
            ->where('active', 1)
            ->get();

        return $query;
    }

    public static function getListKegiatan()
    {
        $query = DB::table('tr_kegiatan')
					->select('id', 'alias')
                    ->where('active', 1)
                    ->get();

        return $query;
    }

    public static function getKegiatanDatatables()
    {
        DB::statement(DB::raw('set @rownum=0'));

        $query = DB::table('tr_kegiatan')
                  ->leftJoin('mst_judul_acara', 'mst_judul_acara.id', '=', 'tr_kegiatan.id_mst_judul_acara')
                  ->leftJoin('mst_jns_kegiatan', 'mst_jns_kegiatan.id', '=', 'tr_kegiatan.id_mst_jns_kegiatan')
                  ->leftJoin('mst_semester', 'mst_semester.id', '=', 'tr_kegiatan.id_mst_semester')
                ->select(DB::raw('@rownum  := @rownum  + 1 AS rownum, tr_kegiatan.id,
					mst_judul_acara.judul_acara,
					tr_kegiatan.alias,
					mst_semester.`name` as semester,
					tr_kegiatan.tahun_ajaran,
					DATE_FORMAT(tr_kegiatan.tanggal, "%d/%m/%Y") as tanggal,
					CONCAT(TIME_FORMAT(tr_kegiatan.jamMulai, "%H:%i"), "-", TIME_FORMAT(tr_kegiatan.jamAkhir, "%H:%i") ) as jam,
					tr_kegiatan.lokasi,
					mst_jns_kegiatan.jns_kegiatan'))
            ->get();

        return $query;
    }

    public static function getKegiatanAliasAutoComplete($filter = null)
    {
        $query = DB::table('tr_kegiatan');

        $query->where('active', 1);

        if ($filter != null) {
            $query->where('alias', 'LIKE', '%'.$filter.'%');
        }

        $result = $query->get();

        return $result;
    }

    public static function tambahKegiatan($data)
    {
        $query = DB::table('tr_kegiatan')->insert(
            [
                'id_mst_judul_acara' => @$data['kegiatan_judul_acara'],
                'id_mst_semester' 	 => @$data['kegiatan_semester'],
                'id_mst_jns_kegiatan'=> @$data['kegiatan_jns_kegiatan'],
                'alias' 			 => @$data['kegiatan_alias'],
                'tahun_ajaran'		 => @$data['kegiatan_ta'],
                'tanggal'			 => @$data['kegiatan_tanggal'],
                'jamMulai'			 => @$data['kegiatan_jam_mulai'],
                'jamAkhir'			 => @$data['kegiatan_jam_akhir'],
                'lokasi'			 => @$data['kegiatan_lokasi'],
                'moderator'			 => @$data['kegiatan_moderator']
            ]
        );

        return $result = ($query) ? true : false;
    }

    public static function updateKegiatan($data, $id)
    {
        $query = DB::table('tr_kegiatan')
                        ->where('id', $id)
                        ->update(
                    [
                        'id_mst_judul_acara' 	=> @$data['kegiatan_judul_acara'],
                        'id_mst_semester' 		=> @$data['kegiatan_semester'],
                        'id_mst_jns_kegiatan'   => @$data['kegiatan_jns_kegiatan'],
                        'id_mst_blanko'         => @$data['kegiatan_list_blanko'],
                        'alias' 				=> @$data['kegiatan_alias'],
                        'tahun_ajaran'			=> @$data['kegiatan_ta'],
                        'tanggal'				=> @$data['kegiatan_tanggal'],
                        'jamMulai'				=> @$data['kegiatan_jam_mulai'],
                        'jamAkhir'				=> @$data['kegiatan_jam_akhir'],
                        'lokasi'				=> @$data['kegiatan_lokasi'],
                        'moderator'				=> @$data['kegiatan_moderator']
                    ]
            );

        return $result = ($query) ? true : false;
    }

    public static function detail($id)
    {
        $query = DB::table('tr_kegiatan')->where('tr_kegiatan.id', $id)->get();

        return $result = ($query) ? $query : false;
    }
}
