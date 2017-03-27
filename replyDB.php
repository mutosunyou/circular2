<?php
session_start();
require_once('master/prefix.php');
require_once('Circular.php');



//回答=======================================
$js = json_decode($_POST['aarray']);

$a = new Answer();
for($i=0;$i<count($js);$i++){
  if($js[$i]->cid!=null){
    $cid=$js[$i]->cid;
  }else{
    $cid="";
  }
  if($js[$i]->desc!=null){
    $desc=$js[$i]->desc;
  }else{
    $desc="";
  }
  $a->addAnswer($_SESSION['loginid'],$js[$i]->qid,$cid,$desc);
}

//回覧メンバー
$m = new Member();
$m->setCheckflg($_POST['cid']);

$sql = 'select * from member where circularID='.$_POST['cid'] .' and checked=0';
if(selectData(DB_NAME,$sql)==null){
  $sql = 'update circular set status=1 where id='.$_POST['cid'];
  deleteFrom(DB_NAME,$sql);
}
