<?php
session_start();
require_once('master/prefix.php');
require_once('MemberList.php');

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
$body.='<!-- メニューボタン 
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

$author2=0;
$sql='select * from author';
$rst=selectData(DB_NAME,$sql);
for($i=0;$i<count($rst);$i++){
  if($rst[$i]['userID']==$_SESSION['loginid']){
    $author2=1;
  }
}

//左側
$body.='<ul class="nav navbar-nav">';
$body.='<li id="listrun" class="bankmenu"><a tabindex="-1">回覧板</a></li>';
$body.='<li id="list" class="applymenu"><a href="index.php" tabindex="-1">新規作成</a></li>';
$body.='<li id="input" class="active applymenu"><a href="#" tabindex="-1">回覧リスト</a></li>';
if($author2==1){
  $body.='<li id="input" class="applymenu"><a href="force.php" tabindex="-1">強制既読</a></li>';
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

//隙間調整
$body.='<div id="topspace" style="height:70px;"></div>';

//クラスと変数===========================================
$body.='<input id="status" class="hidden" value="'.$p->status.'">';
$body.='<input id="userID" class="hidden" value="'.$p->userID.'">';

//本文//
//コンテナ始まり=========================================
$body.='<div class="container-fluid">';
$body.='<div class="container">';

//タイトル===============================================
//タブ
$body.='<div class="tabs tabs_default">';
$body.='<h2 class="toptitle">回覧リスト</h2><br>';
$body.='<ul class="horizontal" style="margin:0 0 0 0;">';
$body.='<li id="alltab"><a href="#lister">全部</a></li>';
$body.='<li id="yettab"><a href="#yet">未確認</a></li>';
$body.='<li id="owntab"><a href="#own">自分が作成</a></li>';
$body.='</ul>';

$body.='<hr style="margin:0 0 0 0;">';
$body.='<div id="lister"></div>';
$body.='<div id="yet"></div>';
$body.='<div id="own"></div>';
$body.='</div>';

//コンテナ終わり
$body.='</div>';
$body.='</div>';

//ヘッダー=========================================
$header ='<script type="text/javascript" src="list.js"></script>';
$header.='<style type="text/css">';
$header.='<!--
  .input-group{
  margin:5px 10px 5px 0;
  }
  -->';
$header.='</style>';

echo html('回覧板',$header, $body);
