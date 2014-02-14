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
		$GAMINAS['backtrace'][] = 'taken region set and star set from files';
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
		
		$maincaption = 'График активности в Амарре';
		$mainsupport = '';
		$maincontent = '';
		
		$regions = self::$regions;
		$regCheckBoxes = '';
		
		foreach ($regions as $regID => $regName) {
			if (preg_match('/\w-\w\d{5}/', $regName) !== 0) $newname = '&lt;WH&gt; ' . $regName;
			else $newname = $regName;
			$regCheckBoxes .= '<label><input type="checkbox" name="region" data-id="' . $regID . '">' . $newname . '</label>';
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
			// $sysChecksPart[ $thisSystemRegID ] .= '<label style="display: none;"><input type="checkbox" name="system" data-id="' . $sysID . '" data-regid="' . $sysInfo['regionID'] . '"><div style="width:28px; float: left; color:' . $color . '">' . number_format($ss, 1) . '</div>' . $sysName . '</label>';
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
		$time = urldecode($_GET['time']);
		$mode = urldecode($_GET['mode']);
		$region = urldecode($_GET['region']);
		$star = urldecode($_GET['star']);
		
		$str = "SELECT SQL_CACHE * FROM `activity_$time` WHERE `region` = '$region'";
 		$q = db::query($str);
		// var_dump($q);
		$activity = array();
		$res = '[["date","' . $star . '"],';
		
		foreach ($q as $s) {
			$activity[ $s['region'] ] = json_decode($s['activity']);
		}
		
/* 		foreach ($activity as $region => $systemset) {
			$sumregion = 0;
			
			foreach ($systemset as $system => $act) {
				$sumsystem = 0;

				foreach ($act as $ts => $jumps) {
					$sumsystem += (int)$jumps;
				}
				
				$sumregion += $sumsystem;
				
			}
			
			// $maincontent .= '<div class="sumregion" data-region="' . $region . '" data-jumps="' . $sumregion . '"><div class="regname">' . $region . '(' . $sumregion . ')</div></div>';
			
		} */
		
		foreach ($activity as $region => $systemset) {
			foreach ($systemset as $system => $act) {
				if ($system == $star) {
					foreach ($act as $ts => $jumps) {
						$res .= '["' . date('d-m-Y H:i', $ts) . '",' . $jumps . '],';
					}
				}
			}
		}
		
		$res = substr($res, 0, -1) . ']';
		
		echo $res;
	}

}