<?

// http://eve-marketdata.com/developers/solarsystems.php ID систем
// http://wiki.eve-id.net/Main_Page Очень полезный сайтец с формулами и всякими штуками для АПИ

$rootfolder = isset($_SERVER['HOME']) ? $_SERVER['HOME'].'/gaminas' : $_SERVER['DOCUMENT_ROOT'];

$host = 'localhost';
$user = 'srv44030_gaminas';
$pw = '230105';
$base = 'srv44030_gaminas';

// Подключаемся к базе и ставим чарсет
$link = mysqli_connect($host, $user, $pw, $base);
mysqli_set_charset($link, "utf8");

// Селектим все-все-все

$q = mysqli_query($link, 'SELECT SQL_CACHE * FROM activity_daily');
if (!$q) echo mysqli_error($link);

// И собираем из этого удобоваримый массив
while ($row = mysqli_fetch_assoc($q)) {
	$table[ $row['region'] ] = $row['activity'];
}

// Тырим XML и превращаем его в JSON
$xml = new SimpleXMLElement('https://api.eveonline.com/map/Jumps.xml.aspx',0,TRUE);
$arr = json_decode(json_encode($xml), TRUE);
// Забираем инфу о системах и регионах. записанную в файлы
$stars = json_decode(file_get_contents($rootfolder . '/source/txt/systems.txt'), TRUE);
$regions = json_decode(file_get_contents($rootfolder . '/source/txt/regions.txt'), TRUE);

$cachetime = strtotime($arr['cachedUntil']);																		// Превращаем строку в таймстамп

foreach ($stars as $starID => $star) {
	$skeleton[ $regions[ $star['regionID'] ] ][ $starID ] = $star['name'];				// Из двух массивов собираем один
}

foreach ($skeleton as $region => $systems) {
	$written = json_decode($table[ $region ], TRUE);															// Смотрим, какие данные у нас уже есть
	
	if ($written) {
	
		foreach($systems as $sysid => $system) {
			$activity = isset($written[ $system ]) ? $written[ $system ] : array();		// Активность системы забираем в отдельную переменную
			
			foreach ($activity as $ts => $jumps) {
				$res[ $region ][ $system ][ $ts ] = $jumps;															// В итоговый массив заталкиваем информацию о прошлых часах
			}
			
			$res[ $region ][ $system ][ $cachetime ] = '0';														// Ставим в 0 нынешний час, в XML отсутствуют системы с 0 активностью, так что эта строка необходима для полноты картины, в будущем наверное тоже надо убрать нулевые часы, но только если совсем беда с производительностью будет
		}
		
	} else {
	
		foreach($systems as $sysid => $system) {
			$res[ $region ][ $system ][ $cachetime ] = '0';														// Ну это происходит если у нас вдруг отсутствуют изначальные данные, это уже неактуально, но  лишняя заглушка не помешает
		}
		
	}
	
}

// Вот и пришло время перебрать все, что нам пришло в XML
foreach($arr['result']['rowset']['row'] as $system) {
	$sysid = $system['@attributes']['solarSystemID'];
	$jumps = $system['@attributes']['shipJumps'];
	$res[ $regions[ $stars[ $sysid ]['regionID'] ] ][ $stars[ $sysid ]['name'] ][ $cachetime ] = $jumps;
}

// $arr['currentTime'] - current time ETC
// $arr['result']['dataTime'] - time of query ETC

// Инициализируем управляющие переменные
// $trigger = array();
$query_arr = array();

foreach ($regions as $id => $region) {
	$systemset = $res[ $region ];
	$write = json_encode($systemset);
	// Писать в файлы уже немодно, оставлю с пометкой Deprecated в комментах
	// $file = fopen($rootfolder . '/source/txt/systemstats/' . $region . '.txt', 'w+b');
	// $wrote = fwrite($file, $write);
	// if ($wrote === FALSE) $trigger[] = $region;
	// fclose($file);
	$query_arr[] .= "UPDATE `activity_daily` SET `activity` = '$write' WHERE `region` = '$region'; ";
}

// echo '<pre>';
// var_dump($query_arr);
// echo '</pre>';

// echo 'Now: ' . $arr['currentTime'] . '(' . strtotime($arr['currentTime']) . ')<br/>
			// Cache time: ' . $arr['cachedUntil'] . '(' . strtotime($arr['cachedUntil']) . ')';

// Ну и наконец записываем информацию в базу
foreach ($query_arr as $query_str) {
	mysqli_query($link, trim($query_str, ','));
	echo mysqli_error($link);
}
?>