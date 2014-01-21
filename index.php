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
		$files = array();
		$GAMINAS['backtrace'][] = 'include_classes from index.php here';
    if(gettype($filter) == 'string' && $filter != '') { 												// Если даем строкой только один нужный модуль
			$GAMINAS['backtrace'][] = 'got string filter: ' . $filter;
      $files = glob('php/classes/' . $filter . '.php');
    } else if (gettype($filter) == 'array') { 																	// Если в массиве перечисляем нужные модули
      $backtrace = 'got array filter: ';

			foreach ($filter as $need) {
				$backtrace .= $need . ', ';
        $files = array_merge($files, glob('php/classes/' . $need . '.php'));
      }
			
		$GAMINAS['backtrace'][] = $backtrace;
   
		} else { 																																		// Если вообще не даем параметров, соответственно, нужны вообще все модули, пока что нужно в качестве костыля
			$GAMINAS['backtrace'][] = 'got no filter';
      $files = glob('php/classes/*.php'); 
    }
		// Пробегаемся по составленному списку файлов и инклудим каждый.
		$backtrace = 'found files: ';
    foreach($files as $file) {
			$backtrace .= $file . ', ';
      require_once($file);
    }
		$GAMINAS['backtrace'][] = $backtrace;
  }

/**
*	
*	Метод разбора адреса
*	
*/  
  public function url_parse() {
    global $GAMINAS;
		global $auth;																																// Класс auth полюбому уже объявлен. лишний раз его объявлять не надо, просто обращаемся к глобалке
		$path = explode('/', trim($_SERVER['REQUEST_URI'], '/'));										// Отрезаем крайние слеши у адреса и разбиваем его в массив
		$GAMINAS['controller'] = @$path[0] ? $path[0] : '';													// Первый уровень всегда определяет контроллер
		$GAMINAS['action'] = @$path[1] ? $path[1] : '';															// Второй уровень всегда определяет метод
		
    var_dump($path);
    
  }
	
}

/**
*	
*	Здесь мы объявляем глобальные переменные, стартуем сессию, объявляем необходимые классы
*	
*/
$GAMINAS = array(		 																														// Глобальная переменная, куда будет запихиваться весь нужный хлам
		'maincaption' => 'Default Caption'																					// Стандартный заголовок страницы
	, 'maincontent' => 'NULL'																											// Стандартное содержимое центрального блока
	, 'mainsupport' => 'NULL'																											// Вспомогательный блок
	, 'backtrace' => array()																											// Стандартный бэктрейс
	);
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
include_once('php/firephp/fb.php');																							// Подключаем FirePHP
$page = new root();
$page->url_parse();																															// Разбираем адрес
$page->include_classes();																												// Подключаем все классы
db::init();
auth::init();
$GAMINAS['source'] = 'http://' . $page->path . '/source';												// Папка, откуда берется весь хлам
require_once('php/controllers/index.php');																			// Подключаем контроллер, хорошо бы сделать подгрузку контроллера в зависимости от адреса или что-нибудь типа того
INCLUDE('html/index.html');																											// Ну и подгружаем макет, конечно же


?>