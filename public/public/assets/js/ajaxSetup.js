

$(document).ready(function () {
	
	var xhrOptions;
	var token = $('meta[name="csrf-token"]').attr('content');
	if (token) {
		xhrOptions = {
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'X-CSRF-TOKEN': token
			},
			async: true,
			cache: false,
			xhrFields: {withCredentials: true},
			crossDomain: true
		};
	} else {
		xhrOptions = {
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
			},
			async: true,
			cache: false,
			xhrFields: {withCredentials: true},
			crossDomain: true
		};
	}
	$.ajaxSetup(xhrOptions);
	
});
