<?php
session_start();
require_once('../master/prefix.php');
require_once('../Circular.php');

$js = json_decode($_POST['qarray']);
$jsmem = json_decode($_POST['mem']);
$jsfile= json_decode($_POST['farray']);

$body.='<div class="c" style="background:white;padding:50px 50px 50px 50px;">';
$body.='<h3>回覧内容確認</h3><hr>';
$body.='<h4 style="float:left;">共通事項</h4>';

$body.='<table class="table table-bordered" style="margin:30px 0 0 0;">';
$body.='<tr>';
$body.='<td style="width:80px;">';
$body.='表題';
$body.='</td>';
$body.='<td>';
$body.='<span style="float:left;">'.$js[0]->title.'</span>';
$body.='</td>';
$body.='</tr>';

$body.='<tr>';
$body.='<td>';
$body.='内容';
$body.='</td>';
$body.='<td>';
$body.='<span style="float:left;">'.$js[1]->content.'</span>';
$body.='</td>';
$body.='</tr>';

$body.='<tr>';
$body.='<td>';
$body.='添付';
$body.='</td>';
$body.='<td style="align:left;">';
for($i=0;$i<count($jsfile);$i++){
  $body.='<span style="float:left;">'.$jsfile[$i]->name.'</span><br>';
}
$body.='</td>';
$body.='</tr>';

$body.='<tr>';
$body.='<td>';
$body.='メンバー';
$body.='</td>';
$body.='<td><span style="float:left;">';
for($i=0;$i<count($jsmem);$i++){
  $body.=shortNameFromUserID($jsmem[$i]->num);
  if($i!=(count($jsmem)-1)){
    $body.=', ';
  }
}
$body.='</span></td>';
$body.='</tr>';

$body.='</table>';
$body.='<hr>';

//アンケートの内容=======================================================
$body.='<h4 style="float:left;">アンケート結果: ';

if($js[3]->secret==1){
  $body.='<span>非公開</span><span class="glyphicon glyphicon-lock" aria-hidden="true" style="margin:0px 0 0 10px;"></span></h4><br><div class="clearfix"></div>';
}else{
  $body.='<span>公開</span><span class="glyphicon glyphicon-globe" aria-hidden="true" style="margin:0px 0 0 10px;"></span></h4><br><div class="clearfix"></div>';
}
if(count($js[4])>0){
  for($i=0;$i<count($js[4]);$i++){
    $body.='<h4 style="float:left;">アンケート'.($i+1).'</h4>';
    $body.='<table class="table table-bordered" style="margin:0px 0 30px 0;">';
    $body.='<tr bgcolor=#eee>';
    $body.='<td style="width:70px;">';
    $body.='質問';
    $body.='</td>';
    $body.='<td>';
    $body.='<span style="float:left;">'.$js[4][$i][0]->question.'</span>';
    $body.='</td>';
    $body.='</tr>';
    for($j=1;$j<count($js[4][$i]);$j++){
      $body.='<tr>';
      $body.='<td>';
      $body.='回答'.$j;
      $body.='</td>';
      $body.='<td>';
      $body.='<span style="float:left;">'.$js[4][$i][$j]->answer.'</span>';
      $body.='</td>';
      $body.='</tr>';
    }
    //自由解答欄
    $body.='<tr align="left"><td colspan="2">';
    $body.='<span>自由解答欄</span>';
    if($js[4][$i][0]->check==1){
      $body.='<input type="text" class="form-control form-inline">';
    }else{
      $body.='なし';
    }
    $body.='</td></tr>';

    //選択方式
    if(count($js[4][$i])>1){
      $body.='<tr align="left"><td colspan="2">';
      if(count($js[4][$i])>1){
        $body.='選択方式：';
        if($js[4][$i][0]->stype==0){
          $body.='択一';
        } 
        if($js[4][$i][0]->stype==1){
          $body.='複数選択可';
        }
      }
      $body.='</td></tr>';
    }
    $body.='</table>';
  }
}


$body.='<button class="btn btn-success" id="gocircular" style="float:right;margin:0 0 0 20px;">この内容で回覧する</button>';
$body.='<button class="btn btn-default" id="cancel" style="float:right;">キャンセル</button>';
$body.='</div>';
echo $body;

