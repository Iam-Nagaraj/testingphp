
$(document).ready(async function () {
	let columns = [
        {
			title: 'Transaction', data: null,
			orderable: false,
			render: (data, type, row, meta) => {
				if(data.from_user_id == transfer_data){
					return data.receiver ? data.receiver.first_name+' '+data.receiver.last_name : 'N/A';
				} else {
					return data.sender ? data.sender.first_name+' '+data.sender.last_name : 'N/A';
				}
			}
		},
		{ title: 'Date', data: 'transaction_date' },
		{
			title: 'Amount', data: null,
			orderable: false,
			render: (data, type, row, meta) => {
                if(data.from_user_id == transfer_data){
                    return '<p class="completed text-danger">-$'+data.amount+'</p>';
                } else {
                    return '<p class="completed text-success">+$'+data.amount+'</p>';
                }

			}
		},
        {
			title: 'Status', data: null,
			orderable: false,
			render: (data, type, row, meta) => {
				if(data.status == 0){
                    return '<p class="completed text-primary">Pending</p>';
                } else if(data.status == 1){
                    return '<p class="completed text-success">Completed</p>';
                } else if(data.status == 2){
                    return '<p class="completed text-danger">Fail</p>';
                } else if(data.status == 3){
                    return '<p class="completed text-warning">Cancelled</p>';
                }
                return '<p class="completed text-primary">Pending</p>';
			}
		},
	];
	await InitDataTable('transaction-table', transaction_datatable_url, columns);

	const statusButton = document.querySelector('.status-action');
	if (statusButton) {
		statusButton.addEventListener('change', (element) => {
			transactionStatus($(element.target).attr('data-id'), $(element.target).attr('data-status'));
		});
	}

	$(document).on('click', '.transaction-list-tab', async function () {
		let $this = $(this);
		let status = $this.attr('data-type');
		await InitDataTable('transaction-table', transaction_datatable_url, columns, status);
	})

})

function depositPopUp(account_id)
{
    $('#form_account_id').val(account_id);
    $('#depositModel').modal('show');
}

var bank_account_html = `
<div class="card-body col-md-12">
    <div class="table-responsive">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <input class="col-md-1" type="radio" value="account_id" name="destination_id" id="account_label">
                    <label class="card-text col-md-10" for="account_label_">bank_name <span>bank_status </span> </label>
                </div>
            </div>
        </div>
    </div>
</div>
`;

function withdrawPopUp(){

    $('#withdrawModel').modal('show');
    getBanks();

}

function getBanks() {
    $.ajaxSetup({
        headers : {
            'X-CSRF-Token' : $('meta[name="_token"]').attr('content')
        }
    });
    $.ajax({
        type: "post",
        url: bankListUrl,
        success: async function (response) {
            $('#bank_list_loader').remove();
			$("#bank_account_list").html("");
            var NewData = response.data;

            for (var key in NewData) {
                if (!isNaN(key)) {
                    var value = NewData[key];
                    if(value.type != 'balance'){
                        var bank_account_html1 = bank_account_html.replace("bank_name", value.bank_name);
                        var bank_account_html2 = bank_account_html1.replace("bank_status", value.status);
                        var bank_account_html3 = bank_account_html2.replace("bank_type", value.bankAccountType);
                        var bank_account_html4 = bank_account_html3.replace("account_label", "account_label"+key);
                        var bank_account_html5 = bank_account_html4.replace("account_id", value.bank_id);
                        var bank_account_html6 = bank_account_html5.replace("account_label_", "account_label"+key);
                        $("#bank_account_list").append(bank_account_html6);
                    } else {
                        wallet_id = value.bank_id
                    }
                }
            }

        }, error: async function (response) {
            $.NotificationApp.send("Error", response.responseJSON.message, "top-right", "#ff4c4c", "danger");
           

        }
    })
}

$(document).on('submit', '#withdraw-wallet-form', function (e) {
	e.preventDefault();
	let myForm = document.getElementById('withdraw-wallet-form');
	let target = "withdraw-wallet-body";
	let formData = new FormData(myForm);
	let url = myForm?.getAttribute('action');

	AjaxFromWithdrawSubmit(formData, url, 'POST', target)

});

async function AjaxFromWithdrawSubmit(formData, url, method, target) {
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
            $('.amount').val('');
            $('.pin').val('');
            $('#withdrawModel').modal('hide');
            $('#successModel').modal('show');


		}, error: async function (response) {
			if (response.status === 422) {
				$('.is-invalid').hide();
				let errors = response.responseJSON.errors;
				Object.keys(errors).forEach(function (key) {
					$("#_" + key).text(errors[key][0]);
					$("#_" + key).show();
				});

			}
			let errors = response.responseJSON
			if (errors?.errors?.error) {
				$(".show_all_error.invalid-feedback").show();
				$(".show_all_error.invalid-feedback").text(errors?.message);
			}
			if(response.responseJSON.errors == true){
				if (response.responseJSON.message) {
					$("#_all_errors").text(response.responseJSON.message);
					$("#_all_errors").show();
				}
			}
			$(".loader_box").hide();
			return response;
		}
	})
}


$(document).on('submit', '#deposit-wallet-form', function (e) {
	e.preventDefault();
	let myForm = document.getElementById('deposit-wallet-form');
	let target = "deposit-wallet-body";
	let formData = new FormData(myForm);
	let url = myForm?.getAttribute('action');

	AjaxFromDepositSubmit(formData, url, 'POST', target)

});

async function AjaxFromDepositSubmit(formData, url, method, target) {
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
			// await new Promise(resolve => setTimeout(resolve, 2500));
			// navigateTo('/bank');
			$(".loader_box").hide();
            $('#form_account_id').val('');
			$('.amount').val('');
			$('.pin').val('');
            $('#depositModel').modal('hide');
            $('#withdrawModel').modal('hide');
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

