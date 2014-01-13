<?php

class root {
  var $path;
  
  function root() {
    $address = $_SERVER['HTTP_HOST'];
    $this->path = $address;
    $this->server = $_SERVER;
  }
  
  function include_php($filter = '') {
    global $GAMINAS;
    if(gettype($filter) == 'string' && $filter != '') { // Если даем строкой только один нужный модуль
      $files[0] = glob('php/' . $filter . '.php');
    } else if (gettype($filter) == 'array') { // Если в массиве перечисляем нужные модули
      foreach ($filter as $need) {
        $files[] = glob('php/' . $need . '.php');
      }
    } else { // Если вообще не даем параметров, соответственно, нужны вообще все модули, пока что нужно в качестве костыля
      $files = glob('php/*.php'); 
    }
    foreach($files as $file) {
      require_once($file);
    }
  }
  
}

$GAMINAS = array(); // Глобальная переменная, куда будет запихиваться весь нужный хлам
$GAMINAS['test'] = 'test';
session_start();
$page = new root();
$page->include_php();
$GAMINAS['source'] = 'http://' . $page->path . '/source';
// var_dump($page->server);

// require_once('php/index.php');
INCLUDE('html/index.html');
?>