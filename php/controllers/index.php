<?php

/**
*	
*	Главный контроллер, надо будет перепилить в нормальном виде, но пока знаний маловато
*	@author: Пищулин Сергей
*	@version: 0.0.1
*	
*/
if ($GAMINAS['maincontent'] === 'NULL') {																				// Переменная может быть неопределена потому что мы обращаемся в корень, не подгружается почему-то класс или контроллер
	$GAMINAS['maincaption'] = 'Главная';																					// Ну и соответственно, мы определяем ее и ее попутчиков.
	$GAMINAS['mainsupport'] = 'Вспомогательный блок';
	$GAMINAS['maincontent'] = 'Центральный блок';
}

if (root::$server['REQUEST_URI'] == '/') {																			// Условие нужно для того, чтобы разграничивать то, что происходит в корне и все остальное, ибо здешний код выполняется в любом случае.
	db::query('INSERT INTO `users` (`login`, `pw`) VALUES ("query_test_0", PASSWORD("ololo"))');
}
?>