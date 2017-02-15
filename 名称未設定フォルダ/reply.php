<?php
//初期==============================================
session_start();
require_once('Circular.php');

//localのみ=========================================
$_SESSION['login_name']="武藤　一徳";
$_SESSION['loginid']=10042;

$p = new Circular();
$p->initWithID($_POST['cid']);
//var_dump($p);

$body='';

$body.='<div class="panel panel-default">';
$body.='<div class="panel-heading">アンケート</div>';
$body.='<table class="table table-bordered">';

for($i=0;$i<count($p->questions);$i++){
  $body.='<thead><tr><td colspan="2" class="info">'.$p->questions[$i]->content.'</td></tr></thead>';
  $body.='<tbody>';
  for($j=0;$j<count($p->questions[$i]->candidates);$j++){
    $body.='<tr>';
    $body.='<td>';
    $body.='<div class="radio" style="margin:0 0 0 0;">';
    $body.='<label>';
    $body.='<input type="radio" name="optionsRadios'.$i.'">'.$p->questions[$i]->candidates[$j].'<br>';
    $body.='</label>';
    $body.=' </div>';
    $body.='</td>';
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
