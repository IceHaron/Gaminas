
/**
*	
*	Ну конечно же, всякие разные штуки при завершении загрузки страницы
*	
**/
$(document).ready(function() {

/**
*	
*	Клик на заголовке скрывает/раскрывает фильтр и что-то еще делает, пока не придумал
*	
**/
$('#namesearch').keyup(function(key) {
	if (key.keyCode != '16' && $(this).val().length > 0) {												// Не буду отслеживать Шифт
		what = $(this).val().toLowerCase();
		filtered = 0;
		agreed = 0;
		$('.gamename').each(function() {
			where = $(this).text().toLowerCase();
			if (where.search(what) == -1) { $(this).parent().hide(); filtered++; }		// Все, что отфильтровывается, скрываем
			else { $(this).parent().show();	agreed++; }																// А все, что удовлетворяет, показываем.
		});
		$('#filtercomment').text('Показано ' + agreed + ', отфильтровано ' + filtered);
	} else if ($(this).val().length == 0) $('#filtercomment').text('');
});

/**
*	
*	Клик на заголовке скрывает/раскрывает фильтр и что-то еще делает, пока не придумал
*	
**/
  $('#maincaption').click(function() {
    $('#mainsupport').toggle();																									// Фильтр пока существует только в библиотеке, в этот блок думаю напихать всяких разных удобных штук
  });
  
/**
*	
*	Перехватчик клика на логофф, кнопка должна что-то делать хитрое,
*	пока просто перенаправляет на страничку логоффа
*	
**/
  $('#logoff').click(function() {
    window.location = '/auth/logoff';
  });
	
/**
*	
*	Работа с табличкой TODO
*	
**/

	$('.DONE').parent().hide();																										// Скрываем то, что я уже сделал, чтоб не мешались
	
	$('#todo tbody tr').hover(function() {
		$(this).children('td').animate({'opacity': '1'}, 100);
	},
	function() {
		if ($(this).children('td').eq(2).attr('class') == 'UNDONE-right')	$(this).children('td').animate({'opacity': '0.7'}, 100);
		else $(this).children('td').animate({'opacity': '0.3'}, 100);
	});
	
/**
*	
*	Вывод графика активности по регионам
*	
**/
	
	if ($('.graph').attr('class') != null && $('.sumregion').attr('class') != null) {
		$('.graph').each(function () {
			var w = parseInt($(this).width());
			var h = parseInt($(this).height());
			$(this).height(w);
			$(this).width(500);
			$(this).css( 'margin-bottom', h - w - 500 );
		});
		var actarr = {};
		var max = 0;
		var height = Math.floor( $('.graph').height() / 48 );
		var leftpos = $('.graph').offset().left;
		
		$('.sumregion').each(function() {
			var name = this.getAttribute('data-region');
			var jumps = parseInt(this.getAttribute('data-jumps'));
			actarr[name] = jumps;
			
			if (jumps > max) max = jumps
			
		});
		
		$('.sumregion').each(function() {
			var jumps = parseInt(this.getAttribute('data-jumps'));
			var width = Math.floor( jumps / max * $('.graph').width() ) + 1;
			$(this).css({ 'height' : height, 'width' : width });
			$(this).children('div').css( 'font-size', height - 1 );
		});
	}

	
/* End of READY() */
});


/**
*	
*	Функция логина от uLogin
*	
**/
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