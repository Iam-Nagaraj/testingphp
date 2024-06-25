
$(document).on('submit', '#profile-form', function (e) {
	e.preventDefault();
	let myForm = document.getElementById('profile-form');
	let target = "profile-body";
	let formData = new FormData(myForm);
	let url = myForm?.getAttribute('action');

	AjaxFromLoginSubmit(formData, url, 'POST', target)

	// AjaxFromSubmit(formData, url,'POST',true);


});

async function AjaxFromLoginSubmit(formData, url, method, target) {
	$(".loader_box").show();
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


var imageDataURI ;
    var qrcodeImage = document.getElementById('qrcode').getElementsByTagName('img')[0];
    qrcodeImage.addEventListener('click', function() {
        $('#otpModal').modal('show');
        $('#download_qr').attr('src', imageDataURI);
    });

    setTimeout(function() {
        imageDataURI = document.getElementById("qrcode").getElementsByTagName("img")[0].src;
    }, 500);

    function downloadImageDataUrl() {
        // Your data URI
        var imageDataUrl = imageDataURI;

        // Create a temporary link element
        var downloadLink = document.createElement('a');

        // Set the href attribute to the data URI
        downloadLink.href = imageDataUrl;

        // Set the download attribute with a desired filename
        downloadLink.download = 'downloaded-image.png';

        // Append the link to the document
        document.body.appendChild(downloadLink);

        // Trigger a click on the link element
        downloadLink.click();

        // Remove the link element from the document
        document.body.removeChild(downloadLink);
    }