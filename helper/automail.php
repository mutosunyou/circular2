<?php
require_once("../master/prefix.php");

$sql = 'select distinct userID from member where checked = 0';
$result = selectData('circular2', $sql);

mb_language("Japanese");
mb_internal_encoding("UTF-8");

$to='';
for($i=0;$i<count($result);$i++){
  $to = mailAddress($result[$i]['userID']);
  if($i!=(count($)-1)){
    $to.=', ';
  }
}

$subject = '【回覧通知】未確認の回覧があります';
$message = '未確認の回覧があります。システムで確認してください。'."\r\n";
$message.= 'http://192.168.100.209/circular2/list.php'."\r\n";
$headers = 'From: System<remote_manager@sunyou.co.jp>'."\r\n";

mb_send_mail($to, $subject, $message, $headers);

function mailAddress($id){
  $sql = 'select mail from employee where id = '.$id;
  $res = selectData('master', $sql);
  return $res[0]['mail'];
}

