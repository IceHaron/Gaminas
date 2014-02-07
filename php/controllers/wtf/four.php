<?

/**
*	
*	Тестовый класс, воротим здесь что захотим и всякие штуки тестируем именно тут.
*	
**/

class wtf_four {
	
	public static function init() {
		global $GAMINAS;
		return new self();
	}
	
	private function wtf_four() {
		$GAMINAS['backtrace'][] = 'initialized wtf/four';
	}
	
	public static function level($par) {
		global $GAMINAS;
		foreach ($par as $thispar) {
		var_dump($thispar);
			$GAMINAS['backtrace'][] = $thispar;
		}
	}

}