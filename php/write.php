<?
$rootfolder = isset($_SERVER['HOME']) ? $_SERVER['HOME'].'/gaminas' : $_SERVER['DOCUMENT_ROOT'];

$host = 'localhost';
$user = 'srv44030_gaminas';
$pw = '230105';
$base = 'srv44030_gaminas';

// Подключаемся к базе
$link = mysqli_connect($host, $user, $pw, $base);
mysqli_set_charset($link, "utf8");

$q = mysqli_query($link, 'SELECT SQL_CACHE * FROM activity_daily');
if (!$q) echo mysqli_error($link);
while ($row = mysqli_fetch_assoc($q)) {
	$table[ $row['region'] ] = $row['activity'];
}

// Тырим XML, превращаем его в JSON и выдираем нужные нам системы
$text = file_get_contents('https://api.eveonline.com/map/Jumps.xml.aspx');
// http://eve-marketdata.com/developers/solarsystems.php ID систем
// http://wiki.eve-id.net/Main_Page Очень полезный сайтец с формулами и всякими штуками для АПИ
$xml = new SimpleXMLElement('https://api.eveonline.com/map/Jumps.xml.aspx',0,TRUE); // 30002187 Amarr
$arr = json_decode(json_encode($xml), TRUE);
$stars = json_decode(file_get_contents($rootfolder . '/source/txt/systems.txt'), TRUE);
$regions = json_decode(file_get_contents($rootfolder . '/source/txt/regions.txt'), TRUE);

$cachetime = strtotime($arr['cachedUntil']);

foreach ($stars as $starID => $star) {
	$skeleton[ $regions[ $star['regionID'] ] ][ $starID ] = $star['name'];
}

foreach ($skeleton as $region => $systems) {
	$written = json_decode($table[ $region ], TRUE);
	if ($written) {
		foreach($systems as $sysid => $system) {
			$activity = isset($written[ $system ]) ? $written[ $system ] : array();
			foreach ($activity as $ts => $jumps) {
				$res[ $region ][ $system ][ $ts ] = $jumps;
				$res[ $region ][ $system ][ $cachetime ] = '0';
			}
		}
	} else {
		foreach($systems as $sysid => $system) {
			$res[ $region ][ $system ][ $cachetime ] = '0';
		}
	}
}
foreach($arr['result']['rowset']['row'] as $system) {
	$sysid = $system['@attributes']['solarSystemID'];
	$jumps = $system['@attributes']['shipJumps'];
	$res[ $regions[ $stars[ $sysid ]['regionID'] ] ][ $stars[ $sysid ]['name'] ][ $cachetime ] = $jumps;
}

// $arr['currentTime'] - current time ETC
// $arr['result']['dataTime'] - time of query ETC

$trigger = array();
$query_arr = array();

foreach ($regions as $id => $region) {
	$systemset = $res[ $region ];
	$write = json_encode($systemset);
	$file = fopen($rootfolder . '/source/txt/systemstats/' . $region . '.txt', 'w+b');
	$wrote = fwrite($file, $write);
	if ($wrote === FALSE) $trigger[] = $region;
	fclose($file);
	$query_arr[] .= "UPDATE `activity_daily` SET `activity` = '$write' WHERE `region` = '$region'; ";
}

// echo '<pre>';
// var_dump($query_arr);
// echo '</pre>';

// echo 'Now: ' . $arr['currentTime'] . '(' . strtotime($arr['currentTime']) . ')<br/>
			// Cache time: ' . $arr['cachedUntil'] . '(' . strtotime($arr['cachedUntil']) . ')';

foreach ($query_arr as $query_str) {
	mysqli_query($link, trim($query_str, ','));
	echo mysqli_error($link);
}
?>