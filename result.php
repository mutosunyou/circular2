<?php
//初期==============================================
session_start();
require_once('Circular.php');

//localのみ=========================================
$_SESSION['login_name']="武藤　一徳";
$_SESSION['loginid']=10042;

$body='';
for($i=0;$i<count($p->questions);$i++){
  $body.='<input class="hidden" value="'.$p->questions[$i]->content.'">';
  $body.='<div class="panel panel-default">';
  $body.='<div class="panel-heading">アンケート結果'.$i.'</div>';
  $body.='<div class="panel-body">';
  $body.='<div class="charts">';
  $body.='</div>';
  $body.='</div>';
  $body.='</div>';
}


echo $body;
