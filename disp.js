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
      console.log(a);
      for(var i=0;i<a.questions.length;i++){
        var sum= new Array();
        for(var j=0;j<a.questions[i].candidates.length;j++){
          sum[j]=0;
          for(var k=0;k<a.questions[i].answers.length;k++){
            if(a.questions[i].answers[k]==j){
              sum[j]++;
            }
          }
          console.log(sum[j]);
        }
        
        $('.charts'+i).highcharts({
          chart: {
            width:600,
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
            data: [{
              name: a.questions[i].candidates[a.questions[i].answers[0].answer],
              y: 56.33
            },{
              name: 'Chrome',
              y: 24.03,
            },{
              name: 'Firefox',
              y: 10.38
            },{
              name: 'Safari',
              y: 4.77
            }]
          }]
        });
      }
    }
  );

  $(window).resize(function(){
    var w = $(window).width();
    if (w <= 980) {
      $('#topspace').hide();
    }else{
      $('#topspace').show();
    }
  }); 

  $('#replylist input').click(function(){
    qarray=[];
    j=0;
    for(var i=0;i<$('#qcount').val();i++){
      if($('#replylist input[name="optionsRadios'+i+'"]:radio').attr('qid')==null){
        //チェックボックスのとき
        $('#replylist input[name="optionsRadios'+i+'"]:checked').each(function(){
          qarray.push({qid:$('#replylist input[name="optionsRadios'+i+'"]:checked').attr('qid'),cid:$('#replylist input[name="optionsRadios'+i+'"]:checked:eq('+j+')').val()});
          j=j+1;
        });
      }else{
        //ラジオボタンのとき
        if($('#replylist input[name="optionsRadios'+i+'"]:radio:checked').attr('qid')!=null){
          qarray.push({qid:$('#replylist input[name="optionsRadios'+i+'"]:radio:checked').attr('qid'),cid:$('#replylist input[name="optionsRadios'+i+'"]:radio:checked').val()});
        }
      }
    }
    console.log(qarray);
  });

  ////////////////////////////////////////////////////////////////
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
      }
    );
  });

});







