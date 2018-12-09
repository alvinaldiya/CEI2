<?php

namespace App;
use App\User;

use App\Peserta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Peserta extends Model
{
    

    public static function getPesertaDatatables()
    {
        DB::statement(DB::raw('set @rownum=0'));

        $query = DB::table('tr_peserta')
            ->leftJoin('tr_kegiatan', 'tr_kegiatan.id', '=', 'tr_peserta.id_tr_kegiatan')
            ->leftJoin('mst_judul_acara', 'mst_judul_acara.id', '=', 'tr_kegiatan.id_mst_judul_acara')
            ->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'tr_peserta.id', 'mst_judul_acara.judul_acara', 'tr_peserta.nim', 'tr_peserta.nama', 'tr_kegiatan.alias', 'tr_peserta.asal_kampus'])
            ->where('tr_peserta.active', 1)
            ->get();

        return $query;
    }

    public static function getSertifikatDatatables($data)
    {
        DB::statement(DB::raw('set @rownum=0'));

        $query = DB::table('tr_peserta')
            ->leftJoin('tr_kegiatan', 'tr_kegiatan.id', '=', 'tr_peserta.id_tr_kegiatan')
            ->leftJoin('mst_judul_acara', 'mst_judul_acara.id', '=', 'tr_kegiatan.id_mst_judul_acara')
            ->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'tr_peserta.id', 'mst_judul_acara.judul_acara', 'tr_peserta.nim', 'tr_peserta.nama', 'tr_kegiatan.alias', 'tr_peserta.asal_kampus'])
            ->where('tr_peserta.active', 1);

        if ($id = $data['id_kegiatan']) {
            $query->where('tr_peserta.id_tr_kegiatan', $id);
        } else if ($data['id_kegiatan'] == null) {
            $query->where('tr_peserta.id_tr_kegiatan', null);
        }

        return $query->get();
    }

    public static function getListPeserta($id_tr_kegiatan = null, $status = null)
    {
        DB::statement(DB::raw('set @rownum=0'));

        $query = DB::table('tr_peserta')
            ->leftJoin('tr_kegiatan', 'tr_kegiatan.id', '=', 'tr_peserta.id_tr_kegiatan')
            ->leftJoin('mst_judul_acara', 'mst_judul_acara.id', '=', 'tr_kegiatan.id_mst_judul_acara')
            ->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'tr_peserta.id', 'mst_judul_acara.judul_acara', 'tr_peserta.nim', 'tr_peserta.nama', 'tr_peserta.nilai', 'tr_peserta.grade', 'tr_kegiatan.alias', 'tr_peserta.asal_kampus'])
            ->where('tr_peserta.active', 1);

        if ($id_tr_kegiatan != null) {
            $query->where('tr_peserta.id_tr_kegiatan', $id_tr_kegiatan);
        }

        if ($status != null) {
            $query->where('tr_peserta.status', $status);
        } else {
            $query->whereNull('tr_peserta.status');
        }

        return $query->get();
    }

    public static function getDetailPeserta($id)
    {
        $query = DB::table('tr_peserta')
            ->select([DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'tr_peserta.id', 'mst_judul_acara.judul_acara', 'tr_kegiatan.tanggal as tgl_kegiatan', 'tr_peserta.nim', 'tr_peserta.nama', 'tr_peserta.nilai', 'tr_peserta.grade', 'tr_kegiatan.alias', 'tr_peserta.asal_kampus', 'tr_nourut.no_urut', 'mst_semester.name as jns_semester', 'tr_kegiatan.tahun_ajaran', 'tr_kegiatan.id_mst_blanko', 'mst_blanko.nama_file'])
            ->leftJoin('tr_kegiatan', 'tr_kegiatan.id', '=', 'tr_peserta.id_tr_kegiatan')
            ->leftJoin('mst_judul_acara', 'mst_judul_acara.id', '=', 'tr_kegiatan.id_mst_judul_acara')
            ->leftJoin('tr_nourut', 'tr_peserta.id', '=', 'tr_nourut.id_peserta')
            ->leftJoin('mst_semester', 'mst_semester.id', '=', 'tr_kegiatan.id_mst_semester')
            ->leftJoin('mst_blanko', 'mst_blanko.id', '=', 'tr_kegiatan.id_mst_blanko')
            ->where('tr_peserta.id', $id)
            ->where('tr_peserta.active', 1)
            ->where('mst_blanko.active', 1)
            ->first();

        return $result = ($query) ? $query : false; 
    }

    public static function tambahPeserta($data)
    {

        $id_list_kegiatan = $data['list_kegiatan'];
        $nim = $data['nim'];
        $nama = $data['nama'];
        $asal_kampus = $data['asal_kampus'];
        $id_jns_kegiatan = $data['id_mst_jns_kegiatan'];

        DB::beginTransaction();

        try {

            $id = DB::select('SELECT LPAD(IF(RIGHT(MAX(id),4) IS NULL, LPAD(1,4,\'0\'), RIGHT(MAX(id),4)+1 ), 4, \'0\') as id FROM `tr_peserta` WHERE created_at = CURDATE();');
            DB::statement('INSERT INTO tr_peserta VALUES(CONCAT(\'CEI\',DATE_FORMAT(CURDATE(),\'%d%m%y\'),?),?,?,?,?,CURDATE(),?,?,?,?)', [$id[0]->id, $id_list_kegiatan, $nim, $nama, $asal_kampus, 0, null, null, 1]);

            DB::commit();

            return true;

        } catch (\Exception $e) {

            DB::rollback();

            return false;

        }
    }

    public static function cekKepesertaanNim($nim = null, $id_kegiatan)
    {
        $query = DB::table('tr_peserta')
            ->where('nim', $nim)
            ->where('id_tr_kegiatan', $id_kegiatan)
            ->where('active', 1)
            ->get();

        return $result = (count($query) == 1) ? $query : false;
    }

    public static function getJumlahPeserta($id)
    {
        $query = DB::table('tr_peserta')
            ->where('id_tr_kegiatan', $id)
            ->where('active', 1)
            ->get();

        return $result = count($query);
    }

    public static function updatePeserta($data, $id)
    {
        $query = DB::table('tr_peserta')
            ->where('id', $id)
            ->update(['nama' => $data['modal_peserta_nama'], 'asal_kampus' => $data['modal_peserta_asal_kampus']]);

        return $result = ($query) ? true : false;
    }

    public static function hapusPeserta($id)
    {
        $query = DB::table('tr_peserta')
            ->where('id', $id)
            ->update(['active' => 0]);

        return $result = ($query) ? true : false;
    }

    public static function getListPesertaByKegiatan($id)
    {
        $query = DB::table('tr_peserta')
            ->select('tr_peserta.id', 'tr_peserta.nim', 'tr_peserta.nama', 'tr_peserta.nilai', 'tr_peserta.grade', 'tr_kegiatan.alias')
            ->leftJoin('tr_kegiatan', 'tr_kegiatan.id', '=', 'tr_peserta.id_tr_kegiatan')
            ->where('id_tr_kegiatan', $id['cari_peserta'])
            ->where('tr_peserta.active', 1)
            ->get();

        return $result = ($query) ? $query : false;
    }

    public static function getNoUrutSertifikat($id)
    {

        $query = DB::table('tr_nourut')
            ->where('id_peserta', $id)
            ->get();

        return $result = ($query) ? $query : false;
    }


}
