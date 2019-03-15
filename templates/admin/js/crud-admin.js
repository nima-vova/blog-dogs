/**
 * Created by nima on 08.11.18.
 */
(function($) {

	var editElement = function (thas, id) {
    // if passed parameter id, then this is editing without ajax - we are looking for
    // tr where the name of the class is passed to the id.
    // If parameter is not passed then this is ajax so we find tr
    // on this (the button is pressed for editing and select the entire line for editing in which the button was pressed)

		if (typeof id !== 'undefined'){
			var parentRow = $('tr.show-element.'+id+':first');
		}
		 else {
			// find the about parent of the attribute (in this case, the string we will edit)
			var parentRow = $(thas).closest("tr");
			var id = $(parentRow).children('td').eq(0).text();
			// is defined from the address bar by the name of the entity over which the operations are performed
            // (pathname part url without host- / admin / users-show
			// split("/")[2]- breaks the string into an array by "/", we select the third element of the users-show array
			// split("-")[0]- break and take away the first users)
			var nameForSaving = window.location.pathname.split("/")[2].split("-")[0];
			window.history.pushState("null", "null", "//blog-dogs.com/admin/"
				+nameForSaving+"/edit/"+id);
		}

		var fieldsValue = [];
		var fieldsWidth = [];
		var fieldsClassName = [];
		// fix each cell of the string and write the value to the array.
		// children('td').length-1 we reject the last cell because it contains buttons for deletion and editing
		for (i = 0; i < $(parentRow).children('td').length - 1; i++) {
			fieldsValue[i] = $(parentRow).children('td').eq(i).text();
			fieldsWidth[i] = $(parentRow).children('td').eq(i).width();
			fieldsClassName[i] = $(parentRow).children('td').eq(i).attr("class");
		}
		//alert(fieldsWidth[0]);
		// remove all the descendants of the line (we make it empty to wrap the fields for editing)
		parentRow.empty();
		for (i = 0; i < fieldsValue.length; i++) {
			//the first cell we make is not a variable (without the possibility of editing) because it is id - it is not reregive, insert back as it was before
			if (i == 0) {
				parentRow.append("<td class="+fieldsClassName[i]+">" + fieldsValue[i] + "</td>");
			} else {
				 // insert multi-line text fields for editing in each cell of the line,
                 // and also set the width of these fields in accordance with other cells that are not editable
                // subtracts from the width of 5 pixels, so approximately so that it does not float beyond the table
				parentRow.append("<td class="+fieldsClassName[i]+"><textarea rows='5' name=" + fieldsValue[i] + " style='width:" + (fieldsWidth[i] - 5) + "px;'>"
					+ fieldsValue[i] + "</textarea></td>");
			}
		}
		parentRow.append("<td><input type='button' class='save-row' value='зберегти'>" +
			"<input type='button' class='cancel-edit-row' value='відміна'></td>");
	};

	$.fn.crudAdmin = function(id) {
		// if the parameter id means it is not ajax so we call the function to edit the line in which there is id
		if (id) {
			return editElement(this, id);
		};
		// as the buttons are editable and deleted in the spreadsheet are dynamically created,
        // then the usual way the handler will not be able to debug only through delegation,
        // so the worker hangs on the glittering ancestor of an element that was not created dynamically
        // (it is possible on the body, but it is more expedient to cling to the next parent),
        // and then in the crudAdmin function, hang the mouse click event and we make this event to the descendant
        // created dynamically (this.on ("click", ".edit-row", function () {...})
        // also in the middle of the processor this is the very descendant whom the delegate delegated to the event
		this.on("click", ".edit-row",
			function(){
				var countSaveInput = $("input.save-row").length;
				// if other lines are not open for editing, then you can edit it
                // (so that no more than one line is open for editing at a time)
				if (countSaveInput == 0) {
				   var thas = this;
				   editElement(thas);
                }
			}
		);
		// delete the record-sends the id of the item to delete and then, if a successful reply,
        // then we completely delete this row in the table without reloading the page
		this.on("click", ".delete-row", function () {
				// find the bright parent of the attribute (in this case, the string we will edit)
				var parentRow = $(this).closest("tr");
				// we take the id of the element (to send to the server for deletion)
				fieldValue = parentRow.children('td').eq(0).text();
				// is defined from the address bar by the name of the entity over which the operations are performed
                // (pathname part url without host- / admin / users-show,
                // split ("/") [2] - splits the string into an array by "/",
				// we select the third element of the array (for example, users),
				var nameForSaving = window.location.pathname.split("/")[2];
				$.ajax({
					url: '//blog-dogs.com/admin/'+nameForSaving+"/delete",
					//url: '//blog-dogs.com/admin/users-edit',
					type: 'post',
					cache: false,
					// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! 
					// specify dataType: "json",
                    // then it is necessary and the server response does in json (in the php header does
                    // header ('Content-type: application / json') and echo response json_encode ()),
                    // if it does not specify to miss the dataType, then in php a simple echo will output the response
					//dataType: "json",
					data: {'id': fieldValue},
					success: function(response){
						console.log(response);
						if (response =='ok'){
							// delete the entire line
							$(parentRow).remove();
						}
						else{
							//console.log(response);
							alert('видалення не відбулось'+response)
						}
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
		}
		);
		// function that cancels editing information-returns a cell line to the previous state
        // Also, we work out the worker through the delegation of the event (explained above)
		this.on("click", ".cancel-edit-row", function () {
			// find the brilliant ancestor of the attribute (in this case, the string we will edit)
			var parentRow = $(this).closest("tr");
			var fieldsValue = [];
            var fieldsClassName = [];

			// run each cell of the string and then in the cell is still a child element textarea,
            // it reads the value of name (there we pre-recorded the value of the fields (initial) when you clicked the "edit")
            // write to the array.
            // children ('td'). length-1 reject the last cell because it contains buttons to save and unmount
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
			// delete all the descendants of the string (we make it empty to wrap the fields for editing)
			parentRow.empty();
			// form the string by inserting cells corresponding to the value of the array (actually turn everything back)
			for (i = 0; i < fieldsValue.length; i++) {
				parentRow.append("<td class="+fieldsClassName[i]+">" + fieldsValue[i] + "</td>");
			}
			// insert the last cell in the line where the form with the editing and deleting buttons will be
			parentRow.append(
				"<td><form name='test' method='post' action=''>" +
				"<input type='button' class='edit-row' value='редагувати' >" +
				"<input type='button' class='delete-row' value='видалити' >" +
				"</form></td>");

		});
		// save the changes made in the edit line - send it to the server so that you can log in to the database,
        // after a positive server response, change the string to the edited data
		this.on("click", ".save-row", function () {
			// we find neighbour parent of the attribute (in this case, the string we will store)
			var parentRow = $(this).closest("tr");
			// fieldsValue-is an object, because it's better to pass ajax
			var fieldsValue = {};
			// will be the property of the fieldValue object with the field name and the value of this field
			var fieldsClassName = [];
			// is defined from the address bar by the name of the entity over which the operations are performed
            // (pathname part url without host- / admin / users-show,
            // split ("/") [2] - splits the string into an array by "/", we select the third element of the array of the name of the entity
			var nameForSaving = window.location.pathname.split("/")[2];
			 // run each cell of the string and then in the cell is still a child element textarea,
             // it reads value val
             // write to the array.
            // children ('td'). length-1 reject the last cell because it contains buttons to save and unmount
			for (i = 0; i < $(parentRow).children('td').length-1; i++) {
				fieldsClassName[i] = parentRow.children('td').eq(i).attr("class");
				// check the first cell because it does not change the field with the value id of the element, so we read it in another
				if (i == 0) {
					//fieldsValue[fieldsClassName[i]] = parentRow.children('td').eq(i).text();
					fieldsValue[fieldsClassName[i]] = parentRow.children('td').eq(i).text();
				} else {
					var field = parentRow.children('td').eq(i);
					// write to the array
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
				// specify dataType: "json",
                // then it is necessary and the server response does in json (in the php header does
                // header ('Content-type: application / json') and echo response json_encode ()),
                // if it does not specify to miss the dataType, then in php a simple echo will output the response
                // dataType: "json"
				data: fieldsValue,
				success: function(response){
					console.log(response);
					if (response =='ok'){
						// after saving the edited data, we must change the url because it will already be
                        // not editing all the elements of the entity (/ show)
						window.history.pushState("null", "null", "//blog-dogs.com/admin/"+nameForSaving+"/show");
						//console.log('good');
						// delete all the childrens of the string (we make it empty to wrap the fields for editing)
						parentRow.empty();
						// form the string by inserting cells corresponding to the value of the array (actually turn everything back)
						for (i = 0; i < fieldsClassName.length; i++) {
							parentRow.append("<td class="+fieldsClassName[i]+">" + fieldsValue[fieldsClassName[i]] + "</td>");
						}
						// insert the last cell in the line where the form with the editing and deleting buttons will be
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
				// so it's more convenient to see the mistake of knowing why the ajax does not work
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
					console.log(msg);				}
			});

		});	

	};
})(jQuery);
