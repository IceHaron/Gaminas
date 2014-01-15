<?


/**
*	
*	 ласс, св€занный с аутентификацией пользователей,
*	все действи€, как-то касающиес€ авторизации, логина, регистрации и прочей чепухи нужно кидать сюда.
*	
*/
class auth {


/**
*	
*	 онструктор
*	
*/
	public function auth() {
		global $GAMINAS;
		
// Ћогинизаци€ от uLogin, оставлю пока здесь, мало ли пригодитс€
		
		if (isset($_POST['token'])) {
		$s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
		$user = json_decode($s, true);
		//$user['network'] - соц. сеть, через которую авторизовалс€ пользователь
		//$user['identity'] - уникальна€ строка определ€юща€ конкретного пользовател€ соц. сети
		//$user['first_name'] - им€ пользовател€
		//$user['last_name'] - фамили€ пользовател€
		// var_dump($user);
		}
		
//  онец логинизации через uLogin, теперь все по хардкору
		
		if (isset($_SESSION['uid']) && $_SESSION['uid'] !== '' && $_SESSION['uid'] !== NULL) {
			// ѕровер€ем, может, в сессии лежит айдишник? это значит, что мы уже авторизованы
			$GAMINAS['uid'] = $_SESSION['uid'];
			echo '<br/>Logged in through session<br/>';
		} else if (isset($_COOKIE['uid']) && $_COOKIE['uid'] !== '' && $_COOKIE['uid'] !== NULL) {
			// Ќе в сессии, так в печеньках
			$GAMINAS['uid'] = $_COOKIE['uid'];
			$_SESSION['uid'] = $GAMINAS['uid'];
			echo '<br/>Logged in through cookie<br/>';
		} else $GAMINAS['uid'] = 0;																									// Ќу если даже в печеньках нет нашего юида, то все-таки мы не логинились
		
		$uid = $GAMINAS['uid'] ? $GAMINAS['uid'] : 0;																// ƒелалось дл€ сокращени€ кода, после модификаций можно будет это убрать и лишний раз не переобъ€вл€ть переменную
		
		if ($uid !== 0) {																														// ≈сли мы авторизованы, надо подгрузить из стима наши данные
			$str = '';

			// Profile
			$profile_str = file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=0BE85074D210A01F70B48205C44D1D56&steamids=' . $uid);
// ¬от эта строка - и есть заглушка на случай если стим недоступен
			// $profile_str = '{"response":{"players":[{"personaname":"Dummy","profileurl":"gaminas.ice","avatar":"http://placehold.it/32x32"}]}}';
			$str .= '{"profile":' . $profile_str . '';

			// Inventory
			$inv_str = file_get_contents('http://steamcommunity.com/profiles/' . $uid . '/inventory/json/753/1');
// » это - тоже заглушка
			// $inv_str = '{}';
			$str .= '}';

			// echo '<pre>';
			// echo $str;
			// echo '</pre>';
			$json = json_decode($str);																								// —обираем из строки массив...
			// echo '<pre>';
			// var_dump($json);
			// echo '</pre>';
			$GAMINAS['username'] = $json->profile->response->players[0]->personaname;	// ...и парсим...
			$GAMINAS['profurl'] = $json->profile->response->players[0]->profileurl;		// ...парсим...
			$GAMINAS['avatar'] = $json->profile->response->players[0]->avatar;				// ...и еще раз парсим...
		} else {
			// Ќу и если все-таки мы не авторизованы, выводим сообщение, не знаю пока, зачем.
			$GAMINAS['nologin'] = 'You are not logged in!';
		}
		// echo '<pre>';
		// var_dump('GLOBAL', $GAMINAS);
		// echo '</pre>';
	}
	

/**
*	
*	ћетод логоффа
* ¬се просто: подчищаем сессию, убиваем куку, редиректим пока что на главную, надо будет сделать редирект обратно
*	
*/
	public function logoff() {
		unset ($_SESSION['uid']);
		setcookie('uid', '', time());
		echo '<meta http-equiv="refresh" content="0;URL=http://gaminas.ice" />';
	}
}

?>
