<?php

/**
*	
*	Корневой контроллер, ядро всего сайта
*	@author: Пищулин Сергей
*	@version: 0.0.1
*	
*/
class root {

  public static $path; 																																		// Адрес, куда мы обращаемся, берется из $_SERVER['HTTP_HOST']
  public static $server; 																																	// Переменная $_SERVER, добавил сюда просто для тренировки.

	public static function init() {
		return new self();
	}
	
/**
*
*	Конструктор
*
*/
  private function root() {
		header("HTTP/1.0 200 OK");																									// Вывешивается хэдер, иначе любая страница кроме / выдает 404 в хэдере
    $address = $_SERVER['HTTP_HOST'];
    self::$path = $address;																											// Отдаем в классовое свойство адрес...
    self::$server = $_SERVER;																										// ...и переменную $_SERVER
		self::url_parse();																													// Разбираем адрес
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
  public static function include_classes($filter = '') {
    global $GAMINAS;
		$files = array();
		
/* 		// Проверка входных данных
		if (gettype($filter) == 'string' && $filter != '') {
			$ver[$filter] = preg_match('/[\W]/', $filter);														// При такой проверке допускаются буквы, цифры и нижний слэш, например, ololo_2trololo
		} else if (gettype($filter) == 'array') {
		
		} else {
		
		}
		var_dump($ver);
		die; */
		
		// Проверка пройдена, начинаем разбор
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
		if ($files) {
			// Пробегаемся по составленному списку файлов и инклудим каждый.
			$backtrace = 'found files: ';
			foreach($files as $file) {
				$backtrace .= $file . ', ';
				require_once($file);
			}
		} else {
			$backtrace = 'We found no classes with that filter';
		}
		$GAMINAS['backtrace'][] = $backtrace;
  }

/**
*	
*	Метод разбора адреса
*	
*/  
  private static function url_parse() {
    global $GAMINAS;
		global $auth;																																// Класс auth полюбому уже объявлен. лишний раз его объявлять не надо, просто обращаемся к глобалке
		$path = explode('/', trim($_SERVER['REQUEST_URI'], '/'));										// Отрезаем крайние слеши у адреса и разбиваем его в массив
		$GAMINAS['folder'] = $path[0];																							// Первый уровень всегда определяет группу контроллеров
		$a = $path;
		
		if (strpos(array_pop($a), '.') === FALSE) {
		
			if ($path[0] != '') {
				$count = count($path);
				
				if ($count == 1) {
					$GAMINAS['controller'] = 'index';
					$GAMINAS['action'] = 'init';
					$GAMINAS['params'] = array();

				} else if ($count == 2) {
					$GAMINAS['controller'] = 'index';
					$GAMINAS['action'] = $path[1];
					$GAMINAS['params'] = array();
					
				} else if ($count == 3) {
					$GAMINAS['controller'] = $path[1];
					$GAMINAS['action'] = $path[2];
					$GAMINAS['params'] = array();
					
				} else if ($count >= 4) {
					$GAMINAS['folder'] = $path[0];
					$GAMINAS['controller'] = $path[1];
					$GAMINAS['action'] = $path[2];
					$GAMINAS['params'] = array_slice($path, 3);
				
				} else {
					header("HTTP/1.0 404 Not Found");
					echo file_get_contents('error/404.php');
					die;
				}
			}
				
			fb($path, 'PATH');
			$GAMINAS['isfile'] = FALSE;
			self::include_classes(array('auth', 'db'));
		
		} else {
			$GAMINAS['isfile'] = TRUE;
		}
		// self::include_classes('ololo_2_trololo');																										// Подключаем все классы
  }
	
}

/**
*	
*	Самое начало работы сайта
*	
*/

// Объявляем глобалку

$GAMINAS = array(		 																														// Глобальная переменная, куда будет запихиваться весь нужный хлам
		'maincaption' => 'Default Caption'																					// Стандартный заголовок страницы
	, 'maincontent' => 'NULL'																											// Стандартное содержимое центрального блока
	, 'mainsupport' => 'NULL'																											// Вспомогательный блок
	, 'backtrace' => array()																											// Стандартный бэктрейс
	);
	
// Делаем блок TODO, надо бы это запихнуть в какой-нибудь отдельный файл

$file = fopen('source/txt/TODO.txt', 'r');																									// Разбираем TODO.txt
$c = 0;
while ($todostring = fgets($file)) {
	$todoarr = explode('--', $todostring);
	$GAMINAS['todo'][$c]['class'] = trim($todoarr[0]);
	$GAMINAS['todo'][$c]['text'] = trim($todoarr[1]);
	$GAMINAS['todo'][$c]['state'] = trim($todoarr[2]);
	$c++;
}

session_start();																																// Стартуем сессию
INCLUDE_ONCE('php/firephp/fb.php');																							// Подключаем FirePHP
// fb($_SERVER);																																		// Сразу пишем в консоль $_SERVER
root::init();																																		//

if (!$GAMINAS['isfile']) {
	db::init();																																			// Инициализируем все коренные классы
	auth::init();																																		//

	$GAMINAS['source'] = 'http://' . root::$path . '/source';												// Папка, откуда берется весь хлам

	require_once('php/controllers/index.php');																			// Подключаем контроллер, хорошо бы сделать подгрузку контроллера в зависимости от адреса или что-нибудь типа того

	fb($GAMINAS);																																		// Напоследок смотрим дефолтную конфигурацию

	INCLUDE_ONCE('html/index.html');																								// Ну и подгружаем макет, конечно же

	$controller = $GAMINAS['folder'] . '_' . $GAMINAS['controller'];
	INCLUDE_ONCE('php/controllers/' . $GAMINAS['folder'] . '/' . $GAMINAS['controller'] . '.php');
	INCLUDE_ONCE('html/index.html');
	INCLUDE_ONCE('html/views/' . $GAMINAS['folder'] . '/' . $GAMINAS['controller'] . '.html');
	$controller::$GAMINAS['action']('Amarr', 1034, '2014-01-23');
} else {
	INCLUDE(trim($_SERVER['REQUEST_URI'], '/'));
}
?>