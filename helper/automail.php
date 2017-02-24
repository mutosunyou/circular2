<?php
require_once("master/prefix.php");
require_once('Circular.php');
$cf = new Circular();

$sql = 'select distinct member.userID from member, circular where member.checked = 0 and member.circularID = circular.id and circular.status = 0 order by member.userID desc';
$result = selectData('circular', $sql);
print_r($result);
$mem = new MemberList();
$uarray = array();
for ($i=0; $i < count($result); $i++) { 
  $uarray[] = $mem->mailAddress($result[$i]['userID']);
}

$cf->mailing($uarray, '【回覧通知】未確認の回覧があります', '未確認の回覧があります。'.PHP_EOL.'システムで確認してください。'.PHP_EOL.'http://192.1
