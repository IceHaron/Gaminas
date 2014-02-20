<?

/**
*	
*	Статистика систем
*	
**/

class wtf_systemstats {

	public static $regions;
	public static $stars;
	
	public static function init() {
		return new self();
	}
	
/**
*	
*	Конструктор: здесь мы собираем из файлов инфу о системах и регионах
*	
**/
	
	private function wtf_systemstats() {
		global $GAMINAS;
		self::$regions = json_decode(file_get_contents(root::$rootfolder . '/source/txt/regions.txt'), TRUE);
		self::$stars = json_decode(file_get_contents(root::$rootfolder . '/source/txt/systems.txt'), TRUE);
		$GAMINAS['backtrace'][] = 'Took region set and star set from files';
	}
	
/**
*	
*	Метод, отбирающий системы для определенных регионов
*	@param	regions - строка, где записаны ID нужных регионов через запятую (если отсутствует, берется $_GET)
*	@return JSON-строка с информацией о системах
*	
**/
	
	public static function getSystems($regions = '') {
		global $GAMINAS;
		self::init();
		$GAMINAS['notemplate'] = TRUE;
		$r = $regions ? $regions : urldecode($_GET['regions']);
		$regionset = explode(',', $r);
		foreach ($regionset as $regID) {
			foreach (self::$stars as $starid => $starinfo) {
				if ($starinfo['regionID'] == $regID) {
					$res[ $starid ] = $starinfo;
					$res[ $starid ]['regionName'] = self::$regions[ $regID ];
				}
			}
		}
		echo json_encode($res);
	}
	
/**
*	
*	Метод, отображающий фильтры и график
*	@param	what - фильтр по регионам (не уверен, что пригодится, хотя посмотрим, лишним не будет)
*	@return	строки с HTML-кодом чекбоксов регионов и систем
*	
**/
	
	public static function show($what = '') {
		global $GAMINAS;
		self::init();
		
		$time = isset($_GET['time']) ? urldecode($_GET['time']) : 'daily';
		$mode = isset($_GET['mode']) ? urldecode($_GET['mode']) : 'system';
		$regions = $_GET['region'] ? explode(',', urldecode($_GET['region'])) : 'default';
		$stars = $_GET['star'] ? self::parseStarList(urldecode($_GET['star'])) : 'default';
		
		$maincaption = 'График активности в системах';
		$mainsupport = '<label>Ссылка на график<input type="text" name="link" id="graphLink" value="' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '"></label>';
		$maincontent = '<div id="strForChart">' . self::getStringForGraph($time, $mode, $regions, $stars) . '</div>';
		
		$regions = self::$regions;
		$regCheckBoxes = '';
		
		foreach ($regions as $regID => $regName) {
			if (preg_match('/\w-\w\d{5}/', $regName) !== 0) $newname = '&lt;WH&gt; ' . $regName;
			else $newname = $regName;
			$regCheckBoxes .= '<label><input type="checkbox" name="region" data-name="' . $regName . '" data-id="' . $regID . '">' . $newname . '</label>';
			$sysChecksPart[ $regID ] = '<label style="display: none;"><input type="checkbox" name="region" data-regid="' . $regID . '">' . $newname . '</label>';
		}
		
		$stars = self::$stars;
		$sysCheckBoxes = '';
		
		foreach ($stars as $sysID => $sysInfo) {
			$ss = round($sysInfo['security'], 1);
			$thisSystemRegID = $sysInfo['regionID'];
			if ($ss === 1.0) $color = 'skyblue';
			if ($ss <= 0.9 && $ss > 0.6) $color = 'green';
			if ($ss <= 0.6 && $ss > 0.4) $color = 'yellow';
			if ($ss <= 0.4 && $ss > 0.0) $color = 'orange';
			if ($ss <= 0.0) $color = 'red';
			if (preg_match('/J\d{6}/', $sysInfo['name']) !== 0) $sysName = '&lt;WH&gt; ' . $sysInfo['name'];
			else $sysName = $sysInfo['name'];
			// $sysChecksPart[ $thisSystemRegID ] .= '<label style="display: none;"><input type="checkbox" name="system" data-name="' . $sysName . '" data-id="' . $sysID . '" data-regid="' . $sysInfo['regionID'] . '"><div style="width:28px; float: left; color:' . $color . '">' . number_format($ss, 1) . '</div>' . $sysName . '</label>';
		}
		
		foreach ($sysChecksPart as $part) $sysCheckBoxes .= $part;
		
		// $sysCheckBoxes .= 'Выберите один или несколько регионов';
		
		$GAMINAS['maincaption'] = $maincaption;
		$GAMINAS['mainsupport'] = $mainsupport;
		$GAMINAS['maincontent'] = $maincontent;
		$GAMINAS['regcheckboxes'] = $regCheckBoxes;
		$GAMINAS['syscheckboxes'] = $sysCheckBoxes;
	}
	
	public static function drawGraph() {
		global $GAMINAS;
		self::init();
		$GAMINAS['notemplate'] = TRUE;
		$time = isset($_GET['time']) ? urldecode($_GET['time']) : 'daily';
		$mode = isset($_GET['mode']) ? urldecode($_GET['mode']) : 'system';
		$regions = $_GET['region'] ? explode(',', urldecode($_GET['region'])) : 'default';
		$stars = $_GET['star'] ? self::parseStarList(urldecode($_GET['star'])) : 'default';

		$res = self::getStringForGraph($time, $mode, $regions, $stars);
		
		echo $res;
	}
	
	private static function parseStarList($string) {
		$array['starNames'] = explode(',', preg_replace('/\s*\_\-*\d+/', '', $string));
		$array['secures'] = explode(',', preg_replace('/(\D|\A)(\-?\d)/', '$1$2.', preg_replace('/[a-zA-Z0-9\s\-]+_/', '', $string)));
		
		return $array;
	}
	
	public static function getStringForGraph($time = 'daily', $mode = 'system', $regions = 'default', $stars = 'default') {
		global $GAMINAS;
		self::init();
		if ($regions === 'default')
			$regions = array(
					'Domain'
				, 'The Forge'
				, 'Sinq Laison'
				, 'Heimatar'
			);
		if ($stars === 'default')
			$stars = array(
				'starNames' => array(
						'Amarr'
					, 'Jita'
					, 'Dodixie'
					, 'Rens'
				),
				'secures' => array(
						'1.'
					, '0.9'
					, '0.9'
					, '0.9'
				)
			);
		$regionQuery = implode("','", $regions);
		
		$str = "SELECT SQL_CACHE * FROM `activity_$time` WHERE `region` IN ('$regionQuery')";
 		$q = db::query($str);
		// var_dump($q);
		$activity = array();
		
		foreach ($q as $s) {
			$activity[ $s['region'] ] = json_decode($s['activity']);
		}
		foreach ($activity as $region => $systemset) {
			foreach ($systemset as $system => $act) {
				if (array_search($system, $stars['starNames']) !== FALSE) {
					if ($time == 'daily') {
						foreach ($act as $ts => $jumps) {
							$arr[ $ts ][ $system ] = $jumps;
							$resHead[ $system ] = $system . '(' . number_format($stars['secures'][ array_search($system, $stars['starNames']) ], 1, '.', '') . ')';
						}
					} else if ($time == 'monthly') {
						foreach ($act as $ts => $jumps) {
							$arr[ $ts ][ $system ] = $jumps;
							$resHead[ $system ] = $system . '(' . number_format($stars['secures'][ array_search($system, $stars['starNames']) ], 1, '.', '') . ')';
						}
					}
				}
			}
		}
		
		$res = '{"head":["' . implode('","', $resHead) . '"],"content":[';
		ksort($arr);
		
		foreach ($arr as $date => $systems) {
			$act = implode(',', $systems);
			$res .= '[new Date(' . $date . '000),' . $act . '],';
		}
		
		$res = substr($res, 0, -1) . ']}';
		
		return $res;
	}

}