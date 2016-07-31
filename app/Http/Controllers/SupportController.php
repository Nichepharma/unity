<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use App;

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
    $data['messages'] = App\CSRequest::find($id)->messages;
    return view('support.request', $data);
  }

}
