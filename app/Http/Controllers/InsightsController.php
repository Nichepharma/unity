<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\EvalSession;
use DB;
use DateInterval;
use DatePeriod;
use DateTime;
use date;

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

class InsightsController extends Controller
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
        return "Index";
    }

    public function getAccumlativeDetails(Request $request, $company, $uid)
    {
        if ($request->Input('type')) {
            $visited = [];
            switch ($request->Input('type')) {
              case 'doctors':
                  if($company == 1){
                    $sqlv = "SELECT doctor.did AS customer_id, FullName AS  `name` ,  `speciality` , TPMCClass as `grade`,
                                          `visit`.`rep_id` as rep_id,
                                          `visit`.`time` as `time`,
                                          MONTH (`visit`.`date`) as month, DAY (`visit`.`date`) as day,
                    WEEK(`visit`.`date`, 5) - WEEK(DATE_SUB(`visit`.`date`, INTERVAL DAYOFMONTH(`visit`.`date`) - 1 DAY), 5) + 1 as week
                    FROM nichepha_tabukdb.`Doctors` doctor
                    JOIN nichepha_unity.`kol_visits` visit ON  `visit`.`customer_id` =  `doctor`.`did`
                    WHERE `visit`.`company`=$company and `visit`.`user_id`=$uid
                    AND DATE(`visit`.`date`) BETWEEN '" . $request->Input('datefrom') . "' and '" . $request->Input('dateto') . "'";

                  }elseif($company == 2){
                      if(isset($_GET['isRep'])){
                        $sqlv = "SELECT doctor.cid as customer_id, Fullname as `name` ,  `speciality` ,  `grade`,
                                              ' ' as `time`,
                                              MONTH (`visit`.`date`) as month, DAY (`visit`.`date`) as day,
                        WEEK(`visit`.`date`, 5) - WEEK(DATE_SUB(`visit`.`date`, INTERVAL DAYOFMONTH(`visit`.`date`) - 1 DAY), 5) + 1 as week
                        FROM nichepha_chiesi.`doctor` doctor
                        JOIN nichepha_chiesi.`visit` visit
                        ON `visit`.`cid`=`doctor`.`cid`
                        WHERE `visit`.`uid`=$uid
                        AND DATE(`visit`.`date`) BETWEEN '{$request->Input('datefrom')}' and '{$request->Input('dateto')}'";
                      }else{
                        $sqlv = "SELECT doctor.cid as customer_id, Fullname as `name` ,  `speciality` ,  `grade`,
                                              `visit`.`time` as `time`,
                                              `visit`.`rep_id` as rep_id,
                                              MONTH (`visit`.`date`) as month, DAY (`visit`.`date`) as day,
                        WEEK(`visit`.`date`, 5) - WEEK(DATE_SUB(`visit`.`date`, INTERVAL DAYOFMONTH(`visit`.`date`) - 1 DAY), 5) + 1 as week
                        FROM nichepha_chiesi.`doctor` doctor
                        JOIN nichepha_unity.`kol_visits` visit
                        ON `visit`.`customer_id`=`doctor`.`cid`
                        WHERE `visit`.`company`=$company and `visit`.`user_id`=$uid
                        AND DATE(`visit`.`date`) BETWEEN '{$request->Input('datefrom')}' and '{$request->Input('dateto')}'";
                      }
                }
                  $visits = DB::select($sqlv);
                  $visits = arrayGroupBy($visits, 'customer_id'); // group by customer to prevent repeating
                  foreach($visits as $customerId=>$customerVisits){
                    $visitsDetails = [];
                    foreach($customerVisits as $visit){
                        $text_before = '';
                        $text_after = '';
                        if(isset($visit->rep_id) && $visit->rep_id != 0){
                          $text_before = '((';
                          $text_after = '))';
                        }
                        $visitsDetails[] = array(
                            'month'=>$visit->month,
                            'day'=>$visit->day,
                            'week'=>$visit->week,
                            'time'=>$visit->time,
                            'text_before' => $text_before,
                            'text_after' => $text_after
                        );
                    }
                    $visited[] = array(
                        'customer_id'=>$customerId,
                        'name'=>$customerVisits[0]->name,
                        'grade'=>$customerVisits[0]->grade,
                        'speciality'=>$customerVisits[0]->speciality,
                        'visits'=>$visitsDetails
                    );
                  }

                  if($company == 1){
                        $sql = "SELECT doctor.did AS customer_id, FullName AS  `name` ,  `speciality` , TPMCClass as `grade`
                                FROM nichepha_tabukdb.`Doctors` doctor
                                JOIN nichepha_tabukdb.`DoctorRep` ON `doctor`.`did` =  `DoctorRep`.`did`
                                WHERE  `DoctorRep`.`uid` =$uid
                                AND doctor.did NOT IN (SELECT DISTINCT customer_id from nichepha_unity.kol_visits visit
                                                          WHERE company=$company and user_id=$uid
                                                          And DATE(`visit`.`date`) BETWEEN '" . $request->Input('datefrom') . "' and '" . $request->Input('dateto') . "')";
                  }elseif($company == 2){
                        if(isset($_GET['isRep'])){
                          $sql = "SELECT DISTINCT cid from nichepha_chiesi.visit visit
                          WHERE uid={$uid}
                          And DATE(`visit`.`date`) BETWEEN '{$request->Input('datefrom')}' and '{$request->Input('dateto')}'";

                          $visited_to_trim = DB::select($sql);

                          $visited_to_trim_array = '';
                          foreach ($visited_to_trim as $key => $value) {
                            $visited_to_trim_array .= $value->cid . ",";
                          }
                          $visited_to_trim_array = '(' . rtrim($visited_to_trim_array, ',') . ')';

                          $sql = "SELECT doctor.cid as customer_id, Fullname as `name` ,  `speciality` ,  `grade`
                                  FROM nichepha_chiesi.`doctor` doctor
                                  JOIN nichepha_chiesi.`list` ON `doctor`.`cid` =  `list`.`cid`
                                  WHERE  `list`.`uid` =$uid
                                  AND doctor.cid NOT IN {$visited_to_trim_array}";
                          }else{
                            $sql = "SELECT doctor.cid as customer_id, Fullname as `name` ,  `speciality` ,  `grade`
                                    FROM nichepha_chiesi.`doctor` doctor
                                    JOIN nichepha_chiesi.`list` ON `doctor`.`cid` =  `list`.`cid`
                                    WHERE  `list`.`uid` =$uid
                                    AND doctor.cid NOT IN (SELECT DISTINCT customer_id from nichepha_unity.kol_visits visit
                                                              WHERE company=$company and user_id=$uid
                                                              And DATE(`visit`.`date`) BETWEEN '" . $request->Input('datefrom') . "' and '" . $request->Input('dateto') . "')";
                          }
                  }
                  $nonVisited = DB::select($sql);
                  $data['doctors'] = array_merge($visited,$nonVisited);

                  //$sqlv = "";
                  $data['sql'] = $sql;

                  return $data;

              case "types":
                $sql = "SELECT count(id) as total,
                sum(case when rep_id=0 then 1 end) as v_single,
                sum(case when not rep_id=0 then 1 end) as v_double
                FROM `kol_visits`
                where user_id=22";
                $data['types'] = DB::select($sql);
                return $data;

              case "evals":
                  if($company == 1 || $company == 2){
                      $sqlv = "SELECT rep_id, rep_signature as `name` ,
                                            MONTH (`date`) as month, DAY (`date`) as day,
                      WEEK(`date`, 5) - WEEK(DATE_SUB(`date`, INTERVAL DAYOFMONTH(`date`) - 1 DAY), 5) + 1 as week
                      FROM eval_sessions
                      WHERE `company`=$company and `supervisor_id`=$uid
                      AND DATE(`date`) BETWEEN '" . $request->Input('datefrom') . "' and '" . $request->Input('dateto') . "'";
                }
                $visits = DB::select($sqlv);
                $visits = arrayGroupBy($visits, 'rep_id'); // group by customer to prevent repeating
                foreach($visits as $repId=>$repVisits){
                  $visitsDetails = [];
                  foreach($repVisits as $visit){
                      $visitsDetails[] = array(
                          'month'=>$visit->month,
                          'day'=>$visit->day,
                          'week'=>$visit->week,
                      );
                  }
                  $visited[] = array(
                      'rep_id'=>$repId,
                      'name'=>$repVisits[0]->name,
                      'visits'=>$visitsDetails
                  );
                }
                $data['evals'] = $visited;
                return $data;

                case "samples":
                    if($company == 2){
                        $sql_samples_products = "SELECT products.pname, sum(visitSample.number) as samples
                        from nichepha_chiesi.visit visit
                        join nichepha_chiesi.visitSample visitSample on visit.vid = visitSample.vid
                        join nichepha_chiesi.products products on visitSample.pid = products.pid
                        Where DATE(nichepha_chiesi.visit.date) BETWEEN ' {$request->Input('datefrom')} ' and ' {$request->Input('dateto')} '
                        GROUP by visitSample.pid";

                        $sql_samples_products_customers = "SELECT products.pname,customertypes.type, sum(visitSample.number) as samples
                        from nichepha_chiesi.visit visit
                        join nichepha_chiesi.visitSample visitSample on visit.vid = visitSample.vid
                        join nichepha_chiesi.customers customers on visit.cid = customers.cid
                        join nichepha_chiesi.customertypes customertypes on customers.type = customertypes.typeid
                        join nichepha_chiesi.products products on visitSample.pid = products.pid
                        Where DATE(nichepha_chiesi.visit.date) BETWEEN ' {$request->Input('datefrom')} ' and ' {$request->Input('dateto')} '
                        GROUP by visitSample.pid,customertypes.typeid";

                        $sql_samples_sp = "SELECT v.pid,v.pname,doctor.speciality, sum(v.number) as samples from nichepha_chiesi.doctor
                        join (SELECT visit.vid,visit.cid,visitSample.number,visitSample.pid as pid,products.pname as pname
                        from nichepha_chiesi.visit
                        join nichepha_chiesi.visitSample on visit.vid = visitSample.vid
                        join nichepha_chiesi.products products on visitSample.pid = products.pid
                        WHERE DATE(nichepha_chiesi.visit.date) BETWEEN ' {$request->Input('datefrom')} ' and ' {$request->Input('dateto')} ') v on doctor.cid = v.cid
                        GROUP BY v.pid, doctor.speciality";

                        $sql_samples_rep = "SELECT user.fullname, sum(number) as samples
                        FROM nichepha_chiesi.visit visit
                        JOIN nichepha_chiesi.visitSample visitSample on visit.vid=visitSample.vid
                        JOIN nichepha_chiesi.user user on visit.uid = user.uid
                        WHERE DATE(visit.date) BETWEEN ' {$request->Input('datefrom')} ' and ' {$request->Input('dateto')} '
                        AND number>0
                        GROUP by visit.uid
                        ORDER BY samples DESC";
                  }
                  $samples_products = DB::select($sql_samples_products);
                  $samples_products_customers = DB::select($sql_samples_products_customers);
                  $samples_sp = DB::select($sql_samples_sp);
                  $samples_rep = DB::select($sql_samples_rep);

                  $data['samples'] = $samples_products;
                  $data['samples_customers'] = $samples_products_customers;
                  $data['samples_sp'] = $samples_sp;
                  $data['samples_rep'] = $samples_rep;
                  //$data['sql'] = $sqlv;
                  return $data;

                case "teams":
                  //For Teams table
                  $data['teams_days'][0] = $request->Input('dateto');
                  $data['teams_days'][1] = date('Y-m-d', strtotime($data['teams_days'][0] .' -1 day'));
                  $data['teams_days'][2] = date('Y-m-d', strtotime($data['teams_days'][1] .' -1 day'));
                  $data['teams_days'][3] = date('Y-m-d', strtotime($data['teams_days'][2] .' -1 day'));
                  $data['teams_days'][4] = date('Y-m-d', strtotime($data['teams_days'][3] .' -1 day'));
                  $data['teams_days'][5] = date('Y-m-d', strtotime($data['teams_days'][4] .' -1 day'));
                  $data['teams_days'][6] = date('Y-m-d', strtotime($data['teams_days'][5] .' -1 day'));

                  //Next Day, just to solve last day problem
                  $data['teams_days'][7] = date('Y-m-d', strtotime($data['teams_days'][0] .' +1 day'));

                  if($company == 2){
			
			if ($uid==0){
                        	$uid='relations.up and user.uid not in (0,21,2,4,43,75,74)';
                      	}
			
                    $sql = "SELECT user.fullname,
                            replace(replace(replace(customers.type, 1, 'Private Market'), 2, 'Pharmacies'), 3, 'Hospital') as type,
                            count(visit.vid) as total,
                            sum(case when date(visit.date) = '{$data['teams_days'][6]}' then 1 else 0 end) day1,
                            sum(case when date(visit.date) = '{$data['teams_days'][5]}' then 1 else 0 end) day2,
                            sum(case when date(visit.date) = '{$data['teams_days'][4]}' then 1 else 0 end) day3,
                            sum(case when date(visit.date) = '{$data['teams_days'][3]}' then 1 else 0 end) day4,
                            sum(case when date(visit.date) = '{$data['teams_days'][2]}' then 1 else 0 end) day5,
                            sum(case when date(visit.date) = '{$data['teams_days'][1]}' then 1 else 0 end) day6,
                            sum(case when date(visit.date) = '{$data['teams_days'][0]}' then 1 else 0 end) day7
                            FROM nichepha_chiesi.relations relations
                            join nichepha_chiesi.user on relations.down = user.uid
                            join (select * from nichepha_chiesi.visit GROUP BY uid,cid,date(date)) visit on user.uid = visit.uid
                            join nichepha_chiesi.customers on visit.cid = customers.cid
                            where relations.up=$uid
                            AND (
                              date between '{$data['teams_days'][6]}' and '{$data['teams_days'][7]}'
                              )
                            GROUP BY user.uid, customers.type";
                    }
                  $teams = DB::select($sql);
                  $data['teams'] = $teams;
                  $data['sql'] = $sql;
                  return $data;

                case "private_list_ana":
                  if($company == 2){
                    $sql = "SELECT UserList.fullname,
                    COUNT(UserList.fullname) as s_total,
                    sum(case when doctor.speciality = 'ORS' and doctor.grade='A' then 1 else 0 end) s_ors_a,
                    sum(case when doctor.speciality = 'ORS' and doctor.grade='B' then 1 else 0 end) s_ors_b,
                    sum(case when doctor.speciality = 'ORS' and doctor.grade='C' then 1 else 0 end) s_ors_c,

                    sum(case when doctor.speciality = 'GP'  and doctor.grade='A' then 1 else 0 end) s_gp_a,
                    sum(case when doctor.speciality = 'GP'  and doctor.grade='B' then 1 else 0 end) s_gp_b,
                    sum(case when doctor.speciality = 'GP'  and doctor.grade='C' then 1 else 0 end) s_gp_c,

                    sum(case when doctor.speciality = 'IM'  and doctor.grade='A' then 1 else 0 end) s_im_a,
                    sum(case when doctor.speciality = 'IM'  and doctor.grade='B' then 1 else 0 end) s_im_b,
                    sum(case when doctor.speciality = 'IM'  and doctor.grade='C' then 1 else 0 end) s_im_c,

                    sum(case when doctor.speciality = 'S'  and doctor.grade='A' then 1 else 0 end) s_s_a,
                    sum(case when doctor.speciality = 'S'  and doctor.grade='B' then 1 else 0 end) s_s_b,
                    sum(case when doctor.speciality = 'S'  and doctor.grade='C' then 1 else 0 end) s_s_c,

                    sum(case when doctor.speciality = 'U'  and doctor.grade='A' then 1 else 0 end) s_u_a,
                    sum(case when doctor.speciality = 'U'  and doctor.grade='B' then 1 else 0 end) s_u_b,
                    sum(case when doctor.speciality = 'U'  and doctor.grade='C' then 1 else 0 end) s_u_c,

                    sum(case when doctor.speciality = 'N'  and doctor.grade='A' then 1 else 0 end) s_n_a,
                    sum(case when doctor.speciality = 'N'  and doctor.grade='B' then 1 else 0 end) s_n_b,
                    sum(case when doctor.speciality = 'N'  and doctor.grade='C' then 1 else 0 end) s_n_c,

                    sum(case when doctor.speciality = 'ON'  and doctor.grade='A' then 1 else 0 end) s_on_a,
                    sum(case when doctor.speciality = 'ON'  and doctor.grade='B' then 1 else 0 end) s_on_b,
                    sum(case when doctor.speciality = 'ON'  and doctor.grade='C' then 1 else 0 end) s_on_c,

                    sum(case when doctor.speciality = 'ENT'  and doctor.grade='A' then 1 else 0 end) s_ent_a,
                    sum(case when doctor.speciality = 'ENT'  and doctor.grade='B' then 1 else 0 end) s_ent_b,
                    sum(case when doctor.speciality = 'ENT'  and doctor.grade='C' then 1 else 0 end) s_ent_c,

                    sum(case when doctor.speciality = 'DEN'  and doctor.grade='A' then 1 else 0 end) s_den_a,
                    sum(case when doctor.speciality = 'DEN'  and doctor.grade='B' then 1 else 0 end) s_den_b,
                    sum(case when doctor.speciality = 'DEN'  and doctor.grade='C' then 1 else 0 end) s_den_c,

                    sum(case when doctor.speciality = 'GE'  and doctor.grade='A' then 1 else 0 end) s_ge_a,
                    sum(case when doctor.speciality = 'GE'  and doctor.grade='B' then 1 else 0 end) s_ge_b,
                    sum(case when doctor.speciality = 'GE'  and doctor.grade='C' then 1 else 0 end) s_ge_c,

                    sum(case when doctor.speciality = 'PUD'  and doctor.grade='A' then 1 else 0 end) s_pud_a,
                    sum(case when doctor.speciality = 'PUD'  and doctor.grade='B' then 1 else 0 end) s_pud_b,
                    sum(case when doctor.speciality = 'PUD'  and doctor.grade='C' then 1 else 0 end) s_pud_c,

                    sum(case when doctor.speciality = 'GYN'  and doctor.grade='A' then 1 else 0 end) s_gyn_a,
                    sum(case when doctor.speciality = 'GYN'  and doctor.grade='B' then 1 else 0 end) s_gyn_b,
                    sum(case when doctor.speciality = 'GYN'  and doctor.grade='C' then 1 else 0 end) s_gyn_c,

                    sum(case when doctor.speciality = 'RHU'  and doctor.grade='A' then 1 else 0 end) s_rhu_a,
                    sum(case when doctor.speciality = 'RHU'  and doctor.grade='B' then 1 else 0 end) s_rhu_b,
                    sum(case when doctor.speciality = 'RHU'  and doctor.grade='C' then 1 else 0 end) s_rhu_c,

                    sum(case when doctor.speciality = 'ID'  and doctor.grade='A' then 1 else 0 end) s_id_a,
                    sum(case when doctor.speciality = 'ID'  and doctor.grade='B' then 1 else 0 end) s_id_b,
                    sum(case when doctor.speciality = 'ID'  and doctor.grade='C' then 1 else 0 end) s_id_c,
                    sum(case when
                      doctor.speciality = 'ORS' or
                      doctor.speciality = 'GP' or
                      doctor.speciality = 'IM' or
                      doctor.speciality = 'S' or
                      doctor.speciality = 'U' or
                      doctor.speciality = 'N' or
                      doctor.speciality = 'ON' or
                      doctor.speciality = 'ENT' or
                      doctor.speciality = 'DEN' or
                      doctor.speciality = 'GE' or
                      doctor.speciality = 'PUD' or
                      doctor.speciality = 'GYN' or
                      doctor.speciality = 'RHU' or
                      doctor.speciality = 'ID'
                       then 0 else 1 end) s_others
                    FROM
                    (SELECT user.fullname,list.cid
                    from user
                    join relations ON user.uid = relations.down
                    join list on user.uid = list.uid
                    JOIN customers on list.cid=customers.cid and customers.type=1
                    WHERE user.Job = '1'
                    And user.uid not in (4,2,43,21)) UserList
                    JOIN doctor on UserList.cid = doctor.cid
                    GROUP by UserList.fullname
                    Order By UserList.fullname";
                    }
                  $teams = DB::connection('chiesi')->select($sql);
                  $data['private_list_ana'] = $teams;
                  $data['sql'] = $sql;
                  return $data;

                case "private_list_ana_byarea":
                  if($company == 2){
                    $sql = "SELECT UserList.area,
                    COUNT(UserList.area) as s_total,
                    sum(case when doctor.speciality = 'ORS' and doctor.grade='A' then 1 else 0 end) s_ors_a,
                    sum(case when doctor.speciality = 'ORS' and doctor.grade='B' then 1 else 0 end) s_ors_b,
                    sum(case when doctor.speciality = 'ORS' and doctor.grade='C' then 1 else 0 end) s_ors_c,

                    sum(case when doctor.speciality = 'GP'  and doctor.grade='A' then 1 else 0 end) s_gp_a,
                    sum(case when doctor.speciality = 'GP'  and doctor.grade='B' then 1 else 0 end) s_gp_b,
                    sum(case when doctor.speciality = 'GP'  and doctor.grade='C' then 1 else 0 end) s_gp_c,

                    sum(case when doctor.speciality = 'IM'  and doctor.grade='A' then 1 else 0 end) s_im_a,
                    sum(case when doctor.speciality = 'IM'  and doctor.grade='B' then 1 else 0 end) s_im_b,
                    sum(case when doctor.speciality = 'IM'  and doctor.grade='C' then 1 else 0 end) s_im_c,

                    sum(case when doctor.speciality = 'S'  and doctor.grade='A' then 1 else 0 end) s_s_a,
                    sum(case when doctor.speciality = 'S'  and doctor.grade='B' then 1 else 0 end) s_s_b,
                    sum(case when doctor.speciality = 'S'  and doctor.grade='C' then 1 else 0 end) s_s_c,

                    sum(case when doctor.speciality = 'U'  and doctor.grade='A' then 1 else 0 end) s_u_a,
                    sum(case when doctor.speciality = 'U'  and doctor.grade='B' then 1 else 0 end) s_u_b,
                    sum(case when doctor.speciality = 'U'  and doctor.grade='C' then 1 else 0 end) s_u_c,

                    sum(case when doctor.speciality = 'N'  and doctor.grade='A' then 1 else 0 end) s_n_a,
                    sum(case when doctor.speciality = 'N'  and doctor.grade='B' then 1 else 0 end) s_n_b,
                    sum(case when doctor.speciality = 'N'  and doctor.grade='C' then 1 else 0 end) s_n_c,

                    sum(case when doctor.speciality = 'ON'  and doctor.grade='A' then 1 else 0 end) s_on_a,
                    sum(case when doctor.speciality = 'ON'  and doctor.grade='B' then 1 else 0 end) s_on_b,
                    sum(case when doctor.speciality = 'ON'  and doctor.grade='C' then 1 else 0 end) s_on_c,

                    sum(case when doctor.speciality = 'ENT'  and doctor.grade='A' then 1 else 0 end) s_ent_a,
                    sum(case when doctor.speciality = 'ENT'  and doctor.grade='B' then 1 else 0 end) s_ent_b,
                    sum(case when doctor.speciality = 'ENT'  and doctor.grade='C' then 1 else 0 end) s_ent_c,

                    sum(case when doctor.speciality = 'DEN'  and doctor.grade='A' then 1 else 0 end) s_den_a,
                    sum(case when doctor.speciality = 'DEN'  and doctor.grade='B' then 1 else 0 end) s_den_b,
                    sum(case when doctor.speciality = 'DEN'  and doctor.grade='C' then 1 else 0 end) s_den_c,

                    sum(case when doctor.speciality = 'GE'  and doctor.grade='A' then 1 else 0 end) s_ge_a,
                    sum(case when doctor.speciality = 'GE'  and doctor.grade='B' then 1 else 0 end) s_ge_b,
                    sum(case when doctor.speciality = 'GE'  and doctor.grade='C' then 1 else 0 end) s_ge_c,

                    sum(case when doctor.speciality = 'PUD'  and doctor.grade='A' then 1 else 0 end) s_pud_a,
                    sum(case when doctor.speciality = 'PUD'  and doctor.grade='B' then 1 else 0 end) s_pud_b,
                    sum(case when doctor.speciality = 'PUD'  and doctor.grade='C' then 1 else 0 end) s_pud_c,

                    sum(case when doctor.speciality = 'GYN'  and doctor.grade='A' then 1 else 0 end) s_gyn_a,
                    sum(case when doctor.speciality = 'GYN'  and doctor.grade='B' then 1 else 0 end) s_gyn_b,
                    sum(case when doctor.speciality = 'GYN'  and doctor.grade='C' then 1 else 0 end) s_gyn_c,

                    sum(case when doctor.speciality = 'RHU'  and doctor.grade='A' then 1 else 0 end) s_rhu_a,
                    sum(case when doctor.speciality = 'RHU'  and doctor.grade='B' then 1 else 0 end) s_rhu_b,
                    sum(case when doctor.speciality = 'RHU'  and doctor.grade='C' then 1 else 0 end) s_rhu_c,

                    sum(case when doctor.speciality = 'ID'  and doctor.grade='A' then 1 else 0 end) s_id_a,
                    sum(case when doctor.speciality = 'ID'  and doctor.grade='B' then 1 else 0 end) s_id_b,
                    sum(case when doctor.speciality = 'ID'  and doctor.grade='C' then 1 else 0 end) s_id_c,
                    sum(case when
                      doctor.speciality = 'ORS' or
                      doctor.speciality = 'GP' or
                      doctor.speciality = 'IM' or
                      doctor.speciality = 'S' or
                      doctor.speciality = 'U' or
                      doctor.speciality = 'N' or
                      doctor.speciality = 'ON' or
                      doctor.speciality = 'ENT' or
                      doctor.speciality = 'DEN' or
                      doctor.speciality = 'GE' or
                      doctor.speciality = 'PUD' or
                      doctor.speciality = 'GYN' or
                      doctor.speciality = 'RHU' or
                      doctor.speciality = 'ID'
                       then 0 else 1 end) s_others
                    FROM
                    (SELECT area.name as area,list.cid
                    from user
                    join relations ON user.uid = relations.down
                    join list on user.uid = list.uid
                    JOIN customers on list.cid=customers.cid and customers.type=1
                    JOIN area on customers.area = area.areaid
                    WHERE user.Job = '1'
                    And user.uid not in (4,2,43,21)) UserList
                    JOIN doctor on UserList.cid = doctor.cid
                    GROUP by UserList.area
                    Order By UserList.area";
                    }
                  $teams = DB::connection('chiesi')->select($sql);
                  $data['private_list_ana_byarea'] = $teams;
                  $data['sql'] = $sql;
                  return $data;


                case "repoveralls":
                    if($company == 2){

                      if ($uid==0){
                        $uid='relations.up and user.uid not in (0,21,2,4,43,75,74)';
                      }

                        $sqlv = "SELECT fullname as `name`,area as governorate,
                        visit_type.t1 as t1, visit_type.t2 as t2, visit_type.t3 as t3, visit_type.t_total as t_total,
                        s_ors,s_gp,s_im,s_s,s_u,s_n,s_on,s_ent,s_den,s_ge,s_pud,s_gyn,s_rhu,s_id,s_others,s_total
                        FROM nichepha_chiesi.user user
                        Join nichepha_chiesi.relations relations on user.uid=relations.down and relations.up = $uid
                        left Join (SELECT uid,
                              count(c.type) t_total,
                              sum(case when c.type = 1 then 1 else 0 end) t1,
                              sum(case when c.type = 2 then 1 else 0 end) t2,
                              sum(case when c.type = 3 then 1 else 0 end) t3
                              FROM (select * from nichepha_chiesi.visit GROUP BY uid,cid,date(date)) visit
                              join nichepha_chiesi.customers c on visit.cid=c.cid
                              WHERE DATE(visit.date) BETWEEN '{$request->Input('datefrom')}' and '{$request->Input('dateto')}'
                              group by uid) visit_type on user.uid=visit_type.uid
                        left Join (SELECT uid ,
                              COUNT(uid) as s_total,
                              sum(case when doctor.speciality = 'ORS' then 1 else 0 end) s_ors,
                              sum(case when doctor.speciality = 'GP' then 1 else 0 end) s_gp,
                              sum(case when doctor.speciality = 'IM' then 1 else 0 end) s_im,
                              sum(case when doctor.speciality = 'S' then 1 else 0 end) s_s,
                              sum(case when doctor.speciality = 'U' then 1 else 0 end) s_u,
                              sum(case when doctor.speciality = 'N' then 1 else 0 end) s_n,
                              sum(case when doctor.speciality = 'ON' then 1 else 0 end) s_on,
                              sum(case when doctor.speciality = 'ENT' then 1 else 0 end) s_ent,
                              sum(case when doctor.speciality = 'DEN' then 1 else 0 end) s_den,
                              sum(case when doctor.speciality = 'GE' then 1 else 0 end) s_ge,
                              sum(case when doctor.speciality = 'PUD' then 1 else 0 end) s_pud,
                              sum(case when doctor.speciality = 'GYN' then 1 else 0 end) s_gyn,
                              sum(case when doctor.speciality = 'RHU' then 1 else 0 end) s_rhu,
                              sum(case when doctor.speciality = 'ID' then 1 else 0 end) s_id,
                              sum(case when
                                doctor.speciality = 'ORS' or
                                doctor.speciality = 'GP' or
                                doctor.speciality = 'IM' or
                                doctor.speciality = 'S' or
                                doctor.speciality = 'U' or
                                doctor.speciality = 'N' or
                                doctor.speciality = 'ON' or
                                doctor.speciality = 'ENT' or
                                doctor.speciality = 'DEN' or
                                doctor.speciality = 'GE' or
                                doctor.speciality = 'PUD' or
                                doctor.speciality = 'GYN' or
                                doctor.speciality = 'RHU' or
                                doctor.speciality = 'ID'
                                 then 0 else 1 end) s_others
                              FROM (select * from nichepha_chiesi.visit GROUP BY uid,cid,date(date)) visit
                              join nichepha_chiesi.customers c on visit.cid = c.cid and c.type=1
                              join nichepha_chiesi.doctor doctor on visit.did = doctor.did
                              WHERE DATE(visit.date) BETWEEN '{$request->Input('datefrom')}' and '{$request->Input('dateto')}'
                              GROUP BY uid) visit_spec on user.uid=visit_spec.uid
                        ";
                        /*
                        WHERE `company`=$company and `supervisor_id`=$uid
                        AND DATE(`date`) BETWEEN '" . $request->Input('datefrom') . "' and '" . $request->Input('dateto') . "'";
                        */
                  }
                  $visits = DB::select($sqlv);
                  $data['repoveralls'] = $visits;
                  $data['sql'] = $sqlv;
                  return $data;

                case "repareas":
                    if($company == 2){
                        $sqlv = "SELECT fullname as `name`,visit_spec.area ,
                        s_ors_a,s_ors_b,s_ors_c,
                        s_gp_a,s_gp_b,s_gp_c,
                        s_im_a,s_im_b,s_im_c,
                        s_s_a,s_s_b,s_s_c,
                        s_u_a,s_u_b,s_u_c,
                        s_n_a,s_n_b,s_n_c,
                        s_on_a,s_on_b,s_on_c,
                        s_ent_a,s_ent_b,s_ent_c,
                        s_den_a,s_den_b,s_den_c,
                        s_ge_a,s_ge_b,s_ge_c,
                        s_pud_a,s_pud_b,s_pud_c,
                        s_gyn_a,s_gyn_b,s_gyn_c,
                        s_rhu_a,s_rhu_b,s_rhu_c,
                        s_id_a,s_id_b,s_id_c,
                        s_others,
                        s_total
                        FROM nichepha_chiesi.user user

                        left Join (SELECT uid, doctor.area,
                              COUNT(uid) as s_total,
                              sum(case when doctor.speciality = 'ORS' and doctor.grade='A' then 1 else 0 end) s_ors_a,
                              sum(case when doctor.speciality = 'ORS' and doctor.grade='B' then 1 else 0 end) s_ors_b,
                              sum(case when doctor.speciality = 'ORS' and doctor.grade='C' then 1 else 0 end) s_ors_c,

                              sum(case when doctor.speciality = 'GP'  and doctor.grade='A' then 1 else 0 end) s_gp_a,
                              sum(case when doctor.speciality = 'GP'  and doctor.grade='B' then 1 else 0 end) s_gp_b,
                              sum(case when doctor.speciality = 'GP'  and doctor.grade='C' then 1 else 0 end) s_gp_c,

                              sum(case when doctor.speciality = 'IM'  and doctor.grade='A' then 1 else 0 end) s_im_a,
                              sum(case when doctor.speciality = 'IM'  and doctor.grade='B' then 1 else 0 end) s_im_b,
                              sum(case when doctor.speciality = 'IM'  and doctor.grade='C' then 1 else 0 end) s_im_c,

                              sum(case when doctor.speciality = 'S'  and doctor.grade='A' then 1 else 0 end) s_s_a,
                              sum(case when doctor.speciality = 'S'  and doctor.grade='B' then 1 else 0 end) s_s_b,
                              sum(case when doctor.speciality = 'S'  and doctor.grade='C' then 1 else 0 end) s_s_c,

                              sum(case when doctor.speciality = 'U'  and doctor.grade='A' then 1 else 0 end) s_u_a,
                              sum(case when doctor.speciality = 'U'  and doctor.grade='B' then 1 else 0 end) s_u_b,
                              sum(case when doctor.speciality = 'U'  and doctor.grade='C' then 1 else 0 end) s_u_c,

                              sum(case when doctor.speciality = 'N'  and doctor.grade='A' then 1 else 0 end) s_n_a,
                              sum(case when doctor.speciality = 'N'  and doctor.grade='B' then 1 else 0 end) s_n_b,
                              sum(case when doctor.speciality = 'N'  and doctor.grade='C' then 1 else 0 end) s_n_c,

                              sum(case when doctor.speciality = 'ON'  and doctor.grade='A' then 1 else 0 end) s_on_a,
                              sum(case when doctor.speciality = 'ON'  and doctor.grade='B' then 1 else 0 end) s_on_b,
                              sum(case when doctor.speciality = 'ON'  and doctor.grade='C' then 1 else 0 end) s_on_c,

                              sum(case when doctor.speciality = 'ENT'  and doctor.grade='A' then 1 else 0 end) s_ent_a,
                              sum(case when doctor.speciality = 'ENT'  and doctor.grade='B' then 1 else 0 end) s_ent_b,
                              sum(case when doctor.speciality = 'ENT'  and doctor.grade='C' then 1 else 0 end) s_ent_c,

                              sum(case when doctor.speciality = 'DEN'  and doctor.grade='A' then 1 else 0 end) s_den_a,
                              sum(case when doctor.speciality = 'DEN'  and doctor.grade='B' then 1 else 0 end) s_den_b,
                              sum(case when doctor.speciality = 'DEN'  and doctor.grade='C' then 1 else 0 end) s_den_c,

                              sum(case when doctor.speciality = 'GE'  and doctor.grade='A' then 1 else 0 end) s_ge_a,
                              sum(case when doctor.speciality = 'GE'  and doctor.grade='B' then 1 else 0 end) s_ge_b,
                              sum(case when doctor.speciality = 'GE'  and doctor.grade='C' then 1 else 0 end) s_ge_c,

                              sum(case when doctor.speciality = 'PUD'  and doctor.grade='A' then 1 else 0 end) s_pud_a,
                              sum(case when doctor.speciality = 'PUD'  and doctor.grade='B' then 1 else 0 end) s_pud_b,
                              sum(case when doctor.speciality = 'PUD'  and doctor.grade='C' then 1 else 0 end) s_pud_c,

                              sum(case when doctor.speciality = 'GYN'  and doctor.grade='A' then 1 else 0 end) s_gyn_a,
                              sum(case when doctor.speciality = 'GYN'  and doctor.grade='B' then 1 else 0 end) s_gyn_b,
                              sum(case when doctor.speciality = 'GYN'  and doctor.grade='C' then 1 else 0 end) s_gyn_c,

                              sum(case when doctor.speciality = 'RHU'  and doctor.grade='A' then 1 else 0 end) s_rhu_a,
                              sum(case when doctor.speciality = 'RHU'  and doctor.grade='B' then 1 else 0 end) s_rhu_b,
                              sum(case when doctor.speciality = 'RHU'  and doctor.grade='C' then 1 else 0 end) s_rhu_c,

                              sum(case when doctor.speciality = 'ID'  and doctor.grade='A' then 1 else 0 end) s_id_a,
                              sum(case when doctor.speciality = 'ID'  and doctor.grade='B' then 1 else 0 end) s_id_b,
                              sum(case when doctor.speciality = 'ID'  and doctor.grade='C' then 1 else 0 end) s_id_c,
                              sum(case when
                                doctor.speciality = 'ORS' or
                                doctor.speciality = 'GP' or
                                doctor.speciality = 'IM' or
                                doctor.speciality = 'S' or
                                doctor.speciality = 'U' or
                                doctor.speciality = 'N' or
                                doctor.speciality = 'ON' or
                                doctor.speciality = 'ENT' or
                                doctor.speciality = 'DEN' or
                                doctor.speciality = 'GE' or
                                doctor.speciality = 'PUD' or
                                doctor.speciality = 'GYN' or
                                doctor.speciality = 'RHU' or
                                doctor.speciality = 'ID'
                                 then 0 else 1 end) s_others
                              FROM (select * from nichepha_chiesi.visit GROUP BY uid,cid,date(date)) visit
                              join nichepha_chiesi.customers c on visit.cid = c.cid and c.type=1
                              join nichepha_chiesi.doctor doctor on visit.did = doctor.did
                              WHERE DATE(visit.date) BETWEEN '{$request->Input('datefrom')}' and '{$request->Input('dateto')}'
                              GROUP BY uid,doctor.area) visit_spec on user.uid=visit_spec.uid
                              Where user.uid=$uid
                        ";
                        /*
                        WHERE `company`=$company and `supervisor_id`=$uid
                        AND DATE(`date`) BETWEEN '" . $request->Input('datefrom') . "' and '" . $request->Input('dateto') . "'";
                        */
                  }
                  $visits = DB::select($sqlv);
                  $data['repareas'] = $visits;
                  //$data['sql'] = $sqlv;
                  return $data;

            }
        }

        $data['userId'] = $uid;
        $data['isRep'] = $request->Input('isRep');
        $data['w'] = $request->Input('w');
        $data['userData'] = DB::table('users')->where(['company' => $company, 'native_id' => $uid])->get();

        //if it's not found in Unity database, try to find it on the original database
        if ($data['userData'] == 0 || count($data['userData']) == 0){
          if($company == 2){
            $data['userData'] = DB::connection('chiesi')->table('user')->where(['uid' => $uid])->get(['fullname as name', 'uid as native_id']);
          }
        }


        if($uid == 0){
          $data['userData'][0] = json_decode(json_encode(array('name' => "General Statistics And Reports", 'native_id' => 0)));
        }


        if ($uid!=0 && ($data['userData'] == 0 || count($data['userData']) == 0)){
          return "System Message : There is no user here or this user has no data in Unity database";
        }
        $data['company'] = $company;

        $start = new DateTime($request->Input('datefrom'));
        $end = new DateTime($request->Input('dateto'));
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $data['months'] = [];
        foreach ($period as $dt) {
            $data['months'][$dt->format("n")] = $dt->format("M Y");
        }
        $lastMonth = date('n', strtotime($request->Input('dateto')));
        if (!isset($data['months'][$lastMonth])) {
            $data['months'][$lastMonth] = date('M Y', strtotime($request->Input('dateto')));
        }

        //For Teams table
        $data['teams_days'][0] = $request->Input('dateto');
        $data['teams_days'][1] = date('Y-m-d', strtotime($data['teams_days'][0] .' -1 day'));
        $data['teams_days'][2] = date('Y-m-d', strtotime($data['teams_days'][1] .' -1 day'));
        $data['teams_days'][3] = date('Y-m-d', strtotime($data['teams_days'][2] .' -1 day'));
        $data['teams_days'][4] = date('Y-m-d', strtotime($data['teams_days'][3] .' -1 day'));
        $data['teams_days'][5] = date('Y-m-d', strtotime($data['teams_days'][4] .' -1 day'));
        $data['teams_days'][6] = date('Y-m-d', strtotime($data['teams_days'][5] .' -1 day'));

        $data['startDate']=$request->Input('datefrom');
        $data['endDate']=$request->Input('dateto');
        return view('insights/accumlative_details', $data);
    }
}
