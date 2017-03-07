<?php

function h($s){
    return htmlspecialchars($s);
}

function r($s){
    return mysql_real_escape_string($s);
}

function jump($s){
    header('Location: '.SITE_URL.$s);
}

function unixTimestampToMySQLDatetime($theTimeSTamp){
  return date("Y-m-d H:i:s", $theTimeSTamp);
}

function unixTimestampToMySQLDate($theTimeSTamp){
  return date("Y-m-d", $theTimeSTamp);
}

function mySQLDatetimeToUnixTimestamp($theDatetime){
  return strtotime($theDatetime);
}

function base64_urlsafe_encode($val) {
	$val = base64_encode($val);
	return str_replace(array('+', '/', '='), array('_', '-', '.'), $val);
}

function base64_urlsafe_decode($val) {
	$val = str_replace(array('_','-', '.'), array('+', '/', '='), $val);
	return base64_decode($val);
}
function weekInJapanese($week){
  switch ($week) {
  case '0':
    return '日';
    break;
  case '1':
    return '月';
    break;
  case '2':
    return '火';
    break;
  case '3':
    return '水';
    break;
  case '4':
    return '木';
    break;
  case '5':
    return '金';
    break;
  case '6':
    return '土';
    break;
  default:
    return 'エラー';
    break;
  }
}
function sendmail($madd, $cc, $title, $cont, $from){
  exec("nohup php -c '' '/var/www/Documents/mailmanager/circularmail.php' '".$madd."' '".$title."' '".$cont."' '".$from."' '".$cc."'> /dev/null &");
}
