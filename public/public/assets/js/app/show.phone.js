

$(document).ready(function () {
	
	$('.phoneBlock').click(function (e) {
		e.preventDefault(); /* prevents the submit or reload */
		
		showPhone();
		
		return false;
	});
	
});

/**
 * Show the Contact's phone
 * @returns {boolean}
 */
function showPhone() {
	let postId = $('#postId').val();
	
	if (postId === 0 || postId === '0' || postId === '') {
		return false;
	}
	
	let resultCanBeCached = true;
	let url = siteUrl + '/ajax/post/phone';
	
	$.ajax({
		method: 'POST',
		url: url,
		data: {
			'postId': postId,
			'_token': $('input[name=_token]').val()
		},
		cache: resultCanBeCached
	}).done(function (data) {
		if (typeof data.phone == 'undefined') {
			return false;
		}
		
		let phoneBlockEl = $('.phoneBlock');
		
		phoneBlockEl.html('<i class="fas fa-mobile-alt"></i> ' + data.phone);
		phoneBlockEl.attr('href', data.link);
		phoneBlockEl.tooltip('dispose'); /* Disable Tooltip */
		
		if (resultCanBeCached) {
			$('#postId').val(0);
		}
	});
}
