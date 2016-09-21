<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use App;
use Auth;

Class ListController extends Controller{

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index($company){
    $data['company'] = $company;
    $lists = App\ListModel::with('company');
    $data['lists'] = $lists->get();
    return view('list.index', $data);
  }

  public function getRequest($company, $id){
    $data['company'] = $company;
    $data['id'] = $id;
    $data['request'] = App\CSRequest::findOrFail($id);
    $data['messages'] = App\CSRequest::find($id)->messages;
    return view('support.request', $data);
  }

  public function postList(Request $request, $company){
    $list = App\ListModel::create([
      'company_id' => $company,
      'user_id' => Auth::user()->native_id,
      'type' => 'Doctors',
      'status' => ''
    ]);

    if(!$request['lst'] || !$request->file('lst')->isValid() || $request->file('lst')->guessClientExtension() != 'csv'){
      $list->status = 'Error : Please upload a valid CSV file';
      $list->save();
    }

    //if there's no problem
    if($list->status == ''){
      $storing_name = $list->id . '.' . $request->file('lst')->guessClientExtension();
      $request->file('lst')->move(storage_path().'/lists', $storing_name);
      $list->status = 'CSV file was uploaded';
      $list->save();
    }
    return redirect("/list/{$company}");

  }
}
