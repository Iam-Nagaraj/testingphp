
var map;
var marker;

function initMap() {
	map = new google.maps.Map(document.getElementById('map'), {
		center: { lat: 0, lng: 0 },
		zoom: 2
	});

	
	if(marker_lat != NaN && marker_lang != NaN){
		console.log(marker_lat, marker_lang);
		marker = new google.maps.Marker({
			map: map,
			draggable: true,
			position: { lat: marker_lat, lng: marker_lang }
		});
		
	} else {
		marker = new google.maps.Marker({
			map: map,
			draggable: true,
			position: { lat: 0, lng: 0 }
		});		
	}


	google.maps.event.addListener(marker, 'dragend', function () {
		var position = marker.getPosition();
		
		$('#latitude').val(position.lat());
		$('#longitude').val(position.lng());
		geocodeLatLng(position);
	});
}

function geocodeLatLng(latlng) {

	var geocoder = new google.maps.Geocoder();
	geocoder.geocode({ 'location': latlng }, function (results, status) {
		if (status === 'OK') {
			if (results[0]) {

				var address = results[0].formatted_address;
				var city, state;

				for (var i = 0; i < results[0].address_components.length; i++) {
					var component = results[0].address_components[i];
					if (component.types.includes('locality')) {
						city = component.long_name;
					} else if (component.types.includes('administrative_area_level_1')) {
						state = component.long_name;
					}
				}

				document.getElementById('address').value = address;
				document.getElementById('city').value = city;
				document.getElementById('state').value = state;
			} else {
				console.log('No results found');
			}
		} else {
			console.log('Geocoder failed due to: ' + status);
		}
	});
}


$(document).on('submit', '#merchant-certified-form', function (e) {
	e.preventDefault();
	let myForm = document.getElementById('merchant-certified-form');
	let target = "state-body";
	let formData = new FormData(myForm);
	let url = myForm?.getAttribute('action');

	AjaxFromLoginSubmit(formData, url, 'POST', target)

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
			window.location.reload();

		}, error: async function (response) {
			if (response.status === 422) {
				$('.is-invalid').hide();
				let errors = response.responseJSON.errors;
				Object.keys(errors).forEach(function (key) {
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

