var searchInput = 'myAddress';
    
document.addEventListener("DOMContentLoaded", function () {
	var addressInput = document.getElementById("myAddress");

	// Create a new Autocomplete object
	var autocomplete = new google.maps.places.Autocomplete(addressInput);

	// Set the types to restrict the autocomplete results to addresses
	autocomplete.setTypes(['address']);

	// Add event listener for when a place is selected
	autocomplete.addListener('place_changed', function () {
		var place = autocomplete.getPlace();

		// Process address components
		place.address_components.forEach(function (component) {
			switch (component.types[0]) {
				case 'country':
				$('#country').val(component.long_name);
				break;
				case 'administrative_area_level_1':
				$('#state').val(component.short_name);
				$('#state_long_name').val(component.long_name);
				break;
				case 'locality':
				$('#city').val(component.long_name);
				break;
				case 'postal_code':
				$('#business_zip_code').val(component.long_name);
				break;
				case 'street_number':
				var myaddres = $('#myAddress').val();
				$('#line_1').val(myaddres);
				$('#myAddress').val(component.long_name);
				break;
				case 'route':
				var line_1 = $('#myAddress').val();
				$('#myAddress').val(line_1+' '+component.long_name);
				break;
			}
		});

		// Set latitude and longitude
		$('#latitude').val(place.geometry.location.lat());
		$('#longitude').val(place.geometry.location.lng());
		
		checkState()

	});
});

var searchInput = 'myAddress2';
    
document.addEventListener("DOMContentLoaded", function () {
	var addressInput2 = document.getElementById("myAddress2");

	// Create a new Autocomplete object
	var autocomplete2 = new google.maps.places.Autocomplete(addressInput2);

	// Set the types to restrict the autocomplete results to addresses
	autocomplete2.setTypes(['address']);

	// Add event listener for when a place is selected
	autocomplete2.addListener('place_changed', function () {
		var place2 = autocomplete2.getPlace();

		// Process address components
		place2.address_components.forEach(function (component) {
			switch (component.types[0]) {
				case 'country':
				$('#business_country').val(component.long_name);
				break;
				case 'administrative_area_level_1':
				$('#business_state').val(component.short_name);
				$('#business_state_long_name').val(component.long_name);
				break;
				case 'locality':
				$('#business_city').val(component.long_name);
				break;
				case 'postal_code':
				$('#business_zip_code2').val(component.long_name);
				break;
				case 'street_number':
				var myaddres2 = $('#myAddress').val();
				$('#business_line_1').val(myaddres2);
				$('#myAddress2').val(component.long_name);
				break;
				case 'route':
				var line_12 = $('#myAddress2').val();
				$('#myAddress2').val(line_12+' '+component.long_name);
				break;
			}
		});

		// Set latitude and longitude
		$('#business_latitude_2').val(place2.geometry.location.lat());
		$('#business_longitude_2').val(place2.geometry.location.lng());

	});
});