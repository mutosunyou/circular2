<?php

function deleteFrom($db, $sql){
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

function insertAI($db, $sql){
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

function selectData($db, $sql){
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
function selectData2($db, $sql){
  //接続
  //return DB_PASSWORD;
    $mysqli = new mysqli("192.168.100.208", DB_USER, DB_PASSWORD, $db);
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
function writeData($db, $tableName, $dict){
    //接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, $db);
    /* 接続状況をチェックします */
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }
    $addsql = 'insert into ' . $tableName . ' (';
    foreach ($dict as $key => $value){
        $addsql .= $key;
        $addsql .= ', ';
    }
    $addsql = substr($addsql, 0, -2);
    $addsql .= ') values (';
    foreach ($dict as $key => $value){
        if (is_numeric($value)){
            
        }else{
            $addsql .= '"';
        }
        $addsql .= $value;
        if (is_numeric($value)){
            
        }else{
            $addsql .= '"';
        }
        $addsql .= ', ';
    }
    $addsql = substr($addsql, 0, -2);
    $addsql .= ')';
    
    $addresult = $mysqli->query($addsql) or die("クエリの送信に失敗しました。<br />SQL:".$addsql);
    
    $mysqli->close();
    
    
    return $addresult;
    
}

function writeAI($db, $tableName, $dict){
    //接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, $db);
    /* 接続状況をチェックします */
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }
    $addsql = 'insert into ' . $tableName . ' (';
    foreach ($dict as $key => $value){
        $addsql .= $key;
        $addsql .= ', ';
    }
    $addsql = substr($addsql, 0, -2);
    $addsql .= ') values (';
    foreach ($dict as $key => $value){
        if (is_string($value)){
            $addsql .= '"';
        }
        $addsql .= $value;
        if (is_string($value)){
            $addsql .= '"';
        }
        $addsql .= ', ';
    }
    $addsql = substr($addsql, 0, -2);
    $addsql .= ')';
    
    $addresult = $mysqli->query($addsql) or die("クエリの送信に失敗しました。<br />SQL:".$addsql);
    
    $last_id = $mysqli->insert_id;
    
    $mysqli->close();
    
    //新しくデータ追加して、AutoIncrementされたidを取得する
    return $last_id;
    
}


