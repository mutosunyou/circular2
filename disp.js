$(function() {
  reloadTable();

  /*
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
*/


  //ボタン==================================================
  //ページあたりの表示数変更
  $('#sendbtn').click( function (){
    
  });

});

//関数////////////////////////////////////////////////////////////
//アンケートを表示する。
function reloadTable(){
  $.post(
    "result.php",
    {
    },
    function(data){
      $('#resultlist').html(data);
    }
  );
  
  $.post(
    "reply.php",
    {
      cid:$('#cid').val() 
    },
    function(data){
      $('#replylist').html(data);
    }
  );
}

