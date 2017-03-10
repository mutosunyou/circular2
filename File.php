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
    var_dump($arr);
    $ext = $arr[(count($arr) - 1)];
    var_dump($ext);
    $fp = str_replace('/Volumes','http://192.168.100.209/mnt',$filepath);
    $sql = 'insert into files (id,circularID, filepath, uptime,isalived) values (null,'.$cid.',"'.$fp.'","'.date('Y-m-d H:i:s').'",1)';
    $this->id=insertAI("circular2", $sql);
    $this->reload();
  }

  //伝票IDを入れてリロードする。
  function reload(){
    $this->initWithFileID($this->id);
  }

}


