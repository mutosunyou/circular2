<?php
//初期==============================================
session_start();

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
    $rst = selectData2(DB_NAME, $sql);
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
    $this->id=insertAI2(DB_NAME,$sql);
    $this->reload();
  }

  function setCheckflg($cid){
    $sql='update member set checked=1 where circularID='.$cid.' and userID='.$_SESSION['loginid'];
    deleteFrom3(DB_NAME,$sql);
  }

  function reload(){
    $this->initWithMemberID($this->id);
  }
}

function deleteFrom3($db, $sql){
    //接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, $db);
    /* 接続状況をチェックします */
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }
    $addresult = $mysqli->query($sql) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
    
    $mysqli->close();
    
    return $addresult;
}

function insertAI2($db, $sql){
    //接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, $db);
    /* 接続状況をチェックします */
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }
    $addresult = $mysqli->query($sql) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
    $last_id = $mysqli->insert_id;
    
    $mysqli->close();
    
    //新しくデータ追加して、AutoIncrementされたidを取得する
    $arr = array($addresult, $last_id);
    return $arr;
}

function selectData2($db, $sql){
  //接続
  //return DB_PASSWORD;
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, $db);
    /* 接続状況をチェックします */
    $mysqli->set_charset("utf8");
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }
    if ($result = $mysqli->query($sql)) {
      
        $bigArray = array();
       
        while($col = $result->fetch_array(MYSQLI_ASSOC)){
        
            $smallArray = array();
            foreach ($col as $key => $value){
                $smallArray[$key] = $value;
            }
            $bigArray[] = $smallArray;
            
        }
        
        $result->close();
        $mysqli->close();
        return $bigArray;
        
    }
    
    $mysqli->close();
    
}
