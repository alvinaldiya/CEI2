<?php

namespace App\Http\Controllers;

use App\Kegiatan;
use App\Libraries\RapiClass;
use App\Peserta;
use Helper;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Database\Eloquent\Model;
use DB;
use Charts;

class PesertaController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    

    public function index()
    {
        $getListKegiatan = Kegiatan::getListKegiatan();

        $data = array(
            "list_kegiatan" => $getListKegiatan,
        );

        return view('peserta.index', $data);
    }

    public function getPesertaDatatables()
    {
        $peserta = Peserta::getPesertaDatatables();
        return Datatables::of($peserta)
            ->addColumn('action', function ($peserta) {
                return '<button type="button" class="btn btn-mini" onclick="hapusPeserta(\'' . $peserta->id . '\')"><i class="fontello-icon-trash-3"></i></button>';
            })
            ->make(true);
    }

    public function getDetailPeserta($id)
    {
        $getDetailPeserta = Peserta::getDetailPeserta($id);

        if ($getDetailPeserta) {
            return response()->json(['code' => 200, 'data' => $getDetailPeserta, 'message' => '']);
        } else {
            return response()->json(['code' => 500, 'data' => array(), 'message' => 'Data tidak ditemukan.']);
        }
    }

    public function tambahPeserta(request $request)
    {
        $data = $request->input();

        if ($data['peserta']['nim'] == null || $data['peserta']['nim'] == '') {

            //GET JENIS ACARA KEGIATAN
            $detailKegiatan = Kegiatan::detail($data['peserta']['list_kegiatan']);

            //tambah data array peserta
            $data['peserta']['id_mst_jns_kegiatan'] = $detailKegiatan[0]->id_mst_jns_kegiatan;

            $savePeserta = Peserta::tambahPeserta($data['peserta']);

            if ($savePeserta == true) {
                return response()->json(['code' => 200, 'message' => 'Data berhasil diinput.']);
            } else {
                return response()->json(['code' => 500, 'message' => 'Data gagal diinput.']);
            }
        } else {

            //CEK APAKAH NIM TERSEBUT SUDAH TERDAFTAR DALAM ACARA KEGIATAN
            $cekNim = Peserta::cekKepesertaanNim($data['peserta']['nim'], $data['peserta']['list_kegiatan']);

            if ($cekNim) {
                return response()->json(['code' => 500, 'message' => 'Mahasiswa dengan NIM : ' . $cekNim[0]->nim . ' sudah terdaftar di acara kegiatan ini.']);
            } else {

                //GET JENIS ACARA KEGIATAN
                $detailKegiatan = Kegiatan::detail($data['peserta']['list_kegiatan']);

                //tambah data array peserta
                $data['peserta']['id_mst_jns_kegiatan'] = $detailKegiatan[0]->id_mst_jns_kegiatan;

                $savePeserta = Peserta::tambahPeserta($data['peserta']);

                if ($savePeserta == true) {
                    return response()->json(['code' => 200, 'message' => 'Data berhasil diinput.']);
                } else {
                    return response()->json(['code' => 500, 'message' => 'Data gagal diinput.']);
                }
            }
        }
    }

    public function updatePeserta(request $request, $id)
    {
        $peserta = $request->input();

        $updatePeserta = Peserta::updatePeserta($peserta['formModalPeserta'], $id);

        if ($updatePeserta == true) {
            return response()->json(['code' => 200, 'message' => 'Data berhasil di Update.']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Data gagal di Update.']);
        }
    }

    public function hapusPeserta($id)
    {
        $hapusPeserta = Peserta::hapusPeserta($id);

        if ($hapusPeserta == true) {
            return response()->json(['code' => 200, 'message' => 'Data berhasil di Hapus.']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Data gagal di Hapus.']);
        }
    }

    public function getListPesertaByKegiatan(Request $request)
    {
        $data = $request->input('gradebook');

        $listPeserta = Peserta::getListPesertaByKegiatan($data);

        if (count($listPeserta) > 0) {
            return response()->json(['code' => 200, 'data' => $listPeserta, 'message' => '']);
        } else {
            return response()->json(['code' => 500, 'data' => array(), 'message' => 'Data tidak ada.']);
        }
    }

    public function JumlahPeserta()
    {
        $pesertaCount = DB::table('tr_peserta')->get();
        return view('layouts.admin.home', compact('pesertaCount'));
    }


   
}
