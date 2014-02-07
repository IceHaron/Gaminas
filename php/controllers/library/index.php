<?

/**
*	
*	Библиотека игр, думаю, тут можно много няшек наворотить, типа подгрузки информации об играх и прочего...
*	@author: Пищулин Сергей
*	@version: 0.0.1
*	
*/
class library_index {

	public static function init() {
		return new self();
	}

/**
*	
*	Собираем инфу об играх на аккаунте со стима и выводим плиточку
*	
*/
	public function library_index() {
		global $GAMINAS;

// Library
		$lib_str = file_get_contents('http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=0BE85074D210A01F70B48205C44D1D56&steamid=' . $GAMINAS['uid'] . '&format=json&include_appinfo=1');
		$json = json_decode($lib_str);																							// Разбираем полученную строку на массив
		$gamearray = $json->response->games;
		$gamestable = '';
    foreach ($gamearray as $num => $gameinfo) {																	// И клеим ее обратно в строку :D только другую
			$gamestable .= '
				<div class="gamefield">
					<img class="gamelogo" src="http://media.steampowered.com/steamcommunity/public/images/apps/' . $gameinfo->appid . '/' . $gameinfo->img_logo_url . '.jpg">
          <span class="gamename">' . $gameinfo->name . '</span>
        </div>
			';
		}
// Определяем заголовок страницы и содержимое центрального блока.
		$GAMINAS['maincaption'] = 'Библиотека ваших игр';
		$GAMINAS['mainsupport'] = '<input id="namesearch" type="text" placeholder="Название"/><div id="filtercomment"></div>';
		$GAMINAS['maincontent'] = $gamestable;
	}
}

?>