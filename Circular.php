<?php

require_once('Member.php');
require_once('File.php');
require_once('Question.php');

class Circular 
{
  public $id;//id
  public $ownerID;//作成者
  public $title;//表題
  public $content;//内容
  public $submitDate;//回覧開始日
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
      $this->ownerID = $rst[0]['ownerID'];
      $this->title   = $rst[0]['title'];
      $this->content = $rst[0]['content'];
      $this->submitDate = $rst[0]['submitDate'];
      $this->status = $rst[0]['status'];
      $this->secret = $rst[0]['secret'];

      $sql1 = 'select id from files where circularID = '.$this->id;
      $sql2 = 'select id from member where circularID = '.$this->id;
      $sql3 = 'select id from question where circularID = '.$this->id;

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
  function AddCircular($title,$content,$secret){
    if($secret==true){
      $secret=1;
    }else{
      $secret=0;
    }
    $sql='insert into circular values (null,'.$_SESSION['loginid'].',"'.myescape($title).'","'.myescape($content).'","'.date('Y-m-d H:i:s').'",0,'.$secret.')';
    $this->id=insertAI(DB_NAME,$sql);
    $this->reload();
  }

  //伝票IDを入れてリロードする。
  function reload(){
    $this->initWithID($this->id);
  }

}

