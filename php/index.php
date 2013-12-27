<?php
if (isset($_POST['token'])) {
$s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
$user = json_decode($s, true);
//$user['network'] - соц. сеть, через которую авторизовался пользователь
//$user['identity'] - уникальная строка определяющая конкретного пользователя соц. сети
//$user['first_name'] - имя пользователя
//$user['last_name'] - фамилия пользователя
}
// var_dump($_COOKIE);
if (isset($_COOKIE['uid']) && $_COOKIE['uid'] !== '') $uid = $_COOKIE['uid'];
else $uid = 0;

if ($uid !== 0) {
  $str = '';

  // Profile
  $profile_str = file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=0BE85074D210A01F70B48205C44D1D56&steamids=' . $uid);
  // $profile_str = '{"response":{"players":[{"personaname":"Dummy","profileurl":"gaminas.ice","avatar":"http://placehold.it/32x32"}]}}';
  $str .= '{"profile":' . $profile_str . ',';

  // Library
  $lib_str = file_get_contents('http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=0BE85074D210A01F70B48205C44D1D56&steamid=' . $uid . '&format=json&include_appinfo=1');
  // $lib_str = '{"response":{"games":[]}}';
  $str .= '"lib":' . $lib_str . '';

  // Inventory
  $inv_str = file_get_contents('http://steamcommunity.com/profiles/' . $uid . '/inventory/json/753/1');
  // $inv_str = '{}';
  $str .= '}';
}

// echo '<pre>';
// echo $str;
// echo '</pre>';
$json = json_decode($str);
$username = $json->profile->response->players[0]->personaname;
$profurl = $json->profile->response->players[0]->profileurl;
$avatar = $json->profile->response->players[0]->avatar;
$gamearray = $json->lib->response->games;
?>