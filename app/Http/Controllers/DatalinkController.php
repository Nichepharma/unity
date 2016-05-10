<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;


function ireturn($str, $msg = ""){
  if($msg != "" ){return "{\"data\":[{\"msg\":\"{$msg}\"}]}";}
  return "{\"data\":" .  $str . "}";
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
          $iOS_user = $data_iOS->user;
          $iOS_pass = $data_iOS->pass;
          $company = $data_iOS->company;



          switch ($company) {
            case 'tabuk':
              $response = DB::connection($company)->table('User')->where(['UserName' => $iOS_user, 'password' => $iOS_pass])->get(['uid as id']);
              break;

            case 'chiesi':
              $response = DB::connection($company)->table('user')->where(['username' => $iOS_user, 'password' => $iOS_pass])->get(['uid as id']);
              break;

            case 'dermazone':
              $response = DB::connection($company)->table('user')->where(['username' => $iOS_user, 'pass' => $iOS_pass])->get(['id']);
              break;

            default:
              return "404";
          }

          if (count($response)){
            return ireturn(json_encode($response));;
          }else {
            return ireturn("[{\"id\":\"invalid\"}]");
          }

        default:
          return "404";
      }
    }

}
