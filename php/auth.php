<?
if (isset($_POST['token'])) {
$s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
$user = json_decode($s, true);
//$user['network'] - соц. сеть, через которую авторизовался пользователь
//$user['identity'] - уникальная строка определяющая конкретного пользователя соц. сети
//$user['first_name'] - имя пользователя
//$user['last_name'] - фамилия пользователя
var_dump($user);
}
var_dump('SESSION', $_SESSION);
var_dump('COOKIE', $_COOKIE);
if (isset($_SESSION['uid']) && $_SESSION['uid'] !== '' && $_SESSION['uid'] !== NULL) {
  $GAMINAS['uid'] = $_SESSION['uid'];
  echo '<br/>Logged in through session<br/>';
} else if (isset($_COOKIE['uid']) && $_COOKIE['uid'] !== '' && $_COOKIE['uid'] !== NULL) {
  $GAMINAS['uid'] = $_COOKIE['uid'];
  $_SESSION['uid'] = $GAMINAS['uid'];
  echo '<br/>Logged in through cookie<br/>';
} else $GAMINAS['uid'] = 0;
var_dump('GLOBAL', $GAMINAS);
