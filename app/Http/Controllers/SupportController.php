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
    $reqs = App\CSRequest::with('company');
    $native_id = Auth::user()->native_id;

    //if not cs >>> where here
    //hint : collect suitable users using array_push
    if($company == 2){
      $reqs->where('company_id', 2);
      $position = DB::connection('chiesi')->select("select * from user where uid={$native_id}")[0]->Job;
      if($position == 1){
        $reqs->where('user_id', Auth::user()->native_id);
      }elseif ($position == 3) {
        $visible_users = array(Auth::user()->native_id);

        $visible_reps =  DB::connection('chiesi')->select("SELECT user.* FROM relations JOIN user on relations.down=user.uid where up=" . Auth::user()->native_id);
        foreach ($visible_reps as $key => $value) {
          array_push($visible_users, $value->uid);
        }
        $reqs->whereIn('user_id', $visible_users);
      }
    }

    $data['reqs'] = $reqs->get();
    //return Auth::user()->native_id;
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
