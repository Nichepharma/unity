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
    $data['request'] = App\CSRequest::findOrFail($id);
    $data['messages'] = App\CSRequest::find($id)->messages;
    return view('support.request', $data);
  }

  public function postMessage(Request $request, $company, $id){
    $message = App\CSMessage::create([
      'csrequest_id' => $id,
      'company_id' => $company,
      'user_id' => 0,
      'name' => $request['user_name'],
      'text' => $request['text']
    ]);
    $message_id = 33;
    if($request['img'] && $request->file('img')->isValid()){
      $storing_name = $message_id . '.' . $request->file('img')->guessClientExtension();
      $request->file('img')->move(storage_path().'/cs/messages', $storing_name);
      return $storing_name;
    }else {
      return "Nope";
    }

}

}
