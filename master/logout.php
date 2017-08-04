<?php
session_start();
session_regenerate_id(true);
$_SESSION = array();
header("Location: ../../portal/index.php");
exit;
?>
