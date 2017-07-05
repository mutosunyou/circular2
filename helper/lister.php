<?php
session_start();
require_once('../master/prefix.php');
require_once('../Circular.php');

//回覧メンバーに選ばれている回覧IDを検索する
$sql='select * from member where userID='.$_SESSION['loginid'];
$rst=selectData(DB_NAME,$sql);

$sql = 'select id from circular where (';
if($_POST['own']==1){
  $sql.=' ownerID='.$_SESSION['loginid'].' or';
}
$sql.= ' id in (';
for($i=0;$i<count($rst);$i++){
  $sql .= $rst[$i]['circularID'];
  if($i!=(count($rst)-1)){
    $sql .= ',';
  }
}
$sql.='))';
if($_POST['own']==1){
  $sql.=' and ownerID='.$_SESSION['loginid'];
}

//デバッグ用

if($_SESSION['loginid']==10042){
  $sql='select id from circular where 1';
}
 
if (isset($_POST['searchKey']) && strlen($_POST['searchKey']) > 0) {
  $sql .= ' and (title like "%'.$_POST['searchKey'].'%" or content like "%'.$_POST['searchKey'].'%")';
}
if(isset($_POST['sortKey']) && strlen($_POST['sortKey']) > 0){
  $sql .= ' order by '.$_POST['sortKey'];
}
$sql .= ' '.$_POST['sortOrder'];
$cst = selectData(DB_NAME, $sql);
//var_dump($sql);
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

$body .= '表示：<select class="form-control ppi">';
$body .= '<option value="10">10</option>';
for ($i=1; $i < 11; $i++) {
  $body .= '<option value="'.($i * 20).'"';
  if($_POST['itemsPerPage']==$i*20){
    $body .=' selected';
  }
  $body.='>'.($i * 20).'</option>';
}
$body .='</select>件　　';
$body .='<input class="finderfld form-control" type="text" value="'.$_POST['searchKey'].'">';
$body .='<button  class="finderbtn btn btn-default btn-sm">検索</button>';
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
$body .= '</div>';

//有効なプロミス項目を並べて表示
$pname = array(
  " "=>"action".' style="text-align:left;width:50px;"',
  "表題"=>"title".' style="text-align:left;"',
  "状態(確認済数)"=>"status".' style="text-align:left;width:110px;"');
if($_POST['own']==0){
  $pname["作成者"]="ownerID".' style="text-align:left;width:100px;"';
}
$pname["回覧開始日"]="submitDate".' style="text-align:left;width:150px;"';

//表
$body .= '<table class="table table-condensed">';
foreach($pname as $key => $value){
  $body .= '<th class="sorter" name='.$value.'>'.$key.'</th>';
}

//$p=new Circular();
for($i=0;$i<count($cst);$i++){//指定されたuserIDのデータ全て
  //  $p->initWithID($cst[$i]['id']);
  //  $read=0;
  //  $countread=0;
  $sql='select title,ownerID,submitDate from circular where id='.$cst[$i]['id'];
  $rst_circular=selectData(DB_NAME,$sql);

  $rst_kairan=selectData('master','select * from employee where kairan=1');
  
  $sql='select * from member where circularID='.$cst[$i]['id'].' and userID in(';
  for($j=0;$j<count($rst_kairan);$j++){
    $sql.=$rst_kairan[$j]['id'];
    if($j!=(count($rst_kairan)-1)){
      $sql.=',';
    }
  }
  $sql.=')';
  $rst_member=selectData(DB_NAME,$sql);

  $sql.=' and checked=1';
  $rst_read=selectData(DB_NAME,$sql);
  
  $sql.=' and userID='.$_SESSION['loginid'];
  $rst_man=selectData(DB_NAME,$sql);

  if(count($rst_man)==1){
    $read=1;
  }else{
    $read=0;
  }
  /*
  for($j=0;$j<count($p->members);$j++){
    if($p->members[$j]->checked==1){
      $countread++;
      if($p->members[$j]->userID==$_SESSION['loginid']){
        $read=1;
      }
    }
  }
  */

  $body .= '<tr';
  if($read==1){
    $body .= ' style="background:silver;"';
  }
  $body .= '>';
  $body .= '<td style="nowrap"><button  name="'.$cst[$i]['id'].'" class="dispcontents btn btn-default btn-xs">表示</button></td>';
  $body .= '<td style="nowrap">'.$rst_circular[0]['title'].'</td>';
  $body .= '<td style="nowrap">';
  if(count($rst_read)!=count($rst_member)){
  if($read==1){
    $body .= '既読 ';
  }else{
    $body .= '<font color="red">未読 </font>';
  }
  $body .= '('.count($rst_read).'／'.count($rst_member).')';
  }else{
    $body .= '完';
  }
  $body.='</td>';
  if($_POST['own']==0){
    $body .= '<td style="nowrap">'.nameFromUserID($rst_circular[0]['ownerID']).'</td>';
  }
  $body .= '<td style="nowrap">'.date('Y-m-d H:i:s',strtotime($rst_circular[0]['submitDate'])).'</td>';
  $body .= '</tr>';
}
$body .= '</table>';

echo $body;

