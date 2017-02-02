<?php
session_start();
require_once('Answer.php');

class Question 
{
  public $id;//id
  public $circularID;
  public $content;
  public $freespace;

  function initWithQuestionID($id)//8桁(期+6桁)の伝票番号で初期化
  {
    $this->id= $id;
    $sql = 'select * from question where id = '.$this->id;
    $rst = selectData(DB_NAME, $sql);
    if($rst!=null){
      $this->circularID = $rst[0]['circularID'];
      $this->content = $rst[0]['content'];
      $this->freespace = $rst[0]['freespace'];
    }
  }

  function reload(){
    $this->initWithQuestionID($this->id);
  }


