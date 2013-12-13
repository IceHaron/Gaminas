<!DOCTYPE html>
<head>
  <title>Gaminas</title>
  <link type="text/css" rel="stylesheet" href="css/main.css"></link>
</head>
<?php
$str = 'http://steamcommunity.com/profiles/76561197991665605/?xml=1';
$apistr = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=0BE85074D210A01F70B48205C44D1D56&steamids=76561197991665605';
$xml = simplexml_load_file($str);
$json = json_decode(file_get_contents($apistr));
$username = $json->response->players[0]->personaname;
$profurl = $json->response->players[0]->profileurl;
$avatar = $json->response->players[0]->avatar;
?>
<body>
  <div id="main">
    <div id="header">
      <div id="titleholder">
        <h1>Gaminas: let`s change!</h1>
      </div>
      <div id="righthead">
        <a target="blank" href="<?=$profurl?>"><div id="loginholder"><div id="login"><?=$username?></div><img id="head_ava" src="<?=$avatar?>"/></div></a>
      </div>
    </div>
    <div class="clear"></div>
  </div>
</body>

<!--

Steam API key for gaminas.ru: 0BE85074D210A01F70B48205C44D1D56 
Остановился на чтении вот этой странички про .htaccess: http://www.htaccess.net.ru/doc/Redirect/Redirect.php
Моя учетка в XML-формате: http://steamcommunity.com/id/ice_haron/?xml=1
Моя учетка в JSON-формате: http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=0BE85074D210A01F70B48205C44D1D56&steamids=76561197991665605
Мой каталог игрушек: http://steamcommunity.com/id/Ice_Haron/games/?tab=all&l=russian&xml=1
Интересный сайтец, вводим юзернейм или айдишник и получаем дохера инфы: http://heavenanvil.ru/
Ну и статейка на хабрахабре про Steam API: http://habrahabr.ru/post/172223/

-->
