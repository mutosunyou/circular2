<?php
session_start();
session_regenerate_id(true);
$_SESSION = array();
header("Location: index.php");
exit;
?>