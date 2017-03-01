<?php

class MemberList
{
  public function memberList($bumon){//bumon == 0 でAll
    $sql = 'select id, bumon_code, short_name from employee where kairan = 1';
    if ($bumon != 0) $sql .= ' and bumon_code = '.$bumon;
    $sql .= ' order by ruby asc';
    return selectData('master', $sql);
  }

  public function bumonList(){
    $sql = 'select bid, name from bumon where bid != 1';
    return selectData('master', $sql);
  }

  public function mailAddress($id){
    $sql = 'select mail from employee where id = '.$id;
    $res = selectData('master', $sql);
    return $res[0]['mail'];
  }
}


