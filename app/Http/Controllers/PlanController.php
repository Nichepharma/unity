<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use DB;

function arrayGroupBy($arr, $field)
{
    $result = array();
    foreach ($arr as $data) {
        $id = $data->$field;
        if (isset($result[$id])) {
            $result[$id][] = $data;
        } else {
            $result[$id] = array($data);
        }
    }
    return $result;
}

class PlanController extends Controller
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

    public function index($company){
      $data['company'] = $company;
      $native_id = Auth::user()->native_id;

      if(Input::get('area') !== null){
        $regid = Input::get('area');
      }

      if($company == 1){
        $user = DB::connection('tabuk')->select("SELECT *,FullName as name from User where uid={$native_id}");

        if($user[0]->JobTitle == 'System Admin' || $user[0]->JobTitle == 'MARKETING DIRECTOR'){
          //If he's upper supervisor > he's here to see all supervisors
          $data['users'] = DB::connection('tabuk')->select("SELECT *,FullName as name from User where JobTitle='SuperVisor'");
          $data['areas'] = DB::connection('tabuk')->select("SELECT regid as id,region as name from regions");
        }elseif($user[0]->JobTitle == 'COUNTRY MANAGER' || $user[0]->JobTitle == 'SALES MANGER'){
          $data['users'] = DB::connection('tabuk')->select("SELECT *,FullName as name from User where JobTitle='SuperVisor' and region={$user[0]->region}");

        }elseif($user[0]->JobTitle == 'DEPUTY MARKETING MANAGER'){
          if ($user[0]->UserName=='matef'){
            $data['users'] = DB::connection('tabuk')->select("SELECT *,FullName as name from User where JobTitle='SuperVisor' and region in (1,2,3,4,12)");
            $data['areas'] = DB::connection('tabuk')->select("SELECT regid as id,region as name from regions where regid in (1,2,3,4,12)");
          }elseif($user[0]->UserName =='fibrahim'){
            $data['users'] = DB::connection('tabuk')->select("SELECT *,FullName as name from User where JobTitle='SuperVisor' and region in (5,11)");
            $data['areas'] = DB::connection('tabuk')->select("SELECT regid as id,region as name from regions where regid in (5,11)");
          }

        }elseif($user[0]->JobTitle == 'SuperVisor'){
          //but if he's a supervisor, he can see himself
          $data['users'] = $user;
        }

        if (isset($regid)){
          $data['users'] = DB::connection('tabuk')->select("SELECT *,FullName as name from User where JobTitle='SuperVisor' AND region={$regid}");
          $data['regid'] = $regid;
        }
      }elseif($company == 2){
        $user = DB::connection('chiesi')->select("SELECT *,fullname as name from user where uid={$native_id}");
        if($user[0]->Job == 4){
          //If he's upper supervisor > he's here to see all supervisors
          $data['users'] = DB::connection('chiesi')->select("SELECT *,fullname as name from user where Job=3");
        }elseif($user[0]->Job == 3){
          //but if he's a supervisor, he can see himself
          $data['users'] = $user;
        }
      }
      return View('plan/user_list', $data);
    }

    public function plan($company, $uid)
    {
        $data['company'] = $company;
        $data['uid'] = $uid;

        if (Input::has('from')) {
            $data['from'] = Input::get('from');
            $data['to'] = date('Y-m-d', strtotime($data['from']. ' + 7 days'));

            //You Can reduction the next lines of code by spliting only Customer inner select line
            if($company == 1){
              $sql_plans = "SELECT DATE(plan_visit.date) as date , customer.name , customer.customer_type , customer.speciality , customer.customer_id,
                            case
                            when exists (select id from nichepha_unity.kol_visits v where v.customer_id = plan_visit.customer_id and date(plan_visit.date) = date(v.date)) then
                                'success'
                            else
                                'danger'
                        end as visited
                        FROM
                            nichepha_unity.plan_visit plan_visit,
                            (select FullName as name , did as customer_id , '1' as customer_type , speciality from nichepha_tabukdb.Doctors) as customer

                        where user_id = $uid
                    		and plan_visit.company = $company
                            and date(plan_visit.date) between date('{$data['from']}')
                            and date('{$data['to']}')
                            and customer.customer_id = plan_visit.customer_id
                        order by  FIELD(customer.customer_type,3,1,2) , date ,   FIELD(visited ,'success','danger')";

              $sql_visits = "SELECT DATE(visit.date) as date , customer.name , customer.customer_type , customer.speciality , customer.customer_id
                            FROM nichepha_unity.kol_visits visit,
                                (select FullName as name , did as customer_id , '1' as customer_type , speciality from nichepha_tabukdb.Doctors) as customer
                            where visit.user_id = $uid
                            and date(visit.date) between date('{$data['from']}')
                            and date('{$data['to']}')
                            and customer.customer_id = visit.customer_id
                            and not exists (select id from nichepha_unity.plan_visit where plan_visit.company=1 and plan_visit.customer_id = visit.customer_id and date(visit.date) = date(plan_visit.date) and visit.user_id = plan_visit.user_id)";

              $sql_plans_eval = "SELECT DATE(plan_eval.date) as date , rep.name , rep.rep_id,
                            case
                            when exists (select id from nichepha_unity.eval_sessions e where e.rep_id = plan_eval.rep_id and date(e.date) = DATE(plan_eval.date) and plan_eval.user_id = e.supervisor_id) then
                                'success'
                            else
                                'danger'
                            end as visited
                            FROM
                                nichepha_unity.plan_eval plan_eval,
                                (select FullName as name , uid as rep_id from nichepha_tabukdb.User) as rep

                            where user_id = $uid
                        		and plan_eval.company = $company
                                and date(plan_eval.date) between date('{$data['from']}')
                                and date('{$data['to']}')
                                and rep.rep_id = plan_eval.rep_id
                            order by  date ,   FIELD(visited ,'success','danger')";

              $sql_visits_eval = "SELECT DATE(e.date) as date , rep.name , rep.rep_id
                                  FROM nichepha_unity.eval_sessions e,
                    						  (select FullName as name , uid as rep_id from nichepha_tabukdb.User) as rep
                    						  where e.supervisor_id = $uid
                                  and date(e.date) between date('{$data['from']}')
                                  and date('{$data['to']}')
                                  and rep.rep_id = e.rep_id
                    						  and not exists (select id from nichepha_unity.plan_eval where plan_eval.company=$company and plan_eval.rep_id = e.rep_id and date(e.date) = date(plan_eval.date) and plan_eval.user_id = e.supervisor_id)";

            }elseif($company == 2){
              $sql_plans = "SELECT DATE(plan_visit.date) as date , customer.name , customer.customer_type , customer.speciality , customer.customer_id,
                            case
                            when exists (select id from nichepha_unity.kol_visits v where v.customer_id = plan_visit.customer_id and date(plan_visit.date) = date(v.date)) then
                                'success'
                            else
                                'danger'
                        end as visited
                        FROM
                            nichepha_unity.plan_visit plan_visit,
                            (select fullname as name , cid as customer_id , '1' as customer_type , speciality from nichepha_chiesi.doctor) as customer

                        where user_id = $uid
                    		and plan_visit.company = $company
                            and date(plan_visit.date) between date('{$data['from']}')
                            and date('{$data['to']}')
                            and customer.customer_id = plan_visit.customer_id
                        order by  FIELD(customer.customer_type,3,1,2) , date ,   FIELD(visited ,'success','danger')";

              $sql_visits = "SELECT DATE(visit.date) as date , customer.name , customer.customer_type , customer.speciality , customer.customer_id
                            FROM nichepha_unity.kol_visits visit,
                                (select fullname as name , cid as customer_id , '1' as customer_type , speciality from nichepha_chiesi.doctor) as customer
                            where visit.user_id = $uid
                            and date(visit.date) between date('{$data['from']}')
                            and date('{$data['to']}')
                            and customer.customer_id = visit.customer_id
                            and not exists (select id from nichepha_unity.plan_visit where plan_visit.company=$company and plan_visit.customer_id = visit.customer_id and date(visit.date) = date(plan_visit.date) and visit.user_id = plan_visit.user_id)";

            $sql_plans_eval = "SELECT DATE(plan_eval.date) as date , rep.name , rep.rep_id,
                          case
                          when exists (select id from nichepha_unity.eval_sessions e where e.rep_id = plan_eval.rep_id and date(e.date) = DATE(plan_eval.date) and plan_eval.user_id = e.supervisor_id) then
                              'success'
                          else
                              'danger'
                          end as visited
                          FROM
                              nichepha_unity.plan_eval plan_eval,
                              (select fullname as name , uid as rep_id from nichepha_chiesi.user) as rep

                          where user_id = $uid
                      		and plan_eval.company = $company
                              and date(plan_eval.date) between date('{$data['from']}')
                              and date('{$data['to']}')
                              and rep.rep_id = plan_eval.rep_id
                          order by  date ,   FIELD(visited ,'success','danger')";
            $sql_visits_eval = "SELECT DATE(e.date) as date , rep.name , rep.rep_id
                                FROM nichepha_unity.eval_sessions e,
                  						  (select fullname as name , uid as rep_id from nichepha_chiesi.user) as rep
                  						  where e.supervisor_id = $uid
                                            and date(e.date) between date('{$data['from']}')
                                            and date('{$data['to']}')
                                            and rep.rep_id = e.rep_id
                  						  and not exists (select id from nichepha_unity.plan_eval where plan_eval.company=$company and plan_eval.rep_id = e.rep_id and date(e.date) = date(plan_eval.date) and plan_eval.user_id = e.supervisor_id)";
            }

            $plans = DB::select($sql_plans);
            $visits = DB::select($sql_visits);

            $plans_eval = DB::select($sql_plans_eval);
            $visits_eval = DB::select($sql_visits_eval);

            $data['plan'] = arrayGroupBy($plans, 'customer_type');
            $data['visits'] = arrayGroupBy($visits, 'customer_type');
            $data['plan_eval'] = $plans_eval;
            $data['visits_eval'] = $visits_eval;

            return View('plan/user_plan_table', $data);
        }

        if($company == 1){
          $data['userData'] = DB::connection('tabuk')->table('User')->where(['uid' => $uid])->get(['FullName as name', 'uid as native_id']);
        }elseif($company == 2){
          $data['userData'] = DB::connection('chiesi')->table('user')->where(['uid' => $uid])->get(['fullname as name', 'uid as native_id']);
        }
        return View('plan/user_plan', $data);
    }

    public function report($company, $uid){
      $data['company'] = $company;
      $data['uid'] = $uid;
      if (Input::has('from')) {
        $from = $_GET["from"];
        $to = date('Y-m-d', strtotime($from. ' + 5 days'));

        if ($_GET["t"] == 'report'){
          $data['results'] = DB::select("SELECT date,time, 'kol' as type,cid, '' as kol_id,Fullname as name,speciality,'' as v_doctors,'' as v_pharms FROM `kol_visits`
          JOIN nichepha_chiesi.doctor doctor on kol_visits.customer_id = doctor.cid
          where company=2 and user_id={$uid}
          and kol_visits.date between date('{$from}') and date('{$to}')

          UNION ALL

          SELECT date(date) as date,time,'eval' as type,'' as cid ,eval_sessions.id as kol_id, eval_sessions.rep_signature as name,'' as speciality
          ,COALESCE(Sum(Case When q_id=47 Then answer End), 0) as v_doctors
          ,COALESCE(Sum(Case When q_id=48 Then answer End), 0) as v_pharms
          FROM `eval_sessions`
          left join eval_ans on eval_sessions.id=eval_ans.eval_session_id and q_id in (47,48)
          where company=2 and eval_sessions.supervisor_id={$uid}
          and date(eval_sessions.date) between date('{$from}') and date('{$to}')
          GROUP by eval_sessions.id");

        }elseif($_GET["t"] == 'plan'){
          $data['results'] = DB::select("SELECT date,time, 'plan_kol' as type,cid, Fullname as name,speciality FROM `plan_visit`
          JOIN nichepha_chiesi.doctor doctor on plan_visit.customer_id = doctor.cid
          where company=2 and user_id={$uid}
          and plan_visit.date between date('{$from}') and date('{$to}')

          UNION ALL

          SELECT date,time, 'plan_eval' as type,uid, fullname as name,'' as speciality FROM `plan_eval`
          JOIN nichepha_chiesi.user user on plan_eval.rep_id = user.uid
          where company=2 and user_id={$uid}
          and plan_eval.date between date('{$from}') and date('{$to}')");
        }

        //return $data;
        return View('plan/user_report_content', $data);
      }
      if($company == 2){
        $data['userData'] = DB::connection('chiesi')->table('user')->where(['uid' => $uid])->get(['fullname as name', 'uid as native_id']);
      }
      return View('plan/user_report', $data);
    }

}
