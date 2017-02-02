<?php

session_start();
require_once('Member.php');
require_once('File.php');
require_once('Question.php');

//localのみ
$_SESSION['login_name']="武藤　一徳";
$_SESSION['loginid']=10042;

class Circular 
{
  public $id;//id
  public $owner;//作成者
  public $title;//表題
  public $content;//内容
  public $submittime;//回覧開始日
  public $status;//0:まだ,1:fin
  public $secret;//非公開にするか
  public $questions;//質問
  public $members;//回覧メンバー
  public $files;//ファイル

  function initWithID($id)//8桁(期+6桁)の伝票番号で初期化
  {
    $this->id= $id;

    //伝票番号の中で最新の枝番のデータ
    $sql = 'select * from circular where id = '.$this->id;
    $rst = selectData(DB_NAME, $sql);
    
    if($rst!=null){
      $this->owner = $rst[0]['owner'];
      $this->title = $rst[0]['title'];
      $this->content = $rst[0]['content'];
      $this->submittime = $rst[0]['submittime'];
      $this->path = $rst[0]['path'];
      $this->status = $rst[0]['status'];
      $this->secret = $rst[0]['secret'];

      $this->files = $rst[0]['files'];
      $this->members = $rst[0]['members'];
      $this->questions = $rst[0]['questions'];

      $sql1 = 'select id from files where circularID = '.$this->id;
      $sql2 = 'select id from members where circularID = '.$this->id;
      $sql3 = 'select id from questions where circularID = '.$this->id;

      $rst1 = selectData(DB_NAME, $sql1);
      $rst2 = selectData(DB_NAME, $sql2);
      $rst3 = selectData(DB_NAME, $sql3);

      $this->files=array();
      for($i=0;$i<count($rst1);$i++){
        $ps = new File;
        $ps->initWithFileID($rst1[$i]['id']);
        $this->files[] = $ps;
      }
      $this->members=array();
      for($i=0;$i<count($rst2);$i++){
        $ps = new Member;
        $ps->initWithMemberID($rst2[$i]['id']);
        $this->members[] = $ps;
      }
      $this->questions=array();
      for($i=0;$i<count($rst3);$i++){
        $ps = new Question;
        $ps->initWithQuestionID($rst3[$i]['id']);
        $this->questions[] = $ps;
      }
    }
  }

  //回覧作成
  function AddCircular($title,$content,$sdate,$secret,){
    $sql='insert into circular values (null,'.$_SESSION['loginid'].',"'.$title.'","'.$content.'","'.date('Y-m-d').'",0,'.$secret.')';
    deleteFrom(DB_NAME,$sql);
  }

  //伝票IDを入れてリロードする。
  function reload(){
    $this->initWithID($this->id);
  }
}

