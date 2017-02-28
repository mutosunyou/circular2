<?php
session_start();
require_once('master/config.php');
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
    $rst = selectData(DB_NAME, $sql);
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
    deleteFrom2(DB_NAME, $sql);
    $this->reload();
  }
  
  //伝票IDを入れてリロードする。
  function reload(){
    $this->initWithFileID($this->id);
  }
}

function deleteFrom2($db, $sql){
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
