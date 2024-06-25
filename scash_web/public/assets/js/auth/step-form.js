	var navListItems = $('div.setup-panel div a'),
	allWells = $('.setup-content'),
	allNextBtn = $('.nextBtn'),
	stepTwoBtn = $('.stepTwoBtn'),
	allSubmitBtn = $('.submitBtn'),
	allPrevBtn = $('.prevBtn');

	allWells.hide();

	navListItems.click(function (e) {
		e.preventDefault();
		var $target = $($(this).attr('href')),
		$item = $(this);

		if (!$item.hasClass('disabled')) {
			navListItems.removeClass('btn-primary').addClass('btn-default');
			$item.addClass('btn-primary');
			allWells.hide();
			$target.show();
			$target.find('input:eq(0)').focus();
		}
	});
	$('.stepwizard-row').hide()

	$('#SSN_NEXT_BUTTON').click(function(){
		var tax_type = $("input[name=tax_type]:checked").val();
		if(tax_type == 1){
			$('#SSN_EIN_FORM').html(EIN_DIV);
		} else {
			$('#SSN_EIN_FORM').html(SSN_DIV);
		}
	});


	$('#CHECK_TYPE_BUTTON').click(function(){
		var CHECK_TYPE_BUTTON = $("input[name=registration_type]:checked").attr('data-value');
		if(CHECK_TYPE_BUTTON == 1){
			$('.SSN_RADIO').prop('checked', true);
		} else {
			$('.EIN_RADIO').prop('checked', true);
		}
	});

	var timeoutId; // Variable to store the timeout ID
	  $("#email").on("change", function() {
		var email = $(this).val();
		// Clear any existing timeout
		clearTimeout(timeoutId);
		if (email.length >= 4) {
			timeoutId = setTimeout(function() {
			$.ajax({
				type: "POST",
				url: email_ajax_url,
				data: { email: email },
				dataType: "json",
				success: function(response) {
					if (response.exists) {
						$(".email").text("Email already exists!");
						$(".form-email").addClass("has-error");
					} else {
						$(".email").text("");
						$(".form-email").removeClass("has-error");
					}
				},
				error: function(response) {                  
					$(".email").text(response.responseJSON.message);
					$(".form-email").addClass("has-error");
				}
			});
			}, 500);
		}
	});

	// Phone Country Code
	// $("#mobile_code").intlTelInput({
	// 	initialCountry: "us",
	// 	setCountry: "us",
	// 	separateDialCode: true,
	// 	allowDropdown: false,
	// 	nationalMode: true
	// });
	  var selectedCountryCode = 1;
	  $("#mobile_code").on("blur", function () {
		var countryData = $("#mobile_code").intlTelInput("getSelectedCountryData");
		var selectedCountryCode = countryData.dialCode;
		$("#dial_code").val('+'+selectedCountryCode);
	  });

	// Password Toggle
	function initPasswordToggle() {
		var type = $('#password').attr('type');
		if (type == 'password') {
			$('#password').attr('type', 'text');
		} else {
			$('#password').attr('type', 'password');
		}
	}
	function initPasswordToggle2() {
		var type = $('#confirm_password').attr('type');
		if (type == 'password') {
			$('#confirm_password').attr('type', 'text');
		} else {
			$('#confirm_password').attr('type', 'password');
		}
	}

	$('#password').change(function (){
		var password = $('#password').val();
		var confirm_password = $('#confirm_password').val();
		if(password != confirm_password){
			$(".confirm_password").text('Password & conform password must match');
			$(".confirm_password").show();
			$(".form-password").addClass("has-error");
		} else {
			$(".confirm_password").text('');
			$(".form-password").removeClass("has-error");

		}
	});

	$('#confirm_password').change(function (){
		var password = $('#password').val();
		var confirm_password = $('#confirm_password').val();
		if(password != confirm_password){
			$(".confirm_password").text('Password & Confirm password must match');
			$(".confirm_password").show();
			$(".form-password").addClass("has-error");
		} else {
			$(".confirm_password").text('');
			$(".form-email").removeClass("has-error");
			$(".form-password").removeClass("has-error");
		}
	});
  

	allPrevBtn.click(function(){
		var curStep = $(this).closest(".setup-content"),
		curStepBtn = curStep.attr("id"),
		prevStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");

		prevStepWizard.removeAttr('disabled').trigger('click');
	});


	allNextBtn.click(function(){

		var curStep = $(this).closest(".setup-content"),
		curStepBtn = curStep.attr("id"),
		nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
		curInputs = curStep.find("input[type='text'],input[type='url'],input[type='email'],input[type='password'],input[type='date']"),
		isValid = true;

		$(".form-group").removeClass("has-error");
		
		for(var i=0; i<curInputs.length; i++){
			if (!curInputs[i].validity.valid){
				isValid = false;
				$(curInputs[i]).closest(".form-group").addClass("has-error");
			}
		}

		if (isValid){
			nextStepWizard.removeAttr('disabled').trigger('click');
		}
	});

	stepTwoBtn.click(function(){

		var curStep = $(this).closest(".setup-content"),
		curStepBtn = curStep.attr("id"),
		nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
		curInputs = curStep.find("input[type='text'],input[type='url'],input[type='email'],input[type='password'],input[type='date']"),
		isValid = true;

		$(".form-group").removeClass("has-error");
		checkState();
		
		for(var i=0; i<curInputs.length; i++){
			if (!curInputs[i].validity.valid){
				isValid = false;
				$(curInputs[i]).closest(".form-group").addClass("has-error");
			}
		}

		if (isValid){
			nextStepWizard.removeAttr('disabled').trigger('click');
		}
	});

	allSubmitBtn.click(function(){
		var curStep = $(this).closest(".setup-content"),
		curStepBtn = curStep.attr("id"),
		nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
		curInputs = curStep.find("input[type='text'],input[type='url'],input[type='email'],input[type='password'],input[type='date']"),
		isValid = true;

		$(".form-group").removeClass("has-error");
		checkState();
		
		for(var i=0; i<curInputs.length; i++){
			if (!curInputs[i].validity.valid){
				isValid = false;
				$(curInputs[i]).closest(".form-group").addClass("has-error");
			}
		}

		// var isValidSsnCheck = isValidSsn();
		// var isValidEinCheck = isValidEin();

		// if(isValidEinCheck == false || isValidSsnCheck == false){
		// 	isValid = false;
		// 	$('.submitBtn').removeAttr("type").attr("type", "button");
		// }

		if($('input[type=radio][name=privacy_policy]:checked').length == 0)
		{
			$(".privacy_policy_error").text('Please agree terms & conditions');
			$(".privacy_policy_error").show();
			$(".form-privacy").addClass("has-error");
			isValid = false;
		} else {
			$(".privacy_policy_error").text('');
			$(".form-privacy").removeClass("has-error");
		}

		if (isValid){
			nextStepWizard.removeAttr('disabled').trigger('click');
			$('.submitBtn').removeAttr("type").attr("type", "submit");
		}
	});

	$('div.setup-panel div a.btn-primary').trigger('click');

	var SSN_DIV = `
		<label class="control-label my-2">4-digit SSN</label>
		<input type="text" id="checkSSN" maxlength="9" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" name="ssn_itin" required="required" class="form-control" onkeyup="isValidSsn()" placeholder="Enter 4-digit Business SSN">
		<strong class="text-danger is-invalid confirm_ssn"></strong>

		<label class="control-label my-2">9-digit Business EIN</label>
		<input type="text" id="checkEIN" maxlength="9" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" name="business_ein" required="required" class="form-control" onkeyup="isValidEin()" placeholder="Enter 4-digit Business EIN">
		<strong class="text-danger is-invalid confirm_ssn"></strong>
	`;

	var EIN_DIV = `
		<label class="control-label my-2">4-digit SSN</label>
		<input type="text" id="checkSSN" maxlength="9" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" name="ssn_itin" required="required" class="form-control" onkeyup="isValidSsn()" placeholder="Enter 4-digit Business SSN">
		<strong class="text-danger is-invalid confirm_ssn"></strong>
	`;

	function setDate(){
		// Calculate the maximum allowed date (18 years ago from today)
		var maxDate = new Date();
		maxDate.setFullYear(maxDate.getFullYear() - 21);
		
		// Format the date in YYYY-MM-DD for the input's max attribute
		var maxDateString = maxDate.toISOString().split('T')[0];
		
		// Set the max attribute of the date input
		document.getElementById('birthdate').max = maxDateString;
	}

	function checkState()
	{
		var state_code = $('#state').val();
		var city_code = $('#city').val();

		if(state_code.length != 2 || city_code.length == 0){
			$(".confirm_state").text('Invalid Location');
			$(".confirm_state").show();
			$(".form-state").addClass("has-error");
		} else {
			$(".confirm_state").text('');
			$(".form-state").removeClass("has-error");
		}
	}

	function isValidSsn(){
		return false;
		let regex = new RegExp(/^(?!666|000|9\d{2})\d{3}(?!00)\d{2}(?!0{4})\d{4}$/);
		let str = $('#checkSSN').val();
		let checkValue = false;

		if($('#checkSSN').length == 0){
			return true;
		}

		//  if str CODE
		// is empty return false
		if (str == null) {
			checkValue = false;
		}
	
		// Return true if the str
		// matched the ReGex
		if (regex.test(str) == true) {
			checkValue = true;
		}
		else {
			checkValue = false;
		}
		if(checkValue){
			$(".confirm_ssn").text('');
			$(".form-ssn").removeClass("has-error");
		} else {
			$(".confirm_ssn").text('Invalid SSN');
			$(".confirm_ssn").show();
			$(".form-ssn").addClass("has-error");
		}
		return checkValue;
	
	}

	function isValidEin(){
		return false;
		let regex = new RegExp(/^01\d{7}$/);
		let str = $('#checkEIN').val();
		let checkValue = false;

		if($('#checkEIN').length == 0){
			return true;
		}

		//  if str CODE
		// is empty return false
		if (str == null) {
			checkValue = false;
		}
	
		// Return true if the str
		// matched the ReGex
		if (regex.test(str) == true) {
			checkValue = true;
		}
		else {
			checkValue = false;
		}

		if(checkValue){
			$(".confirm_ssn").text('');
			$(".form-ssn").removeClass("has-error");
		} else {
			$(".confirm_ssn").text('Invalid EIN');
			$(".confirm_ssn").show();
			$(".form-ssn").addClass("has-error");
		}
		return checkValue;
	
	}

	function getBusinessSubCategory(){
		let business_category_id = $('#business_category_id').val();
		console.log(business_category_id);

		$.ajaxSetup({
			headers : {
				'CSRFToken' : $('meta[name="_token"]').attr('content')
			}
		});
		$.ajax({
			type: 'POST',
			url: business_subcategory_url,
			data: {business_category_id : business_category_id},
			success: async function (response) {
				$('#getBusinessSubCategory').html(response);


			}, error: async function (response) {
				$("#all_errors").hide();

				if (errors?.errors?.error) {
					$(".show_all_error.invalid-feedback").show();
					$(".show_all_error.invalid-feedback").text(errors?.message);
				}

				return response;
			}
		})

	}



	$(document).on('submit', '#stepForm', function (e) {
		e.preventDefault();
		let myForm = document.getElementById('stepForm');
		let target = "city-body";
		let formData = new FormData(myForm);
		let url = myForm?.getAttribute('action');

		AjaxFromLoginSubmit(formData, url, 'POST', target)


	});

	async function AjaxFromLoginSubmit(formData, url, method, target) {
		$(".loader_box").show();
		$('.submitBtn').hide();
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
				navigateTo('/auth/otp-verification');

			}, error: async function (response) {
				$("#all_errors").hide();

				if (response.status === 403) {
					$('.is-invalid').hide();
					let errors = response.responseJSON.errors;
					Object.keys(errors).forEach(function (key) {
						console.log(key);
						$("#" + key).text(errors[key][0]);
						$("#" + key).show();
					});
					$("#all_errors").text(response.responseJSON.message);
					$("#all_errors").show();

				}
				if (response.status === 500) {
					$("#all_errors").text(response.responseJSON.message);
					$("#all_errors").show();
					if(response.responseJSON.message == "SSN or EIN not valid need document"){
						$(".upload_document_verification").show();
					}
				}
				let errors = response.responseJSON
				if (errors?.errors?.error) {
					$(".show_all_error.invalid-feedback").show();
					$(".show_all_error.invalid-feedback").text(errors?.message);
				}
				$(".loader_box").hide();
				$('.submitBtn').show();
				return response;
			}
		})
	}
