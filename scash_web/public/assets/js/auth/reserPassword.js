$(document).on('submit', '#changePassword-form', function (e) {
	e.preventDefault();
	let myForm = document.getElementById('changePassword-form');
	let target = "changePassword-body";
	let formData = new FormData(myForm);
	let url = myForm?.getAttribute('action');

	AjaxFromchangePasswordSubmit(formData, url, 'POST', target)

	// AjaxFromSubmit(formData, url,'POST',true);


})


function AjaxFromchangePasswordSubmit(formData, url, method, target) {
	$.ajaxSetup({
        headers : {
            'CSRFToken' : $('meta[name="_token"]').attr('content')
        }
    });
	$.ajax({
		type: method,
		url: url,
		data: formData,
		contentType: false,
		processData: false,
		success: function (response) {
			$('.is-invalid').removeClass('is-invalid');
			$('.invalid-feedback strong').html('');
			$('#' + target).html(response?.html);
			$.NotificationApp.send("Success", response?.msg, "top-right", "#4cffc5", "success");
			navigateTo('/auth/password-reset-confirmation');

		}, error: function (response) {
			if (response.status === 422) {
				$('.is-invalid').removeClass('is-invalid');
				$('.invalid-feedback strong').html('');
				let errors = response.responseJSON.errors;
				Object.keys(errors).forEach(function (key) {
					$("#" + key + "Input input").addClass("is-invalid");
					$("#" + key + "Input select").addClass("is-invalid");
					$("#" + key + "Input span.invalid-feedback").children("strong").text(errors[key][0]);
					$("#" + key + "Input span.invalid-feedback").show();
					$("#" + key).text(errors[key][0]);
					$("#" + key).show();
				});

			}
			let errors = response.responseJSON
			if (errors?.errors?.error) {
				$(".show_all_error.invalid-feedback").show();
				$(".show_all_error.invalid-feedback").text(errors?.message);
			}
			if (response.responseJSON.message) {
				$("#name").text(response.responseJSON.message);
				$("#name").show();
			}
			return response;
		}
	})
}
