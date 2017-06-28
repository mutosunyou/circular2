<?php
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
$body.='<li id="input" class="applymenu"><a href="list.php" tabindex="-1">回覧リスト</a></li>';
$body.='<li id="display" class="active applymenu"><a href="#" tabindex="-1">回覧内容確認</a></li>';
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

//隙間調整=========================================
$body.='<div id="topspace" style="height:70px;"></div>';

$p = new Circular();
$p->initWithID($_GET['cid']);

$author=0;
for($i=0;$i<count($p->members);$i++){
  if(($_SESSION['loginid']==$p->ownerID || ($p->secret==0 && $_SESSION['loginid']==$p->members[$i]->userID && $p->members[$i]->checked==1)) && count($p->questions)>0){
    $author=1;
  }
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

//一番上のエリア
$body.='<div class="panel panel-default">';
$body.='<div class="panel-heading">作成者</div>';
$body.='<div class="panel-body">'.nameFromUserID($p->ownerID).'</div>';
$body.='</div>';

$body.='<div class="panel panel-default">';
$body.='<div class="panel-heading">タイトル</div>';
$body.='<div class="panel-body">'.$p->title.'</div>';
$body.='</div>';

$body.='<div class="panel panel-default">';
$body.='<div class="panel-heading">内容</div>';
$body.='<div class="panel-body">'.$p->content.'</div>';
$body.='</div>';

$body.='<div class="panel panel-default">';
$body.='<div class="panel-heading">添付資料</div>';
$body.='<div class="panel-body">';
for($i=0;$i<count($p->files);$i++){
  $body.='<a href="'.$p->files[$i]->filepath.'">'.$p->files[$i]->filepath.'</a><br>';
}
$body.='</div>';
$body.='</div>';
$body.='<hr>';
if(count($p->questions)>0){
$body.='<h3>アンケート: ';
if($p->secret==1){
  $body.='<span>非公開</span><span class="glyphicon glyphicon-lock" aria-hidden="true" style="margin:0px 0 0 10px;"></span></h4><br><div class="clearfix"></div>';
}else{
  $body.='<span>公開</span><span class="glyphicon glyphicon-globe" aria-hidden="true" style="margin:0px 0 0 10px;"></span></h4><br><div class="clearfix"></div>';
}
$body.='</h3>';
}
//アンケート集計結果
for($i=0;$i<count($p->members);$i++){
  //質問が１個以上で、作成者もしくは回覧メンバーに入っていて回答済みのとき結果を見せる
  if(($_SESSION['loginid']==$p->ownerID || ($p->secret==0 && $_SESSION['loginid']==$p->members[$i]->userID && $p->members[$i]->checked==1)) && count($p->questions)>0){
    $body.='<div id="resultlist">';
    //j番目の質問とそれぞれの集計結果を示す。
    for($j=0;$j<count($p->questions);$j++){//j: 質問番号
      $body.='<div class="panel panel-default">';
      $body.='<div class="panel-heading">アンケート結果 '.($j+1).'</div>';
      $body.='<table class="table table-bordered">';
      $chartflg=0;
      for($k=0;$k<count($p->questions[$j]->answers);$k++){
        if($p->questions[$j]->answers[$k]->answer!=null ){
          $chartflg=1;//j番目の質問に回答があればchartflg=1
        }
      }
      $body.='<thead>';
      $body.='<tr><td colspan="3">'.$p->questions[$j]->content;
      $body.='</td></tr>';
      if($author==1){
        $body.='<tr><td colspan="3" class="info">グラフ</td></tr>';
        $body.='<tr><td colspan="3"><div class="charts'.$j.'"></div></td></tr>';
      }
      if($chartflg==1){
        $body.='<tr><td colspan="3" class="info">';
        if($author==1){
          $body.='集計結果';
        }else{
          $body.='回答結果';
        }
        $body.='</td></tr>';
        $body.='<tr><th style="width:100px;">集計</th><th>項目</th><th>メンバー</th></tr>';
      }
      $body.='</thead>';

      $body.='<tbody>';
      //k番目の回答とその数を数える
      if($chartflg==1){
        for($k=0;$k<count($p->questions[$j]->candidates);$k++){//k: 候補番号
          $body.='<tr>';
          $body.='<td>';
          if($author==1){
            $sql='select answer,count(*) from answer where questionID='.$p->questions[$j]->id.' and answer='.$k.' group by answer';
            $rst=selectData(DB_NAME,$sql);
            if($rst!=null){
              $body.=$rst[0]['count(*)'];
            }else{
              $body.='0';
            }
          }
          $body.='</td>';
          $body.='<td>';
          $body.='<div class="charttitle'.$k.'" value="'.$p->questions[$j]->candidates[$k].'">'.$p->questions[$j]->candidates[$k].'</div>';
          $body.='</td>';
          $body.='<td style="font-size:small;">';
          //k番目の回答を選択したメンバーの名前を羅列する。
          $sql='select memberID from answer where questionID='.$p->questions[$j]->id.' and answer='.$k;
          if($author==0){
            $sql.=' and memberID='.$_SESSION['loginid'];
          }
          $rst=selectData(DB_NAME,$sql);
          for($l=0;$l<count($rst);$l++){
              $body.=shortNameFromUserID($rst[$l]['memberID']);
              if($l!=(count($rst)-1)){
                $body.=', ';
              }
          }
          $body.='</td>';
          $body.='</tr>';
        }
      }
      if($p->questions[$j]->freespace==1){
        $body.='<tr><td colspan="3" class="info">自由記入欄</td></tr>';
        for($k=0;$k<count($p->questions[$j]->answers);$k++){
          if($p->questions[$j]->answers[$k]->description!=null){
            if($author==1 || $p->questions[$j]->answers[$k]->memberID==$_SESSION['loginid']){
              $body.='<tr><td colspan="3">';
              $body.=shortNameFromUserID($p->questions[$j]->answers[$k]->memberID).': '.$p->questions[$j]->answers[$k]->description;
              $body.='</td></tr>';
            }
          }
        }
      }
      $body.='</tbody>';
      $body.='</table>';
      $body.='</div>';
    }
    $body.='<hr>';
    $body.='</div>';
    break;
  }
}

$author=0;
for($i=0;$i<count($p->members);$i++){
  if(($_SESSION['loginid']==$p->members[$i]->userID) && $p->members[$i]->checked==0){
    $yetanswer=1;
  }
}

//ユーザーがメンバーに入っていて、未回答であれば回答フォームを出す
if($yetanswer==1 && count($p->questions)>0){
  $body.='<div id="replylist">';
  for($j=0;$j<count($p->questions);$j++){
    $body.='<div class="panel panel-default">';
    $body.='<div class="panel-heading">アンケート'.($j+1);
    if($p->questions[$j]->nothaveto==0){
      $body.='<font color="red">　　※回答必須</font>';
    }
    $body.='</div>';
    $body.='<table class="table table-bordered">';
    $body.='<thead><tr><td colspan="2">'.$p->questions[$j]->content.'</td></tr></thead>';
    $body.='<tbody>';
    for($k=0;$k<count($p->questions[$j]->candidates);$k++){
      $body.='<tr>';
      $body.='<td>';
      $body.='<div class="';
      if($p->questions[$j]->stype==0){
        $body.='radio';
      }else{
        $body.='checkbox'; 
      }
      $body.='" style="margin:0 0 0 0;">';
      $body.='<label>';
      $body.='<input type="';
      if($p->questions[$j]->stype==0){
        $body.='radio';
      }else{
        $body.='checkbox'; 
      }
      $body.='" name="optionsRadios'.$j.'" value="'.$k.'" qid="'.$p->questions[$j]->id.'">'.$p->questions[$j]->candidates[$k].'<br>';
      $body.='</label>';
      $body.='</div>';
      $body.='</td>';
      $body.='</tr>';
    }
    if($p->questions[$j]->freespace==1){
      $body.='<tr><td colspan="2">自由記入欄<input type="text" class="form-control freespace" name="fs'.$j.'" qid="'.$p->questions[$j]->id.'"></td></tr>';
    }
    $body.='</tbody>';
    $body.='</table>';
    $body.='</div>';
  }
  $body.='</div>';
}

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
