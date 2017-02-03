<?php
//初期==============================================
session_start();

//$js = json_decode($_POST['qarray']);
$js2 = json_decode($_POST['id']);
//var_dump($js);
var_dump($js2);
/*
for($i = 0; $i < count($js); $i++){
    $smallArray = array();
    $smallArray['title'] = $js[$i][0]->title;
    $smallArray['content'] = $js[$i][1]->content;
    $smallArray['secret'] = $js[$i][2]->secret;
    $smallArray['userID'] = $js[$i][3]->userID;
    $commitArray[] = $smallArray;
 //   writeData('workflow', 'contents', $smallArray);
  }

  //flowをアップデート
//  deleteFrom('workflow', 'update flow set revision = "'.$revision.'", distination = "'.$distination.'", routeID = '.$bumon.' where generalID = "'.$generalID.'"');

 */
