<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Helper;
use Illuminate\Database\Eloquent\Model;
use DB;
use Charts;


class UserController extends Controller
{
    public static function getJumlahUsers()
    {
        $userCount = DB::table('users')->get();
        $pesertaCount = DB::table('tr_peserta')->get();
        $kegiatanCount = DB::table('tr_kegiatan')->get();
        $templateCount = DB::table('mst_blanko')->get();



        $GrafikPesertaBulan = DB::table('tr_peserta')
            ->where(DB::raw("(DATE_FORMAT(created_at,'%Y'))"),date('Y')) 
                        ->get();
            

        $GrafikPesertaTahun = DB::table('tr_peserta')->get();
                
        $chartPesertaBulan = Charts::database($GrafikPesertaBulan, 'line', 'highcharts')

                  ->title("Jumlah Sertifikat")
                  ->elementLabel("Total Sertifikat")
                  ->dimensions(700, 500)
                  ->responsive(false)
                  ->groupByMonth(date('Y'), true);

        $chartPesertaTahun = Charts::database($GrafikPesertaTahun, 'line', 'highcharts')

                  ->title("Jumlah Sertifikat")
                  ->elementLabel("Total Sertifikat")
                  ->dimensions(700, 500)
                  ->responsive(false)
                  ->colors(['#ff0000'])
                  ->groupByYear(5);

        return view('layouts.admin.home', compact('userCount', 'pesertaCount', 'kegiatanCount', 'templateCount','chartPesertaBulan', 'chartPesertaTahun'));
    }

    public static function getJumlahSertifikat()
    {
        
    }




    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      
    }

    
    public function destroy($id)
    {
        //
    }
}
