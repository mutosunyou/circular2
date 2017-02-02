<?php
//初期==============================================
session_start();

$js = json_decode($_POST['qarray']);
//var_dump($js);
$body=' ';
for($i=0;$i<count($js);$i++){
  $body.='<div class="panel panel-default">';
  $body.='<div class="panel-heading">';
  $body.='<div class="input-group input-group-sm">';
  $body.='<span class="input-group-addon">質問'.($i+1).':</span>';
  $body.='<input type="text" style="font-size:13px;" class="form-control form-inline">';
  $body.='</div>';
  $body.='</div>';  
  
  $body.='<div class="panel-body">';
  for($j=1;$j<=$js[$i];$j++){
    $body.='<div class="input-group input-group-sm">';
    $body.='<span class="input-group-addon">回答'.$j.'</span>';
    $body.='<input type="text" style="font-size:13px;" class="form-control">';
    $body.='</div>';
  }
  $body.='<button class="btn btn-primary addask" question="'.$i.'" style="float:right;">+</button><span style="float:right;">回答項目追加</span>';
  $body.='<input type="checkbox" class="ask '.$i.'">自由解答欄を設ける';
  $body.='</div>';
  $body.='</div>';
}
  $body.='<button id="addq" class="btn btn-primary" style="float:right;">+</button><span style="float:right;">質問追加</span>';
echo $body;
