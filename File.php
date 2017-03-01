<?php
session_start();

class File
{
  public $id;
  public $circularID;
  public $filepath;
  public $uptime;
  public $isalived;

  //初期化
  function initWithFileID($id)//8桁(期+6桁)の伝票番号で初期化
  {
    $this->id= $id;
    $sql = 'select * from files where id = '.$this->id;
    $rst = selectData("circular2", $sql);
    if($rst!=null){
      $this->circularID = $rst[0]['circularID'];
      $this->filepath = $rst[0]['filepath'];
      $this->uptime = $rst[0]['uptime'];
      $this->isalived = $rst[0]['isalived'];
    }
  }

  //ファイル追加
  function addFile($cid,$filepath){
    $arr = explode('.', $filepath);
    $ext = $arr[(count($arr) - 1)];
    $fp = str_replace('/Volumes','http://192.168.100.209/mnt',$filepath);
    $sql = 'insert into files (id,circularID, filepath, uptime,isalived) values (null,'.$cid.',"'.$fp.'","'.date('Y-m-d H:i:s').'",1)';
    $this->id=insertAI3("circular2", $sql);
    $this->reload();
  }
  
  //伝票IDを入れてリロードする。
  function reload(){
    $this->initWithFileID($this->id);
  }
}

function insertAI3($db, $sql){
    //接続
    $mysqli = new mysqli("localhost", "root", "root", $db);
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

function deleteFrom2($db, $sql){
    //接続
    $mysqli = new mysqli("localhost", "root", "root", $db);
    /* 接続状況をチェックします */
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }
    $addresult = $mysqli->query($sql) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
    $mysqli->close();
    return $addresult;
}
