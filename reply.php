<?php
//初期==============================================
session_start();
require_once('Circular.php');

//localのみ=========================================
$_SESSION['login_name']="武藤　一徳";
$_SESSION['loginid']=10042;

$p = new Circular();
$p->initWithID($_POST['cid']);
var_dump($p);
$body='';
for($i=0;$i<count($p->questions);$i++){
  $body.='<div class="panel panel-default">';
  $body.='<div class="panel-heading">アンケート'.$i.'　'.$p->questions[$i]->content.'</div>';
  $body.='<div class="panel-body">';
  for($j=0;$j<count($p->questions[$i]->candidates);$j++){
    $body.='<div class="radio">';
    $body.='<label>';
    $body.='<input type="radio" name="optionsRadios'.$i.'">'.$p->questions[$i]->candidates[$j].'<br>';
    $body.='</label>';
    $body.=' </div>';
  }
  $body.='</div>';
  $body.='</div>';
}

echo $body;
