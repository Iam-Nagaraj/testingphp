
$(document).on('submit', '#otp-verification-form', function (e) {
	e.preventDefault();
	let myForm = document.getElementById('otp-verification-form');
	let target = "otp-body";
	let formData = new FormData(myForm);
	let url = myForm?.getAttribute('action');

	AjaxFromotpSubmit(formData, url, 'POST', target)

	// AjaxFromSubmit(formData, url,'POST',true);


})


async function AjaxFromotpSubmit(formData, url, method, target) {
	$(".loader_box").show();
	$(".text-danger").hide();
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
		success: async function (response) {
			$('.is-invalid').removeClass('is-invalid');
			$('.invalid-feedback strong').html('');
			$('#' + target).html(response?.html);
			$.NotificationApp.send("Success", response?.msg, "top-right", "#4cffc5", "success");
			await new Promise(resolve => setTimeout(resolve, 2500));
			if(response.url){
				navigateTo(response.url);
			}

		}, error: function (response) {
			if (response.status === 422) {
				$('.is-invalid').removeClass('is-invalid');
				$('.invalid-feedback strong').html('');
				let errors = response.responseJSON.errors;
				Object.keys(errors).forEach(function (key) {
					$("#" + key).text(errors[key][0]);
					$("#" + key).show();
				});

			}
			if (response.status === 404) {
				console.log(response.responseJSON.message);
				$("#code").text(response.responseJSON.message);
				$("#code").show();
			}
			let errors = response.responseJSON
			if (errors?.errors?.error) {
				$(".show_all_error.invalid-feedback").show();
				$(".show_all_error.invalid-feedback").text(errors?.message);
			}
			$(".loader_box").hide();
			return response;
		}
	})
}


