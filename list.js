var ownerflg;
//検索キーワード
localStorage.circSearchKey = '';
//並べ替えのキー値
localStorage.cirSortKey = "submitDate";
//並べ替えの昇順降順
if (!localStorage.circSortOrder) {
  localStorage.circSortOrder = 'desc';
}
if (!localStorage.cirppi) {
  localStorage.cirppi = 20;
}
if (!localStorage.cirpage) {
  localStorage.cirpage = 1;
}

$(function() {
  localStorage.cirpage = 1;
  ownerflg=0;
  $('.tabs').tabslet();
  $('.finderfld').val(localStorage.cirSearchKey);
  $('.ppi').val(localStorage.cirppi);
  reloadall();
  reloadyet();

  //表のソート
  $('#lister,#own').on('click', '.sorter', function (ev){
    localStorage.cirpage = 1;
    if (localStorage.cirSortKey == $(ev.target).attr('name')) {
      if (localStorage.circSortOrder == 'asc') {
        localStorage.circSortOrder = 'desc';
      }else{
        localStorage.circSortOrder = 'asc';
      }
    }else{
      localStorage.circSortOrder = 'asc';
    }
    localStorage.cirSortKey = $(ev.target).attr('name');
    reloadall();
  });

  //ボタン==================================================
  //ページあたりの表示数変更
  $('#lister').on('change','.ppi', function (){
    localStorage.cirppi = $('#lister .ppi').val();
    localStorage.cirpage = 1;
    reloadall();
  });
  $('#own').on('change','.ppi', function (){
    localStorage.cirppi = $('#own .ppi').val();
    localStorage.cirpage = 1;
    reloadall();
  });
  //ページ切り替え
  $('#lister').on('click','.pagebtn', function (ev){
    localStorage.cirpage = $(ev.target).attr('name');
    reloadall();
  });
  $('#own').on('click','.pagebtn', function (ev){
    localStorage.cirpage = $(ev.target).attr('name');
    reloadall();
  });

  //検索ボタン押された
  $('#lister').on('click','.finderbtn', function (){
    localStorage.circSearchKey = $('#lister .finderfld').val();
    localStorage.cirpage = 1;
    reloadall();
  });
  $('#own').on('click','.finderbtn', function (){
    localStorage.circSearchKey = $('#own .finderfld').val();
    localStorage.cirpage = 1;
    reloadall();
  });

  //検索フィールドでエンター押された
  $('#lister').on('keypress','.finderfld', function (e) {
    if ( e.which == 13 ) {
      localStorage.circSearchKey = $('#lister .finderfld').val();
      localStorage.cirpage = 1;
      reloadall();
      return false;
    }
  });
  $('#own').on('keypress','.finderfld', function (e) {
    if ( e.which == 13 ) {
      localStorage.circSearchKey = $('#own .finderfld').val();
      localStorage.cirpage = 1;
      reloadall();
      return false;
    }
  });

  $('#lister,#own,#yet').on('click','.dispcontents', function (e) {
    location.href='disp.php?cid='+$(e.target).attr('name');
  });

  $('#owntab').click(function(e){
    ownerflg= 1;
    reloadall();
  });
  $('#alltab').click(function(e){
    ownerflg= 0;
    reloadall();
  });
  $('#yettab').click(function(e){
    ownerflg= 0;
    reloadyet();
  });

});

//関数////////////////////////////////////////////////////////////
//アンケートを表示する。
function reloadyet(){
  var mark = '';
  $.post(
    "helper/yet.php",
    {
    },
    function(data){
      $('#yet').html(data);
    }
  );
}

function reloadall(){
  $.post(
    "helper/lister.php",
    {
      "page": localStorage.cirpage,
      "itemsPerPage": localStorage.cirppi,
      "sortKey": localStorage.cirSortKey,
      "sortOrder": localStorage.circSortOrder,
      "searchKey": localStorage.circSearchKey,
      "own":ownerflg
    },
    function(data){
      $('#lister').html(data);
      $('#own').html(data);
      var arr = $('.sorter');
      for (var i=0; i < arr.length; ++i) {
        if ($(arr[i]).attr('name') == localStorage.cirSortKey) {
          var mark = '';
          if (localStorage.circSortOrder == 'asc') {
            mark = '▲';
          }else{
            mark = '▼';
          }
          $(arr[i]).html($(arr[i]).html() + mark);
        }
      }
    });
}
