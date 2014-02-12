<?

/**
*	
*	Статистика систем
*	
**/

class wtf_systemstats {
	
	public static function init() {
		return new self();
	}
	
	private function wtf_systemstats() {
		global $GAMINAS;
		$GAMINAS['backtrace'][] = 'initialized wtf/systemstats';
	}
	
	public static function show($what = '') {
		global $GAMINAS;
		
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
		
		$regions = json_decode(file_get_contents(root::$rootfolder . '/source/txt/regions.txt'), TRUE);
		$regcheckboxes = '';
		foreach ($regions as $regid => $regname) {
			$regcheckboxes .= '<label><input type="checkbox" name="region">' . $regname . '</label>';
		}
		$stars = json_decode(file_get_contents(root::$rootfolder . '/source/txt/systems.txt'), TRUE);
		$syscheckboxes = '';
		foreach ($stars as $sysid => $sysinfo) {
			$ss = round($sysinfo['security'], 1);
			if ($ss === 1.0) $color = 'skyblue';
			if ($ss <= 0.9 && $ss > 0.4) $color = 'green';
			if ($ss <= 0.4 && $ss > 0.0) $color = 'orange';
			if ($ss <= 0.0) $color = 'red';
			$syscheckboxes .= '<label><input type="checkbox" name="system"><div style="width:25px; float: left; color:' . $color . '">' . $ss . '</div>' . $sysinfo['name'] . '</label>';
		}
		
		$GAMINAS['maincaption'] = $maincaption;
		$GAMINAS['mainsupport'] = $mainsupport;
		$GAMINAS['maincontent'] = $maincontent;
		$GAMINAS['regcheckboxes'] = $regcheckboxes;
		$GAMINAS['syscheckboxes'] = $syscheckboxes;
	}

}