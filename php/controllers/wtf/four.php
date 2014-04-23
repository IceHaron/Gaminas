<?

/**
*	
*	Тестовый класс, воротим здесь что захотим и всякие штуки тестируем именно тут.
*	Заодно это шаблон для создания других классов.
*	
**/

class wtf_four {	//<< Переименовать класс в <папка>_<контроллер>
	
	public static function init() {
		return new self();
	}
	
	private function wtf_four() {	// << переименовать конструктор под стать классу
		
		root::$_ALL['maincaption'] = 'Заголовок страницы';
		root::$_ALL['mainsupport'] = 'Содержание вспомогательного блока';
		root::$_ALL['maincontent'] = 'Содержание центрального блока';
		root::$_ALL['backtrace'][] = 'initialized wtf/four';
	}
	
	
///////////////////// Шаблон закончился, осталось только закрыть фигурную скобку
	
	
	public static function level($par) {
		// echo file_get_contents('http://steamcommunity.com/profiles/76561198131843447/inventory/json/753/1');
		// foreach (json_decode(file_get_contents(root::$_ALL['rootfolder'] . '/source/txt/systems.txt'), TRUE) as $sysid => $sysinfo) {
			// echo "$sysid => <br/>";
		// }
	}

}