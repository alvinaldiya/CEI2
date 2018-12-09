<?php

namespace App\Libraries;

class RapiClass {
	//init variabels

  public function getDataDosen($filter=null) {

    $url = "http://rapi.raharja.me/JSON/qTWfK1EAET9mMJ4=/NamaDosen/".$filter;

    //  Initiate curl
    $ch = curl_init();
    // Disable SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // Will return the response, if false it print the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Set the url
    curl_setopt($ch, CURLOPT_URL, $url);
    // Execute
    $result=curl_exec($ch);
    // Closing
    curl_close($ch);

    $data = json_decode($result);

    $dataArray = array();

    foreach ($data as $value) {
      $newArray = array(
        'value' => $value->NamaDosen.', '.$value->NamaGelar,
        'data' => $value->NID
      );

      array_push($dataArray, $newArray);
    }

    return $dataArray;
  }

  public function getDataMhs($nim) {

    $url = "http://rapi.raharja.me/JSON/qTWfK1EAGJSbLKAcp3qu/NIM/".$nim;

    //  Initiate curl
    $ch = curl_init();
    // Disable SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // Will return the response, if false it print the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Set the url
    curl_setopt($ch, CURLOPT_URL, $url);
    // Execute
    $result=curl_exec($ch);
    // Closing
    curl_close($ch);

    $hasil = json_decode($result);

    if(count($hasil) > 0){
      $data = array(
        'code'    => 200,
        'data'    => $hasil,
        'message' => ''
      );
    }else{
      $data = array(
        'code'    => 500,
        'data'    => array(),
        'message' => 'Data tidak ditemukan'
      );
    }

    return response()->json($data);
  }

}
