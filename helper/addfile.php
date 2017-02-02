<?php
require_once('../master/prefix.php');

$ev = new File();
$ev->initWithID($_POST['fid']);
$ev->addFile($_POST['cid'],$_POST['path'],$_POST['did']);


