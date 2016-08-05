var $$ = jQuery;

var tmp = {

	get: function (name) {

		var ajax = $$.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				module : name,
				action : 'content_editor'
			}
		});


		return ajax;

	}

};