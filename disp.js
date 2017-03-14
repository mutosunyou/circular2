var qarray = new Array();///質問の内容。２次元配列
var j;
var obj;
var a;

$(function(){ 
  $.post(
    "jsondata.php",
    {
      "cid":$('#cid').val()
    },
    function(data){
      a=$.parseJSON(data);

      for(var i=0;i<a.questions.length;i++){
        var sum= new Array();
        var darr=new Array();
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
              plotOptions: {
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
    });//postスクリプトで送る内容終わり



  $(window).resize(function(){
    var w = $(window).width();
    if (w <= 980) {
      $('#topspace').hide();
    }else{
      $('#topspace').show();
    }
  }); 

  $('#replylist input').change(function(){
    qarray=[];
    j=0;
    for(var i=0;i<$('#qcount').val();i++){
      if($('#replylist input[name="optionsRadios'+i+'"]:radio').attr('qid')==null){
        //チェックボックスのとき→チェックが入っている項目を全部配列に入れる（質問ID、回答ID)
        $('#replylist input[name="optionsRadios'+i+'"]:checked').each(function(){
          qarray.push({qid:$('#replylist input[name="optionsRadios'+i+'"]:checked').attr('qid'),cid:$('#replylist input[name="optionsRadios'+i+'"]:checked:eq('+j+')').val()});
          j=j+1;
        });
      }else{
        //ラジオボタンのとき→配列にチェックが入っている情報のみ入れる
        if($('#replylist input[name="optionsRadios'+i+'"]:radio:checked').attr('qid')!=null){
          qarray.push({qid:$('#replylist input[name="optionsRadios'+i+'"]:radio:checked').attr('qid'),cid:$('#replylist input[name="optionsRadios'+i+'"]:radio:checked').val()});
        }
      }
      if($('#replylist input[name="fs'+i+'"]').val()!=null){
        qarray.push({qid:$('#replylist input[name="fs'+i+'"]').attr('qid'),desc:$('#replylist input[name="fs'+i+'"]').val()});
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
});//スクリプト終わり

$('#forcebtn').click(function(){
  console.log($('#forceread > option').attr('name'));
  $.post(
    "foeceread.php",
    {
      "member":$('#forceread > option').attr('name')
    },
    function(){
      location.href="list.php";
    }
  );
});

