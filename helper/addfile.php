<?php

require_once('../File.php');

$ev = new File();
var_dump($ev);
$ev->addFile($_POST['cid'],$_POST['path']);

