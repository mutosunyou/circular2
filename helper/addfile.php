<?php

require_once('../File.php');

$ev = new File();
echo $ev->addFile($_POST['cid'],$_POST['path']);

