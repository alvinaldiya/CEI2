<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Grafik;
use Charts;
use DB;


class GrafikController extends Controller
{
    //

    public static function JumlahSertifikat()
    {
    	$Grafikpeserta = DB::table('tr_peserta')
	        ->where(DB::raw("(DATE_FORMAT(created_at,'%Y'))"),date('Y'))
	                    ->get();
	    	
        $chartPeserta = Charts::database($Grafikpeserta, 'line', 'highcharts')

                  ->title("Jumlah Sertifikat")
                  ->elementLabel("Total Sertifikat")
                  ->dimensions(1000, 500)
                  ->responsive(true)
                  ->groupByMonth(date('Y'), true);

        return view('layouts.admin.home', compact('chartPeserta'));
    }
}
