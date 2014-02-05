<?
global $GAMINAS;
$text = file_get_contents('https://api.eveonline.com/map/Jumps.xml.aspx');
// $text = file_get_contents('http://eve-marketdata.com/developers/solarsystems.php'); ID систем
// http://wiki.eve-id.net/Main_Page Очень полезный сайтец с формулами и всякими штуками для АПИ
// var_dump($text);
$xml = new SimpleXMLElement('https://api.eveonline.com/map/Jumps.xml.aspx',0,TRUE); // 30002187 Amarr
$arr = json_decode(json_encode($xml), TRUE);
foreach($arr['result']['rowset']['row'] as $system) {
	if ($system['@attributes']['solarSystemID'] == 30002187) $amarr = $system['@attributes']['shipJumps'];
}
// echo '<pre>';
// var_dump($xml->result->rowset->row);
// $arr['currentTime'] - current time ETC
// $arr['result']['dataTime'] - time of query ETC
// echo '</pre>';

$written = json_decode(file_get_contents('source/txt/amarr.txt'), TRUE);
$written[ $arr['cachedUntil'] ] = $amarr;
$write = json_encode($written);
$file = fopen('source/txt/amarr.txt', 'w+b');
fwrite($file, $write);
fclose($file);
?>