<?


/**
*	
*	�����, ��������� � ��������������� �������������,
*	��� ��������, ���-�� ���������� �����������, ������, ����������� � ������ ������ ����� ������ ����.
*	
*/
class auth {


/**
*	
*	�����������
*	
*/
	public function auth() {
		global $GAMINAS;
		
// ����������� �� uLogin, ������� ���� �����, ���� �� ����������
		
		if (isset($_POST['token'])) {
		$s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
		$user = json_decode($s, true);
		//$user['network'] - ���. ����, ����� ������� ������������� ������������
		//$user['identity'] - ���������� ������ ������������ ����������� ������������ ���. ����
		//$user['first_name'] - ��� ������������
		//$user['last_name'] - ������� ������������
		// var_dump($user);
		}
		
// ����� ����������� ����� uLogin, ������ ��� �� ��������
		
		if (isset($_SESSION['uid']) && $_SESSION['uid'] !== '' && $_SESSION['uid'] !== NULL) {
			// ���������, �����, � ������ ����� ��������? ��� ������, ��� �� ��� ������������
			$GAMINAS['uid'] = $_SESSION['uid'];
			echo '<br/>Logged in through session<br/>';
		} else if (isset($_COOKIE['uid']) && $_COOKIE['uid'] !== '' && $_COOKIE['uid'] !== NULL) {
			// �� � ������, ��� � ���������
			$GAMINAS['uid'] = $_COOKIE['uid'];
			$_SESSION['uid'] = $GAMINAS['uid'];
			echo '<br/>Logged in through cookie<br/>';
		} else $GAMINAS['uid'] = 0;																									// �� ���� ���� � ��������� ��� ������ ����, �� ���-���� �� �� ����������
		
		$uid = $GAMINAS['uid'] ? $GAMINAS['uid'] : 0;																// �������� ��� ���������� ����, ����� ����������� ����� ����� ��� ������ � ������ ��� �� ������������� ����������
		
		if ($uid !== 0) {																														// ���� �� ������������, ���� ���������� �� ����� ���� ������
			$str = '';

			// Profile
			$profile_str = file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=0BE85074D210A01F70B48205C44D1D56&steamids=' . $uid);
// ��� ��� ������ - � ���� �������� �� ������ ���� ���� ����������
			// $profile_str = '{"response":{"players":[{"personaname":"Dummy","profileurl":"gaminas.ice","avatar":"http://placehold.it/32x32"}]}}';
			$str .= '{"profile":' . $profile_str . '';

			// Inventory
			$inv_str = file_get_contents('http://steamcommunity.com/profiles/' . $uid . '/inventory/json/753/1');
// � ��� - ���� ��������
			// $inv_str = '{}';
			$str .= '}';

			// echo '<pre>';
			// echo $str;
			// echo '</pre>';
			$json = json_decode($str);																								// �������� �� ������ ������...
			// echo '<pre>';
			// var_dump($json);
			// echo '</pre>';
			$GAMINAS['username'] = $json->profile->response->players[0]->personaname;	// ...� ������...
			$GAMINAS['profurl'] = $json->profile->response->players[0]->profileurl;		// ...������...
			$GAMINAS['avatar'] = $json->profile->response->players[0]->avatar;				// ...� ��� ��� ������...
		} else {
			// �� � ���� ���-���� �� �� ������������, ������� ���������, �� ���� ����, �����.
			$GAMINAS['nologin'] = 'You are not logged in!';
		}
		// echo '<pre>';
		// var_dump('GLOBAL', $GAMINAS);
		// echo '</pre>';
	}
	

/**
*	
*	����� �������
* ��� ������: ��������� ������, ������� ����, ���������� ���� ��� �� �������, ���� ����� ������� �������� �������
*	
*/
	public function logoff() {
		unset ($_SESSION['uid']);
		setcookie('uid', '', time());
		echo '<meta http-equiv="refresh" content="0;URL=http://gaminas.ice" />';
	}
}

?>
