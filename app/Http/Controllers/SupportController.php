<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;

Class SupportController extends Controller{

  public function __construct()
  {
      $this->middleware('auth');
  }

  public function index($company){
    $data['company'] = $company;
    return view('support.index', $data);
  }

  public function getRequest($company, $id){
    $data['company'] = $company;
    $data['id'] = $id;
    return view('support.request', $data);
  }

}
