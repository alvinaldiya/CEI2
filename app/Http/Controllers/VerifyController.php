<?php

namespace App\Http\Controllers;

use App\Peserta;
use App\Sertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Validator;

class VerifyController extends Controller
{
    public function show()
    {
        return view('layouts.verify');
    }

    public function nourut(Request $request)
    {
        return view('layouts.verif');
    }

    public function cekSertifikat(Request $request)
    {

        //TES VALIDATOR

        // $validator = Validator::make($request->all(), [
        //     'no_sertifikat' => 'required|max:1',
        //     'tahun_sertifikat' => 'required',
        // ]);

        // if ($validator->fails()) {

        //     $errors = $validator->errors();
        //     print_r($errors);
        //     exit;
        // }

        $data = ['no_sertifikat' => $request->input('no_sertifikat'), 'tahun_sertifikat' => $request->input('tahun_sertifikat')];

        $id = Sertifikat::cekIdPesertaByNoSertifikat($data['no_sertifikat'], $data['tahun_sertifikat']);

        if ($id == false) {
            return response()->json(['code' => 500, 'data' => array(), 'message' => 'ID Sertifikat Invalid']);
        } else {
            $data_sertifikat = Peserta::getDetailPeserta($id[0]->id_peserta);

            if ($data_sertifikat) {
                return response()->json(['code' => 200, 'data' => $data_sertifikat, 'message' => 'ID Sertifikat Valid']);
            } else {
                return response()->json(['code' => 500, 'data' => array(), 'message' => 'ID Sertifikat Invalid']);
            }
        }

    }

    public function verifSertifikat(Request $request)
    {

        $id = $request->input('id');

        $data_sertifikat = Peserta::getDetailPeserta($id);

        if ($data_sertifikat) {

            if ($data_sertifikat->no_urut == null || $data_sertifikat->no_urut == '') {
                return view('layouts.nonverif', compact('data_sertifikat'));
            } else {
                return view('layouts.verif', compact('data_sertifikat'));
            }
        } else {
            return view('layouts.nonverif');
        }

    }
}
