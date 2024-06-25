function initAutocomplete() {
	var input = document.getElementById('autocomplete');
	var options = {
	  types: ['geocode'],
	};

	var autocomplete = new google.maps.places.Autocomplete(input, options);

	autocomplete.addListener('place_changed', function() {
	  var place = autocomplete.getPlace();

	  if (!place.geometry) {
		console.error('Place details not available for input: ' + place.name);
		return;
	  }

	  document.getElementById('_latitude').value = place.geometry.location.lat();
	  document.getElementById('_longitude').value = place.geometry.location.lng();
	  document.getElementById('_state').value = getComponentValue(place, 'administrative_area_level_1');
	  document.getElementById('_city').value = getComponentValue(place, 'locality');
	  document.getElementById('fullAddress').value = place.formatted_address;
	});
  }

  function getComponentValue(place, componentType) {
	for (var i = 0; i < place.address_components.length; i++) {
	  var component = place.address_components[i];
	  for (var j = 0; j < component.types.length; j++) {
		if (component.types[j] === componentType) {
		  return component.long_name;
		}
	  }
	}
	return '';
  }

  document.addEventListener('DOMContentLoaded', function() {
    initAutocomplete();
  });