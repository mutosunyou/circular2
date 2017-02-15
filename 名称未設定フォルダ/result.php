<?php
//初期==============================================
session_start();
require_once('Circular.php');

//localのみ=========================================
$_SESSION['login_name']="武藤　一徳";
$_SESSION['loginid']=10042;

$p = new Circular();
$p->initWithID($_POST['cid']);

$body='';
/*
for($i=0;$i<count($p->questions);$i++){
  $body.='<div class="panel panel-success">';
  $body.='<div class="panel-heading">アンケート結果'.$i.'</div>';
  $body.='<div class="panel-body">';
  $body.='<div class="charts">';
  $body.='</div>';
  $body.='</div>';
  $body.='</div>';
}*/

$body.='<div class="panel panel-default">';
$body.='<div class="panel-heading">アンケート結果</div>';
$body.='<table class="table table-bordered">';

for($i=0;$i<count($p->questions);$i++){
  $body.='<thead><tr><td colspan="2" class="info">'.$p->questions[$i]->content.'</td></tr>';
  $body.='<tr><div class="charts"></div></tr>';
  $body.='<tr><th width="100px">集計</th><th>名前</th></tr></thead>';
  $body.='<tbody>';
  for($j=0;$j<count($p->questions[$i]->candidates);$j++){
    $body.='<tr>';
    $body.='<td></td>';
    $body.='<td>'.$p->questions[$i]->candidates[$j].'</td>';
    $body.='</tr>';
  }

  if($p->questions[$i]->freespace==1){
    $body.='<tr><td colspan="2">自由記入欄</td></tr>';
  }
  $body.='</tbody>';
}
$body.='</table>';

$body.='</div>';

echo $body;
