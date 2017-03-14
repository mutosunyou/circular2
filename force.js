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
});
