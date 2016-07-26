<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Auth\SessionGuard;
use DB;

//Included Models
use App\EvalSession;
use App\EvalAnswer;
use App\KolVisit;
use App\PlanVisit;
use App\PlanEval;


function ireturn($str, $msg = ""){
  if($msg != "" ){return "{\"data\":[{\"msg\":\"{$msg}\"}]}";}
  return "{\"data\":" .  $str . "}";
}

function get_reps($company, $sup_id){
    if ($company == 1){
      $response = DB::connection('tabuk')->table('SuperReps')->where(['supid' => $sup_id])->where(['active' => 1])
      ->leftJoin('User', 'SuperReps.rid', '=', 'User.uid')
      ->select('User.uid as id' , 'User.FullName as name')
      ->get();
    }elseif($company == 2){
      $response = DB::connection('chiesi')->table('relations')->where(['up' => $sup_id])
      ->leftJoin('user', 'relations.down', '=', 'user.uid')
      ->select('user.uid as id' , 'user.fullname as name')
      ->get();
    }elseif($company == 3){
      //Didn't Requested yet
      //$response = DB::connection('dermazone')->table('user')->where(['username' => $user, 'pass' => $pass])->get(['id']);
    }else{
      return 0;
    }
    return $response;
}

function get_doctors($company, $uid){
  if ($company == 1){
    $response = DB::connection('tabuk')->table('DoctorRep')->where(['uid' => $uid])
    ->leftJoin('Doctors', 'DoctorRep.did', '=', 'Doctors.did')
    ->select('Doctors.did as id' , 'Doctors.FullName as name', 'Doctors.speciality as spec')
    ->get();
  }elseif($company == 2){
    $response = DB::connection('chiesi')->table('list')->where(['list.uid' => $uid, 'customers.type' => 1])
    ->leftJoin('customers', 'list.cid', '=', 'customers.cid')
    ->leftJoin('doctor', 'list.cid', '=', 'doctor.cid')
    ->select('doctor.cid as id' , 'doctor.Fullname as name', 'doctor.speciality as spec')

    ->get();
  }elseif($company == 3){
    //Didn't Requested yet
    //$response = DB::connection('dermazone')->table('user')->where(['username' => $user, 'pass' => $pass])->get(['id']);
  }else{
    return 0;
  }
  return $response;
}

function get_lastvisit($company, $uid){
  if ($company == 1){
    $sql = "SELECT Doctors.did as id, 1, IFNULL(lv.lastvisit , 'No Visits') as lastvisit
			      from DoctorRep
            JOIN Doctors on DoctorRep.did=Doctors.did
            LEFT JOIN (select did,max(time) as lastvisit from visit where uid={$uid} group by did) lv on Doctors.did=lv.did
            WHERE uid={$uid}";
    $response = DB::connection('tabuk')->select($sql);
  }elseif($company == 2){
    $sql = "SELECT customers.cid as id,customers.type, IFNULL(lv.lastvisit , 'No Visits') as lastvisit
        		from list
            JOIN customers on list.cid=customers.cid
            LEFT JOIN (select cid,max(date) as lastvisit from visit where uid={$uid} group by cid) lv on customers.cid=lv.cid
            WHERE uid={$uid}";
    $response = DB::connection('chiesi')->select($sql);
  }elseif($company == 3){
    //Didn't Requested yet
  }else{
    return 0;
  }
  return $response;
}

class DatalinkController extends Controller
{
    protected $model;
    protected $my_model;
    protected $data;

    public function index(){
      $action = Input::get('action');
      $data_iOS = json_decode(Input::get('data'));

      //Saving Latest Request
      /*
      $myfile = fopen("local/app/controllers/latest.txt", "w");
      $txt = Input::get('action');
      fwrite($myfile, $txt);
      $txt = Input::get('data');
      fwrite($myfile, $txt);
      fclose($myfile);
      */

      switch ($action) {
        case 'login':
          $response = SessionGuard::login_cross($data_iOS->user, $data_iOS->pass);


          if ($response == 0 || !count($response)){
            return ireturn("[{\"id\":\"invalid\"}]");
          }else {
            return "{\"data\":" .  json_encode($response) . ", \"company_id\":" . SessionGuard::ParseCrossMaill($data_iOS->user, "companyID") . "}";
          }

          case 'get_eval_cats':
            $sql = "SELECT id,name
              FROM eval_cats
              Where eval_cats.company_id={$data_iOS->company}";

              $result = DB::select($sql);
              return ireturn(json_encode($result));

          case 'get_eval_qus':
            $sql = "SELECT eval_qus.id,eval_qus.name,eval_cats.id as cat
              FROM eval_qus
              Join eval_cats on eval_qus.cat_id=eval_cats.id
              Where eval_cats.company_id={$data_iOS->company}";

            $result = DB::select($sql);
            return ireturn(json_encode($result));

          case 'get_reps':
            $response = get_reps($data_iOS->company, $data_iOS->sup_id);

            if ($response == 0 || !count($response)){
              return ireturn("[{\"id\":\"invalid\"}]");
            }else {
              return ireturn(json_encode($response));;
            }

          case 'get_doctors':
            $response = get_doctors($data_iOS->company, $data_iOS->uid);

            if ($response == 0 || !count($response)){
              return ireturn("[{\"id\":\"invalid\"}]");
            }else {
              return ireturn(json_encode($response));;
            }

          case 'get_plan_init':
            $response = get_lastvisit($data_iOS->company, $data_iOS->uid);

            if ($response == 0 || !count($response)){
              return ireturn("[{\"id\":\"invalid\"}]");
            }else {
              return ireturn(json_encode($response));;
            }

          case 'insert_plan_visit':
            $user_id = $data_iOS->user_id;

            //Deleting the previous plan
            $date_start = date('Y-m-1', strtotime('-1 month'));
            $date_end = date('Y-m-t', strtotime('+1 month'));
            DB::table('plan_visit')
            ->where('plan_visit.company', $data_iOS->company)
            ->where('plan_visit.user_id', $user_id)
            ->whereBetween('date', array($date_start, $date_end))
            ->delete();

            $customers = explode("|", $data_iOS->customers);
            $dates = explode("|", $data_iOS->dates);
            foreach ($customers as $key => $customer) {
              $plan = new PlanVisit;
              $plan->company = $data_iOS->company;
              $plan->user_id = $user_id;
              $plan->customer_id = $customer;
              $plan->date = $dates[$key];
              $plan->save();
            }

            return ireturn("", "saved");

          case 'insert_product':
          switch ($data_iOS->company) {
              case '1':
                DB::connection('tabuk')->insert("INSERT INTO Product (Product) VALUES ('$data_iOS->pname')");
                break;
              case '2':
                DB::connection('chiesi')->insert("INSERT INTO products (pname) VALUES ('$data_iOS->pname')");
                break;
              case '3':
                DB::connection('dermazone')->insert("INSERT INTO product (name, slides) VALUES ('$data_iOS->pname', 0)");
                break;
            }
            return "Done";

          case 'insert_plan_eval':
            $user_id = $data_iOS->user_id;

            //Deleting the previous plan
            $date_start = date('Y-m-1', strtotime('-1 month'));
            $date_end = date('Y-m-t', strtotime('+1 month'));
            DB::table('plan_eval')
            ->where('plan_eval.company', $data_iOS->company)
            ->where('plan_eval.user_id', $user_id)
            ->whereBetween('date', array($date_start, $date_end))
            ->delete();

            $reps = explode("|", $data_iOS->reps);
            $dates = explode("|", $data_iOS->dates);
            foreach ($reps as $key => $rep) {
              $plan = new PlanEval;
              $plan->company = $data_iOS->company;
              $plan->user_id = $user_id;
              $plan->rep_id = $rep;
              $plan->date = $dates[$key];
              $plan->save();
            }

            return ireturn("", "saved");

          case 'insert_eval':
            $eval_id = EvalSession::create(['company' => $data_iOS->company,
             'supervisor_id' => $data_iOS->sup_id,
             'supervisor_signature' => SessionGuard::ParseCrossMaill($data_iOS->sup_signature, "user"),
             'rep_id' => $data_iOS->rep_id,
             'rep_signature' => $data_iOS->rep_signature,
             'date' => date("Y-m-d H:i:s", strtotime($data_iOS->date))])->id;

             $qus = explode("|", $data_iOS->qus);
             $answers = explode("|", $data_iOS->answers);

             foreach ($answers as $key=>$answer) {
               $evalanswer = new EvalAnswer;
               $evalanswer->eval_session_id = $eval_id;
               $evalanswer->q_id = $qus[$key];
               if (in_array($evalanswer->q_id, array('45','46','47','48'))){
                  if(!ctype_digit($answers[$key])){
                    $answers[$key] = 0;
                  }
               }
               $evalanswer->answer = $answers[$key];
               $evalanswer->save();
             }
             return ireturn("", $data_iOS->date);

           case 'insert_kol_visit':
             $eval_id = KolVisit::create([
              'company' => $data_iOS->company,
              'user_id' => $data_iOS->user_id,
              'customer_id' => $data_iOS->customer_id,
              'rep_id' => $data_iOS->rep_id,
              'comment' => $data_iOS->comment,
              'time' => $data_iOS->time,
              'date' => date("Y-m-d H:i:s", strtotime($data_iOS->date))])->id;
              return ireturn("", $data_iOS->date);

        default:
          return "404";
      }
    }

}
