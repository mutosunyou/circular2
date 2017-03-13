<?php
require_once("master/prefix.php");
// Set the uplaod director
$uploadDir = '/Volumes/share/system/circular/';

// Set the allowed file extensions
$fileTypes = array('jpg', 'jpeg', 'gif', 'png', 'pdf','key','xlsx'); // Allowed file extensions

setlocale(LC_ALL, 'ja_JP.UTF-8');
$fileParts = pathinfo($_FILES['Filedata']['name']);
$tempFile  = $_FILES['Filedata']['tmp_name'];
$fname = $fileParts['filename'].'_'.time().'.'.$fileParts['extension'];
$targetFile = $uploadDir .$fname;
move_uploaded_file($tempFile, $targetFile);
echo $targetFile;
?>
