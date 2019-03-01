/**
 * Created by nima on 08.11.18.
 */
// по параметру menu опреділяється обект який буде виводиться,
//і визивається відповідна функція яка буде оброблять і виводить переданий обект
var detectMenu = function (data, menu){
	switch (menu) {
		case 'main-page':
			return showMainPage(data);
			break;
		case 'contacts':
			return showContacts(data);
			break;
		case 'actual':
			return showActual(data);
			break;
		//case 'publications-show-id':
		default:
			// якщо це підстрока'publications-show-'
			// то визивається вивід однієї публікації по id( адже ід можить бути різним значенням,
			// тому шукається функція для обробки без id в строкі)
			if (menu.indexOf('publications-show-')!=-1){
			    return showPublicationById(data);
				//return console.log(menu);
			    break;
			}
			// якщо це підстрока'tags-show-'
			// то визивається вивід всіх публікацій по назві тега( адже tag-імя тега можить бути різним значенням,
			// тому шукається функція для обробки без tag-імя в строкі url)
			if (menu.indexOf('tags-show-')!=-1){


				////////////////////////////////////////
				////////////////////////////////////////
				////////////////////////////////////////
				// !!!!!!!!! ЧОГОСЬ ОЦЕЙ tags/show/bull%20terrier НІЧОГО НЕ ВИВОДИТЬ НАДА РОЗІБРАТИСЬ
				//// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

				return showMainPage(data);
				//return console.log(menu);
				break;
			}
	};
};

var showMainPage = function (data) {
	//console.log(data);
	$("div.content").empty();
	//$("div.content").append('div')
	$.each(data, function(i, val) {
		$("div.content").append(
			"<div class='publications-show show-element id-"+val.id+"' " +
			    "id='publications-show-"+val.id+"'>" +
			"<img class='img-show-element' src='"+val.image+
			    "alt='альтернативный текст'>"+val.name+"</div>");
	});
};

var showPublicationById = function (data) {
	    $("div.content").empty();
	    $("div.content").append(
		    //"<div class='show-element id-"+data.id+"'>" +
		    "<img class='img-show-element' src='" + data.image +
		    "alt='альтернативный текст'>" + data.name + "" +
		    "<br>" + data.full_text + "<div class='div-tags'>теги: </div></div>");
	    $.each(data.tags, function (i, val) {
		    $("div.div-tags").append(
			    "<span class='span-tags' id='tags-show-"+val+"'>" +val+"</span>");
	    });
     };

var sendAjax = function (thas) {
	// якщо це посилання на головну сторінку,
	// значить до имені хоста добавиться частина пустого url
	if (thas.id == "main-page") {
		var pathUrl = "";
	}
	else {
		// замінюємо всі "-" на "/" (так формуємо частину url яку будем додавати до основної)
		var pathUrl = thas.id.replace(/-/g, "/");
	}
	$.ajax({
		url: '//blog-dogs.com/' + pathUrl,
		//url: '//blog-dogs.com/admin/users/show',
		type: 'post',
		cache: false,
		dataType: "json",
		data: {"object-show": "ajax"},
		//data: { "users-show": "ajax"},
		success: function (response) {
			// window.history.pushState - html5 для реализации смена URL без перезагрузки страницы
			// и добавляет запись в историю браузера (тобто спрацьовує кнопа "назад")
			window.history.pushState("null", "null", "//blog-dogs.com/" + pathUrl);
			detectMenu(response, thas.id);
		},
		// так проще по виду помилки взнати чому не працює ajax
		error: function (jqXHR, exception) {
			var msg = '';
			if (jqXHR.status === 0) {
				msg = 'Not connect.\n Verify Network.';
			} else if (jqXHR.status == 404) {
				msg = 'Requested page not found. [404]';
			} else if (jqXHR.status == 500) {
				msg = 'Internal Server Error [500].';
			} else if (exception === 'parsererror') {
				msg = 'Requested JSON parse failed.';
			} else if (exception === 'timeout') {
				msg = 'Time out error.';
			} else if (exception === 'abort') {
				msg = 'Ajax request aborted.';
			} else {
				msg = 'Uncaught Error.\n' + jqXHR.responseText;
			}
			//$('body').html(msg);
			console.log(msg);
		}
	});
};
(function($) {
	$.fn.navigationMainPage = function (data, menu) {
		// якщо не аякс (значить в параметрах функції передається назва меню і дані для виведення від сервера
		// тому визиваєм функцію яка по параметру меню індитифікує яке меню виводить)
		if (menu) {
			return detectMenu(data, menu);
		};
		///////////////////////////////////
		// якщо це аякс то в ньому також нада буде використовувать функції showMainPage і тд
		// для елементів з меню(".menu-link") вішаєм відповіднй оброотчик події, виясняєм по ід який і робем відповідний ajax
		$(".menu-link").click(function () {
			//alert(pathUrl);
			sendAjax(this);
		    }
		);
		 // якщо це аякс то в ньому також нада буде використовувать функції showMainPage і тд
		 // якщо елемент був натиснутий в контетні значить нам нада вивести його повністю
		 // але звичайним шляхом обработчик не получится почипить (адже елементи які натискаються,
		// були стоворені динамічно)тільки через делегування,
		 // тому оброботчик вішаєм на блищого предка(".content") елемента який був створений не динамічно
		 // (можна і на body але більш доцільніше на блищого предка чіпляти),
		 // а потім в вішаєм подію натискання миші і делагуємо цю подію на потомка
		// створеного динамічно ($(".content").on("click", ".show-element",, function (){...})
		// також в середені оброботчика this є сам цей потомок якому делегували подію
		$(".content").on("click", ".show-element",
		    function () {
			    //console.log(this.className);
			    sendAjax(this);
		    }
		);

		$(".content").on("click", ".span-tags",
			function () {
				//console.log(this.className);
				sendAjax(this);
			}
		);
	//});
	};

})(jQuery);
