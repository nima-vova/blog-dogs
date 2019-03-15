/**
 * Created by nima on 08.11.18.
 */
// the menu option is divided by the object that will be displayed,
// and called the corresponding function that will process and output the transmitted object
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
			// if this is the substring 'publications-show-'
            // then the output of one publication is called (because the i can be different value,
            // therefore the function for processing without id in the string is searched)
			if (menu.indexOf('publications-show-')!=-1){
			    return showPublicationById(data);
				//return console.log(menu);
			    break;
			}
			// if it is a substring 'tag-show-'
            // then the output of all the publications is called under the title of the tag (because the tag-name tag can be different value,
            // therefore the function for processing without a tag-name is searched in the lines of url)
			if (menu.indexOf('tags-show-')!=-1){
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
	// if this is a link to the main page,
    // means that the empty url is added to the hostname
	if (thas.id == "main-page") {
		var pathUrl = "";
	}
	else {
		// replace all "-" with "/" (so we create the part of url which we will add to the main one)
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
			// window.history.pushState - html5 to implement a URL change without reloading the page
            // and adds a record to the browser history (that is, the "back" button triggered)
			window.history.pushState("null", "null", "//blog-dogs.com/" + pathUrl);
			detectMenu(response, thas.id);
		},
		// so much easier to see why it does not work ajax
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
		// if not ajax (meaning in the parameters of the function the name of the menu
		// is transmitted and the data to be output from the server
        // so we call the function which by the parameter of the menu affects the menu output)
		if (menu) {
			return detectMenu(data, menu);
		};
		///////////////////////////////////
		// if it is ajax then in it also will use functions showMainPage and so on
        // for items from the menu (". menu-link") hang the corresponding event item,
		// explain what and make the corresponding ajax
		$(".menu-link").click(function () {
			//alert(pathUrl);
			sendAjax(this);
		    }
		);
		// if it is ajax then in it also will use functions showMainPage and so on
        // if the element was pressed in the concatenation then we need to print it out completely
        // but in the normal way the handler can not be tweaked (because the elements are pressed,
        // were spoken dynamically) only through delegation,
        // so the worker hangs on the close parent (". content") of the element that was created not dynamically
        // (it is possible on the body, but it is more expedient to cling to the bright ancestor),
        // and then in the hanging event of the mouse click and we make this event on the descendant
        // created dynamically ($ (". content"). on ("click", ".show-element", "function () {...})
        // also in the middle of the processor this is the very descendant whom the delegate delegated to the event
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
