<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Gradebook extends Model
{
    //
    public static function updateGradebook($data)
    {

        if( $data['value'] >= 85 && $data['value'] <= 100  ){
          $grade  = 'A+';
          $status = 'L';
        }elseif ($data['value'] >= 80 && $data['value'] <= 84 ) {
          $grade  = 'A-';
          $status = 'L';
        }elseif ($data['value'] >= 75 && $data['value'] <= 79) {
          $grade  = 'B+';
          $status = 'L';
        }elseif ($data['value'] >= 70 && $data['value'] <= 74) {
          $grade  = 'B';
          $status = 'L';
        }elseif ($data['value'] >= 60 && $data['value'] <= 69) {
          $grade  = 'B-';
          $status = 'L';
        }else{
          $grade  = 'E';
          $status = 'TL';
        }

        $query = DB::table('tr_peserta')
                        ->where('id', $data['id'])
                        ->update(
                    [
                      'nilai'	=> @$data['value'],
                      'grade'	=> $grade,
                      'status'=> $status
                    ]
            );

        return $result = ($query) ? $query : false;
    }

    public static function getGrade($id)
    {

        $query = DB::table('tr_peserta')
                  ->where('id', $id)
                  ->first();

        return $result = ($query) ? $query : false;
    }
}
