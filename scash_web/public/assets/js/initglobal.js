$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('input[name="_token"]').val()
	}
});
class NotificationApp {
	send = function (heading, text, position, color, icon) {
		$.toast({
			heading: heading,
			text: text,
			position: position,
			loaderBg: color,
			icon: icon,
			bgColor: color,
			textColor: "#000000"
		});
	}
}

$.NotificationApp = new NotificationApp();



window.InitDataTable = async function (id, url, columns, status = null, filter = []) {

	$('#' + id).DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		"bDestroy": true,
		ajax: {
			url: url,
			data: function (d) {
				d.status = status;
				d.filter = filter;
			}
		},
		language: {
			paginate: {
				next: '&#8594;', // or '→'
				previous: '&#8592;' // or '←'
			}
		},
		drawCallback: function () {
			$(".dataTables_paginate > .pagination").addClass("pagination-rounded");

		},
		columns: columns
	});
}

window.ajaxRequest = function (options) {
	return new Promise((resolve, reject) => {
		$.ajax({
			...options,
			success: function (response) {
				resolve(response);
			},
			error: function (xhr, status, error) {
				reject({ xhr, status, error });
			}
		});
	});
};


window.AjaxFromSubmit = async function (form_id, formData, url, method, no_notification = false, table_id, datatable_url, columns, is_datatable_init = true) {
	const options = {
		type: method ?? "POST",
		data: formData,
		contentType: false,
		processData: false,
		url: url,
		headers: { Accept: "application/json" },
		beforeSend: function () {
			$(".loader_box").show();
		},
		complete: function () {
			$(".loader_box").hide();
		}
	};

	try {
		const response = await ajaxRequest(options);

		if (response.success === true) {
			$('.is-invalid').removeClass('is-invalid');
			$('.invalid-feedback strong').html('');
			$(".modal.show button[data-bs-dismiss='modal']").click();
			if (!no_notification) {
				$.NotificationApp.send("Success", response.message, "top-right", "#4fa98c", "success");
				await new Promise(resolve => setTimeout(resolve, 2500));
			}
		} else {
			$(".show_all_error.invalid-feedback").show();
			$(".show_all_error.invalid-feedback").text(response.message);
			throw response;
		}

		if (is_datatable_init) {
			InitDataTable(table_id, datatable_url, columns);
			formReset(form_id);
		} else {
			location.reload();
		}
	} catch (error) {
		if (error.xhr && error.xhr.status === 422) {
			$('.is-invalid').removeClass('is-invalid');
			$('.invalid-feedback strong').html('');
			let errors = error.xhr.responseJSON.errors;
			Object.keys(errors).forEach(function (key) {
				$("#" + key + "Input input").addClass("is-invalid");
				$("#" + key + "Input select").addClass("is-invalid");
				$("#" + key + "Input span.invalid-feedback").children("strong").text(errors[key][0]);
				$("#" + key + "Input span.invalid-feedback").show();
			});
		}

		let errors = error.xhr.responseJSON;
		if (errors?.errors?.error) {
			$(".show_all_error.invalid-feedback").show();
			$(".show_all_error.invalid-feedback").text(errors?.message);
		}

		throw error;
	}
};


// window.AjaxFromSubmitRenderDesign = async function (form_id, formData, url, method, no_notification = false, table_id, datatable_url, columns, is_datatable_init = true) {

// 	const options = {
// 		type: method ?? "POST",
// 		data: formData,
// 		contentType: false,
// 		processData: false,
// 		url: url,
// 		headers: { Accept: "application/json" },
// 		beforeSend: function () {
// 			$(".loader_box").show();
// 		},
// 		complete: function () {
// 			$(".loader_box").hide();
// 		}
// 	};

// 	try {
// 		const response = await ajaxRequest(options);

// 		if (response.status === 'success') {
// 			$('.is-invalid').removeClass('is-invalid');
// 			$('.invalid-feedback strong').html('');
// 			$(".modal.show button[data-bs-dismiss='modal']").click();
// 			if (!no_notification) {
// 				$.NotificationApp.send("Success", response.message, "top-right", "#4fa98c", "success");
// 			}
// 			$('.render-image-div').html(`<img src="${response.generateUrl}">`).addClass('filled');
// 		} else {
// 			$(".show_all_error.invalid-feedback").show();
// 			$(".show_all_error.invalid-feedback").text(response.message);
// 			throw response;
// 		}

// 		if (is_datatable_init) {
// 			InitDataTable(table_id, datatable_url, columns);
// 			formReset(form_id);
// 		}
// 	} catch (error) {
// 		if (error.xhr && error.xhr.status === 422) {
// 			$('.is-invalid').removeClass('is-invalid');
// 			$('.invalid-feedback strong').html('');
// 			let errors = error.xhr.responseJSON.errors;
// 			Object.keys(errors).forEach(function (key) {
// 				$("#" + key + "Input input").addClass("is-invalid");
// 				$("#" + key + "Input select").addClass("is-invalid");
// 				$("#" + key + "Input span.invalid-feedback").children("strong").text(errors[key][0]);
// 				$("#" + key + "Input span.invalid-feedback").show();
// 			});
// 		}

// 		console.log(error);

// 		let errors = error.xhr.responseJSON;
// 		if (errors?.errors?.error) {
// 			$(".show_all_error.invalid-feedback").show();
// 			$(".show_all_error.invalid-feedback").text(errors?.message);
// 		}

// 		throw error;
// 	}
// };






Dropzone.autoDiscover = false;

$(document).ready(async function () {
	// $('.dropify').dropify();
	// Dropzone.autoDiscover = false;
	await initDropZone();
});


const formReset = async function (form_id) {
	$('#' + form_id)[0].reset();
	$('.dropify-clear').each(function () {
		$(this).click();
	})
}

// $(document).on('change input', 'input,select,textarea,checkbox', function () {
// 	if ($(this).hasClass('single_vendor_check')) {
// 		return false;
// 	}

// 	if ($(this).attr('id') == "franchise_pincode") {
// 		if ($(this).val().length >= 1) {
// 			$(this).closest('div.cst-pincode-main-inner').find('.invalid-feedback strong').text('');
// 			$(this).closest('div.cst-pincode-main-inner').find('.is-invalid').removeClass('is-invalid');
// 			$(this).closest('div.cst-pincode-main-inner').find('.invalid-feedback').hide();

// 		} else if ($(this).val().length < 1) {
// 			$(this).closest('div.cst-pincode-main-inner').find('.invalid-feedback strong').text('The ' + $(this).attr('name')?.replace('[]', '').replace('_', ' ') + ' field is required.');
// 			$(this).closest('div.cst-pincode-main-inner').find('.form-control').addClass('is-invalid');
// 			$(this).closest('div.cst-pincode-main-inner').find('.invalid-feedback').show();
// 		}
// 	} else {
// 		if ($(this).attr('id') == "commission_fees" && $(this).val().length >= 1) {
// 			if ($(this).val() > 100) {
// 				$(this).val('');
// 			}
// 			if ($(this).val() == 0 && $(this).val().length > 1) {
// 				$(this).val('');
// 			}
// 			$(this).closest('div#' + $(this).attr('name')?.replace('[]', '') + 'Input').find('.invalid-feedback strong').text('');
// 			$(this).closest('div#' + $(this).attr('name')?.replace('[]', '') + 'Input').find('.is-invalid').removeClass('is-invalid');
// 			$(this).closest('div#category_idsInput').find('.invalid-feedback strong').text('');
// 			$(this).closest('div#category_idsInput').find('.is-invalid').removeClass('is-invalid');

// 		} else if ($(this).val().length >= 1 || $(this).prop('checked') == true || $(this).find(':selected').val()) {
// 			$(this).closest('div#' + $(this).attr('name')?.replace('[]', '') + 'Input').find('.invalid-feedback strong').text('');
// 			$(this).closest('div#' + $(this).attr('name')?.replace('[]', '') + 'Input').find('.is-invalid').removeClass('is-invalid');


// 		} else if ($(this).val().length < 1 || $(this).prop('checked') == false || !$(this).find(':selected').val()) {
// 			$(this).closest('div#' + $(this).attr('name')?.replace('[]', '') + 'Input').find('.invalid-feedback strong').text('The ' + $(this).attr('name')?.replace('[]', '').replace('_', ' ') + ' field is required.');
// 			$(this).closest('div#' + $(this).attr('name')?.replace('[]', '') + 'Input').find('.form-control').addClass('is-invalid');

// 		}
// 	}
// });


async function handlePincodeChange(element) {
	const pincodeLength = element.val().length;
	const pincodeContainer = element.closest('div.cst-pincode-main-inner');

	if (pincodeLength >= 1) {
		clearValidationMessages(pincodeContainer);
	} else if (pincodeLength < 1) {
		setValidationMessages(pincodeContainer, 'The ' + element.attr('name')?.replace('[]', '').replace('_', ' ') + ' field is required.');
	}
}

async function handleOtherElementChange(element, elementId, elementName) {
	if (elementId === "commission_fees" && element.val().length >= 1) {
		if (element.val() > 100 || element.val() == 0) {
			element.val('');
		}
		const containerId = elementId === "commission_fees" ? 'category_idsInput' : elementName + 'Input';
		const container = element.closest('div#' + containerId);

		clearValidationMessages(container);

	} else if (element.val().length >= 1 || element.prop('checked') || element.find(':selected').val()) {
		const container = element.closest('div#' + elementName + 'Input');
		clearValidationMessages(container);

	} else if (element.val().length < 1 || !element.prop('checked') || !element.find(':selected').val()) {
		const container = element.closest('div#' + elementName + 'Input');
		setValidationMessages(container, 'The ' + element.attr('name')?.replace('[]', '').replace('_', ' ') + ' field is required.');
	}
}

async function clearValidationMessages(container) {
	container.find('.invalid-feedback strong').text('');
	container.find('.is-invalid').removeClass('is-invalid');
}

async function setValidationMessages(container, message) {
	container.find('.invalid-feedback strong').text(message);
	container.find('.form-control').addClass('is-invalid');
}

async function handleChange(element) {
	if (element.hasClass('single_vendor_check')) {
		return false;
	}

	const elementId = element.attr('id');
	const elementName = element.attr('name')?.replace('[]', '');

	if (elementId === "franchise_pincode") {
		await handlePincodeChange(element);
	} else {
		await handleOtherElementChange(element, elementId, elementName);
	}
}

$(document).on('change input', 'input,select,textarea,checkbox', async function () {
	await handleChange($(this));
});



$(document).on('change', '.image-ajax-upload', async function () {
	let formData = new FormData;
	formData.append('file', $(this)[0].files[0]);

	var modal = $(this).closest('.modal');
	var designTitle = modal.find('.cst-design-title').val();
	var designSelectValue = $('.designSelect:checked').val();
	var designValue = designTitle ? designTitle : (designSelectValue ? designSelectValue : '');
	formData.append('design', designValue);

	let $this = $(this);
	var attrName = $this.attr('name');
	if (!$this.attr('data-name')) {
		$this.attr('data-name', attrName);
	} else {
		attrName = $this.attr('data-name');
	}

	$this.closest('div.form-group').find('.image-ajax-response').remove();
	$this.closest('div.form-group').find('.image-ajax-response-url').remove();
	try {
		let response = await ajaxRequest({
			url: upload_image_url,
			method: 'POST',
			data: formData,
			contentType: false,
			processData: false,
			beforeSend: function () {
				$this.closest('.dropify-wrapper').find('.dropify-preview').addClass('d-none');
				$this.closest('.dropify-wrapper').find('.dropify-loader').addClass('show');
			},
		});

		if (response?.data?.file) {
			$(`<input type="hidden" name="${attrName}" class="image-ajax-response" value="${response?.data?.file}"/>`).insertAfter($this);
			$this.removeAttr('name');
			$this.closest('.dropify-wrapper').find('.dropify-preview').removeClass('d-none');
			$this.closest('.dropify-wrapper').next('.invalid-feedback').find('strong').text('');
			$this.removeClass('is-invalid');
			$(`<input type="hidden" name="${attrName + '_url'}" class="image-ajax-response-url" value="${response?.data?.url}"/>`).insertAfter($this);
		} else {
			$this.attr('name', $this.attr('data-name'));
		}
	} catch (error) {
		console.error(error);
		$this.attr('name', $this.attr('data-name'));
	} finally {
		$this.closest('.dropify-wrapper').find('.dropify-loader').removeClass('show');
	}
});


$(document).on('change', '.video-ajax-upload', async function () {

	let formData = new FormData;
	formData.append('file', $(this)[0].files[0]);

	var modal = $(this).closest('.modal');
	var designTitle = modal.find('.cst-design-title').val();
	var designSelectValue = $('.designSelect:checked').val();
	var designValue = designTitle ? designTitle : (designSelectValue ? designSelectValue : '');
	formData.append('design', designValue);

	let $this = $(this);
	var attrName = $this.attr('name');
	if (!$this.attr('data-name')) {
		$this.attr('data-name', attrName);
	} else {
		attrName = $this.attr('data-name');
	}

	$this.closest('div.form-group').find('.video-ajax-response').remove();
	$this.closest('div.form-group').find('.video-ajax-response-url').remove();
	try {
		let response = await ajaxRequest({
			url: upload_video_url,
			method: 'POST',
			data: formData,
			contentType: false,
			processData: false,
			beforeSend: function () {
				$this.closest('.dropify-wrapper').find('.dropify-preview').addClass('d-none');
				$this.closest('.dropify-wrapper').find('.dropify-loader').addClass('show');
			},
		});

		if (response?.data?.file) {
			$(`<input type="hidden" name="${attrName}" class="video-ajax-response" value="${response?.data?.file}"/>`).insertAfter($this);
			$this.removeAttr('name');
			$this.closest('.dropify-wrapper').find('.dropify-preview').removeClass('d-none');
			$this.closest('.dropify-wrapper').next('.invalid-feedback').find('strong').text('');
			$this.removeClass('is-invalid');
			$(`<input type="hidden" name="${attrName + '_url'}" class="video-ajax-response-url" value="${response?.data?.url}"/>`).insertAfter($this);
			$(`<div class="bg-light mt-2" style="height: 100%;width: max-content;">
			<video width="200" height="200" controls=""
				class="vimg_4932">
				<source src="${response?.data?.url}">
			</video><i class="fa fa-trash m-2 delete-video"
				style="color:red;font-size:24px;float: right;position: relative;z-index: 9;cursor: pointer;"
				(click)="walkthroughVideoDelete($event)"></i>
		</div>`).insertAfter($this.closest('#my-awesome-dropzone-' + attrName));



		} else {
			$this.attr('name', $this.attr('data-name'));
		}
	} catch (error) {
		console.error(error);
		$this.attr('name', $this.attr('data-name'));
	} finally {
		$this.closest('.dropify-wrapper').find('.dropify-loader').removeClass('show');
	}
});


$(document).on('click', '.dropify-clear', function () {
	let attrNAme = $(this).closest('.form-group').find('.image-ajax-response').attr('name')

	console.log(attrNAme);
	$(this).closest('.form-group').find('.dropzone-image').attr('data-default-file', '').attr('name', attrNAme);
	$(this).closest('.form-group').find('.image-ajax-response').remove();
	$(this).closest('.form-group').find('.image-ajax-response-url').remove();
})

$(document).on('click', '.dropify-delete-video', function (e) {
	e.preventDefault();
	let attrNAme = $(this).closest('.form-group').find('.video-ajax-response').attr('name');
	console.log(attrNAme);
	$(this).closest('.form-group').find('.dropzone').attr('data-default-file', '').attr('name', attrNAme);
	$(this).closest('.form-group').find('.video-ajax-response').remove();
	$(this).closest('.form-group').find('.video-ajax-response-url').remove();
	$(this).closest('.form-group').find('.video-output-div').remove();

})



$(document).on('click', '.edit-action', async function () {
	let url = $(this).attr('data-url');
	let target = $(this).attr('data-target');

	try {
		let response = await ajaxRequest({
			type: "GET",
			url: url,
		});

		$('.' + target).html(response?.html);
		$('.' + target).closest('.modal').modal('show');
		await initDropZone(); // Assuming initDropZone is a function defined elsewhere
	} catch (error) {
		console.error(error);
	}
});



$(document).on('click', '.view-action', async function (e) {
	e.preventDefault();

	let url = $(this).attr('data-url');
	let target = $(this).attr('data-target');

	try {
		let response = await ajaxRequest({
			type: "GET",
			url: url,
		});

		$('.' + target).html(response?.html);
		$('.' + target).closest('.modal').modal('show');
	} catch (error) {
		console.error(error);
	}
});




$(document).on('click', '.delete-action', async function (e) {
	e.preventDefault();
	let $this = $(this);

	if (confirm('Are You sure?')) {
		let url = $(this).attr('data-url');
	

		try {
			let response = await ajaxRequest({
				type: "DELETE",
				url: url,
			});

			$.NotificationApp.send("Success", response.message, "top-right", "#4fa98c", "success");

			setTimeout(function () {
				$this.closest('.dataTables_wrapper').find('.dataTables_filter input[type="search"]').val(($.trim($this.closest('.dataTables_wrapper').find('.dataTables_filter input[type="search"]').val()) ? $this.closest('.dataTables_wrapper').find('.dataTables_filter input[type="search"]').val() + ' ' : ' ')).trigger('input').val(($.trim($this.closest('.dataTables_wrapper').find('.dataTables_filter input[type="search"]').val()) ? $this.closest('.dataTables_wrapper').find('.dataTables_filter input[type="search"]').val() : '')).trigger('input');
			}, 2500);
		} catch (error) {
			console.error(error);
		}
	}
});


async function initDropZone() {
	$('.dropzone-image').dropify();
	$('.dropzone-image').attr('accept', '.jpg, .jpeg, .png');

	// $('.ajax-dropzone').remove();
	// $('.dropzone-image').each(function () {

	// 	$('<div class="dropzone dropzone-previews ajax-dropzone"></div>').insertAfter($(this));
	// 	$(this).hide();

	// })

	// $(".ajax-dropzone").dropzone({
	// 	addRemoveLinks: true,
	// 	maxFiles: 1,
	// 	url: upload_image_url, // Your server-side script to handle the upload
	// 	paramName: "file", // Name of the uploaded file in the request
	// 	headers: {
	// 		'X-CSRF-TOKEN': $('input[name="_token"]').val()
	// 	},
	// 	maxFilesize: 5, // Max file size in MB
	// 	acceptedFiles: ".jpg, .jpeg, .png", // Allowed file types
	// 	dictDefaultMessage: "Drop files here or click to upload",
	// 	init: function () {
	// 		this.on("maxfilesexceeded", function (file) {
	// 			this.removeAllFiles();
	// 			this.addFile(file);

	// 		});

	// 		this.on("complete", function (file) {

	// 		});
	// 	},
	// 	success: function (file, response) {
	// 		let filefield = $(file.previewElement).closest('.ajax-dropzone').prev('.dropzone-image');
	// 		let attrName = filefield.attr('name');
	// 		filefield.attr('data-name', attrName);
	// 		$(`<input type="hidden" name="${attrName}" class="image-ajax-response" value="${response?.file}"/>`).insertAfter(filefield);
	// 		$(filefield).closest('#' + attrName + 'Input').find('.invalid-feedback strong').text('');
	// 		filefield.removeAttr('name');

	// 	},
	// 	error: function (file, errorMessage) {
	// 		// Handle error
	// 	}
	// });
}

let input = document.querySelector('.int-tel-input');
if (input) {
	var iti = window.intlTelInput(input, {
		/* utilsScript: 'build/js/utils.js', */
		autoPlaceholder: true,
		preferredCountries: ['ae', 'us', 'in']
	});

	input.addEventListener("countrychange", function () {
		updateCountryCodeDisplay(iti);
	});

	//   input.addEventListener('input change')
	updateCountryCodeDisplay(iti);

	function updateCountryCodeDisplay(iti) {
		var selectedCountry = iti.getSelectedCountryData();
		var countryCode = selectedCountry.dialCode ?? $('.country_code').val();
		$(".iti__flag").text('+' + countryCode);
		$('.country_code').val(countryCode);
		$('.int-tel-input').css('padding-left', $('.iti__selected-flag')?.outerWidth() + 'px');
	}

}


$(document).on('change input', '.int-tel-input', function () {
	if ($(this).val().length >= 10) {
		$(this).val($(this).val().substr(0, 10));
	}
});

$(document).on('change input', '.input-max-val', function () {
	if ($(this).val().length >= $(this).attr('max-length')) {
		$(this).val($(this).val().substr(0, $(this).attr('max-length')));
	}
});



function onlyNumberKey(evt) {

	// Only ASCII character in that range allowed
	var ASCIICode = (evt.which) ? evt.which : evt.keyCode
	if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
		return false;
	return true;
}





var setImageSrcFromDataAttribute = function (imageElement) {
	let imageUrl = imageElement.data('src');
	imageElement.attr('src', imageUrl);
}

var lazyLoadImages = function () {
	let imageElements = $('.lazyload');
	imageElements.each(function () {
		setImageSrcFromDataAttribute($(this));
	});
}




window.navigateTo = async function (url) {

	window.location.href = url;
}


window.urlNavigatorHistory = async function (href) {
	window.history.pushState({ href: href }, '', href);
}