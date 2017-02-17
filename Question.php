<?php
//session_start();
require_once('Answer.php');

class Question 
{
  public $id;//id
  public $circularID;//回覧id
  public $content;//質問文
  public $freespace;//自由記入
  public $stype;//回答方式radio:0, check:1
  public $candidates;//回答候補
  public $answers;//答え

  function initWithQuestionID($id)//8桁(期+6桁)の伝票番号で初期化
  {
    $this->id= $id;
    $sql = 'select * from question where id = '.$this->id;
    $rst = selectData(DB_NAME, $sql);
    if($rst!=null){
      $this->circularID = $rst[0]['circularID'];
      $this->content = $rst[0]['content'];
      $this->freespace = $rst[0]['freespace'];
      $this->stype = $rst[0]['stype'];

      $sql1 = 'select item from candidate where qid = '.$this->id;
      $rst1 = selectData(DB_NAME, $sql1);
      $this->candidates=array();
      for($i=0;$i<count($rst1);$i++){
        $this->candidates[] = $rst1[$i]['item'];
      }
      $sql2 = 'select id from answer where questionID = '.$this->id;
      $rst2 = selectData(DB_NAME, $sql2);
      $this->answers=array();
      for($i=0;$i<count($rst2);$i++){
        $this->answers[]=$rst2[$i]['answer'];
      }
    }
  }

  function AddQuestion($cid,$qarray)
  {
    for($i=0;$i<count($qarray);$i++){
      if($qarray[$i][0]->check==true){
        $check=1;
      }else{
        $check=0;
      }
      $sql='insert into question values (null,'.$cid.',"'.$qarray[$i][0]->question.'",';
      if($qarray[$i][0]->check==true){
        $sql.=1;
      }else{
        $sql.=0;
      }
      $sql.=','.$qarray[$i][0]->stype.')';
      $this->id=insertAI(DB_NAME,$sql);
      $this->reload();
      if(count($qarray[$i])>1){
        $sql='insert into candidate values ';
        for($j=1;$j<count($qarray[$i]);$j++){
          $sql.='(null,'.$this->id[1].',"'.$qarray[$i][$j]->answer.'")';
          if($j!=(count($qarray[$i])-1)){
            $sql.=',';
          }
        }
        deleteFrom(DB_NAME,$sql);
      }
    }
  }

  function reload(){
    $this->initWithQuestionID($this->id);
  }
}
