<?php

//require_once("master/prefix.php");

class MadoFlow
{
  
  public $userID;//ユーザーID

  public function initWithUserID($uid){
    $this->userID = $uid;
  }
  
  public function mailing($madd, $title, $cont){
    //$cont = PHP_EOL.'（このメールはシステムのテストメールなので無視してください。）';
    //$title = 'test';

    date_default_timezone_set("Asia/Tokyo");
    require_once('mail/class.phpmailer.php');
    $mail             = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = "ssl";
    $mail->Host       = "smtp.gmail.com";
    $mail->Port       = 465;
    $mail->Username   = "remote_manager@sunyou.co.jp";
    $mail->Password   = "kohlergenerator";
    //$mail->CharSet    = "iso-2022-jp";
    $mail->CharSet    = "utf-8";

    $mail->Encoding   = "7bit";
    $mail->From       = "remote_manager@sunyou.co.jp";
    $mail->FromName   = mb_encode_mimeheader(mb_convert_encoding("System", "utf-8", "utf-8"));
    $mail->AddReplyTo("remote_manager@sunyou.co.jp", mb_encode_mimeheader(mb_convert_encoding("remote_manager@sunyou.co.jp", "utf-8", "utf-8")));
    $mail->Subject    = mb_convert_encoding($title, "utf-8", "utf-8");
    $mail->Body       = mb_convert_encoding($cont, "utf-8", "utf-8");
    for ($i=0; $i < count($madd); $i++) { 
      $mail->AddAddress($madd[$i], mb_encode_mimeheader(mb_convert_encoding($madd[$i], "utf-8", "utf-8")));
    }

    if(!$mail->Send()) {
      echo "Mailer Error: " . $mail->ErrorInfo . "\n";
    } else {
      echo "succeed";
    }
  }
  public function createCircular($userArray, $circularTitle, $circularCont, $q1cont, $q1ansArray, $q1biko, $q2cont, $q2ansArray, $q2biko, $path, $secret){

    if(count($userArray) > 0){

      //あれば質問を追加
      $q1ID = 0;
      $sqlArray = array();
      if (strlen($q1cont) > 0) {
        $sqlArray['content'] = r($q1cont);
        for ($i=0; $i < count($q1ansArray); $i++) { 
          $sqlArray['ans'.($i+1)] = r($q1ansArray[$i]);
        }
        $sqlArray['biko'] = $q1biko;
        $q1ID = writeAI('circular', 'question', $sqlArray);
      }
      
      $q2ID = 0;
      $sqlArray = array();
      if (strlen($q2cont) > 0) {
        $sqlArray['content'] = r($q2cont);
        for ($i=0; $i < count($q2ansArray); $i++) { 
          $sqlArray['ans'.($i+1)] = r($q2ansArray[$i]);
        }
        $sqlArray['biko'] = $q2biko;
        $q2ID = writeAI('circular', 'question', $sqlArray);
      }

      //circularを追加
      $sqlArray = array();
      $sqlArray['owner'] = $this->userID;
      $sqlArray['title'] = r($circularTitle);
      $sqlArray['datetime'] = unixTimestampToMySQLDatetime(time());

      $sqlArray['content'] = r($circularCont);
      $sqlArray['status'] = 0;
      $sqlArray['q1id'] = $q1ID;
      $sqlArray['q2id'] = $q2ID;
      $sqlArray['path'] = $path;
      $sqlArray['secret'] = $secret;


      $circularID = writeAI('circular', 'circular', $sqlArray);



      //ユーザーを追加
      $mem = new MemberList();
      $madds = array();
      for($i=0; $i < count($userArray); $i++){
        //circularを追加
        $sqlArray = array();
        $sqlArray['circularID'] = $circularID;
        $sqlArray['userID'] = $userArray[$i];
        $sqlArray['checked'] = 0;
        writeAI('circular', 'member', $sqlArray);
        $madd = $mem->mailAddress($userArray[$i]);
        $madds[] = $madd;
      }
      $this->mailing($madds, '【回覧通知】'.$circularTitle, '以下の回覧が来ています。'.PHP_EOL.'システムで確認してください。'.PHP_EOL.'http://192.168.100.209/circular'.PHP_EOL.PHP_EOL.'表題：'.$circularTitle);
      return true;

    }
    return false;

  }


  public function commit($circularID, $q1ans, $q2ans, $free1, $free2){
    $sql = 'update member set checked = 1, q1ans = '.$q1ans.', q2ans = '.$q2ans.', q1biko = "'.r($free1).'", q2biko = "'.r($free2).'" where circularID = '.$circularID.' and userID = ' . $this->userID;
    if(deleteFrom('circular', $sql)){
      $sql = 'select id from member where checked = 0 and circularID = '.$circularID;
      $res = selectData('circular', $sql);
      if (count($res) == 0) {
        $this->finish($circularID);
      }else{
        return 1;
      }
    }
  }


  public function finish($circularID){

    $sql = 'update circular set status = 1 where id = '.$circularID;
    return deleteFrom('circular', $sql);
  }


  public function nameFromUserID($id){
    $name = selectData('master', 'select * from employee where id = '.$id);
    return $name[0]['short_name'];
  }
  public function statusFromInt($id){
    if($id == 0){
      return '<span style="color:#99CCFF">回覧中</span>';
    }elseif ($id == 1){
      return '<span style="color:#FF3333">回覧完了</span>';
    }elseif ($id == 2){
      return '<span style="color:#FF9933">中断</span>';
    }
    return '不明';
  }

  public function userStatusFromInt($id){
    if($id == 0){
      return '<span style="color:red">未確認</span>';
    }elseif ($id == 1){
      return '<span style="color:blue">確認済</span>';
    }
    return '不明';
  }







//stateは checked, notChecked, owner, notOwner finished, notFinished, expire
  public function showList($state, $isFinished){
    $sql = 'select distinct circular.* from circular, member where circular.id = member.circularID and (circular.owner = '.$this->userID.' or member.userID = '.$this->userID.')';
    if($state == 'checked'){
      $sql .= ' and (member.checked = 1 and member.userID = '.$this->userID.')';
    }elseif($state == 'notChecked'){
      $sql .= ' and (member.checked = 0 and member.userID = '.$this->userID.' and circular.status != 2)';
    }elseif($state == 'owner'){
      $sql .= ' and circular.owner = '.$this->userID;
    }elseif($state == 'notOwner'){
      $sql .= ' and circular.owner != '.$this->userID;
    }

    if($isFinished == 'finished'){
      $sql .= ' and circular.status = 1';
    }elseif($isFinished == 'notFinished'){
      $sql .= ' and circular.status = 0';
    }elseif($isFinished == 'expire'){
      $sql .= ' and circular.status = 2';
    }


    $sql .= ' order by circular.datetime desc';

    $result = selectData('circular', $sql);
    //print($sql);
    if (count($result) > 0) {
      $body = '<h4>　未確認の回覧　　<span style="color:red;font-weight:bold;">'.count($result).' 件</span></h4>';
      $body .= '<a href="../circular/" target="_blank"><table class="table table-bordered" style="table-layout:fixed;background-color:#ffffff;">';
      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;" width="20%">作成</th><th style="background-color:#555555;color:#FFFFFF;">表題</th><th style="background-color:#555555;color:#FFFFFF;" width="20%">日付</th>';
      $body .= '</tr>';
    }else{
      $body = '<h4>　未確認の回覧　　0 件</h4>';
    }


    for ($i=0; $i < count($result); $i++) {

      $sql = 'select userID, checked from member where circularID = '.$result[$i]['id'].' and userID = '.$this->userID;
      $memres = selectData('circular', $sql);
      if (count($memres)) {
        $answerer = 1;
        $checked = $memres[0]['checked'];
      }else{
        $answerer = 0;
        $checked = 0;
      }

      if ($result[$i]['owner'] == $this->userID) {
        $owner = 1;
      }else{
        $owner = 0;
      }



        $body .= '<tr style="">';

      $body .= '<td>'.$this->nameFromUserID(intval($result[$i]['owner'])).'</td>';
      
      $body .= '<td>'.$result[$i]['title'].'</td>';
      $body .= '<td>'.date('m/d',mySQLDatetimeToUnixTimestamp($result[$i]['datetime'])).'</td>';

      $body .= '</tr>';
      
    }
    if (count($result) > 0) {
      $body .= '</table></a>';
    }
    return $body;

  }






  
  public function baseData($circularID){
    $sql = 'select * from circular where id = '.$circularID;
    $circularArray = selectData('circular', $sql);
    $sql = 'select userID from member where checked = 1 and circularID = '.$circularID;
    $commitUserArray = selectData('circular', $sql);
    $sql = 'select userID from member where checked = 0 and circularID = '.$circularID;
    $notCommitUserArray = selectData('circular', $sql);
    /*
    print_r($circularArray);
    print_r($commitUserArray);
    print_r($notCommitUserArray);
    print_r($q1Array);
    print_r($q2Array);
    */
    $body = '<table class="table table-bordered">';
    $body .= '<tr>';
    $body .= '<th style="background-color:#555555;color:#FFFFFF;">作成者</th><th style="background-color:#555555;color:#FFFFFF;">ステータス</th><th style="background-color:#555555;color:#FFFFFF;">回覧開始日</th>';
    $body .= '</tr>';
    $body .= '<tr>';
    $body .= '<td>'.$this->nameFromUserID(intval($circularArray[0]['owner'])).'</td>';
    $body .= '<td>'.$this->statusFromInt(intval($circularArray[0]['status'])).'</td>';
    $body .= '<td>'.($circularArray[0]['datetime']).'</td>';
    $body .= '</tr>';
    $body .= '</table>';

    $body .= '<table class="table table-bordered">';
    $body .= '<tr>';
    $body .= '<th style="background-color:#555555;color:#FFFFFF;">表題</th>';
    $body .= '</tr>';
    $body .= '<tr>';
    $body .= '<td>'.($circularArray[0]['title']).'</td>';
    $body .= '</tr>';
    $body .= '</table>';

    $body .= '<table class="table table-bordered">';
    $body .= '<tr>';
    $body .= '<th style="background-color:#555555;color:#FFFFFF;">内容</th>';
    $body .= '</tr>';
    $body .= '<tr>';
    $body .= '<td>'.nl2br($circularArray[0]['content']).'</td>';
    $body .= '</tr>';
    $body .= '</table>';

    $body .= '<table class="table table-bordered">';
    $body .= '<tr>';
    $body .= '<th style="background-color:#555555;color:#FFFFFF;">添付ファイル</th>';
    $body .= '</tr>';
    $body .= '<tr>';
    if (strlen($circularArray[0]['path']) > 0) {
      $body .= '<td><a target="_blank" href="http://192.168.100.209/mnt/share/システム/uploader/'.($circularArray[0]['path']).'">'.($circularArray[0]['path']).'</a></td>';
    }else{
      $body .= '<td>なし</td>';
    }
    $body .= '</tr>';
    $body .= '</table>';

    $body .= '<div style="width:49%;margin-right:1%;float:left;">';
    $body .= '<table class="table table-bordered">';
    $body .= '<tr>';
    $body .= '<th style="background-color:#555555;color:#FFFFFF;">確認済</th>';
    $body .= '</tr>';
    $body .= '<tr><td>';
    for ($i=0; $i < count($commitUserArray); $i++){
      $body .= $this->nameFromUserID($commitUserArray[$i]['userID']).'<br />';
    }
    if(count($notCommitUserArray) - count($commitUserArray) > 0){
      for ($i=0; $i < (count($notCommitUserArray) - count($commitUserArray)); $i++){
        $body .= '<br />';
      }
    }
    $body .= '</td></tr>';
    $body .= '</table>';
    $body .= '</div>';

    $body .= '<div style="width:49%;float:right;margin-left:1%;">';
    $body .= '<table class="table table-bordered">';
    $body .= '<tr>';
    $body .= '<th style="background-color:#555555;color:#FFFFFF;">未確認</th>';
    $body .= '</tr>';
     $body .= '<tr><td>';
    for ($i=0; $i < count($notCommitUserArray); $i++){
      $body .= $this->nameFromUserID($notCommitUserArray[$i]['userID']).'<br />';
    }
    if (count($notCommitUserArray) - count($commitUserArray) < 0){
      for ($i=0; $i < (count($commitUserArray) - count($notCommitUserArray)); $i++){
        $body .= '<br />';
      }
    }
    $body .= '</td></tr>';
    $body .= '</table>';
    $body .= '</div>';

    $body .= '<div style="clear:both;"></div>';
    return $body;
  }


  //詳細な結果
  public function detailResult($circleID){
    $body = '';
    $sql = 'select * from member where circularID = '.$circleID;
    $memberArray = selectData('circular', $sql);
    $sql = 'select * from circular where id = '.$circleID;
    $circularArray = selectData('circular', $sql);
    if ($circularArray[0]['q1id'] > 0) {
      $sql = 'select * from question where id = '.$circularArray[0]['q1id'];
      $q1Array = selectData('circular', $sql);
      if ($circularArray[0]['q2id'] > 0) {
        $sql = 'select * from question where id = '.$circularArray[0]['q2id'];
        $q2Array = selectData('circular', $sql);
      }
    }
    if($circularArray[0]['q1id'] > 0){
      $body .= '<table class="table table-bordered">';
      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;" colspan=4>アンケート質問１</th>';
      $body .= '</tr>';
      $body .= '<tr>';
      $body .= '<td colspan=4>'.($q1Array[0]['content']).'</td>';
      $body .= '</tr>';
      $answer1Array = array();
      for ($i=0; $i < 5; $i++) { 
        $sql = 'select count(q1ans) from member where circularID = '.$circleID.' and q1ans = '.($i + 1);
        $res = selectData('circular', $sql);
        $answer1Array[] = $res[0]['count(q1ans)'];
      }

      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;" colspan=4>回答の集計</th>';
      $body .= '</tr>';
      $body .= '<tr><td colspan=4>';
      if(strlen($q1Array[0]['ans1']) > 0) $body .= $q1Array[0]['ans1'].'　………………………　'.$answer1Array[0].'<br />';
      if(strlen($q1Array[0]['ans2']) > 0) $body .= $q1Array[0]['ans2'].'　………………………　'.$answer1Array[1].'<br />';
      if(strlen($q1Array[0]['ans3']) > 0) $body .= $q1Array[0]['ans3'].'　………………………　'.$answer1Array[2].'<br />';
      if(strlen($q1Array[0]['ans4']) > 0) $body .= $q1Array[0]['ans4'].'　………………………　'.$answer1Array[3].'<br />';
      if(strlen($q1Array[0]['ans5']) > 0) $body .= $q1Array[0]['ans5'].'　………………………　'.$answer1Array[4].'<br />';

      $body .= '<div id="graph1"></div><script type="text/javascript">';
      $body .= "
        $(function () {
          $('#graph1').highcharts({
            chart: {
              plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
    },
    title: {
      text: '".$q1Array[0]['content']."'
    },
    tooltip: {
      pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
          cursor: 'pointer',
          dataLabels: {
            enabled: true,
              color: '#000000',
              connectorColor: '#000000',
              format: '<b>{point.name}</b>: {point.y}'
    }
    }
    },
      series: [{
        type: 'pie',
          name: '".$q1Array[0]['content']."',
          data: [";

      if(strlen($q1Array[0]['ans1']) > 0) $body .= "['".$q1Array[0]['ans1']."',   ".$answer1Array[0]."],";
      if(strlen($q1Array[0]['ans2']) > 0) $body .= "['".$q1Array[0]['ans2']."',   ".$answer1Array[1]."],";
      if(strlen($q1Array[0]['ans3']) > 0) $body .= "['".$q1Array[0]['ans3']."',   ".$answer1Array[2]."],";
      if(strlen($q1Array[0]['ans4']) > 0) $body .= "['".$q1Array[0]['ans4']."',   ".$answer1Array[3]."],";
      if(strlen($q1Array[0]['ans5']) > 0) $body .= "['".$q1Array[0]['ans5']."',   ".$answer1Array[4]."],";

        $body = rtrim($body, ',');
   
      $body .= "
     ]
    }]
    });
    });


    ";

    $body .= '</script>';
    $body .= '</td></tr>';

    //追加分
    $body .= '<tr><th style="background-color:#555555;color:#FFFFFF;">回答者</th><th style="background-color:#555555;color:#FFFFFF;">確認</th><th style="background-color:#555555;color:#FFFFFF;">選択した回答</th><th style="background-color:#555555;color:#FFFFFF;">自由回答</th></tr>';
    for ($i=0; $i < count($memberArray); $i++) { 
      $body .= '<tr><td>'.$this->nameFromUserID($memberArray[$i]['userID']).'</td><td>'.$this->userStatusFromInt($memberArray[$i]['checked']).'</td><td>'.$q1Array[0]['ans'.($memberArray[$i]['q1ans'])].'</td><td>'.$memberArray[$i]['q1biko'].'</td></tr>';
    }

    $body .= '</table>';

    }

    if($circularArray[0]['q2id'] > 0){
      $body .= '<table class="table table-bordered">';
      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;" colspan=4>アンケート質問２</th>';
      $body .= '</tr>';
      $body .= '<tr>';
      $body .= '<td colspan=4>'.($q2Array[0]['content']).'</td>';
      $body .= '</tr>';
      $answer2Array = array();
      for ($i=0; $i < 5; $i++) { 
        $sql = 'select count(q2ans) from member where circularID = '.$circleID.' and q2ans = '.($i + 1);
        $res = selectData('circular', $sql);
        $answer2Array[] = $res[0]['count(q2ans)'];
      }

      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;" colspan=4>回答の集計</th>';
      $body .= '</tr>';
      $body .= '<tr><td colspan=4>';
      if(strlen($q2Array[0]['ans1']) > 0) $body .= $q2Array[0]['ans1'].'　………………………　'.$answer2Array[0].'<br />';
      if(strlen($q2Array[0]['ans2']) > 0) $body .= $q2Array[0]['ans2'].'　………………………　'.$answer2Array[1].'<br />';
      if(strlen($q2Array[0]['ans3']) > 0) $body .= $q2Array[0]['ans3'].'　………………………　'.$answer2Array[2].'<br />';
      if(strlen($q2Array[0]['ans4']) > 0) $body .= $q2Array[0]['ans4'].'　………………………　'.$answer2Array[3].'<br />';
      if(strlen($q2Array[0]['ans5']) > 0) $body .= $q2Array[0]['ans5'].'　………………………　'.$answer2Array[4].'<br />';

      $body .= '<div id="graph2"></div><script type="text/javascript">';
      $body .= "
        $(function () {
          $('#graph2').highcharts({
            chart: {
              plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
    },
    title: {
      text: '".$q2Array[0]['content']."'
    },
    tooltip: {
      pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
          cursor: 'pointer',
          dataLabels: {
            enabled: true,
              color: '#000000',
              connectorColor: '#000000',
              format: '<b>{point.name}</b>: {point.y}'
    }
    }
    },
      series: [{
        type: 'pie',
          name: '".$q2Array[0]['content']."',
          data: [";

      if(strlen($q2Array[0]['ans1']) > 0) $body .= "['".$q2Array[0]['ans1']."',   ".$answer2Array[0]."],";
      if(strlen($q2Array[0]['ans2']) > 0) $body .= "['".$q2Array[0]['ans2']."',   ".$answer2Array[1]."],";
      if(strlen($q2Array[0]['ans3']) > 0) $body .= "['".$q2Array[0]['ans3']."',   ".$answer2Array[2]."],";
      if(strlen($q2Array[0]['ans4']) > 0) $body .= "['".$q2Array[0]['ans4']."',   ".$answer2Array[3]."],";
      if(strlen($q2Array[0]['ans5']) > 0) $body .= "['".$q2Array[0]['ans5']."',   ".$answer2Array[4]."],";

        $body = rtrim($body, ',');
   
      $body .= "
     ]
    }]
    });
    });
    ";

    $body .= '</script>';
    $body .= '</td></tr>';
        //追加分
    $body .= '<tr><th style="background-color:#555555;color:#FFFFFF;">回答者</th><th style="background-color:#555555;color:#FFFFFF;">確認</th><th style="background-color:#555555;color:#FFFFFF;">選択した回答</th><th style="background-color:#555555;color:#FFFFFF;">自由回答</th></tr>';
    for ($i=0; $i < count($memberArray); $i++) { 
      $body .= '<tr><td>'.$this->nameFromUserID($memberArray[$i]['userID']).'</td><td>'.$this->userStatusFromInt($memberArray[$i]['checked']).'</td><td>'.$q2Array[0]['ans'.($memberArray[$i]['q2ans'])].'</td><td>'.$memberArray[$i]['q2biko'].'</td></tr>';
    }

    $body .= '</table>';

    }
    return $body;

  }



  public function userDetail($cid, $uid){
    $sql = 'select * from member where circularID = '.$cid.' and userID = '.$uid;
    $memberArray = selectData('circular', $sql);
    $sql = 'select * from circular where id = '.$cid;
    $circularArray = selectData('circular', $sql);
    $body = '<h3>あなたの回答</h3><table class="table table-bordered">';
    $body .= '<tr><th style="background-color:#555555;color:#FFFFFF;">質問</th><th style="background-color:#555555;color:#FFFFFF;">回答</th><th style="background-color:#555555;color:#FFFFFF;">備考</th></tr>';
    if ($circularArray[0]['q1id'] > 0) {
      $sql = 'select * from question where id = '.$circularArray[0]['q1id'];
      $q1Array = selectData('circular', $sql);
      $body .= '<tr><td>'.$q1Array[0]['content'].'</td><td>'.$q1Array[0]['ans'.($memberArray[0]['q1ans'])].'</td><td>'.$memberArray[0]['q1biko'].'</td></tr>';
      if ($circularArray[0]['q2id'] > 0) {
        $sql = 'select * from question where id = '.$circularArray[0]['q2id'];
        $q2Array = selectData('circular', $sql);
        $body .= '<tr><td>'.$q2Array[0]['content'].'</td><td>'.$q2Array[0]['ans'.($memberArray[0]['q2ans'])].'</td><td>'.$memberArray[0]['q2biko'].'</td></tr>';
      }
    }

    $body .= '</table>';
    return $body;

  }


  //結果グラフ表示
  public function readResult($circleID){
    $body = '';

    $sql = 'select * from circular where id = '.$circleID;
    $circularArray = selectData('circular', $sql);
    if ($circularArray[0]['q1id'] > 0) {
      $sql = 'select * from question where id = '.$circularArray[0]['q1id'];
      $q1Array = selectData('circular', $sql);
      if ($circularArray[0]['q2id'] > 0) {
        $sql = 'select * from question where id = '.$circularArray[0]['q2id'];
        $q2Array = selectData('circular', $sql);
      }
    }
    if($circularArray[0]['q1id'] > 0){
      $body .= '<table class="table table-bordered">';
      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;">アンケート質問１</th>';
      $body .= '</tr>';
      $body .= '<tr>';
      $body .= '<td>'.($q1Array[0]['content']).'</td>';
      $body .= '</tr>';
      $answer1Array = array();
      for ($i=0; $i < 5; $i++) { 
        $sql = 'select count(q1ans) from member where circularID = '.$circleID.' and q1ans = '.($i + 1);
        $res = selectData('circular', $sql);
        $answer1Array[] = $res[0]['count(q1ans)'];
      }

      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;">回答の集計</th>';
      $body .= '</tr>';
      $body .= '<tr><td>';
      if(strlen($q1Array[0]['ans1']) > 0) $body .= $q1Array[0]['ans1'].'　………………………　'.$answer1Array[0].'<br />';
      if(strlen($q1Array[0]['ans2']) > 0) $body .= $q1Array[0]['ans2'].'　………………………　'.$answer1Array[1].'<br />';
      if(strlen($q1Array[0]['ans3']) > 0) $body .= $q1Array[0]['ans3'].'　………………………　'.$answer1Array[2].'<br />';
      if(strlen($q1Array[0]['ans4']) > 0) $body .= $q1Array[0]['ans4'].'　………………………　'.$answer1Array[3].'<br />';
      if(strlen($q1Array[0]['ans5']) > 0) $body .= $q1Array[0]['ans5'].'　………………………　'.$answer1Array[4].'<br />';

      $body .= '<div id="graph1"></div><script type="text/javascript">';
      $body .= "
        $(function () {
          $('#graph1').highcharts({
            chart: {
              plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
    },
    title: {
      text: '".$q1Array[0]['content']."'
    },
    tooltip: {
      pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
          cursor: 'pointer',
          dataLabels: {
            enabled: true,
              color: '#000000',
              connectorColor: '#000000',
              format: '<b>{point.name}</b>: {point.y}'
    }
    }
    },
      series: [{
        type: 'pie',
          name: '".$q1Array[0]['content']."',
          data: [";

      if(strlen($q1Array[0]['ans1']) > 0) $body .= "['".$q1Array[0]['ans1']."',   ".$answer1Array[0]."],";
      if(strlen($q1Array[0]['ans2']) > 0) $body .= "['".$q1Array[0]['ans2']."',   ".$answer1Array[1]."],";
      if(strlen($q1Array[0]['ans3']) > 0) $body .= "['".$q1Array[0]['ans3']."',   ".$answer1Array[2]."],";
      if(strlen($q1Array[0]['ans4']) > 0) $body .= "['".$q1Array[0]['ans4']."',   ".$answer1Array[3]."],";
      if(strlen($q1Array[0]['ans5']) > 0) $body .= "['".$q1Array[0]['ans5']."',   ".$answer1Array[4]."],";

        $body = rtrim($body, ',');
   
      $body .= "
     ]
    }]
    });
    });


    ";

    $body .= '</script>';
    $body .= '</td></tr>';
    $body .= '</table>';

    }

    if($circularArray[0]['q2id'] > 0){
      $body .= '<table class="table table-bordered">';
      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;">アンケート質問２</th>';
      $body .= '</tr>';
      $body .= '<tr>';
      $body .= '<td>'.($q2Array[0]['content']).'</td>';
      $body .= '</tr>';
      $answer2Array = array();
      for ($i=0; $i < 5; $i++) { 
        $sql = 'select count(q2ans) from member where circularID = '.$circleID.' and q2ans = '.($i + 1);
        $res = selectData('circular', $sql);
        $answer2Array[] = $res[0]['count(q2ans)'];
      }

      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;">回答の集計</th>';
      $body .= '</tr>';
      $body .= '<tr><td>';
      if(strlen($q2Array[0]['ans1']) > 0) $body .= $q2Array[0]['ans1'].'　………………………　'.$answer2Array[0].'<br />';
      if(strlen($q2Array[0]['ans2']) > 0) $body .= $q2Array[0]['ans2'].'　………………………　'.$answer2Array[1].'<br />';
      if(strlen($q2Array[0]['ans3']) > 0) $body .= $q2Array[0]['ans3'].'　………………………　'.$answer2Array[2].'<br />';
      if(strlen($q2Array[0]['ans4']) > 0) $body .= $q2Array[0]['ans4'].'　………………………　'.$answer2Array[3].'<br />';
      if(strlen($q2Array[0]['ans5']) > 0) $body .= $q2Array[0]['ans5'].'　………………………　'.$answer2Array[4].'<br />';

      $body .= '<div id="graph2"></div><script type="text/javascript">';
      $body .= "
        $(function () {
          $('#graph2').highcharts({
            chart: {
              plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
    },
    title: {
      text: '".$q2Array[0]['content']."'
    },
    tooltip: {
      pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
          cursor: 'pointer',
          dataLabels: {
            enabled: true,
              color: '#000000',
              connectorColor: '#000000',
              format: '<b>{point.name}</b>: {point.y}'
    }
    }
    },
      series: [{
        type: 'pie',
          name: '".$q2Array[0]['content']."',
          data: [";

      if(strlen($q2Array[0]['ans1']) > 0) $body .= "['".$q2Array[0]['ans1']."',   ".$answer2Array[0]."],";
      if(strlen($q2Array[0]['ans2']) > 0) $body .= "['".$q2Array[0]['ans2']."',   ".$answer2Array[1]."],";
      if(strlen($q2Array[0]['ans3']) > 0) $body .= "['".$q2Array[0]['ans3']."',   ".$answer2Array[2]."],";
      if(strlen($q2Array[0]['ans4']) > 0) $body .= "['".$q2Array[0]['ans4']."',   ".$answer2Array[3]."],";
      if(strlen($q2Array[0]['ans5']) > 0) $body .= "['".$q2Array[0]['ans5']."',   ".$answer2Array[4]."],";

        $body = rtrim($body, ',');
   
      $body .= "
     ]
    }]
    });
    });


    ";

    $body .= '</script>';
    $body .= '</td></tr>';
    $body .= '</table>';

    }
    return $body;
  }






  //idで指定した回覧にコミットおよび回答する
  public function commitAnswer($circleID){
    $body = '';

    $sql = 'select * from circular where id = '.$circleID;
    $circularArray = selectData('circular', $sql);

    if ($circularArray[0]['q1id'] > 0) {
      $sql = 'select * from question where id = '.$circularArray[0]['q1id'];
      $q1Array = selectData('circular', $sql);
      if ($circularArray[0]['q2id'] > 0) {
        $sql = 'select * from question where id = '.$circularArray[0]['q2id'];
        $q2Array = selectData('circular', $sql);
      }
    }

    if($circularArray[0]['q1id'] > 0){
      $body .= '<table class="table table-bordered">';
      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;">アンケート質問１</th>';
      $body .= '</tr>';
      $body .= '<tr>';
      $body .= '<td>'.($q1Array[0]['content']).'</td>';
      $body .= '</tr>';
      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;">回答の候補</th>';
      $body .= '</tr>';
      $body .= '<tr><td>';

      if ($circularArray[0]['secret'] == 1) {
        $body .= '<div class="alert alert-error">この回答は質問の作成者以外には公開されません</div>';
      }else {
        $body .= '<div class="alert alert-info">この回答は公開されます</div>';
      }

      if(strlen($q1Array[0]['ans1']) > 0) $body .= '<input type="radio" name="q1ans" value="1">　'.$q1Array[0]['ans1'].'<br />';
      if(strlen($q1Array[0]['ans2']) > 0) $body .= '<input type="radio" name="q1ans" value="2">　'.$q1Array[0]['ans2'].'<br />';
      if(strlen($q1Array[0]['ans3']) > 0) $body .= '<input type="radio" name="q1ans" value="3">　'.$q1Array[0]['ans3'].'<br />';
      if(strlen($q1Array[0]['ans4']) > 0) $body .= '<input type="radio" name="q1ans" value="4">　'.$q1Array[0]['ans4'].'<br />';
      if(strlen($q1Array[0]['ans5']) > 0) $body .= '<input type="radio" name="q1ans" value="5">　'.$q1Array[0]['ans5'].'<br />';
      if($q1Array[0]['biko'] == 1){
        $body .= '<br />自由記述<br /><textarea id="freeword1" rows="5" class="span6"></textarea>';
      }
      $body .= '</td></tr>';
      $body .= '</table>';
      
    }

    if($circularArray[0]['q2id'] > 0){
      $body .= '<table class="table table-bordered">';
      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;">アンケート質問２</th>';
      $body .= '</tr>';
      $body .= '<tr>';
      $body .= '<td>'.($q2Array[0]['content']).'</td>';
      $body .= '</tr>';
      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;">回答の候補</th>';
      $body .= '</tr>';
      $body .= '<tr><td>';

      if ($circularArray[0]['secret'] == 1) {
        $body .= '<div class="alert alert-error">この回答は質問の作成者以外には公開されません</div>';
      }else {
        $body .= '<div class="alert alert-info">この回答は公開されます</div>';
      }

      if(strlen($q2Array[0]['ans1']) > 0) $body .= '<input type="radio" name="q2ans" value="1">　'.$q2Array[0]['ans1'].'<br />';
      if(strlen($q2Array[0]['ans2']) > 0) $body .= '<input type="radio" name="q2ans" value="2">　'.$q2Array[0]['ans2'].'<br />';
      if(strlen($q2Array[0]['ans3']) > 0) $body .= '<input type="radio" name="q2ans" value="3">　'.$q2Array[0]['ans3'].'<br />';
      if(strlen($q2Array[0]['ans4']) > 0) $body .= '<input type="radio" name="q2ans" value="4">　'.$q2Array[0]['ans4'].'<br />';
      if(strlen($q2Array[0]['ans5']) > 0) $body .= '<input type="radio" name="q2ans" value="5">　'.$q2Array[0]['ans5'].'<br />';
      if($q2Array[0]['biko'] == 1){
        $body .= '<br />自由記述<br /><textarea id="freeword2" rows="5" class="span6"></textarea>';
      }
      $body .= '</td></tr>';
      $body .= '</table>';

      
      
    }
    $body .= '<div style="text-align:right;margin-bottom:60px;"><a id="commitBtn" class="btn btn-primary">確認しました</a></div>';
    return $body;

  }



  //idで指定した回覧を表示するだけ
  public function readFromCircularID($circularID){
    $body = '';

    $sql = 'select * from circular where id = '.$circularID;
    $circularArray = selectData('circular', $sql);

    if ($circularArray[0]['q1id'] > 0) {
      $sql = 'select * from question where id = '.$circularArray[0]['q1id'];
      $q1Array = selectData('circular', $sql);
      if ($circularArray[0]['q2id'] > 0) {
        $sql = 'select * from question where id = '.$circularArray[0]['q2id'];
        $q2Array = selectData('circular', $sql);
      }
    }

    if($circularArray[0]['q1id'] > 0){
      $body .= '<table class="table table-bordered">';
      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;">アンケート質問１</th>';
      $body .= '</tr>';
      $body .= '<tr>';
      $body .= '<td>'.($q1Array[0]['content']).'</td>';
      $body .= '</tr>';
      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;">回答の候補</th>';
      $body .= '</tr>';
      $body .= '<tr><td>';
      if(strlen($q1Array[0]['ans1']) > 0) $body .= ($q1Array[0]['ans1']).'、　';
      if(strlen($q1Array[0]['ans2']) > 0) $body .= ($q1Array[0]['ans2']).'、　';
      if(strlen($q1Array[0]['ans3']) > 0) $body .= $q1Array[0]['ans3'].'、　';
      if(strlen($q1Array[0]['ans4']) > 0) $body .= $q1Array[0]['ans4'].'、　';
      if(strlen($q1Array[0]['ans5']) > 0) $body .= $q1Array[0]['ans5'].'、　';
      if($q1Array[0]['biko'] == 1){
        $body .= '自由記述あり';
      }else{
        $body .= '自由記述なし';
      }
      $body .= '</td></tr>';
      $body .= '</table>';
      
    }

    if($circularArray[0]['q2id'] > 0){
      $body .= '<table class="table table-bordered">';
      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;">アンケート質問２</th>';
      $body .= '</tr>';
      $body .= '<tr>';
      $body .= '<td>'.($q2Array[0]['content']).'</td>';
      $body .= '</tr>';
      $body .= '<tr>';
      $body .= '<th style="background-color:#555555;color:#FFFFFF;">回答の候補</th>';
      $body .= '</tr>';
      $body .= '<tr><td>';
      if(strlen($q2Array[0]['ans1']) > 0) $body .= ($q2Array[0]['ans1']).'、　';
      if(strlen($q2Array[0]['ans2']) > 0) $body .= ($q2Array[0]['ans2']).'、　';
      if(strlen($q2Array[0]['ans3']) > 0) $body .= $q2Array[0]['ans3'].'、　';
      if(strlen($q2Array[0]['ans4']) > 0) $body .= $q2Array[0]['ans4'].'、　';
      if(strlen($q2Array[0]['ans5']) > 0) $body .= $q2Array[0]['ans5'].'、　';
      if($q2Array[0]['biko'] == 1){
        $body .= '自由記述あり';
      }else{
        $body .= '自由記述なし';
      }
      $body .= '</td></tr>';
      $body .= '</table>';
    }
    return $body;
  }





}
