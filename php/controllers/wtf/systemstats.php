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
			
			$maincontent .= '<div class="sumregion" data-region="' . $region . '" data-jumps="' . $sumregion . '"><div class="regname">' . $region . '(' . $sumregion . ')</div></div>';
			
		}
		
		$maincontent .= '</div>';
		
		$GAMINAS['maincaption'] = 'Статистика активности звездных систем';
		$GAMINAS['mainsupport'] = $maincontent;
		$GAMINAS['maincontent'] = '';
	}

}