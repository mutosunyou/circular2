var qarray = new Array();///質問の内容。２次元配列
var a,j,obj;

$(function(){ 
  //初期動作
  $('#sendbtn').removeAttr('disabled');//確認ボタン押せる
  if($('#qcount').val()>0){//質問の数
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
          if(a.questions[i].nothaveto==0){
            $('#sendbtn').attr('disabled', 'disabled');//一つでも回答必須があれば確認ボタン押せない。
          }
          for(var j=0;j<a.questions[i].candidates.length;j++){//i番目の質問のj番目の回答候補について
            sum[j]=0;
            for(var k=0;k<a.questions[i].answers.length;k++){
              if(a.questions[i].answers[k].answer==j){
                sum[j]++;//j番目の回答候補の回答数
              }
            }
            darr.push({name:a.questions[i].candidates[j],y:sum[j]});
          }
          if(a.questions[i].answers.length>0){//i個目の回答数が0以上なら
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
      }
    );//postスクリプトで送る内容終わり
  }//初期動作終わり

  //ラジオボタン、チェックボックス、自由記入欄に入力の変化があったとき
  $('#replylist input').change(function(){
    qarray=[];
    var flg=1;
    j=0;
    $('#sendbtn').removeAttr('disabled');
    for(var i=0;i<$('#qcount').val();i++){
      if(a.questions[i].nothaveto==0){//回答必須のとき
        console.log($('#replylist input[name="optionsRadios'+i+'"]').attr('type'));
        if($('#replylist input[name="optionsRadios'+i+'"]').attr('type')=="checkbox"){
          //チェックボックスのとき→チェックが入っている項目を全部配列に入れる（質問ID、回答ID)
          flg=0;
          $('#replylist input[name="optionsRadios'+i+'"]:checked').each(function(){//チェックボックスにチェックされたものがあれば処理 flg=1
            qarray.push({qid:$('#replylist input[name="optionsRadios'+i+'"]:checked').attr('qid'),cid:$('#replylist input[name="optionsRadios'+i+'"]:checked:eq('+j+')').val()});
            j++;
            flg=1;
          });
        }else if($('#replylist input[name="optionsRadios'+i+'"]').attr('type')=="radio"){
          //ラジオボタンのとき→配列にチェックが入っている情報のみ入れる, 自由解答欄のとき
          flg=0;
          if($('#replylist input[name="optionsRadios'+i+'"]:radio:checked').attr('qid')!=null){//何かしらチェックあり flg=1
            qarray.push({qid:$('#replylist input[name="optionsRadios'+i+'"]:radio:checked').attr('qid'),cid:$('#replylist input[name="optionsRadios'+i+'"]:radio:checked').val()});
            flg=1;
          }
        }
        if($('#replylist input[name="fs'+i+'"]').val()!=null){//自由回答記入欄に何か文字が打ってある(回答必須条件には影響を与えない仕様にする)
          qarray.push({qid:$('#replylist input[name="fs'+i+'"]').attr('qid'),desc:$('#replylist input[name="fs'+i+'"]').val()});
          flg=1;//チェックいれてない状態でも自由記入欄に埋めていれば回答必須条件満たす
        }
        if(flg==0){
          $('#sendbtn').attr('disabled', 'disabled');//確認ボタン押せなくする
        }
      }
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

  $(window).resize(function(){
    var w = $(window).width();
    if (w <= 980) {
      $('#topspace').hide();
    }else{
      $('#topspace').show();
    }
  });
});//スクリプト終わり

