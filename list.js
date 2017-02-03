var AllUserArray;
var sheetarray = new Array();
var qarray = new Array();
//初期動作/////////////////////////////////////////////////////////////////
$(function() {
  $('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});//カレンダーから日付を選ぶ
  var userID = $('#userID').val();
  AllUserArray = $('#userlist>option');
  qarray.push(1);

  reloadTable();

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
      //  location.href = locationtoreturn;
    }
  });

  //データを配列に入れる====================================
  $('#title,#cont').change(function(){
    if(checkflg()==1){
      $('#sendbtn').removeAttr('disabled');
    }else{
      $('#sendbtn').attr('disabled', 'disabled');//disabled属性を付与する
    }
    console.log('change');
  });

  //ボタン==================================================
  //送信ボタンクリック
  $("#sendbtn").click(function (){

  });

  //アンケート===============================================
  $("#qlist").on('click','#addq',function(e) {
    qarray.push(1);
    reloadTable();
  });

  $("#qlist").on('click','.addask',function(e) {
    $(e.target).attr('question');
    qarray[$(e.target).attr('question')]=qarray[$(e.target).attr('question')]+1;
    reloadTable();
  });

  $("#qlist").on('click','.checkask',function(e){
   $(e.target).attr('check') 
    reloadTable();
  });

  //メンバー選択==================================================
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

//関数////////////////////////////////////////////////////////////
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
