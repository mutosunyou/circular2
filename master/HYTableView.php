<?php
/**
 * 
 */
class HYTableView
{

public $dataSource;//データソースになる連想配列の配列
public $keyArray;//キー配列
public $width;//tableWidth
public $pagePerDomain;


  function initWithDataSource($source)
  {
    $this->keyArray = array();
    for ($i=0; $i < count($source); $i++) { 
      foreach($source[$i] as $key => $value){
        $flag = 0;
        foreach($this->keyArray as $kavalue){
          if ($kavalue == $key) $flag = 1;
        }
        if($flag == 0){
          $this->keyArray[] = $key;
        }
      }
    }
    $this->dataSource = $source;
    $this->pagePerDomain = 40;
  }



  function drawTable(){
    $body = '';
    $body .= '<table class="table table-bordered table-striped"';
    if($this->width > 0){
      $body .= ' style="width:'.$this->width.'px;"';
    }
    $body .= '><tr>';
    foreach($this->keyArray as $key){
      if(substr($key,0,3) != '<th') $body .= '<th>';
      $body .= $key.'</th>';
    }
    $body .= '</tr>';
    
    for ($i=0; $i < count($this->dataSource); $i++) { 
      $body .= '<tr>';

      for ($j=0; $j < count($this->keyArray); $j++) { 
        //print $this->keyArray[$j];
        if(isset($this->dataSource[$i][$this->keyArray[$j]])){
          if(substr($this->dataSource[$i][$this->keyArray[$j]],0,3) != '<td'){
            $body .= '<td>';
         }
          $body .= $this->dataSource[$i][$this->keyArray[$j]];
        }else{
          $body .= '<td>';
        }
        $body .= '</td>';
      }

      $body .= '</tr>';
    }

    $body .= '</table>';

    return $body;
  }



  function drawTableWithPagenation($linesPerPage, $page, $pageDomain){
    

    if($pageDomain <= 0){
      if($page % $this->pagePerDomain){
        $pageDomain = intval($page / $this->pagePerDomain) + 1;
      }else{
        $pageDomain = intval($page / $this->pagePerDomain);
      }
    }

    $numOfLine = count($this->dataSource);//アイテム数
    $numOfPage = intval($numOfLine / $linesPerPage);
    if ($numOfLine % $linesPerPage > 0) $numOfPage++;
    $numOfPageDomain = intval($numOfPage / $this->pagePerDomain);
    if ($numOfPage % $this->pagePerDomain > 0) $numOfPageDomain++;

    
    
    $body = '';

    //pagenationを出力
    if($numOfPage > 1){
      $body .= '<div class="pagination pagination-mini"><ul>';

      if($pageDomain == 1){
        $body .= '<li class="disabled"><a>&laquo;</a></li>';
      }else{
        $body .= '<li><a class="pagedomain" domain="'.($pageDomain-1).'" page="'.$page.'">&laquo;</a></li>';
      }

      for ($i=($pageDomain-1)*$this->pagePerDomain; $i < $this->pagePerDomain + (($pageDomain-1)*$this->pagePerDomain); $i++) {
        if($i < $numOfPage){
          $body .= '<li';
          if($i == ($page - 1)) $body .= ' class="active"';
          $body .= '><a class="page" page="'.($i + 1).'">'.($i + 1).'</a></li>';
        }
      }

      if($pageDomain == $numOfPageDomain){
        $body .= '<li class="disabled"><a>&raquo;</a></li>';
      }else{
        $body .= '<li><a class="pagedomain" domain="'. ($pageDomain + 1) .'" page="'.$page.'">&raquo;</a></li>';
      }

      $body .= '</ul></div>';
    }


    //headerを出力
    $body .= '<table class="table table-bordered table-striped"';
    if($this->width > 0){
      $body .= ' style="width:'.$this->width.'px;"';
    }
    $body .= '><tr>';
    foreach($this->keyArray as $key){
      if(substr($key,0,3) != '<th') $body .= '<th>';
      $body .= $key.'</th>';
    }
    $body .= '</tr>';

    //データ出力
    for ($i=0; $i < $linesPerPage; $i++) {
      if($i+($linesPerPage * ($page - 1)) < $numOfLine){
        $body .= '<tr>';
        for ($j=0; $j < count($this->keyArray); $j++) { 
          //print $this->keyArray[$j];
          if(isset($this->dataSource[$i+($linesPerPage * ($page - 1))][$this->keyArray[$j]])){
            if(substr($this->dataSource[$i+($linesPerPage * ($page - 1))][$this->keyArray[$j]],0,3) != '<td'){
              $body .= '<td>';
            }
            $body .= $this->dataSource[$i+($linesPerPage * ($page - 1))][$this->keyArray[$j]];
          }else{
            $body .= '<td>';
          }
          $body .= '</td>';
        }

        $body .= '</tr>';
      }
    }
    $body .= '</table>';



    //pagenationを出力
    if($numOfPage > 1){
      $body .= '<div class="pagination pagination-mini"><ul>';

      if($pageDomain == 1){
        $body .= '<li class="disabled"><a>&laquo;</a></li>';
      }else{
        $body .= '<li><a class="pagedomain" domain="'.($pageDomain-1).'" page="'.$page.'">&laquo;</a></li>';
      }

      for ($i=($pageDomain-1)*$this->pagePerDomain; $i < $this->pagePerDomain + (($pageDomain-1)*$this->pagePerDomain); $i++) {
        if($i < $numOfPage){
          $body .= '<li';
          if($i == ($page - 1)) $body .= ' class="active"';
          $body .= '><a class="page" page="'.($i + 1).'">'.($i + 1).'</a></li>';
        }
      }

      if($pageDomain == $numOfPageDomain){
        $body .= '<li class="disabled"><a>&raquo;</a></li>';
      }else{
        $body .= '<li><a class="pagedomain" domain="'. ($pageDomain + 1) .'" page="'.$page.'">&raquo;</a></li>';
      }

      $body .= '</ul></div>';
    }



    return $body;

  }

}
