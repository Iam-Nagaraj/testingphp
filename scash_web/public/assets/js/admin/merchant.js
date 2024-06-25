$(document).ready(async function () {
	let country_code_symbol = '+';
	let active_status = 1;
	let inactive_status = 2;
	let columns = [

		// {
		// 	title: 'Image', data: null,
		// 	orderable: false,
		// 	render: (data, type, row, meta) => {
		// 		return `<img src="`+data.image_url+`" style="width:100px !important; height:100px !important;" class="form-control">`;
		// 	}
		// },
		{ title: 'Id', data: 'id' },
		{
			title: 'Business Name', data: null,
			orderable: false,
			render: (data, type, row, meta) => {
				return data.business_detail?data.business_detail.business_name:'N/A';
			}
		},
		{
			title: 'Phone Number', data: null,
			orderable: false,
			render: (data, type, row, meta) => {
				if(data.verification){
					return data.verification.phone_number;
				} else {
					return 'N/A';
				}
				
			}
		},
		{ title: 'Email', data: 'email' },
		{
			title: 'City', data: null,
			orderable: false,
			render: (data, type, row, meta) => {
				return data.business_detail?data.business_detail.business_city:'N/A';
			}
		},
		{
			title: 'Status', data: null,
			orderable: false,
			render: (data, type, row, meta) => {
				return `
				<div data-id="${data.id}">
				<label class="switch">
				  <input type="checkbox" hidden="hidden" class="status-action" id="username1" ${data.status == active_status ? 'checked' : ''} >
				  <label class="switch slider round" for="username1"></label>
				</label>
				  
				</div>`;
			},

			createdCell: (cell, cellData, rowData, rowIndex, colIndex) => {

				const statusButton = cell.querySelector('.status-action');
				if (statusButton) {
					statusButton.addEventListener('change', () => {
						userStatus(cellData?.id, cellData.status);
					});
				}


			}
		},
		{
			title: 'Action',
			data: 'action',
			orderable: false,

		}


	];


	await InitDataTable('merchants-table', merchant_datatable_url, columns);

	const statusButton = document.querySelector('.status-action');
	if (statusButton) {
		statusButton.addEventListener('change', (element) => {
			userStatus($(element.target).attr('data-id'), $(element.target).attr('data-status'));
		});
	}

	const userStatus = async (id, status) => {
		if (confirm('Are You sure?')) {
			const fromData = { id: id, status: (status == active_status ? inactive_status : active_status) };
			let url = merchant_status_change_url;
			const options = {
				type: "POST",
				url: url,
				data: fromData,
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
				if (response.success == true) {
					$.NotificationApp.send("Success", response.message, "top-right", "#4fa98c", "success");
					await new Promise(resolve => setTimeout(resolve, 2500));
					location.reload();
				}

			} catch (error) {
				console.log(error);
			}
		}
	}






	$(document).on('click', '.merchant-list-tab', async function () {
		let $this = $(this);
		let status = $this.attr('data-type');

		await InitDataTable('merchants-table', merchant_datatable_url, columns, status);
	})

	$(document).on('change', '#business_category', async function () {
		let business_category = {business_category : $('#business_category').val()};

		await InitDataTable('merchants-table', merchant_datatable_url, columns, null, business_category);
	})

	$(document).on('change', '#business_type', async function () {
		let business_type = {business_type : $('#business_type').val()};

		await InitDataTable('merchants-table', merchant_datatable_url, columns, null, business_type);
	})

	

})

$(document).on('submit', '#merchant-form', function (e) {
	e.preventDefault();
	let myForm = document.getElementById('merchant-form');
	let target = "merchant-body";
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
			navigateTo('/merchant');

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

function payUser(uuid, user_name)
{
	$('#UserNameLabel').text('Pay to '+user_name);
	$('#destination_user_id').val(uuid);

	$('#pauUserModel').modal('show');
}


$(document).on('submit', '#pauUser-wallet-form', function (e) {
	e.preventDefault();
	let myForm = document.getElementById('pauUser-wallet-form');
	let target = "withdraw-wallet-body";
	let formData = new FormData(myForm);
	let url = myForm?.getAttribute('action');

	AjaxFromLoginSubmit(formData, url, 'POST', target)

});

async function AjaxFromLoginSubmit(formData, url, method, target) {
	$("#amount").hide();
	$(".is-invalid").hide();
	$("#destination_id").hide();

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
			// await new Promise(resolve => setTimeout(resolve, 2500));
			// navigateTo('/bank');
			$(".loader_box").hide();
            $('#form_account_id').val('');
            $('#amount').val('');
            $('#pauUserModel').modal('hide');
            $('#successModel').modal('show');


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
			if(response.responseJSON.errors == true){
				if (response.responseJSON.message) {
					$("#all_errors").text(response.responseJSON.message);
					$("#all_errors").show();
				}
			}
			$(".loader_box").hide();
			return response;
		}
	})
}
