
/**
*	
*	Ну конечно же, всякие разные штуки при завершении загрузки страницы
*	
*/
$(document).ready(function() {

/**
*	
*	Клик на заголовке скрывает/раскрывает фильтр и что-то еще делает, пока не придумал
*	
*/
  $('#maincaption').click(function() {
    $('#libfilter').toggle();																										// Фильтр пока существует только в библиотеке, так еще и нереализованный, надо допилить
  });
  
/**
*	
*	Перехватчик клика на логофф, кнопка должна что-то делать хитрое,
*	пока просто перенаправляет на страничку логоффа
*	
*/
  $('#logoff').click(function() {
    window.location = '/logoff';
  });
});


/**
*	
*	Функция логина от uLogin
*	
*/
function login(token){
	// Отправляем AJAX-запрос к ним
	$.getJSON("//ulogin.ru/token.php?host=" + encodeURIComponent(location.toString()) + "&token=" + token + "&callback=?",
	function(data){
		data=$.parseJSON(data.toString());
		console.log(data);
		if(!data.error){
			document.cookie = 'uid=' + data.uid;
			window.location.reload(); 																								// Костыль
		}
	});
}