<?php

require_once('../master/prefix.php');

$sql = 'select distinct userID from member where checked = 0';
$result = selectData('circular2', $sql);

$to='';
for($i=0;$i<count($result);$i++){
  $to .= mailFromUserID($result[$i]['userID']);
  if($i!=(count($result)-1)){
    $to.=', ';
  }
}

$subject = '【回覧通知】未確認の回覧があります';
$message = '未確認の回覧があります。システムで確認してください。'."\r\n";
$message.= 'http://192.168.100.209/workflow/index.php?goto=/circular2/list.php'."\r\n";
$headers = 'From: System<remote_manager@sunyou.co.jp>'."\r\n";

sendmail(str_replace('\'','’',$to),'',str_replace('\'','’',$subject),str_replace('\'','’',$message),$headers);

 
