<?php
require_once('config.php');
require_once('functions.php');
require_once('master_sql_utf9.php');
require_once('htmltemplate.php');
require_once('Parsedown.php');

function userIDFromName($name){
    $name = selectData('master', 'select * from employee where person_name = "'.$name.'"');
    return $name[0]['id'];
}

function shortNameFromUserID($id){
    $name = selectData('master', 'select * from employee where id = '.$id);
    return $name[0]['short_name'];
}

function nameFromUserID($id){
    $name = selectData('master', 'select * from employee where id = '.$id);
    return $name[0]['person_name'];
}

function bumonFromID($id){
  $name = selectData('master', 'select bumon_code from employee where id = '.$id);
  $bumonCD = $name[0]['bumon_code'];
  $name = selectData('master', 'select name from bumon where bid = '.$bumonCD);
  return $name[0]['name'];
}

function mailFromUserID($id){
  $name = selectData('master', 'select mail from employee where id = '.$id);
  return $name[0]['mail'];
}

function myescape($comment){
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  $string = mysqli_real_escape_string($mysqli ,$comment);
  $mysqli->close();
  return $string;
}
?>
