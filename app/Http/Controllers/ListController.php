<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use App;
use Auth;
use Import;
use PHPExcel_IOFactory;
use PHPExcel_Cell;

Class ListController extends Controller{

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index($company){
    $data['company'] = $company;
    $lists = App\ListModel::where('user_id', Auth::user()->native_id)->with('company')->OrderBy('id');
    $data['lists'] = $lists->get();
    //if he's a supervisor
    $position = DB::connection('tabuk')->select("select * from User where uid=" . Auth::user()->native_id)[0]->JobTitle;
    if($position == 'SuperVisor'){
      $reps = DB::connection('tabuk')->select("select * from SuperReps where supid=" . Auth::user()->native_id);
      $reps_array = array();
      foreach ($reps as $key => $value) {
        array_push($reps_array,$value->rid);
      }
      $lists_reps = App\ListModel::whereIn('user_id', $reps_array)->with('company')->OrderBy('id');
      $data['lists'] = $lists_reps->union($lists)->get();
    }
    //return view('list.index', $data);
  }

  public function postList(Request $request, $company){
    $list = App\ListModel::create([
      'company_id' => $company,
      'user_id' => Auth::user()->native_id,
      'user_name' => Auth::user()->name,
      'type' => 'Doctors',
      'status' => ''
    ]);

    if(!$request['lst'] || !$request->file('lst')->isValid() || !in_array($request->file('lst')->guessClientExtension(), array('csv', 'xlsx', 'xls') )){
      $list->status = 'Error : Please upload a valid CSV or Excel file';
      $list->save();
    }

    //if there's no problem
    if($list->status == ''){
      $storing_name = $list->id . '.' . $request->file('lst')->guessClientExtension();
      $request->file('lst')->move(storage_path().'/lists', $storing_name);
      $fullpath = storage_path() . '/lists/' . $storing_name;

      include getcwd() . '/app/Http/Controllers/Classes/PHPExcel/IOFactory.php';
      include getcwd() . '/app/Http/Controllers/Classes/PHPExcel/Cell.php';
      $inputFileName = $fullpath;
      try {
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);
      } catch(Exception $e) {
        die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
      }

      //  Get worksheet dimensions
      $sheet = $objPHPExcel->getSheet(0);
      $highestRow = $sheet->getHighestRow();
      $highestColumn = $sheet->getHighestColumn();
      $colNumber = PHPExcel_Cell::columnIndexFromString($highestColumn);

      //  Loop through each row of the worksheet in turn
      $header_found = false;
      for ($row = 1; $row <= $highestRow; $row++){
        //  Read a row of data into an array
        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL,TRUE,FALSE);

        if ($header_found == false){
          $cols = array(
            "drname" => -1,
            "clinic" => -1,
            "hosp" => -1,
            "speciality" => -1,
            "mobile" => -1,
            "phone" => -1,
            "email" => -1,
            "generalClass" => -1,
            "TPM" => -1,
            "birthd" => -1,
            "martial" => -1,
            "besttime" => -1,
            "waitingTime" => -1,
            "DoctorAtt" => -1,
            "GeneralNeeds" => -1,
            "assistantName" => -1,
            "assistantatt" => -1,
            "service" => -1,
            "contract" => -1,
            "Compatators" => -1,
            "area" => -1,
            "Rxs" => -1,
            "Pharmacy" => -1,
            "PatientsNum" => -1,
            "VisitsNum" => -1
          );
          for ($i=0; $i < $colNumber; $i++) {
            if (array_key_exists(ListController::arrangeIndexes($rowData[0][$i]), $cols)){
              $cols[ListController::arrangeIndexes($rowData[0][$i])] = $i;
            }
          }
          if($cols['drname'] != -1){
            $header_found = true;
            $dataset = array();
            foreach ($cols as $key => $value) {
              $dataset[$key] = array();
            }
          }
        }else{
          //Setup the arrays only if the there's a customer name
          if($rowData[0][$cols['drname']]){
            foreach ($cols as $key => $value) {
              if($key == 'speciality'){
                array_push($dataset[$key] , ListController::modifySpec($rowData[0][$cols[$key]]));
              }elseif($value == -1){
                array_push($dataset[$key] , '');
              }else{
                array_push($dataset[$key] , addslashes($rowData[0][$cols[$key]]));
              }
            }
          }
        }
      }
      //checking if we got a list
      if(isset($dataset) == false){
        $list->status = 'Error : System could not recognize you list format, please make sure you put your list on the first datasheet';
        $list->save();
      }

      if($list->status == ''){
        //Deleting the previos list
        DB::connection('tabuk')->statement('delete from DoctorRep where uid=' .Auth::user()->native_id);

        //Take it doctor by doctor
        $list_new = 0;
        $list_exist = 0;
        foreach ($dataset['drname'] as $key => $value) {
          $check_doctor = DB::connection('tabuk')->table('Doctors')->where('FullName', $value);
          //if($dataset['drname'])
          if ($dataset['speciality'][$key] != 'No Code'){
            $check_doctor->where('speciality', $dataset['speciality'][$key]);
          }
          if (count($check_doctor->get()) == 0){
            //add the new doctor ..
            DB::connection('tabuk')->statement('INSERT into Doctors (
              FullName,
              clinic,
              hospital,
              speciality,
              mobile,
              phone,
              email,
              GeneralClass,
              TPMCClass,
              DOB,
              MaterialStatus,
              bestTimeToVisit,
              waitingTime,
              DoctorAtt,
              GeneralNeeds,
              AssistName,
              AssistAtt,
              ServicesDelivered,
              ContractSituation,
              Compatators,
              area,
              Rxs,
              Pharmacy,
              PatientsNum,
              VisitsNum
            ) Values (' . "
            '{$dataset['drname'][$key]}',
            '{$dataset['clinic'][$key]}',
            '{$dataset['hosp'][$key]}',
            '{$dataset['speciality'][$key]}',
            '{$dataset['mobile'][$key]}',
            '{$dataset['phone'][$key]}',
            '{$dataset['email'][$key]}',
            '{$dataset['generalClass'][$key]}',
            '{$dataset['TPM'][$key]}',
            '{$dataset['birthd'][$key]}',
            '{$dataset['martial'][$key]}',
            '{$dataset['besttime'][$key]}',
            '{$dataset['waitingTime'][$key]}',
            '{$dataset['DoctorAtt'][$key]}',
            '{$dataset['GeneralNeeds'][$key]}',
            '{$dataset['assistantName'][$key]}',
            '{$dataset['assistantatt'][$key]}',
            '{$dataset['service'][$key]}',
            '{$dataset['contract'][$key]}',
            '{$dataset['Compatators'][$key]}',
            '{$dataset['area'][$key]}',
            '{$dataset['Rxs'][$key]}',
            '{$dataset['Pharmacy'][$key]}',
            '{$dataset['PatientsNum'][$key]}',
            '{$dataset['VisitsNum'][$key]}'
            " . ')');
            $did = DB::connection('tabuk')->select('SELECT max(did) as did from Doctors')[0]->did;
            DB::connection('tabuk')->statement('INSERT INTO DoctorRep (uid, did) VALUES (' . Auth::user()->native_id . ", {$did})");
            $list_new++;
          }else{
            //Adding the customer the the rep list
            DB::connection('tabuk')->statement('INSERT INTO DoctorRep (uid, did) VALUES (' . Auth::user()->native_id . ", {$check_doctor->get()[0]->did})");
            $list_exist++;
          }
        }

        $list->status = "List is uploaded ({$list_new} new, {$list_exist} existent)";
        $list->save();
      }
    }
    return redirect("/list/{$company}#l{$list->id}");

  }

  //imported from Tabuk system
  private static function arrangeIndexes($a)
  {


    if((stripos($a,'name') !== false && ((stripos($a,'cust') !== false || stripos($a,'doc') !== false|| stripos($a,'dr') !== false)) || stripos($a,'drname') !== false) || strcasecmp($a,'name') == 0){

      return 'drname';
    }
    elseif(stripos($a,'address') !== false || stripos($a,'clini') !== false /*|| stripos($a,'area') !== false*/){

      return 'clinic';
    }
    elseif(stripos($a,'hosp') !== false || stripos($a,'center') !== false){

      return 'hosp';
    }
    elseif(stripos($a,'sp') === 0 ){

      return 'speciality';
    }
    elseif(stripos($a,'mobile') !== false){
      return 'mobile';
    }
    elseif(stripos($a,'land') !== false){
      return 'phone';
    }
    elseif(stripos($a,'mail') !== false){

      return 'email';
    }
    elseif((stripos($a,'general') !== false) && stripos($a,'class') !== false){

      return 'generalClass';
    }
    elseif(stripos($a,'general') == false && stripos($a,'class') !== false){

      return 'TPM';
    }
    elseif(stripos($a,'birth') !== false || stripos($a,'bod') !== false){

      return 'birthd';
    }
    elseif(stripos($a,'family') !== false || stripos($a,'martial') !== false || stripos($a,'single') !== false){

      return 'martial';
    }
    elseif(stripos($a,'best') !== false && stripos($a,'time') !== false ){

      return 'besttime';
    }
    elseif(stripos($a,'wait') !== false && stripos($a,'time') !== false ){

      return 'waitingTime';
    }
    elseif(((stripos($a,'doc') !== false || stripos($a,'dr') !== false) && stripos($a,'att') !== false )||
    stripos($a,'personal') !== false ){

      return 'DoctorAtt';
    }
    elseif(stripos($a,'need') !== false ){

      return 'GeneralNeeds';
    }
    elseif(stripos($a,'assist') !== false && stripos($a,'name') !== false){

      return 'assistantName';
    }
    elseif(stripos($a,'assist') !== false && stripos($a,'att') !== false){

      return 'assistantatt';
    }
    elseif(stripos($a,'service') !== false ){

      return 'service';
    }
    elseif(stripos($a,'contract') !== false ){

      return 'contract';
    }
    elseif(stripos($a,'Competitor') !== false ){

      return 'Compatators';
    }

    elseif(stripos($a,'area') !== false || stripos($a,'region') !== false || stripos($a,'dist') !== false){

      return 'area';
    }
    elseif(stripos($a,'rxs') !== false )
    {

      return 'Rxs';
    }
    elseif(stripos($a,'pharm') !== false )
    {

      return 'Pharmacy';
    }
    elseif(stripos($a,'patient') !== false )
    {

      return 'PatientsNum';
    }
    elseif(stripos($a,'visit') !== false && stripos($a,'time') === false)
    {

      return 'VisitsNum';
    }

    return "//"; // if nothing of the previous return // as nothing

    #the end of the function
  }

  public static function modifySpec($sp){



    ///  if there is a dots abbrevations remove them

    if (strpos($sp,'.') !== false){
      $sp = str_replace(".", "", $sp);

    }
    if(stripos($sp, 'res') === 0){
      $sp = str_replace("res", "", $sp);
    }
    if(stripos($sp, 'reg') === 0){
      $sp = str_replace("reg", "", $sp);
    }

    if(stripos($sp, 'fellow') === 0){
      $sp = str_replace("fellow", "", $sp);
    }
    $sp = trim($sp);

    // check thta or is on the beginning of the string
    if(stripos($sp, 'or') === 0){
      return "ORS";
    }

    elseif(stripos($sp, 'u') === 0){
      return "U";
    }



    elseif(stripos($sp, 'gy') === 0 || stripos($sp, 'ob') === 0){
      return "GYN";
    }
    elseif(stripos($sp, 'PU') === 0 || stripos($sp, 'ch') === 0 || stripos($sp, 'resp') === 0 || stripos($sp, 'Pn') === 0 ){
      return "PUD";
    }
    elseif(stripos($sp, 'family') !== false || stripos($sp, 'F') === 0){
      return "FP";
    }
    elseif(stripos($sp, 'ent') === 0 || stripos($sp, 'e n') === 0 || stripos($sp, 'ear') === 0 || stripos($sp, 'ORT') === 0 || stripos($sp, 'OTo') === 0){
      return "ENT";
    }

    elseif(stripos($sp, 'Den') !== false || stripos($sp, 'oral') !== false || stripos($sp, 'dont') !== false || stripos($sp, 'Dn') === 0){
      return "DEN";
    }

    // general practitioner
    //
    elseif(stripos($sp, "gp") === 0 || stripos($sp, "gb") === 0 ||(stripos($sp, 'gener') !== false && (stripos($sp, 's') === false || stripos($sp, 'phys') !== false || stripos($sp, 'scop') !== false))){
      return "GP";
    }
    // internal medicine
    elseif(stripos($sp, 'im') === 0 || stripos($sp, 'int') !== false){
      return "IM";
    }
    elseif(stripos($sp, 'Rh') === 0 || stripos($sp, 'Ru') === 0 || stripos($sp, 'RUh') === 0){
      return "RHU";
    }

    elseif(stripos($sp, 'PD') !== false || stripos($sp, 'Ped') === 0 || stripos($sp, "paed") === 0|| stripos($sp, "pead") === 0){
      return "PD";
    }

    //check there is  no overlapping with immunity
    //infection disease

    elseif(stripos($sp, 'i') === 0 && stripos($sp, 'im') == false && stripos($sp, 'ig') == false &&  stripos($sp, 'int') == false && stripos($sp, 'ic') == false){
      return "ID";
    }
    // no overlapping with NS
    //neuro

    elseif(stripos($sp, 'N') === 0 && stripos($sp, 'sur') == false && stripos($sp, 'Ns') !== 0 && stripos($sp, 'nep') !== 0){
      return "N";
    }

    elseif(stripos($sp, "ns") === 0||(stripos($sp, 'N') === 0 && stripos($sp, 'sur') !== false) ){
      return "NS" ;
    }


    elseif(stripos($sp, 'end') === 0 ){
      return "END";
    }
    elseif(stripos($sp, 'an') === 0 ){
      return "AN";
    }
    elseif(stripos($sp, 'cc') === 0 || stripos($sp, 'care') !== false || stripos($sp, 'ic') === 0){
      return "CCU";
    }
    elseif(stripos($sp, 'car') === 0 || stripos($sp, 'CD') === 0 || stripos($sp, 'crd') === 0){
      return "CD";
    }
    elseif(strcasecmp($sp, "ntr") == 0|| stripos($sp, 'nu') === 0 || stripos($sp, 'diet') !== false){
      return "NTR";
    }
    elseif(strcasecmp($sp, "ge") == 0 || stripos($sp, 'Ga') === 0 || stripos($sp, 'GIT') !== false || strcasecmp($sp, "gi") == 0 ){
      return "GE";
    }
    //immunity
    elseif(strcasecmp($sp, "ig") == 0 || stripos($sp, 'imm') === 0 ){
      return "IG";
    }
    elseif(stripos($sp, 'dia') === 0 ){
      return "DIA";
    }
    //plastic surgeon
    elseif(strcasecmp($sp, "ps") == 0 || stripos($sp, 'PL') === 0 || stripos($sp, 'cos') === 0){
      return "PS";
    }
    elseif(stripos($sp, 'OP') === 0 ){
      return "OPH";
    }
    elseif(strcasecmp($sp, "vs") == 0 ||  (stripos($sp, 'v') === 0 &&  stripos($sp, 'sur') !== false )){
      return "VS";
    }
    elseif(strcasecmp($sp, "v") == 0 ||  (stripos($sp, 'v') === 0 &&  stripos($sp, 'sur') == false )){
      return "V";
    }
    elseif(stripos($sp, 's') === 0 || stripos($sp, 'gs') === 0 || (stripos($sp, 'g') === 0 && stripos($sp, 'su') !== false) ||
    stripos($sp, 'surg') !== false ) {
      return "S";
    }
    elseif(stripos($sp, 'em') === 0 || strcasecmp($sp, "er") == 0 ){
      return "EM";
    }
    elseif(stripos($sp, 'nep') === 0 ){
      return "NEP";
    }
    //psychiatric
    elseif(strcasecmp($sp, "p") == 0 ||  stripos($sp, 'psy') === 0 ){
      return "P";
    }
    elseif(stripos($sp, 'ph') === 0 ){
      return "PH";
    }
    elseif(strcasecmp($sp, "D") == 0 || stripos($sp, 'Der') !== false || strcasecmp($sp, "Dr") == 0 ){
      return "D";
    }
    // we did write in on database
    else  if(stripos($sp, 'tr') === 0){
      return "TRS";
    }
    else  if(stripos($sp, 'h') === 0){
      return "HEM";
    }

    else  if(stripos($sp, 'micro') === 0 || strcasecmp($sp, "mm") == 0 ){
      return "MM";
    }
    elseif(stripos($sp, 'on') === 0 ){
      return "ON";
    }
    elseif(stripos($sp, 'med') === 0 ){
      return "MED";
    }
    elseif(stripos($sp,'code') !== false ){
      return "No Code";
    }
    else
    return "No Code";

  }
}
