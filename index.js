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

    console.log('change');
  });

  //ボタン==================================================
  //送信ボタンクリック
  $("#sendbtn").click(function (){
    if(checkflg()==1){//明細が入力されている
    }else{
      reset();
      alertify.alert('明細項目で未入力のものがあります。');
    }
  });

  //アンケート
  $("#qlist").on('click','#addq',function() {
    qarray.push(1);
    reloadTable();
  });

  $("#qlist").on('click','.addask',function(e) {
    // console.log($(e.target).attr('question'));
    //console.log(qarray[$(e.target).attr('question')]);
    qarray[$(e.target).attr('question')]=qarray[$(e.target).attr('question')]+1;
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

  function setUserArrayToSelectedSelector(uarray){
    for (var i=0; i < uarray.length; i++) {
      var arr = $('#selectedlist>option[value="'+$(uarray[i]).val()+'"]');
      if (arr.length == 0){
        $('#selectedlist').append(uarray[i]);//重複排除
        //   console.log($('#selectedlist').append(uarray[i]));
      }
    }
  }

  //メンバー削除ボタンクリック
  $('#removeAllItem').click(function() {
    var selectedArray = $('#selectedlist>option');
    removeUserArrayFromSelectedSelector(selectedArray);
  });

  $('#removeSelectedItem').click(function() {
    var selectedArray = $('#selectedlist>option:selected');
    removeUserArrayFromSelectedSelector(selectedArray);
    //   for(var i=0; i < Object.keys($('#selectedlist>option')).length; i++) { 
    //     console.log($('#selectedlist>option'));
    //   }
  });

  function removeUserArrayFromSelectedSelector(uarray){
    for (var i=0; i < uarray.length; i++) {
      $('#selectedlist>option[value="'+$(uarray[i]).val()+'"]').remove();
    }
  }

  //部門セレクター変更→選択項目候補変更
  $('#bselector').change( function (e){
    setAllUserArrayToUserSelector();
    if($(e.target).val() != 0){
      var userArray = $('#userlist>option[bumon="'+$(e.target).val()+'"]');
      //    console.log(userArray);
      setUserArrayToUserSelector(userArray);
    }
  });

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
//履歴テーブルを更新する。
function reloadTable(){
  // $('#pagenum').show();
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

function checkflg(){
  var flg=0;
  if($('#title').val().length>1 && $('#cont').val().length>0 && $('#selectedlist>option').val()>0) {
    flg=1;
  }
  return flg;


}

