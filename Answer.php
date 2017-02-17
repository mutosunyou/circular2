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
  public $sum;//回答の集計（配列）
  
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
      //回答の数を集計
      $this->sum=array();
      $sql = 'select answer,count(*) from answer where questionID='.$this->qid.' group by answer';
      $rst = selectData(DB_NAME,$sql);
      for($i=0;$i<count($rst);$i++){
        $this->sum[] = array('answer'=>$rst[$i]['answer'],'count'=>$rst[$i]['count(*)']);
      }
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
    $sql = 'insert into answer values(null,'.$uid.','.$qid.','.$a.','.$d.')';
    var_dump($sql);
    $this->id=insertAI(DB_NAME,$sql);
    $this->reload();
  }

  //伝票IDを入れてリロードする。
  function reload(){
    $this->initWithAnswerID($this->id);
  }
}


