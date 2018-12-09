<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Menu;
use Auth;
use Charts;
use DB;

class HomeController extends Controller
{
     public function index()
    {
        return view('layouts.admin.home');
    }

    

    public function __construct()
    {
        $this->middleware('auth');
    }

   
     
}
