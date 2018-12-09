<?php

namespace App\Http\Controllers;

use App\Kegiatan;
use App\Peserta;
use App\Sertifikat;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use PDF;
use View;
use Yajra\Datatables\Datatables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SertifikatController extends Controller
{
    

    public function filee()
    {

        Storage::disk('local')->put('file.txt', 'Contents');

        $files = Storage::allFiles();

        print_r($files);
        exit;

    }

    public function index()
    {
        $getListKegiatan = Kegiatan::getListKegiatan();

        $data = array('list_kegiatan' => $getListKegiatan);

        return view('sertifikat.index', $data);
    }

    //START DATA BLANKO
    //GET DATA SERTIFIKAT UNTUK PESERTA 
    public function getSertifikatDatatables(request $request)
    {
        $sertifikat = Peserta::getSertifikatDatatables($request->input());

        return Datatables::of($sertifikat)
            ->addColumn('action', function ($sertifikat) {
                return '<div class="btn-group"> <a class="btn btn-mini btn-glyph dropdown-toggle" data-toggle="dropdown" href="#"><i class="fontello-icon-print"></i> <span class="caret"></span> </a>
                        <ul class="dropdown-menu">
                            <li><a href="sertifikat/cetak/' . $sertifikat->id . '/blank" target="_blank">Blank</a></li>
                        </ul>
                    </div>';
            })
            ->make(true);

        // ini untuk tombol cetak sertifikat dengan background. (sementara dihilangkan)
        // <li><a href="sertifikat/cetak/'.$sertifikat->id.'/filled" target="_blank">Background</a></li>
    }
    
    //GET DATA MASTER SERTIFIKAT (BLANKO)
    public function getMasterSertifikatDatatables()
    {
        $dataMasterSertifikat = Sertifikat::getMasterSertifikatDatatables();

        return Datatables::of($dataMasterSertifikat)
            ->addColumn('action', function ($dataMasterSertifikat) {
                return '<button type="button" class="btn btn-mini" onclick="hapusMasterBlanko('.$dataMasterSertifikat->id.')"><i class="fontello-icon-trash-3"></i></button>';
            })
            ->make(true);

        // ini untuk tombol cetak sertifikat dengan background. (sementara dihilangkan)
        // <li><a href="sertifikat/cetak/'.$sertifikat->id.'/filled" target="_blank">Background</a></li>
    }

    public function deleteBlanko($id){
        $blanko = Sertifikat::deleteBlanko($id);
        if ($blanko == true) {
            return response()->json(['code' => 200, 'data' => array(), 'message' => 'Berhasil menghapus data']);
        } else {
            return response()->json(['code' => 500, 'data' => array(), 'message' => 'Data gagal dihapus.']);
        }

    }

    public function previewBlanko($blanko)
    {
        PDF::SetTitle('SERTIFIKAT BLANKO 1');
        PDF::AddPage('L', 'A4');
        PDF::setImageScale(1);
        PDF::SetAutoPageBreak(false, 0);

        $data_array = array(
            'no_sertifikat' => 001122,
            'bulan_romawi' => Helper::toRomawi(date('m')),
            'tahun' => date('Y'),
            'nama' => ucwords('SAMPLING'),
            'judul_acara' => ucwords('INI ADALAH JUDUL ACARA'),
            'alias' => ucwords('INI ADALAH JUDUL ALIAS'),
            'nilai' => ucwords('90'),
            'tgl_cetak' => Helper::tglCetak(date('Y-m-d')),
            'jns_semester' => 'GENAP',
            'thn_ajar' => date('Y'),
        );

        $blanko = Sertifikat::getDetailBlanko($blanko); 

        $view = View::make("sertifikat.blanko.".$blanko->nama_file."", $data_array);

        $html = $view->render();

        PDF::writeHTML($html, true, false, true, false, '');

        $style = array(
            'border' => 2,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1, // height of a single module in points
        );

        $url = url('/');
        $link_barcode = $url . '/verify/verifSertifikat?id=123';
        // QRCODE,H : QR-CODE Best error correction
        PDF::write2DBarcode($link_barcode, 'QRCODE,H', 265, 175, 30, 30, $style, 'N');

        $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'solid' => '10,20,5,10', 'phase' => 10, 'color' => array(0, 0, 0));
    
        PDF::Output('hello_world.pdf');

    }
    //END DATA BLANKO

    public function cetak($id_peserta, $bg)
    {
        $data_peserta = Peserta::getDetailPeserta($id_peserta);

        if($data_peserta){
            //fungsi untuk mengambil dan mengolah no_sertifikat
            $no_sertifikat = Peserta::getNoUrutSertifikat($id_peserta);
            $no_sertifikat = (count($no_sertifikat) > 0) ? $no_sertifikat[0]->no_urut : '';

            //fungsi untuk mengambil semester ganjil/genap
            $jns_semester = ($data_peserta->jns_semester == 'Ganjil') ? 'Semester Ganjil' : 'Semester Genap';

            if ($no_sertifikat == '') {
                echo "<h1 align='center'>No Sertifikat belum di Generate! </h1>";
            } else {

                $path = resource_path() . '/images/002.jpg';

                PDF::SetTitle('SERTIFIKAT BLANKO 1');
                PDF::AddPage('L', 'A4');
                PDF::setImageScale(1);
                PDF::SetAutoPageBreak(false, 0);

                $data_array = array(
                    'no_sertifikat' => $no_sertifikat,
                    'bulan_romawi' => Helper::toRomawi(date('m')),
                    'tahun' => date('Y'),
                    'nama' => ucwords($data_peserta->nama),
                    'judul_acara' => ucwords($data_peserta->judul_acara),
                    'alias' => ucwords($data_peserta->alias),
                    'nilai' => ucwords($data_peserta->nilai),
                    'tgl_cetak' => Helper::tglCetak(date('Y-m-d')),
                    'jns_semester' => $jns_semester,
                    'thn_ajar' => $data_peserta->tahun_ajaran,
                );

                $bg = ($bg != "blank") ? PDF::Image($path, 0, 0, 296, 210, '', '', '', false, 96, '', false, false, 0) : '';

                $view = View::make("sertifikat.blanko.".$data_peserta->nama_file."", $data_array);

                $html = $view->render();

                // $html = '<table width="100%" cellpadding="3px;" cellspacing="0;">
                //           <tbody>
                //           <tr>
                //           <td>&nbsp;</td>
                //           </tr>
                //           <tr>
                //           <td>&nbsp;</td>
                //           </tr>
                //           <tr>
                //           <td>&nbsp;</td>
                //           </tr>
                //           <tr>
                //           <td>&nbsp;</td>
                //           </tr>
                //           <tr>
                //           <td>&nbsp;</td>
                //           </tr>
                //           <tr>
                //           <td>&nbsp;</td>
                //           </tr>
                //           <tr>
                //           <td>&nbsp;</td>
                //           </tr>
                //           <tr>
                //           <td>&nbsp;</td>
                //           </tr>
                //           <tr>
                //           <td style="text-align: center;"><font style="font-size:12px;font-weight:bold;font-family:Times;">No. 3060/SERTIFIKAT/REC/VI/2017</font></td>
                //           </tr>
                //           <tr>
                //           <td style="text-align: center;"><font style="font-size:12spx;font-family:Times;">This is to certify that</font></td>
                //           </tr>
                //           <tr>
                //           <td style="text-align: center;"><h1 style="font-family:Times;font-size:24px;">'.ucwords($data_peserta->nama).'</h1></td>
                //           </tr>
                //           <tr>
                //           <td style="text-align: center;"><font style="font-size:12spx;font-family:Times;">Has successfuly completed 35 hours training of</font></td>
                //           </tr>
                //           <tr>
                //           <td style="text-align: center;"><h1 style="font-family:Times;font-size:24px;">'.$data_peserta->judul_acara.'</h1></td>
                //           </tr>
                //           <tr>
                //           <td style="text-align: center;"><font style="font-size:12spx;font-family:Times;">Administired by Perguruan Tinggi Raharja </font></td>
                //           </tr>
                //           <tr>
                //           <td style="text-align: center;"><font style="font-size:16spx;font-family:Times;font-weight:bold;">With Scored '.$data_peserta->nilai.'</font></td>
                //           </tr>
                //           <tr>
                //           <td style="text-align: center;"><font style="font-size:12spx;font-family:Times;">And</font></td>
                //           </tr>
                //           <tr>
                //           <td style="text-align: center;"><font style="font-size:12spx;font-family:Times;">has fulfilled the requirements to be recognised as</font></td>
                //           </tr>
                //           <tr>
                //           <td style="text-align: center;"><font style="font-size:12spx;font-family:Times;font-weight:bold;">Raharja iLearning Junior Professional (RiJP)</font></td>
                //           </tr>
                //           </tbody>
                //           </table>';
                // output the HTML content
                PDF::writeHTML($html, true, false, true, false, '');

                // PDF::SetFont('times', 'B', 12);
                // PDF::SetCellMargins(0, 58, 0, 0);
                // PDF::setCellPaddings(0, 0, 0, 0);
                // PDF::MultiCell(275, 5, "No. 3060/SERTIFIKAT/REC/VI/2017 \n", 0, 'C', 0, 1, '', '', true);
                //
                // PDF::SetFont('times', '', 12);
                // PDF::SetCellMargins(0, 5, 0, 0);
                // PDF::setCellPaddings(0, 0, 0, 0);
                // PDF::MultiCell(275, 5, "This is to certify that \n", 0, 'C', 0, 1, '', '', true);
                //
                // PDF::SetFont('times', 'B', 22);
                // PDF::SetCellMargins(0, 1, 0, 0);
                // PDF::setCellPaddings(0, 0, 0, 0);
                // PDF::MultiCell(275, 5, "Cahyo Anggoro Seto \n", 0, 'C', 0, 1, '', '', true);
                //
                // PDF::SetFont('times', '', 12);
                // PDF::SetCellMargins(0, 2, 0, 0);
                // PDF::setCellPaddings(0, 0, 0, 0);
                // PDF::MultiCell(275, 5, "Has successfuly completed 35 hours training of \n", 0, 'C', 0, 1, '', '', true);
                //
                // PDF::SetFont('times', 'B', 22);
                // PDF::SetCellMargins(0, 5, 0, 0);
                // PDF::setCellPaddings(0, 0, 0, 0);
                // PDF::MultiCell(275, 5, "    Build Web With Framework (BW2F) \n", 0, 'C', 0, 1, '', '', true);
                //
                // PDF::SetFont('times', '', 12);
                // PDF::SetCellMargins(0, 5, 0, 0);
                // PDF::setCellPaddings(0, 0, 0, 0);
                // PDF::MultiCell(275, 5, "Administired by Perguruan Tinggi Raharja \n", 0, 'C', 0, 1, '', '', true);
                //
                // PDF::SetFont('times', 'B', 14);
                // PDF::SetCellMargins(0, 1, 0, 0);
                // PDF::setCellPaddings(0, 0, 0, 0);
                // PDF::MultiCell(275, 5, "With Scored 450 \n", 0, 'C', 0, 1, '', '', true);
                //
                // PDF::SetFont('times', '', 12);
                // PDF::SetCellMargins(0, 1, 0, 0);
                // PDF::setCellPaddings(0, 0, 0, 0);
                // PDF::MultiCell(275, 5, "and \n", 0, 'C', 0, 1, '', '', true);
                //
                // PDF::SetFont('times', '', 12);
                // PDF::SetCellMargins(0, 1, 0, 0);
                // PDF::setCellPaddings(0, 0, 0, 0);
                // PDF::MultiCell(275, 5, "has fulfilled the requirements to be recognised as \n", 0, 'C', 0, 1, '', '', true);
                //
                // PDF::SetFont('times', 'B', 12);
                // PDF::SetCellMargins(0, 1, 0, 0);
                // PDF::setCellPaddings(0, 0, 0, 0);
                // PDF::MultiCell(275, 5, "Raharja iLearning Junior Professional (RiJP) \n", 0, 'C', 0, 1, '', '', true);

                // PDF::SetFont('times', 'B', 12);
                // PDF::SetCellMargins(48, 8, 0, 0);
                // PDF::setCellPaddings(0, 0, 0, 0);
                // PDF::MultiCell(70, 5, "Ketua Raharja Enrichment Centre", 0, 'C', 0, 0, '', '', true);
                // PDF::MultiCell(70, 5, "Direktur Perguruan Tinggi Raharja \n", 0, 'C', 0, 1, '', '', true);

                // PDF::SetFont('times', 'B', 12);
                // PDF::SetCellMargins(48, 25, 0, 0);
                // PDF::setCellPaddings(0, 0, 0, 0);
                // PDF::MultiCell(70, 5, "Dr. Ir. Untung Rahardja, M.T.I., MM", 0, 'C', 0, 0, '', '', true);
                // PDF::MultiCell(70, 5, "Drs. Po. Abas Sunarya, M.S.i \n", 0, 'C', 0, 1, '', '', true);

                //set style for barcode
                $style = array(
                    'border' => 2,
                    'vpadding' => 'auto',
                    'hpadding' => 'auto',
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false, //array(255,255,255)
                    'module_width' => 1, // width of a single module in points
                    'module_height' => 1, // height of a single module in points
                );

                $url = url('/');
                $link_barcode = $url . '/verify/verifSertifikat?id=' . $data_peserta->id;
                // QRCODE,H : QR-CODE Best error correction
                PDF::write2DBarcode($link_barcode, 'QRCODE,H', 265, 175, 30, 30, $style, 'N');

                $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'solid' => '10,20,5,10', 'phase' => 10, 'color' => array(0, 0, 0));

                //PDF::Line(60, 200, 123, 200, $style);

                //PDF::Line(182, 200, 239, 200, $style);

                if ($bg != 'filled') {
                    PDF::Output('hello_world.pdf');
                } else {
                    PDF::Output('hello_world.pdf');
                }

            } // end else cek generate no_sertifikat
        }else{
            return "gagal"; exit;
        }

    }

    public function daftarHadir($id_kegiatan)
    {
        PDF::SetTitle('DAFTAR HADIR');
        PDF::AddPage('P', 'A4');
        PDF::SetFont('helvetica', '', 10);

        $data = array('cari_peserta' => $id_kegiatan);

        $listPeserta = Peserta::getListPesertaByKegiatan($data);

        $dataArray = array();

        foreach ($listPeserta as $key) {
            $newArray = array(
                'nim' => ($key->nim) ? $key->nim : '-',
                'nama' => $key->nama,
            );

            array_push($dataArray, $newArray);
        }

        // -----------------------------------------------------------------------------

        $tbl = '
      <table cellspacing="0" cellpadding="1" border="1">
          <tr>
              <td>
                <table cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td>DAFTAR HADIR PESERTA DAN INSTRUKTUR</td>
                    </tr>
                    <tr>
                        <td>ILECTURE</td>
                    </tr>
                    <tr>
                        <td>' . Helper::tglIndo(date('Y/m/d')) . '</td>
                    </tr>
                </table>
              </td>
              <td>
                <table cellspacing="0" cellpadding="0" border="0">
                    <tr>
                      <td style="text-align:left;">Tanggal Revisi</td>
                      <td style="text-align:left;width:10px;">:</td>
                      <td style="text-align:left;"></td>
                    </tr>
                    <tr>
                      <td style="text-align:left;">Tanggal Berlaku</td>
                      <td style="text-align:left;width:10px;">:</td>
                      <td style="text-align:left;">1 Januari 2018</td>
                    </tr>
                    <tr>
                      <td style="text-align:left;">Kode Dokumen</td>
                      <td style="text-align:left;"width:10px;>:</td>
                      <td style="text-align:left;">FM-RHJ-01012018</td>
                    </tr>
                </table>
              </td>
          </tr>
      </table>';

        PDF::writeHTML($tbl, true, false, false, false, '');

        // table 2
        $maxRow = 20;

        $tbl_head_paraf = '';

        if ($maxRow % 2 == 0) {
            $tbl_head_paraf = '<th colspan="2" width="20%" style="text-align:center;vertical-align:top;background-color:#cccccc;">PARAF</th>';
        } else {
            $tbl_head_paraf = '<th width="20%" style="text-align:center;vertical-align:top;background-color:#cccccc;">PARAF</th>';
        }

        $tbl = '
      <table cellspacing="0" cellpadding="1" border="1" width="100%">
          <tr nobr="true">
              <th width="5%" style="text-align:center;vertical-align:top;background-color:#cccccc;">NO</th>
              <th width="15%" style="text-align:center;vertical-align:top;background-color:#cccccc;">ID</th>
              <th width="30%" style="text-align:center;vertical-align:top;background-color:#cccccc;">NAMA</th>
              <th width="10%" style="text-align:center;vertical-align:top;background-color:#cccccc;">KOMP</th>
              <th width="10%" style="text-align:center;vertical-align:top;background-color:#cccccc;">NILAI</th>
              <th width="10%" style="text-align:center;vertical-align:top;background-color:#cccccc;">GRADE</th>
              ' . $tbl_head_paraf . '
          </tr>';

        for ($i = 1; $i <= $maxRow; $i++) {

            if ($maxRow % 2 == 0) {

                $tbl_ganjil = '';
                $tbl_genap = '';

                if ($i % 2 != 0) {
                    $tbl_ganjil .= '<td width="10%" rowspan="2" style="text-align:left;vertical-align:top;">' . $i . '.</td>';
                }

                if ($i % 2 != 0) {
                    $tbl_genap .= '<td width="10%" rowspan="2" style="text-align:left;vertical-align:top;">' . ($i + 1) . '.</td>';
                }

                $nim = (empty($dataArray[$i - 1]['nim'])) ? '' : $dataArray[$i - 1]['nim'];
                $nama = (empty($dataArray[$i - 1]['nama'])) ? '' : $dataArray[$i - 1]['nama'];

                $tbl .= '<tr nobr="true">
                  <td width="5%" style="text-align:center;vertical-align:top;">' . $i . '</td>
                  <td width="15%" style="text-align:center;vertical-align:top;">' . $nim . '</td>
                  <td width="30%" style="text-align:left;vertical-align:top;">' . ucwords(strtolower($nama)) . '</td>
                  <td width="10%" style="text-align:center;vertical-align:top;"></td>
                  <td width="10%" style="text-align:center;vertical-align:top;"></td>
                  <td width="10%" style="text-align:center;vertical-align:top;"></td>
                  ' . $tbl_ganjil . '
                  ' . $tbl_genap . '
                  </tr>';
            } else {
                $tbl .= '<tr nobr="true">
                  <td width="5%" style="text-align:center;vertical-align:top;">' . $i . '</td>
                  <td width="15%" style="text-align:center;vertical-align:top;">1233372674</td>
                  <td width="30%" style="text-align:center;vertical-align:top;">Cahyo Anggoro Seto</td>
                  <td width="10%" style="text-align:center;vertical-align:top;"></td>
                  <td width="10%" style="text-align:center;vertical-align:top;"></td>
                  <td width="10%" style="text-align:center;vertical-align:top;"></td>
                  <td width="20%" height="30px;" style="text-align:left;vertical-align:top;"></td>
                  </tr>';
            }

        }

        $tbl .= '</table>';

        PDF::writeHTML($tbl, true, false, false, false, '');

        PDF::Output('daftar hadir.pdf', 'I');
    }

    public function tambahBlanko(request $request)
    {

        $path = base_path('resources/views/sertifikat/blanko');

        $data = $request->all();

        $file               = $request->file('file');
        $ext                = $file->getClientOriginalExtension();
        $getNamaFile        = explode('.', $file->getClientOriginalName());
        $data['nama_file']  = $getNamaFile[0];
        $upload             = $file->move($path, "{$getNamaFile[0]}.blade.{$ext}");

        if($upload){
            $query = Sertifikat::tambahBlanko($data);

            if ($query == true) {
                return response()->json(['code' => 200, 'data' => array(), 'message' => 'Berhasil menambahkan data']);
            } else {
                return response()->json(['code' => 500, 'data' => array(), 'message' => 'Data gagal disimpan']);
            }
        }
    }

    public function inputNoSertifikat(request $request)
    {

        $data = $request->all();

        //LANGKAH 1 CEK APAKAH ADA SISWA YANG BELUM DI BERIKAN GRADE
        //JIKA DA TAMPILKAN DATANYA

        $pesertaNULL = Peserta::getListPeserta($data['gradebook']['cari_peserta'], '');

        $countPesertaNull = count($pesertaNULL);

        if ($countPesertaNull > 0) {
            return response()->json(['code' => 500, 'data' => array(), 'message' => 'Ada ' . $countPesertaNull . ' Peserta yang belum di berikan grade']);
        }

        //LANGKAH KE 2, JIKA SEMUA SISWA SUDAH DIBERIKAN GRADE
        //HITUNG JUMLAH SISWA YANG LULUS, KEMUDIAN BUATKAN NO SERTIFIKATNYA

        $pesertaL = Peserta::getListPeserta($data['gradebook']['cari_peserta'], 'L');

        $input_no_sertifikat = Sertifikat::inputNoSertifikat($pesertaL);

        if ($input_no_sertifikat > 0) {
            return response()->json(['code' => 200, 'data' => array('jumlah' => $input_no_sertifikat), 'message' => 'Berhasil menambahkan']);
        } else {
            return response()->json(['code' => 500, 'data' => array(), 'message' => 'Seluruh Data Sudah Diproses']);
        }
    }

    
}
