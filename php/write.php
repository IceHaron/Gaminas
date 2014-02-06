<?
$rootfolder = isset($_SERVER['HOME']) ? $_SERVER['HOME'].'/gaminas' : $_SERVER['DOCUMENT_ROOT'];
// Тырим XML, превращаем его в JSON и выдираем нужные нам системы
$text = file_get_contents('https://api.eveonline.com/map/Jumps.xml.aspx');
// http://eve-marketdata.com/developers/solarsystems.php ID систем
// http://wiki.eve-id.net/Main_Page Очень полезный сайтец с формулами и всякими штуками для АПИ
$xml = new SimpleXMLElement('https://api.eveonline.com/map/Jumps.xml.aspx',0,TRUE); // 30002187 Amarr
$arr = json_decode(json_encode($xml), TRUE);
$stars = json_decode(file_get_contents($rootfolder . '/source/txt/systems.txt'), TRUE);
$regions = json_decode(file_get_contents($rootfolder . '/source/txt/regions.txt'), TRUE);

// foreach ($stars as $starID => $star) {
	// $res[ $regions[ $star['regionID'] ] ][ $star['name'] ][ $arr['cachedUntil'] ] = '0';
// }

foreach ($regions as $id => $name) {
	$written = json_decode(file_get_contents($rootfolder . '/source/txt/systemstats/' . $name . '.txt'), TRUE);
	$res[ $name ] = $written;
}
foreach($arr['result']['rowset']['row'] as $system) {
	$sysid = $system['@attributes']['solarSystemID'];
	$jumps = $system['@attributes']['shipJumps'];
	$res[ $regions[ $stars[ $sysid ]['regionID'] ] ][ $stars[ $sysid ]['name'] ][ $arr['cachedUntil'] ] = $jumps;
}

// echo '<pre>';
// var_dump($res);
// echo '</pre>';

// $arr['currentTime'] - current time ETC
// $arr['result']['dataTime'] - time of query ETC

$trigger = array();

foreach ($res as $region => $systemset) {
$write = json_encode($systemset);
$file = fopen($rootfolder . '/source/txt/systemstats/' . $region . '.txt', 'w+b');
$wrote = fwrite($file, $write);
if ($wrote === FALSE) $trigger[] = $region;
fclose($file);
}
if (count($trigger) == 0) { /*echo 'Writing stats for all regions successfull!';*/ }
else foreach ($trigger as $reg) {
	echo 'Writing stats for ' . $reg . ' failed!<br/>';
}
?>