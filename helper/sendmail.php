<?php
//初期==============================================
session_start();
require_once('../master/prefix.php');

$js2 = json_decode($_POST['id']);
$js3 = json_decode($_POST['mem']);

$to='';
for($i=0;$i<count($js3);$i++){
  $to .= mailFromUserID($js3[$i]->num);
  if($i!=(count($js3)-1)){
    $to.=', ';
  }
}

$subject = '【回覧通知】'.$js2[0]->title;
$message = '回覧が来ています。下記URLより、回覧内容をご確認ください'.PHP_EOL.PHP_EOL;
$message.= 'http://192.168.100.209/circular2/disp.php?cid='.$_POST['cid'].PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL;

$message.= '表題：'.$js2[0]->title;
$headers = 'remote_manager@sunyou.co.jp';

sendmail($to,'',$subject,$message,$headers);

