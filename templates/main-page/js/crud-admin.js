/**
 * Created by nima on 08.11.18.
 */
(function($) {

	var editElement = function (thas, id) {
		// якщо переданий параметр id, то це редагування без аякса - шукаєм
		// tr де ім'я класа є  переданий id.
		// Якщо не переданий параметр то це аякс тому знаходим tr
		// по this (кнопка натиснута для редагування і вибіраєм всю строку для редагування в якій була натиснута кнопка)

		if (typeof id !== 'undefined'){
			var parentRow = $('tr.show-element.'+id+':first');
		}
		 else {
			// знаходим блищого предка по атрибуту (в даному випадку строку яку будем редагувати)
			var parentRow = $(thas).closest("tr");
			var id = $(parentRow).children('td').eq(0).text();
			// визначаєемо з адресної строки назву сутністі над якою відбуваються операції
			// (pathname частина url без host-/admin/users-show,
			// split("/")[2]- розбиває строку на масив по "/",вибіраєм третій елемент масиву users-show,
			// split("-")[0]- розбиваєм і забираєм перший users)
			var nameForSaving = window.location.pathname.split("/")[2].split("-")[0];
			window.history.pushState("null", "null", "//blog-dogs.com/admin/"
				+nameForSaving+"/edit/"+id);
		}

		var fieldsValue = [];
		var fieldsWidth = [];
		var fieldsClassName = [];
		//перебіраєм кожну ячейку строки і значення записуєм в масив.
		// children('td').length-1 відкидаємо останню ячейку адже вона містить кнопки видалення і редагування
		for (i = 0; i < $(parentRow).children('td').length - 1; i++) {
			fieldsValue[i] = $(parentRow).children('td').eq(i).text();
			fieldsWidth[i] = $(parentRow).children('td').eq(i).width();
			fieldsClassName[i] = $(parentRow).children('td').eq(i).attr("class");
		}
		//alert(fieldsWidth[0]);

		// видаляєм всих потомків строки (робим пустою щоб, втавити поля для редагування)
		parentRow.empty();
		for (i = 0; i < fieldsValue.length; i++) {
			//першу ячейку робем не змінною(без можливості редагувати) бо це id - його не редегуєм, вставляєм назад як було до цього
			if (i == 0) {
				parentRow.append("<td class="+fieldsClassName[i]+">" + fieldsValue[i] + "</td>");
			} else {
				// вставляєм багатострокові текстові поля для редагування у кожну ячейку строчки,
				// а також встановлюємо ширину цих полів відповідно до інших ячейок що не редагуються
				// віднімаєя від ширини 5 пікселів, так приблизно щоб не вилазило за межі таблиці
				parentRow.append("<td class="+fieldsClassName[i]+"><textarea rows='5' name=" + fieldsValue[i] + " style='width:" + (fieldsWidth[i] - 5) + "px;'>"
					+ fieldsValue[i] + "</textarea></td>");
			}
		}
		parentRow.append("<td><input type='button' class='save-row' value='зберегти'>" +
			"<input type='button' class='cancel-edit-row' value='відміна'></td>");
	};


	$.fn.crudAdmin = function(id) {
        //якщо параметр id значить це не аякс тому ми визиваєм функцію для редагування строки в якій є id
		if (id) {
			return editElement(this, id);
		};
		
		//так як кнопочки редагування і видалення в табличці створюються динамічно,
		// то звичайним шляхом обработчик не получится почипить тільки через делегування,
		// тому оброботчик вішаєм на блищого предка елемента який був створений не динамічно
		// (можна і на body але більш доцільніше на блищого предка чіпляти),
		// а потім в в фунції crudAdmin- вішаєм подію натискання миші і делагуємо цю подію на потомка
		// створеного динамічно (this.on("click", ".edit-row", function (){...})
		// також в середені оброботчика this є сам цей потомок якому делегували подію
		this.on("click", ".edit-row",
			function(){
				var countSaveInput = $("input.save-row").length;
                //якщо не відкриті інші строки для редагування значить можна проводить редагування
				// (щоб не було більше однієї строки відкрито для редагування одночасно)
				if (countSaveInput == 0) {
				   var thas = this;
				   editElement(thas);
                }
			}
		);
        // видалення запису- відправляється id елемента для видалення і після чого, якщо удачна відповідь,
		//то ми видаляємо повністю цю строку в таблиці без перезагрузки сторінки
		this.on("click", ".delete-row", function () {
				// знаходим блищого предка по атрибуту (в даному випадку строку яку будем редагувати)
				var parentRow = $(this).closest("tr");
			// берем  id елемента (для відпправки на сервер для видалення)
				fieldValue = parentRow.children('td').eq(0).text();			 
				// визначаєемо з адресної строки назву сутністі над якою відбуваються операції
				// (pathname частина url без host-/admin/users-show,
				// split("/")[2]- розбиває строку на масив по "/",вибіраєм третій елемент масиву (наприклад users),

				var nameForSaving = window.location.pathname.split("/")[2];
				$.ajax({
					url: '//blog-dogs.com/admin/'+nameForSaving+"/delete",
					//url: '//blog-dogs.com/admin/users-edit',
					type: 'post',
					cache: false,
					// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! 
					// ящо вказати dataType: "json",
					//то потрібно і відповідь сервера робить в json (в php заголовок робить 
					// header('Content-type: application/json'), і echo response json_encode() ),
					// якщо не вказувать пропустить dataType, то можна в php простим echo вивиодить response  
					//dataType: "json",
					data: {'id': fieldValue},
					success: function(response){
						console.log(response);
						if (response =='ok'){
							//видаляєм всю строку
							$(parentRow).remove();
						}
						else{
							//console.log(response);
							alert('видалення не відбулось'+response)
						}
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
		}
		);

		// функція яка відміняє редагування інформаці- повертає назад строку з ячейками до попереднього стану
		// Також оброботчик робем через делегування події(вище описано чому)
		this.on("click", ".cancel-edit-row", function () {
			// знаходим блищого предка по атрибуту (в даному випадку строку яку будем редагувати)
			var parentRow = $(this).closest("tr");
			var fieldsValue = [];
            var fieldsClassName = [];

			//перебіраєм кожну ячейку строки і потім в ячейкі ще дочірній елемент textarea,
			// в нього зчитуєм значення name (туди ми попередньо записали значення полів (початкові) коли натискали "редагування")
			// яке записуєм в масив.
			// children('td').length-1 відкидаємо останню ячейку адже вона містить кнопки зберегти  і відміну
			for (i = 0; i < $(parentRow).children('td').length - 1; i++) {
				fieldsClassName[i] = parentRow.children('td').eq(i).attr("class");
				// перевіряєм першу ячейку адже в ній не змінне поле із значенням id елемента, тому ми зчитуєм його по іншому
				if (i == 0) {
					fieldsValue[i] = parentRow.children('td').eq(i).text();
				} else {
					var field = parentRow.children('td').eq(i);
					fieldsValue[i] = field.children('textarea').attr('name');
				}
			}
			// видаляєм всих потомків строки (робим пустою щоб, втавити поля для редагування)
			parentRow.empty();
			//формуємо строку вставляючи ячейки із відповідниим значенням масиву (фактично повертаєм все назад)
			for (i = 0; i < fieldsValue.length; i++) {
				parentRow.append("<td class="+fieldsClassName[i]+">" + fieldsValue[i] + "</td>");
			}
			// вставляємо останню ячейку в строку де буде форма з кнопками редагування і видалення
			parentRow.append(
				"<td><form name='test' method='post' action=''>" +
				"<input type='button' class='edit-row' value='редагувати' >" +
				"<input type='button' class='delete-row' value='видалити' >" +
				"</form></td>");

		});

        // зберігаємо внесені зміни в строку редагування - відправляєм на сервер щоб змни записались в базу,
		// після позитивної відповіді сервера змінюєм строку на редаговані дані
		this.on("click", ".save-row", function () {
			// знаходим блищого предка по атрибуту (в даному випадку строку яку будем зберігати)
			var parentRow = $(this).closest("tr");
			// fieldsValue-є обектом, бо так краще по ajax передавать
			var fieldsValue = {};
			// буде властивістю обєкта fieldsValue з назвою полем і з значенням цього поля
			var fieldsClassName = [];
			// визначаєемо з адресної строки назву сутністі над якою відбуваються операції
			// (pathname частина url без host-/admin/users-show,
			// split("/")[2]- розбиває строку на масив по "/",вибіраєм третій елемент масиву назву сучності
			var nameForSaving = window.location.pathname.split("/")[2]
			//перебіраєм кожну ячейку строки і потім в ячейкі ще дочірній елемент textarea,
			// в нього зчитуєм значення val
			// яке записуєм в масив.
			// children('td').length-1 відкидаємо останню ячейку адже вона містить кнопки зберегти  і відміну
			for (i = 0; i < $(parentRow).children('td').length-1; i++) {
				fieldsClassName[i] = parentRow.children('td').eq(i).attr("class");
				// перевіряєм першу ячейку адже в ній не змінне поле із значенням id елемента, тому ми зчитуєм його по іншому
				if (i == 0) {
					//fieldsValue[fieldsClassName[i]] = parentRow.children('td').eq(i).text();
					fieldsValue[fieldsClassName[i]] = parentRow.children('td').eq(i).text();
				} else {
					var field = parentRow.children('td').eq(i);
					// записуєм в обект влас
					fieldsValue[fieldsClassName[i]] = field.children('textarea').val();
				}
			}
			//console.log(fieldsValue);
			$.ajax({
				url: '//blog-dogs.com/admin/'+nameForSaving+"/edit",
				//url: '//blog-dogs.com/admin/users/edit',
				type: 'post',
				cache: false,
				// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! 
				// ящо вказати dataType: "json",
				//то потрібно і відповідь сервера робить в json (в php заголовок робить 
				// header('Content-type: application/json'), і echo response json_encode() ),
				// якщо не вказувать пропустить dataType, то можна в php простим echo вивиодить response  
				//dataType: "json",
				data: fieldsValue,
				success: function(response){
					console.log(response);
					if (response =='ok'){

						// після збереження редагованих даних ми повинні змінити url адже це вже буде
						// не редагування а всих елементів сучності (/show)
						window.history.pushState("null", "null", "//blog-dogs.com/admin/"+nameForSaving+"/show");
						//console.log('good');
						// видаляєм всих потомків строки (робим пустою щоб, втавити поля для редагування)
						parentRow.empty();
						//формуємо строку вставляючи ячейки із відповідниим значенням масиву (фактично повертаєм все назад)
						for (i = 0; i < fieldsClassName.length; i++) {
							parentRow.append("<td class="+fieldsClassName[i]+">" + fieldsValue[fieldsClassName[i]] + "</td>");
						}
						// вставляємо останню ячейку в строку де буде форма з кнопками редагування і видалення
						parentRow.append(
							"<td><form name='test' method='post' action=''>" +
							"<input type='button' class='edit-row' value='редагувати' >" +
							"<input type='button' class='delete-row' value='видалити' >" +
							"</form></td>");
					}
					else{
					//console.log(response);
						alert('редагування не відбулось')
					}
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

		});	

	};
})(jQuery);
