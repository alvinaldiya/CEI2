<?php

namespace App\Http\Controllers;

use App\Kegiatan;
use App\Peserta;
use Helper;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class KegiatanController extends Controller
{
    public function getKegiatanDatatables()
    {
        $kegiatan = Kegiatan::getKegiatanDatatables();
        return Datatables::of($kegiatan)
            ->addColumn('jml_peserta', function ($kegiatan) {
                // return '<a href="#" onclick="showListPeserta('.$kegiatan->id.')">'.Peserta::getJumlahPeserta($kegiatan->id).'</a>';
                return '<a class="btn btn-mini btn-black" href="#" onclick="showListPeserta(' . $kegiatan->id . ')">' . Peserta::getJumlahPeserta($kegiatan->id) . '</a>';
            })
            ->rawColumns(['jml_peserta'])
            ->make(true);
    }

    public function getKegiatanAliasAutoComplete(request $request)
    {
        $array = array();

        if ($request->input()) {
            $filter = $request->input('query');
        } else {
            $filter = null;
        }

        $kegiatan = Kegiatan::getKegiatanAliasAutoComplete($filter);

        foreach ($kegiatan as $key) {
            $newArray = array(
                'value' => $key->alias,
                'data' => $key->id,
            );

            array_push($array, $newArray);
        }

        $nilai_akhir = array(
            'query' => "Unit",
            'suggestions' => $array,
        );

        return response()->json($nilai_akhir);
    }

    public function save(request $request)
    {
        $data = $request->input('formListKegiatan');

        $data['kegiatan_tanggal'] = Helper::dateHUmanToMySqlDate($data['kegiatan_tanggal']);

        $kegiatan = Kegiatan::tambahKegiatan($data);

        if ($kegiatan) {
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil diinput.',
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'message' => 'Data Gagal diinput.',
            ]);
        }
    }

    public function update(request $request, $id)
    {
        $data = $request->input('formListKegiatan');

        $data['kegiatan_tanggal'] = Helper::dateHUmanToMySqlDate($data['kegiatan_tanggal']);

        $kegiatan = Kegiatan::updateKegiatan($data, $id);

        if ($kegiatan) {
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil di update.',
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'message' => 'Data Gagal di update.',
            ]);
        }
    }

    public function detail($id)
    {
        $detail = Kegiatan::detail($id);

        $detail[0]->jamMulai = substr($detail[0]->jamMulai, 0, 5);
        $detail[0]->jamAkhir = substr($detail[0]->jamAkhir, 0, 5);

        if ($detail) {
            return response()->json([
                'code' => 200,
                'data' => $detail,
                'message' => 'Data ada.',
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'data' => array(),
                'message' => 'Tidak ada data.',
            ]);
        }
    }
}
