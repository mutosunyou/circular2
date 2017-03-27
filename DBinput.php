<?php
session_start();
require_once('master/prefix.php');
require_once('Circular.php');

$js2 = json_decode($_POST['id']);
$js3 = json_decode($_POST['mem']);

$c=new Circular();
$m=new Member();
$q=new Question();

//回覧追加
if($js2[3]->secret==true){
  $secret=1;
}else{
  $secret=0;
}
$c->AddCircular($js2[0]->title,$js2[1]->content,$secret);

//追加した回覧にメンバーを追加
$m->AddMember($c->id[1],$js3);

//追加した回覧にアンケートを追加
$q->AddQuestion($c->id[1],$js2[4]);

echo $c->id[1];
