$(function(){ 
  $(window).resize(function(){
    var w = $(window).width();
    if (w <= 980) {
      $('#topspace').hide();
    }else{
      $('#topspace').show();
    }
  }); 


  $('#forcebtn').click(function(){
    console.log($('#forceread>option:selected').attr('name'));
    $.post(
      "foeceread.php",
      {
        "member":$('#forceread>option:selected').attr('name')
      },
      function(){
        location.href="list.php";
      }
    );
  });
});
