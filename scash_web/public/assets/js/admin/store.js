$(document).ready(async function () {
	$('.is-invalid').hide();
	let country_code_symbol = '+';
	let active_status = 1;
	let inactive_status = 2;
	let columns = [

		{ title: 'Branch ID', data: 'branch_id' },
		{ title: 'Branch NAME', data: 'name' },
		{
			title: 'Balance', data: null,
			orderable: false,
			render: (data, type, row, meta) => {
                return '$'+data.wallet_balance;
			}
		},
		{ title: 'Email', data: 'email' },
		{ title: 'Phone Number', data: 'phone' },
		{ title: 'City', data: 'city' },
		{ title: 'State', data: 'state' },
		{
			title: 'Action',
			data: 'action',
			orderable: false,

		}

	];
	await InitDataTable('store-table', cashback_datatable_url, columns);


	$(document).on('click', '.cashback-list-tab', async function () {
		let $this = $(this);
		let status = $this.attr('data-type');

		await InitDataTable('cashback-table', cashback_datatable_url, columns, status);
	})

});

$(document).on('submit', '#cashback-form', function (e) {
	e.preventDefault();
	let myForm = document.getElementById('cashback-form');
	let target = "cashback-body";
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
			navigateTo('/store/list');

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

