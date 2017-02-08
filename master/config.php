<?php

define('DB_HOST', "localhost");
define('DB_USER', "root");
define('DB_PASSWORD', "root");
define('DB_NAME', "circular2");

//ini_set("display_errors", Off);
error_reporting(0);//本番環境用

define('SITE_URL', 'http://'.$_SERVER["SERVER_NAME"].'/circular2/');

//不適合内容一覧のコンテンツプレビューの字数
$previewlength = 150;
