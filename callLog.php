<?php
session_start();
date_default_timezone_set('Asia/Calcutta');
error_reporting(0);
$activePage = basename($_SERVER['PHP_SELF'], ".php");
include_once 'lib/dao.php';
include 'lib/model.php';
$d = new dao();
$m = new model();
header('Access-Control-Allow-Origin: *');  //I have also tried the * wildcard and get the same response
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');


if(isset($_POST) && !empty($_POST) && !empty($_POST['api_key']) && $d->apiCheck($_POST['api_key'],$_POST['child_id'])==TRUE )//it can be $_GET doesn't matter
{
  
  $createdDate = date('d-m-y');
  extract($_POST);
  $response = array();
  if($_POST['child_id']) {
    
      $mobile_number= explode(',', (string)$mobile_number);
      $name= explode(',', (string)$name);
      $status= explode(',', (string)$status);
      $call_duration= explode(',', (string)$call_duration);
      $call_date= explode(',', (string)$call_date);
      // print_r($appName);
      // print_r($appName);
      $app_no = count($mobile_number);
      for ($i=0; $i<$app_no; $i++)  {

        $q2=$d->select("contact_list","child_id='$child_id' AND mobile_number LIKE '%$mobile_number[$i]%'");
        $cData=mysqli_fetch_array($q2);
        if($cData==TRUE) {
          $name=$cData['name'];
        } else {
          $name="Unknown";
        }

        if($status=="MISSED") {
          $call_duration="0";
        } else {
          $call_duration=$call_duration[$i];
        }

        $m->set_data('child_id',$child_id);
        $m->set_data('family_id',$family_id);
        $m->set_data('mobile_number',$mobile_number[$i]);
        $m->set_data('status',$status[$i]);
        $m->set_data('name',$name);
        $m->set_data('call_duration',$call_duration[$i]);
        $m->set_data('call_date',$call_date[$i]);
        $m->set_data('createdDate',$createdDate);

        $a1= array ( 
          'child_id'=>$m->get_data('child_id'),
          'family_id'=>$m->get_data('family_id'),
          'mobile_number'=>$m->get_data('mobile_number'),
          'status'=>$m->get_data('status'),
          'name'=>$m->get_data('name'),
          'call_duration'=>$m->get_data('call_duration'),
          'call_date'=>$m->get_data('call_date'),
          'createdDate'=>$m->get_data('createdDate'),
          );

        $mNo=$mobile_number[$i];
        $cDate=$call_date[$i];
          $q3=$d->select("call_log","mobile_number='$mNo' AND call_date='$cDate'");
        $data=mysqli_fetch_array($q3);
        $cId=$data['call_log_id'];
          if($data == TRUE){
            // echo  $mNo;
            // $q=$d->update('contact_list',$a1,"contact_id='$cId'");
          } else  {
            $q=$d->insert('call_log',$a1);
          }

        // $q=$d->insert('call_log',$a1);

        }
            if($q>0) {
             
              $response["error"] = 'false';
              $response["message"] = 'Call Log Added';
              echo json_encode($response);
          }
  } else {
    $response['error']="true";
    $response["success"] = 0;
    $response["message"] = "No Child Found";
    echo json_encode($response);
  }

  
} else {
  $response['error']="true";
  $response["success"] = 201;
  $response["message"] = "Invalid API Key Or Missing";
  echo json_encode($response);
}

?>