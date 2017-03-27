var AllUserArray;
var sheetarray = new Array();//入力欄の内容
var qarray     = new Array();///質問の内容。２次元配列
var memarray   = new Array();
var filearray  = new Array();
var wait;
var filenum=0;
var cid;

//初期動作====================================================
$(function() {
  wait=0;
  var userID = $('#userID').val();
  $('#questionnaire').hide();
  $('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});//カレンダーから日付を選ぶ

  //回覧メンバー
  AllUserArray = $('#userlist>option');
  //はじめにqarray
  qarray[0]=[];
  qarray[0].push({question:'',check:'',nothaveto:'',stype:''});
  reloadTable();

//ファイルアップロード=======================================
  $('#file_upload').uploadifive({
    'auto'             : false,
    'checkScript'      : 'check-exists.php',
    'queueID'          : 'queue',
    'buttonClass'      : 'urlbtn',
    'uploadScript'     : 'uploadifive.php',
    'onSelect' : function(queue) {
      console.log(queue);
    },
    'onUpload'         : function(filesToUpload) {
      console.log(filesToUpload);
    },
    'onError'    : function (errorType){
      alert('The error was: ' + errorType);
    },
    'onUploadComplete' : function(file, data) {
      console.log(file.xhr);
      console.log(file.xhr.responseText);
      console.log(cid);
      $.post(
        'helper/addfile.php',
        {
          'path': file.xhr.responseText,
          'cid' : cid
        },
        function(dat){
          console.log("file DB uploaded");
        });
    },
    'onQueueComplete': function(file){
           //メール送信
        $.post(
          "helper/sendmail.php",
          {
            "cid":cid,
            "id" :JSON2,
            "mem":JSON3
          },
          function(dat){
            console.log(dat);
            location.href="./list.php";
          }
        );
    },
    'onCancel' : function(file){ 
      console.log(file.name);
      filenum--;
    },//ファイルを取り消したとき
    'onAddQueueItem': function(file){ 
      console.log(file.name);
      filenum++;
    }//ファイルを選択したとき
  });

  //ボタン======================================================
  //送信内容確認ボタンクリック
  $("#sendbtn").click(function (){
    copytoqarray();
    //登録データ
    sheetarray=[];
    sheetarray.push({'title':$('#title').val().replace(/\r?\n/g, '<br>')});//[0]表題
    sheetarray.push({'content':$('#cont').val().replace(/\r?\n/g, '<br>')});//[1]内容
    sheetarray.push({'userID':$('#userID').val()});//[2]投稿者
    sheetarray.push({'secret':$('#secret').prop('checked')});//[3]隠すか否か
    if($('#enablequestionnaire').prop('checked')){
      if(qarray[0][0]['question']!='""'){
        sheetarray.push(qarray);//[4]アンケートの内容
      }
    }
    JSON2 = $.toJSON(sheetarray);
    
    var len=$('#selectedlist>option').length;
    memarray=[];
    for(var i=0;i<len;i++){
      memarray[i]={num:$("#selectedlist>option:eq("+i+")").val()};
    }
    JSON3 = $.toJSON(memarray);
    console.log(memarray);

    filearray=[];
    for(var i=0;i<filenum;i++){
      filearray.push({'name':$('.filename:eq('+i+')').text()});
    }
    //console.log(filearray);
    JSON4 = $.toJSON(filearray);
    console.log(filearray);
    confirmation();
    $('#hiddenwall').show(); 
  });

  //回覧内容確認→スタートボタン
  $('#confirm').on('click','#gocircular',function(){
    send();
  });

  function send(){
    JSON2 = $.toJSON(sheetarray);
    JSON3 = $.toJSON(memarray);
    //DB入力
    $.post(
      "DBinput.php",
      {
        "id" :JSON2,
        "mem":JSON3
      },
      function(data){
        cid=data;//DBinputで回覧IDを取得
        console.log(cid);
        console.log("before upload");
        $('#file_upload').uploadifive('upload');
        console.log("after upload");
      }
    );
    
  } //回覧開始ボタンの終わり

  $('*').change(function(){
    copytoqarray();
    if(checkflg()==1){
      $('#sendbtn').removeAttr('disabled');
    }else{
      $('#sendbtn').attr('disabled', 'disabled');//disabled属性を付与する
    }
  });

  $('*').click(function(){
    copytoqarray();
    if(checkflg()==1){
      $('#sendbtn').removeAttr('disabled');
    }else{
      $('#sendbtn').attr('disabled', 'disabled');//disabled属性を付与する
    }
  });

  //アンケートフォーム有効
  $('#enablequestionnaire').change(function(){
    if($("#enablequestionnaire").prop('checked')){
      $('#questionnaire').show();
    }else{
      $('#questionnaire').hide();
    }
  });

  //削除ボタン
  $('#qlist').on('click','.delq', function(e){
    copytoqarray();//現状をデータに反映させる。
    qarray.splice($(e.target).attr('delqnum'),1);
    reloadTable();
  });

  //削除ボタン
  $('#qlist').on('click','.delcan', function(e){
    copytoqarray();//現状をデータに反映させる。
    qarray[$(e.target).attr('delqnum')].splice($(e.target).attr('delnum'),1);
    reloadTable();
  });

  //質問追加(アンケート)
  $("#qlist").on('click','#addq',function(e) {
    copytoqarray();//現状をデータに反映させる。
    var n=qarray.length;
    //最後尾に空の質問を追加
    qarray[n]=[];
    qarray[n].push({question:'',check:'',nothaveto:'',stype:0});
    reloadTable();
  });

  //回答追加(アンケート)
  $("#qlist").on('click','.addask',function(e){
    copytoqarray();
    //最後尾に空の回答を追加
    qarray[$(e.target).attr('question')].push({answer:''});
    reloadTable();
  });

  //回覧キャンセル
  $("#confirm").on('click','#cancel',function(e){
    $("#hiddenwall").hide();
  });

  //回覧キャンセル
  $("#hiddenwall").click(function(){
    $("#hiddenwall").hide();
  });

  //質問、回答の数を勝手に数えて配列に入れる。質問数だけはわかっておく必要ある。
  function copytoqarray(){
    var n = qarray.length;//質問数
    var m;
    var tmpsum=0;
    var selecttype;

    for(var i=0;i<n;i++){
      m=qarray[i].length-1;
      qarray[i]=[];
      if($('#qlist input[name="selecttype'+i+'"]:radio:checked').val()=="check"){
        selecttype=1;//チェックボックスであれば1
      }else{
        selecttype=0;//ラジオボタンであれば0もしくは初期値は0
      }
      qarray[i][0]={stype:selecttype,check:$(".checkask:eq("+i+")").prop('checked'),nothaveto:$(".nothaveto:eq("+i+")").prop('checked'),question:$(".question:eq("+i+")").val()};
      for(var j=0;j<m;j++){
        qarray[i][j+1]=[];
        qarray[i][j+1]={answer:$(".answer:eq("+tmpsum+")").val()};
        tmpsum=tmpsum+1;
      }
    }
  }
  //qarray[質問番号][0][質問、チェックフラグ]
  //qarray[質問番号][1][回答1]
  //qarray[質問番号][2][回答2]

  //メンバー選択=============================================
  //メンバー全追加ボタンクリック
  $('#addAllItem').click(function() {
    var selectedUserArray = $('#userlist>option');
    //console.log(selectedUserArray);
    setUserArrayToSelectedSelector(selectedUserArray.clone());
  });

  //メンバー追加ボタンクリック
  $('#addSelectedItem').click(function() {
    var selectedUserArray = $('#userlist>option:selected');
    setUserArrayToSelectedSelector(selectedUserArray.clone());
  });

  //メンバー削除ボタンクリック
  $('#removeAllItem').click(function() {
    var selectedArray = $('#selectedlist>option');
    removeUserArrayFromSelectedSelector(selectedArray);
  });

  $('#removeSelectedItem').click(function() {
    var selectedArray = $('#selectedlist>option:selected');
    removeUserArrayFromSelectedSelector(selectedArray);
  });

  //部門セレクター変更→選択項目候補変更
  $('#bselector').change( function (e){
    setAllUserArrayToUserSelector();
    if($(e.target).val() != 0){
      var userArray = $('#userlist>option[bumon="'+$(e.target).val()+'"]');
      setUserArrayToUserSelector(userArray);
    }
  });

  //吹き出し==================================================
  $("#userlist").hover(function(){
    $('#userlist').showBalloon({
      contents:"複数名選択できます。",
      position:"right",
      minLifetime:0,
    });
  });
  $("#userlist").mouseleave(function(){
    $('#userlist').hideBalloon();
  });
});

//関数////////////////////////////////////////////////////////
//アンケートを表示する。
function reloadTable(){
  JSON = $.toJSON(qarray);
  $.post(
    "helper/qlister.php",
    {
      "qarray":JSON
    },
    function(data){
      $('#qlist').html(data);
    }
  );
}

//必須項目入力されているかチェック
function checkflg(){
  var flg=0;
  var n=qarray.length;
  var tmp=0,ready=1;
  //自由解答欄を設けるか、選択肢が１つ作らないといけない
  for(var i=0;i<n;i++){
    if(qarray[i][0]['check']==true || qarray[i].length>1){
      tmp=1;
    }else{tmp=0;}
    ready=ready*tmp;
  }
  if($('#title').val().length>0 && $('#cont').val().length>0 && ($('#enablequestionnaire').prop('checked')==false || ($('#enablequestionnaire').prop('checked')==true && ready==1))) {
    flg=1;
  }
  return flg;
}

function setUserArrayToSelectedSelector(uarray){
  for (var i=0; i < uarray.length; i++) {
    var arr = $('#selectedlist>option[value="'+$(uarray[i]).val()+'"]');
    if (arr.length == 0){
      $('#selectedlist').append(uarray[i]);//重複排除
    }
  }
}

function removeUserArrayFromSelectedSelector(uarray){
  for (var i=0; i < uarray.length; i++) {
    $('#selectedlist>option[value="'+$(uarray[i]).val()+'"]').remove();
  }
}

function setAllUserArrayToUserSelector(){
  $('#userlist>option').remove();
  for (var i=0; i < AllUserArray.length; i++) {
    $('#userlist').append(AllUserArray[i]);
  }
}

function setUserArrayToUserSelector(uarray){
  $('#userlist>option').remove();
  for (var i=0; i < uarray.length; i++) {
    $('#userlist').append(uarray[i]);
  }
}

//確認画面
function confirmation(){
  $.post(
    "helper/confirm.php",
    {
      "qarray":JSON2,
      "mem":JSON3,
      "farray":JSON4
    },
    function(data){
      $('#confirm').html(data);
    }
  );
}
