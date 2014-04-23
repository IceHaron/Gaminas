<?

/**
*	
*	Обменный класс
*	
**/

class trade_index {
	
	public static function init() {
		return new self();
	}
	
	private function trade_index() {

		$url = 'http://api.steampowered.com/ISteamUserAuth/AuthenticateUser/v0001/';

		$options = array(
			  CURLOPT_URL => $url                         # return web page
			, CURLOPT_POST => 1
			, CURLOPT_POSTFIELDS => 'steamid=76561198131843447'
			, CURLOPT_RETURNTRANSFER	=> true          # return web page
			, CURLOPT_HEADER         => true          # return headers
			// , CURLOPT_HTTPHEADER			=> array('Authorization: vfQWAK5EdzcqLWLix5xBl0H56ZEUjr')		# set header
			, CURLINFO_HEADER_OUT		=> true						# show me headers i send
		);

		$ch = curl_init();                                   # инициализируем отдельное соединение (поток)
		curl_setopt_array( $ch, $options );               # применяем параметры запроса
		$page = curl_exec($ch);                          # выполняем запрос
		$info = curl_getinfo($ch);                         # информация об ответе
		$info['error_num'] = curl_errno($ch);          # код ошибки - http://curl.haxx.se/libcurl/c/libcurl-errors.html
		$info['error_txt'] = curl_error($ch);          # текст ошибки
		curl_close($ch);                                   # закрытие потока
		fb($page);
		fb($info);

		root::$_ALL['maincaption'] = 'Заголовок страницы';
		root::$_ALL['mainsupport'] = 'Содержание вспомогательного блока';
		root::$_ALL['maincontent'] = 'Содержание центрального блока';
		root::$_ALL['backtrace'][] = 'initialized trade/index';
	}

}