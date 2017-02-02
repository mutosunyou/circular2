<?php
/*
UploadiFive
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
*/
require_once("master/prefix.php");
// Set the uplaod directory
$uploadDir = '/Volumes/share/system/circular/';

// Set the allowed file extensions
$fileTypes = array('jpg', 'jpeg', 'gif', 'png'); // Allowed file extensions

$verifyToken = md5('sunyou' . $_POST['timestamp']);

if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
  $fileParts = pathinfo($_FILES['Filedata']['name']);
  $tempFile   = $_FILES['Filedata']['tmp_name'];
  $uploadDir  = $uploadDir;
  $fname = $fileParts['filename'].time().'.'.$fileParts['extension'];
  $targetFile = $uploadDir . $fname;
  //echo $uploadDir;
  // Validate the filetype
  if (in_array(strtolower($fileParts['extension']), $fileTypes)) {

    // Save the file
    move_uploaded_file($tempFile, $targetFile);
    //echo $fname;
    
    echo 'http://192.168.100.209/mnt/share/system/circular/'.$fname;

  } else {

    // The file type wasn't allowed
    echo 'Invalid file type.';

  }
}
?>
