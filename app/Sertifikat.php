<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sertifikat extends Model
{

    public static function getMasterSertifikatDatatables()
    {
        $query = DB::table('mst_blanko')
                ->select('id', 'nama_blanko','nama_file')
                ->where('active', 1);

        return $query->get();
    }

    public static function tambahBlanko($data)
    {
        $query = DB::table('mst_blanko')->insert(
            ['nama_blanko' => $data['nama_blanko'], 'nama_file' => $data['nama_file']]
        );
        $result = ($query) ? true : false;
        return $result;
    }

    public static function deleteBlanko($id)
    {

        $query = DB::table('mst_blanko')
            ->where('id', $id)
            ->update(['active' => 0]);
        $result = ($query) ? true : false;
        return $result;
    }

    public static function inputNoSertifikat($data)
    {

        $no = 0;

        foreach ($data as $key) {
            $no_urut = self::getLastId();

            if (self::cekNoSertifikatByID($key->id) == false) {
                $insert = DB::table('tr_nourut')->insert(
                    ['id_peserta' => $key->id,
                        'no_urut' => $no_urut[0]->no_urut,
                        'dateCreated' => date('Y-m-d'),
                    ]
                );
                $no++;
            }
        }

        return $no;
    }

    public static function getLastId()
    {
        $id = DB::select('SELECT IF(YEAR(CURDATE())=YEAR(dateCreated), a.no_urut+1, 1) as no_urut FROM (
				SELECT * FROM `tr_nourut` ORDER BY no_urut DESC LIMIT 1) as a');
        return $id;
    }

    public static function cekNoSertifikatByID($id)
    {
        $query = DB::table('tr_nourut')
            ->where('id_peserta', $id)
            ->get();

        return $result = (count($query) > 0) ? true : false;

    }

    public static function cekIdPesertaByNoSertifikat($no_sertifikat, $thn)
    {
        $query = DB::table('tr_nourut')
            ->where('no_urut', $no_sertifikat)
            ->whereYear('dateCreated', $thn)
            ->get();

        return $result = (count($query) > 0) ? $query : false;

    }

    public static function getListBlanko()
    {
        $query = DB::table('mst_blanko')
            ->where('active', 1)
            ->get();
            
        return $query;
    }

    public static function getDetailBlanko($id)
    {
        $query = DB::table('mst_blanko')
            ->where('active', 1)
            ->where('id', $id)
            ->first();
            
        return $query;
    }

}
