<?php
//session_start();
require_once('master/prefix.php');
class Answer
{
  public $id;
  public $qID;
  public $memberID;
  public $answer;
  public $description;

  function initWithAnswerID($id)//8桁(期+6桁)の伝票番号で初期化
  {
    $this->id= $id;

    $sql = 'select * from answer where id = '.$this->id;
    $rst = selectData(DB_NAME, $sql);
    if($rst!=null){
      $this->qID = $rst[0]['qID'];
      $this->memberID = $rst[0]['memberID'];
      $this->answer = $rst[0]['answer'];
      $this->description = $rst[0]['description'];
    }
  }

  //伝票IDを入れてリロードする。
  function reload(){
    $this->initWithAnswerID($this->id);
  }
}


