<?

/**
*	
*	�������� �����, ������� ����� ��� ������� � ������ ����� ��������� ������ ���.
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
		$GAMINAS['backtrace'][] = $par;
	}

}