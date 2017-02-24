<?php
//session_start();
require_once('master/prefix.php');
class Answer
{
  public $id;
  public $memberID;
  public $qID;//質問番号
  public $answer;//回答番号
  public $description;//自由記入項目
  
  function initWithAnswerID($id)//8桁(期+6桁)の伝票番号で初期化
  {
    $this->id= $id;
    $sql = 'select * from answer where id = '.$this->id;
    $rst = selectData(DB_NAME, $sql);
    if($rst!=null){
      $this->qID = $rst[0]['questionID'];
      $this->memberID = $rst[0]['memberID'];
      $this->answer = $rst[0]['answer'];
      $this->description = $rst[0]['description'];
    }
  }

  function addAnswer($uid,$qid,$answer,$desc){
    if($desc==null){
      $d='null';
    }else{
      $d='"'.$desc.'"';
    }
    if($answer==null){
      $a='null';
    }else{
      $a=$answer;
    }
    $sql = 'insert into answer values(null,'.$uid.','.$qid.','.$a.','.myescape($d).')';
    $this->id=insertAI(DB_NAME,$sql);
    $this->reload();
  }

  //伝票IDを入れてリロードする。
  function reload(){
    $this->initWithAnswerID($this->id);
  }
}


