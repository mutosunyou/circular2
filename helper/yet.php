<?php
session_start();
require_once('../master/prefix.php');
require_once('../Circular.php');

//本文========================================================
$body='';

//回覧メンバーに選ばれている回覧IDを検索する
$sql='select * from member where userID='.$_SESSION['loginid'];
$sql.=' and checked=0';
$rst=selectData(DB_NAME,$sql);

$sql = 'select * from circular where id in (';
for($i=0;$i<count($rst);$i++){
  $sql .= $rst[$i]['circularID'];
  if($i!=(count($rst)-1)){
    $sql .= ',';
  }
}
$sql.=')';
$rst=selectData(DB_NAME,$sql);

if(isset($_POST['sortKey']) && strlen($_POST['sortKey']) > 0){
  $sql .= ' order by '.$_POST['sortKey'];
}
$sql .= ' '.$_POST['sortOrder'];
$cst = selectData(DB_NAME, $sql);

//var_dump($sql);
//有効なプロミス項目を並べて表示
$pname = array(
  "　"=>"aaa".' style="text-align:left;width:50px;"',
  "表題"=>"bbb".' style="text-align:left;"',
  "作成者"=>"ccc".' style="text-align:left;width:100px;"',
  "回覧開始日"=>"ddd".' style="text-align:left;width:100px;"'
);

//表
$body .= '<table class="table table-condensed">';
foreach($pname as $key => $value){
  $body .= '<th class="sorter" name='.$value.'>'.$key.'</th>';
}
for($i=0;$i<count($cst);$i++){//指定されたuserIDのデータ全て
  $body .= '<tr';
  if($cst[$i]['status']==1){
    $body .= ' style="background:silver;"';
  }
  $body .= '>';
  $body .= '<td style="nowrap"><button name="'.$cst[$i]['id'].'" class="dispcontents btn btn-default btn-xs">表示</button></td>';
  $body .= '<td style="nowrap">'.$cst[$i]['title'].'</td>';

  $body .= '<td style="nowrap">'.nameFromUserID($cst[$i]['ownerID']).'</td>';
  $body .= '<td style="nowrap">'.date('Y-m-d',strtotime($cst[$i]['submitDate'])).'</td>';
  $body .= '</tr>';
}
$body .= '</table>';

echo $body;

