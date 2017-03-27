<?php
require_once('../master/prefix.php');

$sql='select circularID from member where userID='.$_POST['member'] .'and checked=0';
$rst=selectData(DB_NAME,$sql);

for($i=0;$i<count($rst);$i++){
  $sql='update member set checked=1 where circularID='.$rst[$i]['circularID'].' and userID='.$_POST['member'];
  deleteFrom(DB_NAME,$sql);
}
