<?php
//初期==============================================
session_start();
require_once('Member.php');
require_once('Answer.php');

//localのみ=========================================
$_SESSION['login_name']="武藤　一徳";
$_SESSION['loginid']=10042;


//回答=======================================
$js = json_decode($_POST['aarray']);
//var_dump($js);
$a = new Answer();
for($i=0;$i<count($js);$i++){
  $a->addAnswer($_SESSION['loginid'],$js[$i]->qid,$js[$i]->cid,"");
}

//回覧メンバー===============================
$m = new Member();
$m->setCheckflg($_POST['cid']);
