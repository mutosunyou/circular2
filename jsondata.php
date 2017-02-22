<?php
//初期==============================================
//session_start();
require_once('Circular.php');

//localのみ=========================================
$_SESSION['login_name']="武藤　一徳";
$_SESSION['loginid']=10042;

$p=new Circular();
$p->initWithID($_POST['cid']);

//回答=======================================
$js = json_encode($p);
echo $js;

