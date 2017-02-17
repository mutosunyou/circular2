var qarray     = new Array();///質問の内容。２次元配列

$(function() {
  qarray=[];
  console.log($('#qcount').val());
  reloadTable();
  
  $('#replylist input').click(function(){
    for(var i=0;i<$('#qcount').val();i++){
      if($('#replylist input[name="optionsRadios'+i+'"]:radio:checked').attr('qid')==null){
        $('#replylist input[name="optionsRadios'+i+'"]:checked').each(function(){
          console.log($('#replylist input[name="optionsRadios'+i+'"]:checked').val());
        });
      }else{
        console.log($('#replylist input[name="optionsRadios'+i+'"]:radio:checked').attr('qid'));
      }
    }
    /*
    for(var i=0;i<$('#qcount').val();i++){
      if($('#replylist input[name="optionsRadios'+i+'"]:radio:checked').attr('qid')==null){
          console.log($('#replylist input[name="optionsRadios'+i+'"]:checked').val());
      }else{
        console.log($('#replylist input[name="optionsRadios'+i+'"]:radio:checked').attr('qid'));
      }
    }
    */
  });
  /*
  $().click(function(){
  });
    for(var i=0;i<n;i++){
      m=qarray[i].length-1;
      qarray[i]=[];
      if($('#qlist input[name="selecttype'+i+'"]:radio:checked').val()=="check"){
        selecttype=1;//チェックボックスであれば1
      }else{
        selecttype=0;//ラジオボタンであれば0もしくは初期値は0
      }
      qarray[i][0]={stype:selecttype,check:$(".checkask:eq("+i+")").prop('checked'),question:$(".question:eq("+i+")").val()};
      for(var j=0;j<m;j++){
        qarray[i][j+1]=[];
        qarray[i][j+1]={answer:$(".answer:eq("+tmpsum+")").val()};
        tmpsum=tmpsum+1;
      }
    }
*/

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
    $.post(
      "replyDB.php",
      {
        cid:$('#cid').val()
      },
      function(data){
      }
    );
  });

});

//関数////////////////////////////////////////////////////////////
//アンケートを表示する。
function reloadTable(){

}

