<?php

require_once('../File.php');

$ev = new File();
$ev->addFile($_POST['cid'],$_POST['path']);

