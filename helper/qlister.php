<?php
//初期==============================================
session_start();

$js = json_decode($_POST['qarray']);
var_dump($js);

$body=' ';
for($i=0;$i<count($js);$i++){
  $body.='<div class="panel panel-info">';
  $body.='<div class="panel-heading">';
  $body.='<div class="input-group input-group-sm">';
  $body.='<span class="input-group-addon">質問　'.$i.'</span>';
  $body.='<input type="text" style="font-size:13px;" class="form-control form-inline question" value="'.$js[$i][0]->question.'">';
  $body.='</div>';
  $body.='</div>';  
  
  $body.='<div class="panel-body">';
  for($j=0;$j<(count($js[$i])-1);$j++){
    $body.='<div class="input-group input-group-sm">';
    $body.='<span class="input-group-addon">回答　'.$j.'</span>';
    $body.='<input type="text" style="font-size:13px;" class="form-control answer" value="'.$js[$i][$j+1]->answer.'">';
    $body.='</div>';
  }
  //回答項目追加ボタン
  $body.='<button class="btn btn-primary btn-xs addask" question="'.$i.'" style="float:right;">　+　</button><span style="float:right;">回答項目追加　</span>';
 
 //自由記入チェックボックス
  if($js[$i][0]->check=="true"){
    $checked='checked';
  }else{
    $checked='';
  }
  $body.='<input type="checkbox" class="checkask" check='.$i.' '.$checked.'>自由解答欄を設ける';
  $body.='</div>';
  $body.='</div>';
}
  $body.='<button id="addq" class="btn btn-primary btn-xs" qnum="'.count($js).'" style="float:right;">　+　</button><span style="float:right;">質問追加　</span>';
echo $body;
