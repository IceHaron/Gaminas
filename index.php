<?php

/**
*	
*	Корневой контроллер, ядро всего сайта
*	@author: Пищулин Сергей
*	@version: 0.0.1
*	
*/
class root {

  var $path; 																																		// Адрес, куда мы обращаемся, берется из $_SERVER['HTTP_HOST']
  var $server; 																																	// Переменная $_SERVER, добавил сюда просто для тренировки.
	
/**
*
*	Конструктор
*
*/
  public function root() {
		header("HTTP/1.0 200 OK");																									// Вывешивается хэдер, иначе любая страница кроме / выдает 404 в хэдере
    $address = $_SERVER['HTTP_HOST'];
    $this->path = $address;																											// Отдаем в классовое свойство адрес...
    $this->server = $_SERVER;																										// ...и переменную $_SERVER
  }

/**
*	
*	Метод подгрузки классов в зависимости от заданных фильтров
*	На вход можно задать фильтр в виде строки, массива или ничего
* @param NULL : Фильтра нет, подгружаем все классы из папки
*	@param string : Фильтр задан строкой, выбираем класс в соответствии с ним (желательно сообщать точное название файла)
*	@param array : Фильтр задан массивом, выбираем файлы в соответствии с элементами этого массива
*	
*/
  function include_classes($filter = '') {
    global $GAMINAS;
		
    if(gettype($filter) == 'string' && $filter != '') { 												// Если даем строкой только один нужный модуль
      $files[0] = glob('php/classes/' . $filter . '.php');
    } else if (gettype($filter) == 'array') { 																	// Если в массиве перечисляем нужные модули
      
			foreach ($filter as $need) {
        $files[] = glob('php/classes/' . $need . '.php');
      }
    
		} else { 																																		// Если вообще не даем параметров, соответственно, нужны вообще все модули, пока что нужно в качестве костыля
      $files = glob('php/classes/*.php'); 
    }
		
		// Пробегаемся по составленному списку файлов и инклудим каждый.
    foreach($files as $file) {
      require_once($file);
    }
  }

/**
*	
*	Метод разбора адреса
*	
*/  
  public function url_parse() {
		global $auth;																																// Класс auth полюбому уже объявлен. лишний раз его объявлять не надо, просто обращаемся к глобалке
		$path = explode('/', trim($_SERVER['REQUEST_URI'], '/'));										// Отрезаем крайние слеши у адреса и разбиваем его в массив
		$action = $path[0];
		
		switch ($action) {																													// Бежим по известным методам, это, конечно, костыль и подлежит полной переработке.
			case 'logoff':																														// Выход из учетки
				$auth->logoff();
				break;
			case 'library':																														// Библиотека игр
				$library = new library();
				break;
		}
		
    var_dump($path);
    
  }
	
}

/**
*	
*	Здесь мы объявляем глобальные переменные, стартуем сессию, объявляем необходимые классы
*	
*/
$GAMINAS = array(); 																														// Глобальная переменная, куда будет запихиваться весь нужный хлам
$file = fopen('TODO.txt', 'r');																									// Разбираем TODO.txt
$c = 0;
while ($todostring = fgets($file)) {
	$todoarr = explode('--', $todostring);
	$GAMINAS['todo'][$c]['class'] = trim($todoarr[0]);
	$GAMINAS['todo'][$c]['text'] = trim($todoarr[1]);
	$GAMINAS['todo'][$c]['state'] = trim($todoarr[2]);
	$c++;
}
session_start();
$page = new root();
$page->include_classes();
$auth = new auth();
$GAMINAS['source'] = 'http://' . $page->path . '/source';												// Папка, откуда берется весь хлам
// var_dump($page->server);
$page->url_parse();																															// Разбираем адрес
require_once('php/controllers/index.php');																			// Подключаем контроллер, хорошо бы сделать подгрузку контроллера в зависимости от адреса или что-нибудь типа того
INCLUDE('html/index.html');																											// Ну и подгружаем макет, конечно же



/*
Нужно сделать фиговину, чтобы в текстовом файле писать всякие прикрутовины типа TODO или заметок о багах
и чтобы из этого файла скрипт подгружал инфу и выводил мне на каждой странице где-нибудь в футере.
*/


?>