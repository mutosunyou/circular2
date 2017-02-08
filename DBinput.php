<?php
//初期==============================================
session_start();
require_once('Circular.php');

$js = json_decode($_POST['id']);
$js2 = json_decode($_POST['mem']);
var_dump($js2);

$p = new Circular();
$p->AddCircular($js[0]->title,$js[1]->content,$js[3]->secret);


