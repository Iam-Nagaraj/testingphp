$(document).ready(async function () {
	let country_code_symbol = '+';
	let active_status = 1;
	let inactive_status = 2;
	let columns = [

		{
			title: 'Image', data: null,
			orderable: false,
			render: (data, type, row, meta) => {
				if(data.banner_image){
					return "<a href='"+data.banner_image+"' target='_blank'><img class='form_card' src='"+data.banner_image+"'></a>";
				} else {
					return 'N/A'
				}
			}
		},
		{ title: 'Name', data: 'name' },
		{
			title: 'Merchant', data: null,
			orderable: false,
			render: (data, type, row, meta) => {
				if(data.merchant){
					return data.merchant.first_name+' '+data.merchant.last_name;
				} else {
					return 'N/A'
				}
			}
		},
		{ title: 'Type', data: 'type_name' },
		{
			title: 'Action',
			data: 'action',
			orderable: false,

		}


	];
	await InitDataTable('banner-table', banner_datatable_url, columns);

	const statusButton = document.querySelector('.status-action');
	if (statusButton) {
		statusButton.addEventListener('change', (element) => {
			bannerStatus($(element.target).attr('data-id'), $(element.target).attr('data-status'));
		});
	}

	const bannerStatus = async (id, status) => {
		if (confirm('Are You sure?')) {
			const fromData = { id: id, status: (status == active_status ? inactive_status : active_status) };
			let url = banner_status_change_url;
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




	$(document).on('click', '.banner-list-tab', async function () {
		let $this = $(this);
		let status = $this.attr('data-type');

		await InitDataTable('banner-table', banner_datatable_url, columns, status);
	})

})

$(document).on('submit', '#banner-form', function (e) {
	e.preventDefault();
	let myForm = document.getElementById('banner-form');
	let target = "banner-body";
	let formData = new FormData(myForm);
	let url = myForm?.getAttribute('action');
	let method = myForm?.getAttribute('method');

	AjaxFromLoginSubmit(formData, url, method, target)


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
			navigateTo('/banner-management');

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

