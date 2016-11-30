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
    if($company == 1){
      $reqs->where('company_id', 1);
      $position = DB::connection('tabuk')->select("select * from User where uid={$native_id}")[0]->JobTitle;
      if($position == 'Sales Rep'){
        $reqs->where('user_id', Auth::user()->native_id);
      }elseif($position == 'SuperVisor'){
        $visible_users = array(Auth::user()->native_id);

        $visible_reps =  DB::connection('tabuk')->select("SELECT * FROM SuperReps where supid=" . Auth::user()->native_id);
        foreach ($visible_reps as $key => $value) {
          array_push($visible_users, $value->rid);
        }
        $reqs->whereIn('user_id', $visible_users);
      }
    }elseif($company == 2){
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
    }elseif($company == 3){
      $reqs->where('company_id', 3);
      $position = DB::connection('dermazone')->select("SELECT * FROM `user` join role_user on user.id = role_user.user_id WHERE user.id={$native_id}")[0]->role_id;
      if($position == 3){
        $reqs->where('user_id', Auth::user()->native_id);
      }elseif($position == 8 || $position == 9){
        $visible_users = array(Auth::user()->native_id);

        $visible_reps =  DB::connection('dermazone')->select("SELECT * FROM user_supervisor where super_id=" . Auth::user()->native_id);
        foreach ($visible_reps as $key => $value) {
          array_push($visible_users, $value->user_id);
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
