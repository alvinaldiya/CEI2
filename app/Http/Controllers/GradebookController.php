<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Peserta;
use App\Kegiatan;
use App\Gradebook;

class GradebookController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $dataPeserta = array();
        $getListKegiatan = Kegiatan::getListKegiatan();

        $data = array('dataPeserta'   => $dataPeserta,
                      'list_kegiatan' => $getListKegiatan);

        return view('gradebook.index',$data);
    }

    public function updateGradebook(request $request)
    {
        $data = $request->input();

        $updateGradebook = Gradebook::updateGradebook($data);
        $getGrade        = Gradebook::getGrade($data['id']);

        if ($updateGradebook) {
          return response()->json(['code' => 200, 'data' => $getGrade, 'message' => 'Data berhasil diupdate.']);
        } else {
          return response()->json(['code' => 500, 'data' => array(), 'message' => 'Data gagal diupdate.']);
        }

    }
}
