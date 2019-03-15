/**
 * Created by nima on 08.11.18.
 */
(function($) {

	var nameFiledsUsers = ['номер','Ім\'я','Прізвище','логін', 'дата реєстрації','роль/права'];
	var nameFiledsPublications = ['номер','назва', 'текст', 'картинка', 'дата', 'створення'];
	var nameFiledsComments = [];
	var nameFiledsTags = ['номер','назва'];

	var returnNameFiledsMenu = function (menu){

		switch (menu) {
			case 'users-show':
				var menuActual = nameFiledsUsers;
				return menuActual;
				break;
			case 'publications-show':
				var  menuActual = nameFiledsPublications;
				return menuActual;
				break;
			case 'tags-show':
				var  menuActual = nameFiledsTags;
				return menuActual;
				break;
			//return menuActual
		};
	};

	var tesShow = function (param, menu) {
		var nameFiledsMenu = returnNameFiledsMenu(menu);
		//console.log(nameFiledsMenu);
		$("div.content").empty();
		$("div.content").append("<table class='show-element'><thead><tr></tr></thead><tbody></tbody></table>");

		$.each(nameFiledsMenu, function(i, val) {
			$("table.show-element > thead > tr:last-child").append(
				"<th>"+val+"</th>");
			//$("div.content").append("<table class='show-element'>" +
			//	"<thead><tr>" +
			// 	"<th>id</th>" +
			// 	"<th>first_name</th>" +
			// 	"<th>last_name</th>" +
			// 	"<th>login</th>" +
			// 	"<th>dt_of_regist</th>" +
			// 	"<th>role_id</th>" +
			// 	"</tr></thead>" +
			// 	"<tbody></tbody>" +
			// 	"</table>");
		});

		//console.log(param);

		$.each(param, function(key, val){
			// insert a table string by adding a show-element class to it and a value class id
            // (the name of the class with the value of id will be searched for a line to edit when the request without ajax)
			$('table.show-element > tbody:last-child').append(
				"<tr class='show-element "+val.id+"'></tr>");

			$.each(val, function(key1, val1){
				$('table.show-element > tbody > tr:last-child').append(
				"<td class="+key1+">"+val1+"</td>");
			});
			$('table.show-element > tbody > tr:last-child').append(
			    "<td><form name='test' method='post' action=''>" +
			    "<input type='button' class='edit-row' value='редагувати' >" +
			"<input type='button' class='delete-row' value='видалити' >" +
			"</form></td>");
		});
	};

	$.fn.navigation = function(type, url) {
			if (type){
			return tesShow(type, url);
		};

		this.click(function() {
			// replace all "-" with "/" (so we create the part of url which we will add to the main) ajax)
			var pathUrl = this.id.replace(/-/g,"/");
            var thas =this;
			$.ajax({
				url: '//blog-dogs.com/admin/'+pathUrl,
				//url: '//blog-dogs.com/admin/users/show',
				type: 'post',
				cache: false,
				dataType: "json",
				data: { "object-show": "ajax"},
				//data: { "users-show": "ajax"},
				success: function(response){
					// window.history.pushState - html5 to implement a change of URL without reloading the page
					// and adds an entry to the browser history (the "back" button works)
					window.history.pushState("null", "null", "//blog-dogs.com/admin/"+pathUrl);
					//window.history.pushState("null", "null", "//blog-dogs.com/admin/users/show");
					
					tesShow(response, thas.id);
				},
				// so more convenient in the form of errors to find out why does not work ajax
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
