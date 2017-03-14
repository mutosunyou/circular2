<?php
//初期==============================================
session_start();
require_once('master/prefix.php');
require_once('Circular.php');

//ログイン処理======================================
$sql = "SELECT * FROM employee";
$rst = selectData('master',$sql);
if (isset($_SESSION["login_name"])){
  $sessionCounter = 0;
  for($i = 0; $i < count($rst); $i++) {
    if ($_SESSION["login_name"] == $rst[$i]["person_name"]){
      $sessionCounter = $sessionCounter + 1;
    }
  }
  if ($sessionCounter == 0){
    header("Location: index.php");
    exit;
  }
  $_SESSION['loginid']=userIDFromName($_SESSION["login_name"]);
}else{
  header("Location: ../portal/index.php");
  exit;
}
$_SESSION['expires'] = time();
if ($_SESSION['expires'] < time() - 7) {
  session_regenerate_id(true);//sessionIDを生成しなおす
  $_SESSION['expires'] = time();
}

//ナビバー=========================================
$body='<nav class="navbar navbar-default navbar-fixed-top" role="navigation">';
$body.='<div class="container-fluid">';
$body.='<div class="navbar-header">';
$body.='<!-- 
  メニューボタン 
  data-toggle : ボタンを押したときにNavbarを開かせるために必要
  data-target : 複数navbarを作成する場合、ボタンとナビを紐づけるために必要
  -->
  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#nav-menu-1">
  <span class="sr-only">Toggle navigation</span>
  <span class="icon-bar"></span>
  <span class="icon-bar"></span>
  <span class="icon-bar"></span>
  </button>';
$body.='<a class="navbar-brand" href="/php/menu" tabindex="-1"><img alt="Brand" src="./master/favicon.ico"></a>'; 
$body.='</div>';
$body.='<div class="collapse navbar-collapse" id="nav-menu-1">';

$author=0;
$sql='select * from author';
$rst=selectData(DB_NAME,$sql);
for($i=0;$i<count($rst);$i++){
  if($rst[$i]['userID']==$_SESSION['loginid']){
    $author=1;
  }
}

//左側
$body.='<ul class="nav navbar-nav">';
$body.='<li id="listrun" class="bankmenu"><a tabindex="-1">回覧板</a></li>';
$body.='<li id="list" class="applymenu"><a href="index.php" tabindex="-1">新規作成</a></li>';
$body.='<li id="input" class="applymenu"><a href="list.php" tabindex="-1">回覧リスト</a></li>';
if($author==1){
  $body.='<li id="input" class="active applymenu"><a href="#" tabindex="-1">強制既読</a></li>';
}
$body.='</ul>';

//右側
$body.='<ul class="nav navbar-nav pull-right">';
$body.='<li><a href="./master/logout.php">ログアウト</a></li>';
$body.='<li><a tabindex="-1">'.$_SESSION['login_name'].'</a></li>';
$body.='</ul>';

$body.='</div>';
$body.='</div>';
$body.='</nav>';

//隙間調整=========================================
$body.='<div id="topspace" style="height:70px;"></div>';

$p = new Circular();
$p->initWithID($_GET['cid']);

$author=0;
if($p->secret==0 || $p->ownerID==$_SESSION['loginid']){//公開もしくは自分が作成者
  $author=1;
}
//クラスと変数=====================================
$body.='<input id="userID" class="hidden" value="'.$_SESSION['loginid'].'">';
$body.='<input id="cid" class="hidden" value="'.$p->id.'">';
$body.='<input id="qcount" class="hidden" value="'.count($p->questions).'">';
$body.='<input id="author" class="hidden" value="'.$author.'">';

//本文/////////////////////////////////////////////
//タイトル=========================================
$body.='<div class="container-fluid">';
$body.='<div class="container">';
$body.='<h2 class="toptitle">';
$body.='回覧内容';
$body.='</h2><hr />';


//閲覧済みチェック//////////////////////////
$body.='<div class="panel panel-default">';
$body.='<div class="panel-heading">閲覧確認</div>';
$body.='<table class="table table-bordered">';
$body.='<tr><th width="50%">閲覧済み</th><th>未確認</th></tr>';
$body.='<tr><td>';
for($i=0;$i<count($p->members);$i++){
  if($p->members[$i]->checked==1){
    $body.=nameFromUserID($p->members[$i]->userID).'<br>';
  }
}
$body.='</td>';
$body.='<td>';
for($i=0;$i<count($p->members);$i++){
  if($p->members[$i]->checked==0){
    $body.=nameFromUserID($p->members[$i]->userID).'<br>';
  }
}
$body.='</td></tr>';
$body.='</table>';

$body.='</div>';//パネル終わり

//送信ボタン=========================================
if($yetanswer==1){
  $body.='<button id="sendbtn" class="btn btn-sm btn-primary pull-right">確認</button>';
}

$body.='</div>';//container
$body.='</div>';//container-fluid

//ヘッダー===========================================
$header ='<script type="text/javascript" src="disp.js"></script>';
$header.='<style type="text/css">';
$header.='<!--
  .input-group{
  margin:5px 10px 5px 0;
  }
  -->';
$header.='</style>';

//HTML作成===========================================
echo html('回覧板',$header, $body);
