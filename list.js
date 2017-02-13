var ownerflg;
//検索キーワード
localStorage.pSearchKey = '';
//並べ替えのキー値
localStorage.pSortKey = "submitDate";
//並べ替えの昇順降順
if (!localStorage.pSortOrder) {
  localStorage.pSortOrder = 'asc';
}
if (!localStorage.ppi) {
  localStorage.ppi = 20;
}
if (!localStorage.page) {
  localStorage.page = 1;
}

$(function() {
   localStorage.page = 1;
  ownerflg=1;
  $('.tabs').tabslet();
  $('#finderfld').val(localStorage.searchKey);
  $('#ppi').val(localStorage.ppi);
  reloadyet();
  reloadall();

  //表のソート
  $('#lister,#own').on('click', '.sorter', function (ev){
    localStorage.page = 1;
    if (localStorage.pSortKey == $(ev.target).attr('name')) {
      if (localStorage.pSortOrder == 'asc') {
        localStorage.pSortOrder = 'desc';
      }else{
        localStorage.pSortOrder = 'asc';
      }
    }else{
      localStorage.pSortOrder = 'asc';
    }
    localStorage.pSortKey = $(ev.target).attr('name');
    reloadall();
  });

  //ボタン==================================================
  //ページあたりの表示数変更
  $('#lister,#own').on('change','#ppi', function (){
    localStorage.ppi = $('#ppi').val();
    localStorage.page = 1;
    reloadall();
  });

  //ページ切り替え
  $('#lister,#own').on('click','.pagebtn', function (ev){
    localStorage.page = $(ev.target).attr('name');
    reloadall();
  });

  //検索ボタン押された
  $('#lister,#own').on('click','#finderbtn', function (){
    localStorage.pSearchKey = $('#finderfld').val();
    localStorage.page = 1;
    reloadall();
  });

  //検索フィールドでエンター押された
  $('#lister,#own').on('keypress','#finderfld', function (e) {
    if ( e.which == 13 ) {
      localStorage.pSearchKey = $('#finderfld').val();
      localStorage.page = 1;
      reloadall();
      return false;
    }
  });

  $('#lister,#own,#yet').on('click','.dispcontents', function (e) {
    location.href='disp.php?cid='+$(e.target).attr('name');
});


  $('#owntab,#alltab').click(function(e){
    ownerflg= 1;
    reloadall();
  });

  $('#yettab').click(function(e){
    ownerflg= 0;
    reloadall();
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
      "page": localStorage.page,
      "itemsPerPage": localStorage.ppi,
      "sortKey": localStorage.pSortKey,
      "sortOrder": localStorage.pSortOrder,
      "searchKey": localStorage.pSearchKey,
      "own":ownerflg
    },
    function(data){
      $('#lister').html(data);
      $('#own').html(data);
      var arr = $('.sorter');
      for (var i=0; i < arr.length; ++i) {
        if ($(arr[i]).attr('name') == localStorage.pSortKey) {
          var mark = '';
          if (localStorage.pSortOrder == 'asc') {
            mark = '▲';
          }else{
            mark = '▼';
          }
          $(arr[i]).html($(arr[i]).html() + mark);
        }
      }
    }
  );
}


