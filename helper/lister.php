<?php
session_start();
require_once('../Circular.php');

//localのみ=========================================
$_SESSION['login_name']="武藤　一徳";
$_SESSION['loginid']=10042;

//回覧メンバーに選ばれている回覧IDを検索する
$sql='select * from member where userID='.$_SESSION['loginid'];
$rst=selectData(DB_NAME,$sql);

$sql = 'select id from circular where';
if($_POST['own']==1){
  $sql.=' ownerID='.$_SESSION['loginid'].' and';
}
$sql.= ' id in (';
for($i=0;$i<count($rst);$i++){
  $sql .= $rst[$i]['circularID'];
  if($i!=(count($rst)-1)){
    $sql .= ',';
  }
}
$sql.=')';
if (isset($_POST['searchKey']) && strlen($_POST['searchKey']) > 0) {
  $sql .= ' and (title like "%'.$_POST['searchKey'].'%" or content like "%'.$_POST['searchKey'].'%")';
}
if(isset($_POST['sortKey']) && strlen($_POST['sortKey']) > 0){
  $sql .= ' order by '.$_POST['sortKey'];
}
$sql .= ' '.$_POST['sortOrder'];
$cst = selectData(DB_NAME, $sql);

//項目数を取得
$cr = count($cst);
if ($_POST['itemsPerPage'] != 0) {
  $sql .= ' limit '.$_POST['itemsPerPage'].' offset '.(($_POST['page'] - 1) * $_POST['itemsPerPage']);
}
$cst = selectData(DB_NAME, $sql);

//ページ数を取得
$body = '';
//本文========================================================
//検索
$body .= '<div class="pull-right form-inline" style="float:right;margin:0 0 10px 0;">';
$countofpage = ceil($cr/intval($_POST['itemsPerPage']));
//$body .= '<br>';

$body .= '表示：<select class="form-control" id="ppi">';
$body .= '<option value="10">10</option>';
for ($i=1; $i < 11; $i++) {
  $body .= '<option value="'.($i * 20).'"';
  if($_POST['itemsPerPage']==$i*20){
    $body .=' selected';
  }
  $body.='>'.($i * 20).'</option>';
}
$body .='</select>件　　';
$body .='<input id="finderfld" class="form-control" type="text">';
$body .='<button id="finderbtn" class="btn btn-default btn-sm">検索</button>';
$body .='<div class="clearfix"></div>';

//ページ番号
$body .='<nav class="form-inline pull-right" style="margin:10px 0 0 0;">';
$body .='<ul class="pagination" style="margin:0 0 0 0;">';
for ($i=1; $i <= $countofpage; $i++){
  $body .= '<li';
  if ($i == $_POST['page']){
    $body .= ' class="active"';
  }
  $body .= '><a class="pagebtn" name="'.$i.'">'.$i.'</a></li>';
}
$body .= '</ul>';
$body .= '</nav>';
$body .='</div>';

//有効なプロミス項目を並べて表示
$pname = array(
  " "=>"action".' style="text-align:left;width:50px;"',
  "表題"=>"title".' style="text-align:left;"',
  "状態"=>"status".' style="text-align:left;width:100px;"');
if($_POST['own']==0){
  $pname["作成者"]="ownerID".' style="text-align:left;width:100px;"';
}
  $pname["回覧開始日"]="submitDate".' style="text-align:left;width:100px;"';

//表
$body .= '<table class="table table-condensed">';
foreach($pname as $key => $value){
  $body .= '<th class="sorter" name='.$value.'>'.$key.'</th>';
}

$p=new Circular();
for($i=0;$i<count($cst);$i++){//指定されたuserIDのデータ全て
  $p->initWithID($cst[$i]['id']);
  $body .= '<tr';
  if($p->status==1){
    $body .= ' style="background:silver;"';
  }
  $body .= '>';
  $body .= '<td style="nowrap"><button  name="'.$p->id.'" class="dispcontents btn btn-default btn-sm">表示</button></td>';
  $body .= '<td style="nowrap">'.$p->title.'</td>';
  $body .= '<td style="nowrap">';
  if($p->status==1){
    $body .= '回覧済み';
  }else{
    $body .= '<font color="red">回覧中</font>';
  }
  $body .= '</td>';
  if($_POST['own']==0){
    $body .= '<td style="nowrap">'.nameFromUserID($p->ownerID).'</td>';
  }
  $body .= '<td style="nowrap">'.date('Y-m-d',strtotime($p->submitDate)).'</td>';
  $body .= '</tr>';
}
$body .= '</table>';

echo $body;

