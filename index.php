<?php

class root {
  var $path;
  
  function root() {
    $address = $_SERVER['HTTP_HOST'];
    $this->path = $address;
  }
  
}

$page = new root();
$source = 'http://' . $page->path . '/source';
var_dump($source);

require_once('/php/index.php');
INCLUDE('/html/index.html');

?>