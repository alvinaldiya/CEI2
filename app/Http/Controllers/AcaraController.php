<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Acara;
use App\Kegiatan;
use App\Sertifikat;
use Helper;
use App\Libraries\RapiClass;

class AcaraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $getListMasterAcara = $this->getListMasterAcara();
        $getListJnsKegiatan = Kegiatan::getListJnsKegiatan();
        $getListBlanko      = Sertifikat::getListBlanko();

        $data = array(
                "kegiatan_judul_acara"		=> $getListMasterAcara,
                "kegiatan_list_jns_kegiatan"=> $getListJnsKegiatan,
                "kegiatan_list_blanko"      => $getListBlanko
            );

        return view('acara.index', $data);
    }

    public function getListMasterAcara()
    {
        $masterAcara = Acara::getListMasterAcara();

        return $masterAcara;
    }

    public function getMasterAcaraDatatables()
    {
        $masterAcara = Acara::getMasterAcaraDatatables();
        return Datatables::of($masterAcara)
        ->addColumn('action', function ($masterAcara) {
            return '<button type="button" class="btn btn-mini" onclick="hapusMasterAcara('.$masterAcara->id.')"><i class="fontello-icon-trash-3"></i></button>';
        })
        ->make(true);
    }

    public function getMasterAcaraAutoComplete(request $request)
    {
        $array = array();

        if ($request->input()) {
            $filter = $request->input('query');
        } else {
            $filter = null;
        }

        $masterAcara = Acara::getMasterAcaraAutoComplete($filter);

        foreach ($masterAcara as $key) {
            $newArray = array(
                    'value' => $key->judul_acara,
                    'data'  => $key->id
                );

            array_push($array, $newArray);
        }

        $nilai_akhir = array(
                'query' 		=> "Unit",
                'suggestions'	=> $array
            );

        return response()->json($nilai_akhir);
    }

    public function saveMasterAcara(request $request)
    {
        $data = $request->input('judul_acara');

        $save = Acara::saveMasterAcara($data);

        if ($save == true) {
            return response()->json(['code' => 200,'message' => 'Data berhasil diinput.']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Data gagal diinput.']);
        }
    }

    public function updateMasterAcara(request $request, $id)
    {
        $data = $request->input('judul_acara');

        $update = Acara::updateMasterAcara($data, $id);

        if ($update == true) {
            return response()->json(['code' => 200,'message' => 'Data berhasil diupdate.']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Data gagal diupdate.']);
        }
    }

    public function deleteMasterAcara($id)
    {
        $delete = Acara::deleteMasterAcara($id);

        if ($delete == true) {
            return response()->json(['code' => 200,'message' => 'Data berhasil dihapus.']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Data gagal dihapus.']);
        }
    }

    public function getDetailMasterAcara($id)
    {
        $detailAcara = Acara::getDetailMasterAcara($id);

        if ($detailAcara) {
            return response()->json(['code' => 200, 'data'=> $detailAcara, 'message' => 'Data ditemukan.']);
        } else {
            return response()->json(['code' => 500, 'data'=> '', 'message' => 'Data tidak ditemukan.']);
        }
    }
}
