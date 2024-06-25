// Phone Country Code
$("#mobile_code").intlTelInput({
	initialCountry: "us",
	separateDialCode: true,
});
var selectedCountryCode = 1;
$("#mobile_code").on("blur", function () {
	var countryData = $("#mobile_code").intlTelInput("getSelectedCountryData");
	var selectedCountryCode = countryData.dialCode;
	$("#dial_code").val('+'+selectedCountryCode);
});
	
$(document).on('submit', '#update-phone', function (e) {
	e.preventDefault();
	let myForm = document.getElementById('update-phone');
	let target = "profile-body";
	let formData = new FormData(myForm);
	let url = myForm?.getAttribute('action');

	AjaxFromLoginSubmit(formData, url, 'POST', target)

});

async function AjaxFromLoginSubmit(formData, url, method, target) {
	$(".loader_box").show();
	$(".is-invalid").hide();

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
			$('#otpModal').modal('show');
			$(".loader_box").hide();
			var phone = $("#mobile_code").val();
			var dial_code = $("#dial_code").val();
			$('#new_phone').val(phone);
			$('#new_dial_code').val(dial_code);
			

		}, error: async function (response) {
			if (response.status === 422) {
				$('.is-invalid').hide();
				let errors = response.responseJSON.errors;
				Object.keys(errors).forEach(function (key) {
					console.log(key);
					$("#" + key).text(errors[key][0]);
					$("#" + key).show();
				});

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


$(document).on('submit', '#phone-verify', function (e) {
	e.preventDefault();
	let myForm = document.getElementById('phone-verify');
	let target = "profile-body";
	let formData = new FormData(myForm);
	let url = myForm?.getAttribute('action');

	AjaxFromLoginSubmit2(formData, url, 'POST', target)

});

async function AjaxFromLoginSubmit2(formData, url, method, target) {
	$(".loader_box").show();
	$(".is-invalid").hide();

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
			$('#otpModal').modal('hide');
			$(".loader_box").hide();
			navigateTo('/profile');			

		}, error: async function (response) {
			if (response.status === 422) {
				$('.is-invalid').hide();
				let errors = response.responseJSON.errors;
				Object.keys(errors).forEach(function (key) {
					console.log(key);
					$("#" + key).text(errors[key][0]);
					$("#" + key).show();
				});
			}
			if (response.status === 404) {
				$('.is-invalid').hide();
				let errors = response.responseJSON;
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

