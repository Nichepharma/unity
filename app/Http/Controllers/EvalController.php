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

        $data['eval_total'] = DB::select('SELECT sum(answer) as c FROM `eval_ans` WHERE eval_session_id=' . $evalID);
        $data['company'] = $company;
        return view('eval', $data);
    }
}
