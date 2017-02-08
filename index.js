var AllUserArray;
var sheetarray = new Array();//入力欄の内容
var qarray   = new Array();///質問の内容。２次元配列
var ansarray = new Array();
var tmparray = new Array();

//初期動作/////////////////////////////////////////////////////////////////
$(function() {
  var userID = $('#userID').val();
  AllUserArray = $('#userlist>option');
  qarray[0]=[];
  qarray[0].push({question:'',check:''});
  //  console.log(qarray);
  $('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});//カレンダーから日付を選ぶ
  reloadTable();

  //ファイルアップロード====================================
  $('#file_upload').uploadifive({
    'auto'             : false,
    'checkScript'      : 'check-exists.php',
    'onUpload'         : function(file) {},
    'queueID'          : 'queue',
    'buttonClass'      : 'urlbtn',
    'uploadScript'     : 'uploadifive.php',
    'onUpload'         : function(file) {},
    'onUploadError'    : function (){},
    'onUploadComplete' : function(file, data) {
      $.post(
        'helper/addfile.php',
        {
          'path': file.xhr.responseText,
          'fid' : $('#fid').val(),
          'sender' :$('#userID').val()
        },
        function(data){
          console.log(data);
        });
    },
    'onCancel' : function(file){ console.log(file)},
    'onQueueComplete': function(file){
    }
  });

  //ボタン==================================================
  //送信ボタンクリック
  $("#sendbtn").click(function (){
    copytoqarray();
    send();
  });

  //ボタンの有効無効
  $('#title,#cont').change(function(){
    if(checkflg()==1){
      $('#sendbtn').removeAttr('disabled');
    }else{
      $('#sendbtn').attr('disabled', 'disabled');//disabled属性を付与する
    }
  });

  //質問追加(アンケート)
  $("#qlist").on('click','#addq',function(e) {
    copytoqarray();
    var n=qarray.length;
    qarray[n]=[];
    qarray[n].push({question:'',check:''});
    reloadTable();
  });

  //回答追加(アンケート)
  $("#qlist").on('click','.addask',function(e){
    copytoqarray();
    qarray[$(e.target).attr('question')].push({answer:''});
    //console.log(qarray);
    reloadTable();
  });

  //qarray[質問番号][質問、チェックフラグ]
  //qarray[質問番号][回答1]
  //qarray[質問番号][回答2]
  function copytoqarray(){
    var n = qarray.length;//質問数
    var m;
    var tmpsum=0;
    //qarray=[];//配列を一度空にしてから値を全部入れる。記録はテーブルに残ってる。

    for(var i=0;i<n;i++){
      console.log($(".question:eq("+i+")").val());
      qarray[i][0]={check:$(".checkask:eq("+i+")").prop('checked'),question:$(".question:eq("+i+")").val()};
      m=qarray[i].length-1;
      for(var j=0;j<m;j++){
      //console.log($(".answer:eq("+tmpsum+")").val());
        qarray[i][j+1]={answer:$(".answer:eq("+tmpsum+")").val()};
        tmpsum=tmpsum+1;
      }
    }
    //console.log(qarray);
  }

  //メンバー選択=============================================
  //メンバー全追加ボタンクリック
  $('#addAllItem').click(function() {
    var selectedUserArray = $('#userlist>option');
    console.log(selectedUserArray);
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

//関数//////////////////////////////////////////////////////////////////////
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

function send(){
  sheetarray=[];
  sheetarray.push({'title':$('#title').val()});
  sheetarray.push({'content':$('#cont').val()});
  sheetarray.push({"userID":$('#userID').val()});
  sheetarray.push({'secret':$('#secret').prop('checked')});
  sheetarray.push(qarray);

  JSON2 = $.toJSON(sheetarray);
  JSON3 = $.toJSON($('#selectedlist>option'));
  //DB入力
  console.log($('#selectedlist>option'));
  $.post(
    "DBinput.php",
    {
      "id":JSON2,
      "mem":JSON3
    },
    function(data){
      $('#ppp').html(data);
    }
  );
  /*
  //メール送信
  $.post(
    "helper/sendmail.php",
    {
      "id":JSON
    },
    function(){
    }
  );
  */
  //とばすのは全部おわってから
  //  location.href="./list.php";
}

//必須項目入力されているかチェック
function checkflg(){
  var flg=0;
  if($('#title').val().length>0 && $('#cont').val().length>0) {
    flg=1;
  }
  console.log(flg);
  return flg;
}

//メンバー選定
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


