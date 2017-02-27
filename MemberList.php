<?php

class MemberList
{
  public function memberList($bumon){//bumon == 0 でAll
    $sql = 'select id, bumon_code, short_name from employee where kairan = 1';
    if ($bumon != 0) $sql .= ' and bumon_code = '.$bumon;
    $sql .= ' order by ruby asc';
    return selectData4('master', $sql);
  }

  public function bumonList(){
    $sql = 'select bid, name from bumon where bid != 1';
    return selectData4('master', $sql);
  }

  public function mailAddress($id){
    $sql = 'select mail from employee where id = '.$id;
    $res = selectData4('master', $sql);
    return $res[0]['mail'];
  }
}


function selectData4($db, $sql){
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
