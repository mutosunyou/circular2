<?php

function html($title, $header, $body){
    $cont = '';
    $cont .= '<!DOCTYPE HTML><html lang="ja-JP">';
    $cont .= '<head>';
    
    $cont .= '<meta charset="UTF-8"><title>'.$title.'</title>'."\n";
    $cont .= '<meta name="description" content="" />'."\n";
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/bootstrap/js/jquery-2.1.3.min.js"></script>'."\n";
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/bootstrap/js/jquery-ui.min.js"></script>'."\n";
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/bootstrap/js/jquery.json-2.3.js"></script>';
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/bootstrap/js/bootstrap.js"></script>'."\n";
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/bootstrap/js/bootstrap-dropdown.js"></script>'."\n";
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/bootstrap/js/bootstrap-typeahead.js"></script>'."\n";
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/highcharts.js"></script>'."\n";
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/modules/exporting.js"></script>'."\n";
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/easyconfirm.js"></script>'."\n";
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/jquery.balloon.min.js"></script>'."\n";
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/bootstrap/js/jquery.datetimepicker.js"></script>'."\n";

    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/alertify.js"></script>'."\n";
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/jquery.keyboard.js"></script>'."\n";
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/calculator.js"></script>'."\n";
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/jquery.keyboard.extension-all.js"></script>'."\n";
    //   $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/jcalculator.min.js"></script>'."\n";
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/jquery.uploadifive.min.js"></script>';

    //   $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/js/jcalculator.css" >'."\n";
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/js/keyboard.css" media="all" />'."\n";
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/js/alertify.core.css" media="all" />'."\n";
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/js/alertify.default.css" media="all" />'."\n";

    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/bootstrap/css/bootstrap.css" media="all" />'."\n";
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/bootstrap/css/sticky.css" media="all" />'."\n";
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/bootstrap/css/bootstrap-responsive.css" media="all" />'."\n";
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/bootstrap/css/redmond/jquery-ui.css" media="all" />'."\n";
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/bootstrap/css/redmond/jquery-ui.structure.css" media="all" />'."\n";
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/bootstrap/css/redmond/jquery-ui.theme.css" media="all" />'."\n";
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/bootstrap/css/jquery.datetimepicker.css" media="all" />'."\n";
    $cont .='<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/js/uploadifive.css">';

    $cont .= '<link rel="shortcut icon" href="'.SITE_URL.'master/favicon.ico">'."\n";
    
    //スタイルシート
    $cont .= '<style type="text/css">';
    
    //タイトル
    $cont .= 'h2{'; 
    $cont .= 'font-family:"MS P gosic",Osaka,sans-serif;';
    $cont .= '}'; 
 
    $cont .= '</style>';
    
    $cont .= $header."\n";//additional
    
    $cont .= '</head>'."\n";
    $cont .= '<body>'."\n";
    $cont .= $body."\n";
    
    $cont .= '<footer class="footer">
      <div class="footstick">
      <div style="text-align:right;margin-top:15px;color:#959595;">
      不具合報告、問い合わせは<a target="_blank" href="mailto:system@sunyou.co.jp" tabindex="-1">こちら</a>まで
      　</div>
      </div>
    </footer>';
    $cont .= '</body>'."\n";
    $cont .= '</html>'."\n";
    
    return $cont;
}

