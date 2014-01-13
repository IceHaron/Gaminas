<?php
class root {
  var $path;
  
  public function root() {
    $address = $_SERVER['HTTP_HOST'];
    $this->path = $address;
  }
  
  public function url_parse() {
    var_dump(ltrim($_SERVER['REQUEST_URI'], '/'));
    
  }
}

$page = new root();
$page->url_parse();
$source = 'http://' . $page->path . '/source';
// var_dump($source);

require_once('/php/index.php');
require_once('/html/index.html');

?>