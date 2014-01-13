<?php
$uid = $GAMINAS['uid'];
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

  // echo '<pre>';
  // echo $str;
  // echo '</pre>';
  $json = json_decode($str);
  $username = $json->profile->response->players[0]->personaname;
  $profurl = $json->profile->response->players[0]->profileurl;
  $avatar = $json->profile->response->players[0]->avatar;
  $gamearray = $json->lib->response->games;
} else {
  $nologin = 'You are not logged in!';
}

?>