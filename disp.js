var qarray = new Array();///質問の内容。２次元配列
var j;

$(function(){

  $('#replylist input').click(function(){
    qarray=[];
    j=0;
    for(var i=0;i<$('#qcount').val();i++){
      if($('#replylist input[name="optionsRadios'+i+'"]:radio').attr('qid')==null){
        //チェックボックスのとき
        $('#replylist input[name="optionsRadios'+i+'"]:checked').each(function(){
          //console.log($('#replylist input[name="optionsRadios'+i+'"]:checked:eq('+j+')').val());
          qarray.push({qid:$('#replylist input[name="optionsRadios'+i+'"]:checked').attr('qid'),cid:$('#replylist input[name="optionsRadios'+i+'"]:checked:eq('+j+')').val()});
          j=j+1;
        });
      }else{
        //ラジオボタンのとき
        if($('#replylist input[name="optionsRadios'+i+'"]:radio:checked').attr('qid')!=null){
          //console.log($('#replylist input[name="optionsRadios'+i+'"]:radio:checked').val());
          //console.log($('#replylist input[name="optionsRadios'+i+'"]:radio:checked').attr('qid'));
          qarray.push({qid:$('#replylist input[name="optionsRadios'+i+'"]:radio:checked').attr('qid'),cid:$('#replylist input[name="optionsRadios'+i+'"]:radio:checked').val()});
        }
      }
    }
    console.log(qarray);
  });

  $(window).resize(function(){
    var w = $(window).width();
    if (w <= 980) {
      $('#topspace').hide();
    }else{
      $('#topspace').show();
    }
  });

  $('.charts').highcharts({
    chart: {
      width:900,
      height:300,
      type:'pie'
    },
    title: {
      text: $('.charttitle').val(),
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
      name: 'Brands',
      colorByPoint: true,
      data: [{
        name: 'Microsoft Internet Explorer',
        y: 56.33
      },{
        name: 'Chrome',
        y: 24.03,
        sliced: true,
        selected: true
      },{
        name: 'Firefox',
        y: 10.38
      },{
        name: 'Safari',
        y: 4.77
      },{
        name: 'Proprietary or Undetectable',
        y: 0.2
      }]
    }]
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



