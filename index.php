<?php
//初期==============================================
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

//左側
$body.='<ul class="nav navbar-nav">';
$body.='<li id="listrun" class="bankmenu"><a tabindex="-1">回覧板</a></li>';
$body.='<li id="list" class="active applymenu"><a href="#" tabindex="-1">新規作成</a></li>';
$body.='<li id="input" class="applymenu"><a href="list.php" tabindex="-1">回覧リスト</a></li>';
$body.='<li id="input" class="applymenu"><a href="../circular/index.php" tabindex="-1">旧回覧板</a></li>';
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

//クラスと変数=====================================
$body.='<input id="userID" class="hidden" value="'.$_SESSION['loginid'].'">';

//本文/////////////////////////////////////////////
//タイトル=========================================
$body.='<div class="container-fluid">';
$body.='<div class="container">';
$body.='<h2 class="toptitle">';
$body.='回覧 新規作成';
$body.='</h2><hr />';

//一番上のエリア
$body.='<div style="width:900px;">';

//フォーム=========================================
$body.='<h3>伝達事項<small>　伝達内容を記入してください</small></h3>';

$body.='<div class="input-group input-group-sm">';
$body.='<span class="input-group-addon">タイトル</span>';
$body.='<input type="text" id="title" style="font-size:13px;" class="form-control">';
$body.='</div>';

$body.='<div class="input-group input-group-sm">';
$body.='<span class="input-group-addon">内　　容</span>';
$body.='<textarea class="form-control" id="cont" rows="5" cols="90" style="height:150px;"></textarea>';
$body.='</div>';

$body.='</div>';//一番上のエリア終わり

//左ブロック=======================================
$body.='<div style="display:inline-block;width:520px;vertical-align:top;margin:0 0px 0 0;">';

//回覧対象=========================================
$body.='<h3>回覧対象<small>　回覧対象を選択してください。</small></h3>';
$body.='<table style="font-size:9pt;">';
$body.='<tbody>';
$body.='<tr>';
$body.='<td><h5>回覧するメンバー</h5></td>';
$body.='<td></td>';
$body.='<td>';

$mem = new MemberList();
$bumons = $mem->bumonList(0);

$body.='<select id="bselector" style="height:22px;">';
$body.='<option value="0">全部門</option>';
for($i=0;$i<count($bumons);$i++){
  $body.='<option value="'.$bumons[$i]['bid'].'">'.$bumons[$i]['name'].'</option>';
}
$body.='</select>';
$body.='</td>';
$body.='</tr>';
//-------------------
$body.='<tr>';
$body.='<td >';
$body.='<select id="selectedlist" multiple size="10" style="width:150px;">';
$body.='</select>';
$body.='</td>';

$body.='<td align="center" style="width:100px;">';
$body.='<a id="addAllItem" class="btn btn-xs btn-default">←全追加</a>';
$body.='<br><br>';
$body.='<a id="addSelectedItem" class="btn btn-xs btn-default">←追加</a>';
$body.='<br><br>';
$body.='<a id="removeSelectedItem" class="btn btn-xs btn-default">削除→</a>';
$body.='<br><br>';
$body.='<a id="removeAllItem" class="btn btn-xs btn-default">全削除→</a>';
$body.='</td>';

$body.='<td>';
$sql='select * from employee where kairan=1';
$rst=selectData('master',$sql);

$body.='<select id="userlist" multiple size="8" style="width:150px;">';

$members = $mem->memberList(0);
for($i=0;$i<count($members);$i++){
  $body.=' <option value="'.$members[$i]['id'].'" bumon="'.$members[$i]['bumon_code'].'">'.$members[$i]['short_name'].'</option>';
}
$body.='</select>';
$body.='</td>';
$body.='</tr>';
$body.='</tbody>';
$body.='</table>';
$body.='</font>';

$body.='</div>';//左ブロック終わり

//右ブロック=========================================
$body.='<div style="display:inline-block;width:370px;vertical-align:top;">';

//添付資料===========================================
$body .= '<h3> 添付資料<small>　添付資料があれば選択してください</small></h3>';

//fileのアップロード=================================
$rst = selectData(DB_NAME,'select max(id) from circular');
$cid = $rst[0]['max(id)'];
$cid++;
$body.='<input type="hidden" id="cid" value="'.$cid.'">';

$body.='<div id="queue" class="well" style="border: 1px solid #E5E5E5;overflow: auto;margin-bottom: 10px;padding: 0 3px 3px;min-height:150px;">';
$body.='<span style="font-weight:bold">ここにファイルをドロップしてください(複数可)';
$body.='</span></div>';
$body.='<input id="file_upload" name="file_upload" type="file" multiple="true">';
$body.='<br />';

$body.="<div id='fileup'></div>";

$body.='</div>';//右ブロック終わり

//横線===============================================
$body.='<hr>';
$body.='<input type="checkbox" id="enablequestionnaire" />アンケートを作成する<br>';

//アンケート=========================================
$body.='<div id="questionnaire">';
$body.='<div style="display:inline-block;width:890px;vertical-align:top;margin:0 50px 0 0;">';
$body.='<h3>アンケート<small>　</small></h3>';
$body.='<div id="qlist"></div>';
$body.='</div>';

//横線===============================================
$body.='<hr />';

//シークレット=======================================
$body.='<h3>シークレット<small>　アンケート結果を作成者以外に公開したくないときはチェックを入れてください</small></h3>';
$body.='<input type="checkbox" id="secret" />アンケートの結果を公開しない';

//横線===============================================
$body.='<hr />';

$body.='</div>';//div id=questionnaire

//送信ボタン=========================================
$body.='<button id="sendbtn" class="btn btn-sm btn-success pull-right" disabled="disabled">送信内容確認</button>';

$body.='<div id="ppp"></div>';//デバッグ用

$body.='</div>';//container

//確認画面
$body.='<div id="hiddenwall" style="display:none;width:100%;height:100%;background-color:rgba(10,10,10,0.5);position:fixed;top:0px;z-index:99;">';
$body.='<div id="confirm" style="margin-top:1%;text-align:center;width:750px;height:700px;margin-left:auto;margin-right:auto;overflow:auto;">';
$body.='</div>';
$body.='</div>';//確認画面終わり

$body.='</div>';//container-fluid


//ヘッダー===========================================
$header ='<script type="text/javascript" src="index.js"></script>';
$header.='<style type="text/css">';
$header.='<!--
  .input-group{
  margin:5px 10px 5px 0;
  }
  -->';
$header.='</style>';

//HTML作成===========================================
echo html('回覧板',$header, $body);
