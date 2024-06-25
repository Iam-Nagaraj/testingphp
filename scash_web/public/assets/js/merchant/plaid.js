var wallet_id = '39e99075-6dd0-4fa1-a1cf-c1c928a05663';
var handler = Plaid.create({
    token: link_token,
    onSuccess: function (publicToken, metadata) {
	    $(".loader_box").show();

        // Handle the success event, e.g., send the publicToken to your server
        $.ajaxSetup({
            headers : {
                'X-CSRF-Token' : $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            type: "post",
            url: token_url,
            data: {
                'publicToken' : publicToken
            },
            success: async function (response) {
                var NewData = response.data;

                for (var key in NewData) {
                    if (!isNaN(key)) {
                        var value = NewData[key];
                        var account_card_html1 = account_card_html.replace("bank_name", value.user_account_name);
                        var account_card_html2 = account_card_html1.replace("0000", value.mask);
                        var account_card_html3 = account_card_html2.replace("account_id", value.account_id);
                        var account_card_html4 = account_card_html3.replace("access_token", response.data.access_token);
                        var account_card_html5 = account_card_html4.replace("mask", value.user_account_name);
                        var account_card_html6 = account_card_html5.replace("account_id", value.account_id);
                        $("#account_list").append(account_card_html6);
                    }
                }

                $(".loader_box").hide();
    
            }, error: async function (response) {
                $.NotificationApp.send("Error", response.responseJSON.message, "top-right", "#ff4c4c", "danger");
                $(".loader_box").hide();

            }
        })
    },
    onExit: function (response) {

        // Handle when the user exits Link, e.g., cancel or encounter an error
        console.log('Exit Event:', response.responseJSON.message);
        $.NotificationApp.send("Error", response.responseJSON.message, "top-right", "#ff4c4c", "danger");

    },
});

// Trigger Plaid Link when the button is clicked
document.getElementById('plaid-link-btn').onclick = function () {
    handler.open();
};

var account_card_html = `
<div class="card-body col-md-3" id="account_id">
    <div class="table-responsive">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Bank Account</h5>
                <p class="card-text">bank_name</p>
                <h6 class="card-text">#### #### #### 0000</h6>
            </div>
            <button class="btn btn-primary" onclick="connectToScash('account_id', 'access_token', 'mask')" >Connect</button>
        </div>
    </div>
</div>
`;

var bank_account_html = `
<div class="card-body col-md-3">
    <div class="table-responsive">
        <div class="card">
            <div class="card-body">
            <h5 class="card-title">Bank Account</h5>
            <p class="card-text">bank_name</p>
            <p class="card-text"><span>bank_status </span>| <strong>bank_type</strong></p>
            <small> Added added_time</small>
            </div>
            <button class="btn btn-success" onclick="depositPopUp('account_id')"><b>Deposit To Wallet</b></button>
        </div>
    </div>
</div>
`;

function connectToScash(account_id, access_token, mask)
{
    $(".loader_box").show();

    $.ajaxSetup({
        headers : {
            'X-CSRF-Token' : $('meta[name="_token"]').attr('content')
        }
    });
    $.ajax({
        type: "post",
        url: plaid_dwolla,
        data: {
            'account_id' : account_id,
            'access_token' : access_token,
            'mask' : mask
        },
        success: async function (response) {
            $("#"+account_id).remove();
            $(".loader_box").hide();
			$.NotificationApp.send("Success", "Successfully connected", "top-right", "#4cffc5", "success");

        }, error: async function (response) {
            $.NotificationApp.send("Error", response.responseJSON.message, "top-right", "#ff4c4c", "danger");
            $(".loader_box").hide();

        }
    })

}

setTimeout(getBanks, 2000);

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
            $('#bank_list_loader').hide();
            var NewData = response.data;

            for (var key in NewData) {
                if (!isNaN(key)) {
                    var value = NewData[key];
                    if(value.type != 'balance'){
                        var bank_account_html1 = bank_account_html.replace("bank_name", value.bank_name);
                        var bank_account_html2 = bank_account_html1.replace("bank_status", value.status);
                        var bank_account_html3 = bank_account_html2.replace("bank_type", value.bankAccountType);
                        var bank_account_html4 = bank_account_html3.replace("added_time", value.added);
                        var bank_account_html5 = bank_account_html4.replace("account_id", value.bank_id);
                        $("#bank_account_list").append(bank_account_html5);
                    } else {
                        wallet_id = value.bank_id
                    }
                }
            }

        }, error: async function (response) {
            $.NotificationApp.send("Error", response.responseJSON.message, "top-right", "#ff4c4c", "danger");
            // getBanks();

        }
    })
}

function depositPopUp(account_id)
{
    $('#form_account_id').val(account_id);
    $('#depositModel').modal('show');

}


$(document).on('submit', '#deposit-wallet-form', function (e) {
	e.preventDefault();
	let myForm = document.getElementById('deposit-wallet-form');
	let target = "deposit-wallet-body";
	let formData = new FormData(myForm);
	let url = myForm?.getAttribute('action');

	AjaxFromLoginSubmit(formData, url, 'POST', target)

});

async function AjaxFromLoginSubmit(formData, url, method, target) {
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
            $('#depositModel').modal('hide');
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

