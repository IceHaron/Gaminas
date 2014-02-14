
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
	
	/* Устанавливаем состояние всех чекбоксов в зависимости от GET`а */
	$('.graphfilter input').each(function() {
		this.checked = false;
	});
	
	/* Фильтрация для графика: при клике на "все" выделять/снимать галки у всех детей */
	$('input.all').on('click', function() {
		chk = this.checked;
		$(this).parent().next().children().children('input').each(function() {
			this.checked = chk;
			if ($(this).attr('name') == 'region') toggleStars($(this).attr('data-id'), chk);
		});
	});
	
	/* Фильтрация систем по региону */
	$('#region input[name="region"]').on('click', function() {
		var regid = $(this).attr('data-id');
		var condition = this.checked;
		toggleStars(regid, condition);
		if (!condition) {
			checkStars(regid, condition);
			$('#system [data-regid="' + regid + '"]').each(function() {this.checked = false;});
		}
/*	Составление строки из айдишников нужных регионов, осталось для фильтрации через AJAX */
	});
	
	/* Выделение всех систем региона */
	$('#system input[name="region"]').on('click', function() {
		var regid = $(this).attr('data-regid');
		var condition = this.checked;
		checkStars(regid, condition);
	});
	
	/* Прогрузка систем после загрузки страницы */
	if ($('#loading').attr('data-why') == 'sysfilters') {
		$('#shadow').show();
		$('#loading').show();
		$('#annotation').text('Загружаем системы из txt-файлов');
		$('#progressbar div').css('width', '3%');
		var regionset = '';
		var r = '';
		$('#system input[name="region"]').each(function() {
			var regID = $(this).attr('data-regid');
			r += ',' + regID;
		});
		regionset = escape(r.substr(1));
		writeSystemList(regionset);
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

/**
*	
*	Отбираем системы по регионам  записываем их в блок фильтра
*	@param regions - Строка, в которой ID нужных регионов записаны через запятую
*	
**/
	
function writeSystemList(regions) {
	var sysinputs = {};
	$.ajax({
		type: 'GET',
		url: 'getsystems',
		data: {'regions' : regions},
		dataType: 'json',
		success: function(data) {
		
			for (sysid in data) {
				var sysinfo = data[sysid];
				var ss = parseFloat(sysinfo.security.toPrecision(1));
				if (ss === 1.0) color = 'skyblue';
				if (ss <= 0.9 && ss > 0.6) color = 'green';
				if (ss <= 0.6 && ss > 0.4) color = 'yellow';
				if (ss <= 0.4 && ss > 0.0) color = 'orange';
				if (ss <= 0.0) color = 'red';
				if (sysinfo['name'].search('/J\d{6}/') != -1) sysname = '&lt;WH&gt; ' + sysinfo['name'];
				else sysname = sysinfo['name'];
				sysinputs[ sysinfo['regionID'] ] += '<label style="display: none;"><input type="checkbox" name="system" data-id="' + sysid + '" data-regid="' + sysinfo['regionID'] + '"><div style="width:28px; float: left; color:' + color + '">' + ss + '</div>' + sysname + '</label>';
			}
			
			var width = 3;
			$('#system input[name="region"]').each(function() {
				var regID = $(this).attr('data-regid');
				var regName = $(this).parent().text();
				var inputs = sysinputs[regID].replace(/^undefined/, '');
				$(this).parent().after(inputs);
				$('#progressbar div').css('width', ++width + '%');
				$('#annotation').text('Загружаем системы для региона ' + regName);
			});
		},
		complete: function() {
			$('#shadow').hide();
			$('#loading').hide();
		}
		});
}

/* check/uncheck систем в зависимости от региона и состояния его чекбокса */
function checkStars(regid, state) {
	$('input[name="system"][data-regid="' + regid + '"]').each(function() {
		this.checked = state;
	});
}

/* show/hide систем в зависимости от региона и состояния его чекбокса */
function toggleStars(regid, state) {
	$('input[data-regid="' + regid + '"]').each(function() {
		if (state) $(this).parent().show(); else $(this).parent().hide();
		// this.checked = state;
	});
}

/**
*	
*	Отрисовка графика по входным параметрам
*	
**/

function drawGraph(time = 'daily', mode = 'system', region = 'Domain', star = 'Amarr') {			// На время разработки определю дефолтную отрисовку в Амарре
	$('#shadow').show();
	$('#loading').show();
	$('#annotation').text('Рисуем график активности');
	$('#progressbar div').css('width', '0');
	$.ajax({
		type: 'GET',
		url: 'drawGraph',
		data: {'time': time, 'mode': mode, 'region': region, 'star': star},
		success: function(data) {
			$('#maincontent').html(data);
			formatGraph();
			$('#shadow').hide();
			$('#loading').hide();
		}
	});
}

	
/**
*	
*	Вывод графика активности по регионам
*	
**/

function formatGraph() {
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


