<?php

function html($title, $header, $body){
    $cont = '';
    $cont .= '<!DOCTYPE HTML><html lang="ja-JP">';
    $cont .= '<head>';

    //タイトル--------------------------------------------------------------------------------------------------------------------------------
    $cont .= '<meta charset="UTF-8"><title>'.$title.'</title>'."\n";
    $cont .= '<meta name="description" content="" />'."\n";

    //script----------------------------------------------------------------------------------------------------------------------------------
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/bootstrap/js/jquery-2.1.3.min.js"></script>'."\n";       //jquery
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/bootstrap/js/jquery-ui.min.js"></script>'."\n";          //ui
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/bootstrap/js/jquery.json-2.3.js"></script>';             //JSON
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/bootstrap/js/bootstrap.js"></script>'."\n";              //ブートストラップ
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/bootstrap/js/bootstrap-dropdown.js"></script>'."\n";     //ドロップダウン
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/bootstrap/js/bootstrap-typeahead.js"></script>'."\n";    //
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/highcharts.js"></script>'."\n";                       //
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/modules/exporting.js"></script>'."\n";                //
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/easyconfirm.js"></script>'."\n";                      //
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/jquery.balloon.min.js"></script>'."\n";               //バルーン
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/jquery.tabslet.min.js"></script>'."\n";               //タブ
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/bootstrap/js/jquery.datetimepicker.js"></script>'."\n";  //日付取得
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/alertify.js"></script>'."\n";                         //アラート
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/jquery.keyboard.js"></script>'."\n";                  //キーボード
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/jcalculator.min.js"></script>'."\n";                  //計算機
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/jquery.keyboard.extension-all.js"></script>'."\n";    //キーボード
    $cont .= '<script type="text/javascript" src="'.SITE_URL.'master/js/jquery.uploadifive.min.js"></script>';                //ファイルアップロード

    //CSS-------------------------------------------------------------------------------------------------------------------------------------
  //$cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/js/jcalculator.css" >'."\n";                                          //計算機
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/js/keyboard.css" media="all" />'."\n";                                //キーボード
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/js/alertify.core.css" media="all" />'."\n";                           //アラート
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/js/alertify.default.css" media="all" />'."\n";                        //アラート
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/bootstrap/css/bootstrap.css" media="all" />'."\n";                    //ブートストラップ
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/bootstrap/css/sticky.css" media="all" />'."\n";                       //
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/bootstrap/css/bootstrap-responsive.css" media="all" />'."\n";         //
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/bootstrap/css/redmond/jquery-ui.css" media="all" />'."\n";            //ui
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/bootstrap/css/redmond/jquery-ui.structure.css" media="all" />'."\n";  //ui
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/bootstrap/css/redmond/jquery-ui.theme.css" media="all" />'."\n";      //ui
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/bootstrap/css/jquery.datetimepicker.css" media="all" />'."\n";        //日付取得
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/js/uploadifive.css">';                                                //ファイルアップロード
    $cont .= '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'master/js/tabslet.css">';                                                     //スタイルシート（タブ付属のやつ）

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

