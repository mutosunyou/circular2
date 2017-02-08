<?php
//session_start();

class Member 
{
  public $id;
  public $circularID;
  public $userID;
  public $checked;
  public $checkedDate;

  function initWithMemberID($id)//8桁(期+6桁)の伝票番号で初期化
  {
    $this->id= $id;

    $sql = 'select * from member where id = '.$this->id;
    $rst = selectData(DB_NAME, $sql);
    if($rst!=null){
      $this->circularID = $rst[0]['circularID'];
      $this->userID = $rst[0]['userID'];
      $this->checked = $rst[0]['checked'];
      $this->checkedDate = $rst[0]['checkedDate'];
    }
  }

  function reload(){
    $this->initWithMemberID($this->id);
  }
}

