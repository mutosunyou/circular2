<?php
//初期==============================================
session_start();
require_once('Circular.php');

$p=new Circular();
$p->initWithID($_POST['cid']);

//回答=======================================
$js = json_encode($p);
echo $js;

