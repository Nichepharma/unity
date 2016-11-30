<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\EvalSession;
use DB;

class EvalController extends Controller
{
  /**
  * Create a new controller instance.
  *
  * @return void
  */
  public function __construct()
  {
    $this->middleware('auth');
  }

  /**
  * Show the application dashboard.
  *
  * @return \Illuminate\Http\Response
  */
  public function index($company, $uid)
  {
    $data['eval_sessions'] = DB::table('eval_sessions')->where(['company' => $company, 'rep_id' => $uid])
    //->leftJoin('User', 'SuperReps.rid', '=', 'User.uid')
    //->select('User.uid as id' , 'User.FullName as name')
    ->get();
    $data['company'] = $company;
    $data['uid'] = $uid;
    return view('evals', $data);
  }

  public function getEval($company, $evalID)
  {
    $data['eval_ans'] = DB::table('eval_ans')->where(['eval_session_id' => $evalID])
    ->leftJoin('eval_qus', 'eval_ans.q_id', '=', 'eval_qus.id')
    ->leftJoin('eval_cats', 'eval_qus.cat_id', '=', 'eval_cats.id')
    ->select('eval_cats.name as cat' , 'eval_qus.name' , 'eval_ans.answer')
    ->orderBy('eval_qus.cat_id', 'asc')
    ->orderBy('eval_qus.id', 'asc')

    ->get();

    $data['eval_total'] = DB::select('SELECT sum(answer) as c
    FROM `eval_ans`
    Join eval_qus on eval_ans.q_id= eval_qus.id and eval_qus.cat_id not in (8,9)
    WHERE eval_session_id=' . $evalID);
    $data['company'] = $company;
    return view('eval', $data);
  }

  public function getEvalCharts($company){
    $data['company'] = $company;

    if(isset($_GET['region']) && isset($_GET['month']) && isset($_GET['year'])){
      $sql = "SELECT eval_qus.cat_id, eval_cats.name , COUNT(answer) as totals,
      Sum(Case When answer = 3 Then 1 Else 0 End) as goods,
      Sum(Case When answer = 1 Then 1 Else 0 End) as nis

      FROM `eval_sessions`
      JOIN nichepha_tabukdb.User user on eval_sessions.rep_id=user.uid
      join eval_ans on eval_sessions.id = eval_ans.eval_session_id
      join eval_qus on eval_ans.q_id = eval_qus.id
      JOIN eval_cats on eval_qus.cat_id = eval_cats.id
      where company=1 and user.region={$_GET['region']}
      and month(date) = {$_GET['month']}
      and year(date) = {$_GET['year']}
      and cat_id < 8
      GROUP BY cat_id";
      $data['cats_data'] = DB::select($sql);
    }


    $data['countries'] = DB::connection('tabuk')->select("SELECT * from regions where regid>0");
    return view('evalCharts', $data);
  }
}
