<?php
/**
 * 
 */
class HYCSV
{

  public $dataSource;//データソースになる連想配列の配列
  public $keyArray;//キー配列


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


  function initWithFilePath($path){

    $fp = fopen($path, "r");
    $bigArray = array();
    while(($buf = fgetcsv($fp)) !== false){
       $bigArray[] = $buf;
    }
    print_r($bigArray);
    $dataSource = $bigArray;
  }


  function drawCSV(){
    $body = '';
    for ($i=0; $i < count($this->keyArray); $i++) { 
      $body .= $this->keyArray[$i];
      if($i + 1 != count($this->keyArray)){
        $body .= ',';
      }
    }
    foreach($this->keyArray as $key){
      
    }
    $body .= PHP_EOL;

    for ($i=0; $i < count($this->dataSource); $i++) { 

      for ($j=0; $j < count($this->keyArray); $j++) { 
        if(isset($this->dataSource[$i][$this->keyArray[$j]])){
          $body .= $this->dataSource[$i][$this->keyArray[$j]];
        }
        if($j + 1 != count($this->keyArray)){
          $body .= ',';
        }
      }

      $body .= PHP_EOL;
    }

    return $body;
  }



}
