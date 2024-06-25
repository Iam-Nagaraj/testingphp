$(document).on('submit', '#login-form', function (e) {
	e.preventDefault();
	let myForm = document.getElementById('login-form');
	let target = "login-body";
	let formData = new FormData(myForm);
	let url = myForm?.getAttribute('action');

	AjaxFromLoginSubmit(formData, url, 'POST', target)

	// AjaxFromSubmit(formData, url,'POST',true);


})



$(document).on('submit', '#otp-form', function (e) {
	e.preventDefault();
	var optInput = "";
	$('.digit-group').find('input').each(function () {
		optInput += $(this).val();
	});
	$('.input-otp').val(optInput);
	let myForm = document.getElementById('otp-form');
	let formData = new FormData(myForm);
	let url = myForm?.getAttribute('action');
	AjaxFromSubmit('otp-form',formData, url, 'POST', true,'none','',{},false);

});

function AjaxFromLoginSubmit(formData, url, method, target) {
	$.ajax({
		type: method,
		url: url,
		data: formData,
		contentType: false,
		processData: false,
		complete: function () {
			initOnOtpScreen();
		},
		success: function (response) {
			$('.is-invalid').removeClass('is-invalid');
			$('.invalid-feedback strong').html('');
			$('#' + target).html(response?.html);
			$.NotificationApp.send("Success", response?.msg, "top-right", "#4cffc5", "success");


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
				});

			}
			let errors = response.responseJSON
			if (errors?.errors?.error) {
				$(".show_all_error.invalid-feedback").show();
				$(".show_all_error.invalid-feedback").text(errors?.message);
			}
			return response;
		}
	})
}

function initOnOtpScreen() {

	$('.digit-group').find('input').each(function () {
		$(this).attr('maxlength', 1);
		$(this).on('keyup', function (e) {
            if($(e.target).val()){
			var parent = $($(this).parent());

			if (e.keyCode === 8 || e.keyCode === 37) {
				var prev = parent.find('input#' + $(this).data('previous'));

				if (prev.length) {
					$(prev).select();
				}
			} else if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 65 && e.keyCode <= 90) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode === 39) {
				var next = parent.find('input#' + $(this).data('next'));

				if (next.length) {
					$(next).select();
				} else {

					if (parent.data('autosubmit')) {
						parent.submit();
					}
				}

			}
        }
		});
	});



}
