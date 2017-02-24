<?php
//初期==============================================
session_start();
require_once('../Circular.php');

$js2 = json_decode($_POST['id']);
$js3 = json_decode($_POST['mem']);

$p = new Circular();

/*
$sql = 'select distinct member.userID from member, circular where member.checked = 0 and member.circularID = circular.id and circular.status = 0 order by member.userID desc';
$result = selectData('circular', $sql);
print_r($result);
$mem = new MemberList();
$uarray = array();
for ($i=0; $i < count($result); $i++) { 
  $uarray[] = $mem->mailAddress($result[$i]['userID']);
}

$p->mailing($uarray, '【回覧通知】未確認の回覧があります', '未確認の回覧があります。'.PHP_EOL.'システムで確認してください。'.PHP_EOL.'http://192.168.100.209/circular2/list.php');

 */
