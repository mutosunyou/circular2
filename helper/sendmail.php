<?php
//初期==============================================
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
$message.= 'http://192.168.100.209/workflow/index.php?goto=/circular2/disp.php?cid='.$_POST['cid'].PHP_EOL;
$message.= '※社外で閲覧する方はVPNをONにしてリンク先に移動してください。'.PHP_EOL.PHP_EOL;

$message.= '表題：'.$js2[0]->title.PHP_EOL.PHP_EOL;

$message.= '内容：'.PHP_EOL.str_replace('<br>',PHP_EOL,$js2[1]->content);

$headers = 'remote_manager@sunyou.co.jp';

sendmail($to,'',str_replace('\'','’',$subject),str_replace('\'','’',$message),$headers);

echo $to;
