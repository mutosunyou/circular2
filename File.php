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
    $rst = selectData(DB_NAME, $sql);
    if($rst!=null){
      $this->circularID = $rst[0]['circularID'];
      $this->filepath = $rst[0]['filepath'];
      $this->uptime = $rst[0]['uptime'];
      $this->isalived = $rst[0]['isalived'];
    }
  }

  //ファイル追加
  function addFile($filepath){
    $arr = explode('.', $filePath);
    $ext = $arr[(count($arr) - 1)];
    $fp = str_replace('/Volumes','http://192.168.100.209/mnt',$filepath);
    if($did==NULL){
      $did="NULL";
    }
    $sql = 'insert into files (id,circularID, filepath, uptime,isalived) values (null,'.$this->circularID.',"'.$fp.'","'.date('Y-m-d').'",1)';
    deleteFrom(DB_NAME, $sql);
    $this->reload();
  }
  
  //伝票IDを入れてリロードする。
  function reload(){
    $this->initWithFileID($this->id);
  }
}

