<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use App;
use Auth;

Class SupportController extends Controller{

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index($company){
    $data['company'] = $company;
    $data['reqs'] = App\CSRequest::with('company')->get();
    return view('support.index', $data);
  }

  public function getRequest($company, $id){
    $data['company'] = $company;
    $data['id'] = $id;
    $data['request'] = App\CSRequest::findOrFail($id);
    $data['messages'] = App\CSRequest::find($id)->messages;
    return view('support.request', $data);
  }

  public function postRequest(Request $request, $company){
    $csrequest = new App\CSRequest;
    $csrequest->company_id = $company;
    $csrequest->user_id = Auth::user()->native_id;
    $csrequest->type = $request['lstProblems'];
    $csrequest->status = 'Open';
    $csrequest->save();

    $csmessage = new App\CSMessage;
    $csmessage->name = Auth::user()->name;
    $csmessage->text = '_new_';
    $csrequest->messages()->save($csmessage);

    return redirect("/support/request/{$company}/{$csrequest->id}");
  }

  public function postMessage(Request $request, $company, $id){
    $message = App\CSMessage::create([
      'csrequest_id' => $id,
      'name' => $request['user_name'],
      'text' => $request['text']
    ]);
    if($request['img'] && $request->file('img')->isValid()){
      //Generating a storing name
      $storing_name = $message->id . '.' . $request->file('img')->guessClientExtension();

      //Saving the new file and refering to it on the database
      $request->file('img')->move(storage_path().'/cs/messages', $storing_name);
      $message->file = $storing_name;
      $message->save();
    }
    return redirect("/support/request/{$company}/{$id}");
}

public function api(Request $request, $company, $type){
  return App\CSRequest::all();
}

}
