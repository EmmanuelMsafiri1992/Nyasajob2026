

$(document).ready(function () {
	
	if (modalDefaultAdminCode !== 0 && modalDefaultAdminCode !== '') {
		changeCity(countryCode, modalDefaultAdminCode);
	}
	$('#modalAdminField').change(function () {
		changeCity(countryCode, $(this).val());
	});
	
});

function changeCity(countryCode, modalDefaultAdminCode) {
	/* Check Bugs */
	if (typeof languageCode == 'undefined' || typeof countryCode == 'undefined' || typeof modalDefaultAdminCode == 'undefined') {
		return false;
	}
	
	let url = siteUrl + '/ajax/countries/' + strToLower(countryCode) + '/admin1/cities';
	
	$.ajax({
		method: 'POST',
		url: url,
		data: {
			'languageCode': languageCode,
			'adminCode': modalDefaultAdminCode,
			'currSearch': $('#currSearch').val(),
			'_token': $('input[name=_token]').val()
		}
	}).done(function (data) {
		if (typeof data.adminCities == "undefined") {
			return false;
		}
		$('#selectedAdmin strong').html(data.selectedAdmin);
		$('#adminCities').html(data.adminCities);
		$('#modalAdminField').val(modalDefaultAdminCode).prop('selected');
	});
}