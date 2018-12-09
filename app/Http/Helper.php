<?php
namespace App\Http;

use App\Menu;
use Auth;

class Helper
{

    public static function getMenu($id)
    {
        $menu = Menu::getMenu($id);
        return $menu;
    }

    public static function getRoleID()
    {
        //$role_user = Auth::user()->getIdRole();
        $role_user = Auth::user()['id_mst_role'];
        return $role_user;
    }

    public static function dateHUmanToMySqlDate($date)
    {
        $string = explode('/', $date);
        $date = $string[2] . "-" . $string[1] . "-" . $string[0];

        return $date;
    }

    public static function tglIndo($date)
    {
        $string = explode('/', $date);
        $date = $string[2] . " " . Helper::monthIndonesia($string[1]) . " " . $string[0];

        return $date;
    }

    public static function toRomawi($no)
    {

        $data = array(
            '01' => 'I',
            '02' => 'II',
            '03' => 'III',
            '04' => 'IV',
            '05' => 'V',
            '06' => 'VI',
            '07' => 'VII',
            '08' => 'VIII',
            '09' => 'IX',
            '10' => 'X',
            '11' => 'XI',
            '12' => 'XII',
        );

        foreach ($data as $key => $value) {
            if ($no == $key) {
                $hasil = $value;
                break;
            }
        }

        return $hasil;
    }

    public static function tglCetak($date)
    {

        $string = explode('-', $date);
        $hasil = $string[2] . " " . self::monthIndonesia($string[1]) . " " . $string[0];

        return $hasil;
    }

    public static function monthIndonesia($val)
    {

        $bulan = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        );

        foreach ($bulan as $key => $value) {
            if ($val == $key) {
                $hasil = $value;
                break;
            }
        }

        return $hasil;
    }

}
