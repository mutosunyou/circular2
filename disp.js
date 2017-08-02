var qarray = new Array();///質問の内容。２次元配列
var j;
var obj;
var a;
var available=0;
var tempava=1;//アンケートあるときのavailable監視用

$(function(){ 
  if($('#qcount').val()>0){//質問の数
    $('#sendbtn').attr('disabled', 'disabled');//disabled属性を付与する
    tempava=1;
  }else{
    available=1;
  }
  $.post(
    "jsondata.php",
    {
      "cid":$('#cid').val()
    },
    function(data){
      a=$.parseJSON(data);
      console.log(a);
      for(var i=0;i<a.questions.length;i++){
        var sum= new Array();
        var darr=new Array();
        console.log(a.questions[i].nothaveto);
        console.log(tempava);
        if(a.questions[i].nothaveto==0){
          tempava=0;
        }
        for(var j=0;j<a.questions[i].candidates.length;j++){
          sum[j]=0;
          for(var k=0;k<a.questions[i].answers.length;k++){
            if(a.questions[i].answers[k].answer==j){
              sum[j]++;
            }
          }
          darr.push({name:a.questions[i].candidates[j],y:sum[j]});
        }
        //console.log(a);
        if(a.questions[i].answers.length>0){
          console.log(a);
          if(a.questions[i].answers[0].answer!=null && $('#author').val()==1){
            $('.charts'+i).highcharts({
              chart: {
                width:500,
                height:300,
                type:'pie'
              },
              title: {
                text: a.questions[i].content,
              },
              tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
              },
              plotOptions:{
                pie: {
                  allowPointSelect: true,
                  cursor: 'pointer',
                  dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                      color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                  }
                }
              },
              series: [{
                name: 'Questionaire',
                colorByPoint: true,
                data: darr
              }]
            });//highcharts終わり
          }
        }
      }//for i終わり
      console.log(tempava);
      if(tempava==1){
        $('#sendbtn').removeAttr('disabled');
      }
    });//postスクリプトで送る内容終わり

  $(window).resize(function(){
    var w = $(window).width();
    if (w <= 980) {
      $('#topspace').hide();
    }else{
      $('#topspace').show();
    }
  });

  //ラジオボタン、チェックボックス、自由記入欄に入力の変化があったとき
  $('#replylist input').change(function(){
    qarray=[];
    var flg1;//チェックボックス用
    var flg2;//ラジオボックス用
    var tmp;//確認ボタンの有効フラグ
    j=0;
    available=1;

    for(var i=0;i<$('#qcount').val();i++){
      tmp=1;
      if($('#replylist input[name="optionsRadios'+i+'"]:radio').attr('qid')==null){
        //チェックボックスのとき→チェックが入っている項目を全部配列に入れる（質問ID、回答ID)
        console.log("チェックボックス");
        flg1=0;
        $('#replylist input[name="optionsRadios'+i+'"]:checked').each(function(){//チェックボックスにチェックされたものがあれば処理 flg1=1
          qarray.push({qid:$('#replylist input[name="optionsRadios'+i+'"]:checked').attr('qid'),cid:$('#replylist input[name="optionsRadios'+i+'"]:checked:eq('+j+')').val()});
          j=j+1;
          flg1=1;
        });
        if(a.questions[i].nothaveto==0 && flg1==0){//無回答ダメでflg1=0（チェックなし）
          tmp=0;
        }
      }else{
        //ラジオボタンのとき→配列にチェックが入っている情報のみ入れる, 自由解答欄のとき
        console.log("ラジオボタン");
        flg2=0;
        if($('#replylist input[name="optionsRadios'+i+'"]:radio:checked').attr('qid')!=null){//何かしらチェックあり flg2=1
          qarray.push({qid:$('#replylist input[name="optionsRadios'+i+'"]:radio:checked').attr('qid'),cid:$('#replylist input[name="optionsRadios'+i+'"]:radio:checked').val()});
          flg2=1;
        }
        if(a.questions[i].nothaveto==0 && flg2==0){//無回答ダメでflg2=0（チェックなし）もしくは自由解答欄に記入なし
          tmp=0;
        }
      }
      if($('#replylist input[name="fs'+i+'"]').val()!=null){
        qarray.push({qid:$('#replylist input[name="fs'+i+'"]').attr('qid'),desc:$('#replylist input[name="fs'+i+'"]').val()});
        tmp=1;
      }
      if(tmp==0){
        available=0;
      }
    }
    if(available==1){
      $('#sendbtn').removeAttr('disabled');
    }else{
      $('#sendbtn').attr('disabled', 'disabled');//disabled属性を付与する
    }
    console.log(qarray);
  });

  //ボタン==================================================
  //ページあたりの表示数変更
  $('#sendbtn').click( function (){
    JSON=$.toJSON(qarray);
    $.post(
      "replyDB.php",
      {
        "cid":$('#cid').val(),
        "aarray":JSON
      },
      function(data){
        console.log(data);
        location.href="list.php";
      }
    );
  });//送信ボタンクリック動作終わり

  /*
  $('*').change(function(){
    if(available==1){
      $('#sendbtn').removeAttr('disabled');
    }else{
      $('#sendbtn').attr('disabled', 'disabled');//disabled属性を付与する
    }
  });

  $('*').click(function(){
    if(available==1){
      $('#sendbtn').removeAttr('disabled');
    }else{
      $('#sendbtn').attr('disabled', 'disabled');//disabled属性を付与する
    }
  });
*/
});//スクリプト終わり

