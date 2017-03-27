<?php
$js = json_decode($_POST['qarray']);

$body=' ';
for($i=0;$i<count($js);$i++){
  $body.='<div class="panel panel-info">';
  $body.='<div class="panel-heading">';
  $body.='<div class="input-group input-group-sm">';
  $body.='<span class="input-group-addon">質問　'.($i+1).'</span>';
  $body.='<input type="text" style="font-size:13px;" class="form-control question '.$i.'" value="'.$js[$i][0]->question.'" aria-describedby="sizing-addon3">';
  $body.='<span class="input-group-btn">';
  $body.='<button type="button" style="margin:0 0 0 5px;" class="btn-danger btn-xs delq" delqnum="'.$i.'">削除</button>';
  $body.='</span>';
  $body.='</div>';
  $body.='</div>';

  $body.='<div class="panel-body">';
  if(count($js[$i])>1){
    $body.='<input type="radio" name="selecttype'.$i.'" value="radio"';
    if($js[$i][0]->stype==0){
      $body.=' checked';
    }
    $body.='><label>　択一</label>';//stype=0:ラジオボックス
    $body.='<input type="radio" name="selecttype'.$i.'" value="check" style="margin:0 0 0 30px;"';
    if($js[$i][0]->stype==1){
      $body.=' checked';
    }
    $body.='><label>　複数選択可</label>';//stype=1:チェックボックス
  }

  for($j=0;$j<(count($js[$i])-1);$j++){
    $body.='<div class="input-group input-group-sm">';
    $body.='<span class="input-group-addon">回答　'.($j+1).'</span>';
    $body.='<input type="text" style="font-size:13px;" class="form-control answer" value="'.$js[$i][$j+1]->answer.'" aria-describedby="sizing-addon3">';
    $body.='<span class="input-group-btn">';
    $body.='<button type="button" style="margin:0 0 0 5px;" class="btn-danger btn-xs delcan" delqnum="'.$i.'" delnum="'.($j+1).'">削除</button>';
    $body.='</span>';
    $body.='</div>';
  }
  $body.='<div style="height:20px;"></div>';

  //回答項目追加ボタン
  $body.='<button class="btn btn-primary btn-xs addask" question="'.$i.'" style="float:right;">　+　</button><span style="float:right;">回答項目追加　</span>';

  //自由記入チェックボックス
  if($js[$i][0]->check=="true"){
    $checked='checked';
  }else{
    $checked='';
  }
  if($js[$i][0]->nothaveto=="true"){
    $nothaveto='checked';
  }else{
    $nothaveto='';
  }

  $body.='<input type="checkbox" class="checkask" check='.$i.' '.$checked.'>自由解答欄を設ける';
  $body.='<input type="checkbox" class="nothaveto" check='.$i.' '.$nothaveto.' style="margin:0 0 0 30px;">無回答OKにする';
  $body.='</div>';
  $body.='</div>';
}
$body.='<button id="addq" class="btn btn-primary btn-xs" qnum="'.count($js).'" style="float:right;">　+　</button><span style="float:right;">質問追加　</span>';
echo $body;
