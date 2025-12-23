

/* Prevent errors, If these variables are missing. */
if (typeof packageIsEnabled === 'undefined') {
	var packageIsEnabled = false;
}

$(document).ready(function () {
	
	/* On load */
	var catId = $('#categoryId').val();
	getCategories(siteUrl, languageCode, catId);
	
	/* On category selected */
	$(document).on('click', '.cat-link', function (e) {
		e.preventDefault(); /* prevents the submit or reload */
		
		catId = $(this).data('id');
		if (typeof catId === 'undefined') {
			catId = 0;
		}
		getCategories(siteUrl, languageCode, catId);
	});
	
});

function getCategories(siteUrl, languageCode, catId) {
	let url = siteUrl + '/ajax/category/select-category';
	
	$.ajax({
		method: 'POST',
		url: url,
		data: {
			'_token': $('input[name=_token]').val(),
			'catId': catId
		}
	}).done(function (obj) {
		
		if (typeof obj.html === 'undefined' || typeof obj.hasChildren === 'undefined') {
			return false;
		}
		
		/* Get & append the category's children */
		if (obj.hasChildren) {
			$('#selectCats').html(obj.html);
		} else {
			/* Select the category & append it */
			$('#catsContainer').html(obj.html);
			
			if (typeof obj.category === 'undefined' || typeof obj.category.id === 'undefined') {
				return false;
			}
			
			/* Save data in hidden field */
			$('#categoryId').val(obj.category.id);
			$('#categoryType').val(obj.category.type);
			
			/* Close Categories Modal */
			$('#browseCategories').modal('hide');
		}
	});
}
