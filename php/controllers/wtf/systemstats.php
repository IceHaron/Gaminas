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
	
	private function wtf_systemstats() {
		global $GAMINAS;
		self::$regions = json_decode(file_get_contents(root::$rootfolder . '/source/txt/regions.txt'), TRUE);
		self::$stars = json_decode(file_get_contents(root::$rootfolder . '/source/txt/systems.txt'), TRUE);
		$GAMINAS['backtrace'][] = 'taken region set and star set from files';
	}
	
	public static function getSystems($regions = '') {
		global $GAMINAS;
		self::init();
		$GAMINAS['notemplate'] = TRUE;
		$r = $regions ? $regions : urldecode($_GET['regions']);
		$regionset = explode(',', $r);
		foreach ($regionset as $regid) {
			foreach (self::$stars as $starid => $starinfo) {
				if ($starinfo['regionID'] == $regid) {
					$res[ $starid ] = $starinfo;
					$res[ $starid ]['regionName'] = self::$regions[ $regid ];
				}
			}
		}
		echo json_encode($res);
	}
	
	public static function show($what = '') {
		global $GAMINAS;
		self::init();
		
		if (!isset($_GET['mode']) && !isset($_GET['region']) && !isset($_GET['system'])) {
			$maincaption = 'Статистика активности звездных систем';
			$maincontent = '';
			$mainsupport = '';
		} else {
			$q = db::query('SELECT SQL_CACHE * FROM `activity_daily`');
			// var_dump($q);
			$activity = array();
			$maincontent = '';
			
			foreach ($q as $s) {
				$activity[ $s['region'] ] = json_decode($s['activity']);
			}
			
			$maincontent .= '<div class="graph">';
			
			foreach ($activity as $region => $systemset) {
				$sumregion = 0;
				
				foreach ($systemset as $system => $act) {
					$sumsystem = 0;

					foreach ($act as $ts => $jumps) {
						$sumsystem += (int)$jumps;
					}
					
					$sumregion += $sumsystem;
					
				}
				
				// $maincontent .= '<div class="sumregion" data-region="' . $region . '" data-jumps="' . $sumregion . '"><div class="regname">' . $region . '(' . $sumregion . ')</div></div>';
				
			}
			
			foreach ($activity as $region => $systemset) {
				foreach ($systemset as $system => $act) {
					if ($system == 'Amarr') {
						foreach ($act as $ts => $jumps) {
							// Не имеет смысла, просто для красивости сделал, надо удалить и переделать
							$maincontent .= '<div class="sumregion" data-region="' . $system . '" data-jumps="' . $jumps . '"><div class="regname">' . date('d-m-Y H:i', $ts) . ' (' . $jumps . ')</div></div>';
						}
					}
				}
			}
			
			$maincontent .= '</div>';
		}
		
		$regions = self::$regions;
		$regcheckboxes = '';
		foreach ($regions as $regid => $regname) {
			if (preg_match('/\w-\w\d{5}/', $regname) !== 0) $newname = '&lt;WH&gt; ' . $regname;
			else $newname = $regname;
			$regcheckboxes .= '<label><input type="checkbox" name="region" data-id="' . $regid . '">' . $newname . '</label>';
		}
		$stars = self::$stars;
		$syscheckboxes = '';
		foreach ($stars as $sysid => $sysinfo) {
			$ss = round($sysinfo['security'], 1);
			if ($ss === 1.0) $color = 'skyblue';
			if ($ss <= 0.9 && $ss > 0.6) $color = 'green';
			if ($ss <= 0.6 && $ss > 0.4) $color = 'yellow';
			if ($ss <= 0.4 && $ss > 0.0) $color = 'orange';
			if ($ss <= 0.0) $color = 'red';
			if (preg_match('/J\d{6}/', $sysinfo['name']) !== 0) $sysname = '&lt;WH&gt; ' . $sysinfo['name'];
			else $sysname = $sysinfo['name'];
			// $syscheckboxes .= '<label><input type="checkbox" name="system" data-id="' . $sysid . '"><div style="width:28px; float: left; color:' . $color . '">' . number_format($ss, 1) . '</div>' . $sysname . '</label>';
		}
		
		$syscheckboxes .= 'Выберите один или несколько регионов';
		
		$GAMINAS['maincaption'] = $maincaption;
		$GAMINAS['mainsupport'] = $mainsupport;
		$GAMINAS['maincontent'] = $maincontent;
		$GAMINAS['regcheckboxes'] = $regcheckboxes;
		$GAMINAS['syscheckboxes'] = $syscheckboxes;
	}

}