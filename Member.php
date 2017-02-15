<?php
//初期==============================================
//session_start();
require_once('Circular.php');

//localのみ=========================================
$_SESSION['login_name']="武藤　一徳";
$_SESSION['loginid']=10042;

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

  //回覧ID と メンバーオブジェクト
  function AddMember($cid,$memOB){
    $sql='insert into member values ';
    for($i=0;$i<count($memOB);$i++){
      $sql.='(null,'.$cid.','.$memOB[$i]->num.',0,"'.date('Y-m-d H:i:s').'")';
      if($i!=(count($memOB)-1)){
        $sql.=',';
      }
    }
    $this->id=insertAI(DB_NAME,$sql);
    $this->reload();
  }

  function setCheckflg($cid){
    $sql='update member set checked=1 where circularID='.$cid.' and userID='.$_SESSION['loginid'];
    deleteFrom(DB_NAME,$sql);
  }

  function reload(){
    $this->initWithMemberID($this->id);
  }
}

