
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
			title: 'Type', data: null,
			orderable: false,
			render: (data, type, row, meta) => {
                if(data.type == 2){
                    return 'Cashback';
                } else {
                    return 'Wallet';
                }

			}
		},
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
        {
			title: 'Action', data: null,
			orderable: false,
			render: (data, type, row, meta) => {
                let uuid = data.sender ? data.sender.uuid:'';
                let userName = data.sender ? data.sender.first_name+' '+data.sender.last_name : data.id;
                    return `
                        <button type="button" onClick="payUser('`+uuid+`', '`+userName+`')" >Refund</button>
                    `;
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



var ctx = document.getElementById("chart-bars").getContext("2d");

new Chart(ctx, {
    type: "bar",
    data: {
        labels: ["M", "T", "W", "T", "F", "S", "S"],
        datasets: [{
            label: "Sales",
            tension: 0.4,
            borderWidth: 0,
            borderRadius: 4,
            borderSkipped: false,
            backgroundColor: "rgba(255, 255, 255, .8)",
            data: [50, 20, 10, 22, 50, 10, 40],
            maxBarThickness: 6
        }, ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false,
            }
        },
        interaction: {
            intersect: false,
            mode: 'index',
        },
        scales: {
            y: {
                grid: {
                    drawBorder: false,
                    display: true,
                    drawOnChartArea: true,
                    drawTicks: false,
                    borderDash: [5, 5],
                    color: 'rgba(255, 255, 255, .2)'
                },
                ticks: {
                    suggestedMin: 0,
                    suggestedMax: 500,
                    beginAtZero: true,
                    padding: 10,
                    font: {
                        size: 14,
                        weight: 300,
                        family: "Roboto",
                        style: 'normal',
                        lineHeight: 2
                    },
                    color: "#fff"
                },
            },
            x: {
                grid: {
                    drawBorder: false,
                    display: true,
                    drawOnChartArea: true,
                    drawTicks: false,
                    borderDash: [5, 5],
                    color: 'rgba(255, 255, 255, .2)'
                },
                ticks: {
                    display: true,
                    color: '#f8f9fa',
                    padding: 10,
                    font: {
                        size: 14,
                        weight: 300,
                        family: "Roboto",
                        style: 'normal',
                        lineHeight: 2
                    },
                }
            },
        },
    },
});


var ctx2 = document.getElementById("chart-line").getContext("2d");

new Chart(ctx2, {
    type: "line",
    data: {
        labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
            label: "Mobile apps",
            tension: 0,
            borderWidth: 0,
            pointRadius: 5,
            pointBackgroundColor: "rgba(255, 255, 255, .8)",
            pointBorderColor: "transparent",
            borderColor: "rgba(255, 255, 255, .8)",
            borderColor: "rgba(255, 255, 255, .8)",
            borderWidth: 4,
            backgroundColor: "transparent",
            fill: true,
            data: [50, 40, 300, 320, 500, 350, 200, 230, 500],
            maxBarThickness: 6

        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false,
            }
        },
        interaction: {
            intersect: false,
            mode: 'index',
        },
        scales: {
            y: {
                grid: {
                    drawBorder: false,
                    display: true,
                    drawOnChartArea: true,
                    drawTicks: false,
                    borderDash: [5, 5],
                    color: 'rgba(255, 255, 255, .2)'
                },
                ticks: {
                    display: true,
                    color: '#f8f9fa',
                    padding: 10,
                    font: {
                        size: 14,
                        weight: 300,
                        family: "Roboto",
                        style: 'normal',
                        lineHeight: 2
                    },
                }
            },
            x: {
                grid: {
                    drawBorder: false,
                    display: false,
                    drawOnChartArea: false,
                    drawTicks: false,
                    borderDash: [5, 5]
                },
                ticks: {
                    display: true,
                    color: '#f8f9fa',
                    padding: 10,
                    font: {
                        size: 14,
                        weight: 300,
                        family: "Roboto",
                        style: 'normal',
                        lineHeight: 2
                    },
                }
            },
        },
    },
});

var ctx3 = document.getElementById("chart-line-tasks").getContext("2d");

new Chart(ctx3, {
    type: "line",
    data: {
        labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
            label: "Mobile apps",
            tension: 0,
            borderWidth: 0,
            pointRadius: 5,
            pointBackgroundColor: "rgba(255, 255, 255, .8)",
            pointBorderColor: "transparent",
            borderColor: "rgba(255, 255, 255, .8)",
            borderWidth: 4,
            backgroundColor: "transparent",
            fill: true,
            data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
            maxBarThickness: 6

        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false,
            }
        },
        interaction: {
            intersect: false,
            mode: 'index',
        },
        scales: {
            y: {
                grid: {
                    drawBorder: false,
                    display: true,
                    drawOnChartArea: true,
                    drawTicks: false,
                    borderDash: [5, 5],
                    color: 'rgba(255, 255, 255, .2)'
                },
                ticks: {
                    display: true,
                    padding: 10,
                    color: '#f8f9fa',
                    font: {
                        size: 14,
                        weight: 300,
                        family: "Roboto",
                        style: 'normal',
                        lineHeight: 2
                    },
                }
            },
            x: {
                grid: {
                    drawBorder: false,
                    display: false,
                    drawOnChartArea: false,
                    drawTicks: false,
                    borderDash: [5, 5]
                },
                ticks: {
                    display: true,
                    color: '#f8f9fa',
                    padding: 10,
                    font: {
                        size: 14,
                        weight: 300,
                        family: "Roboto",
                        style: 'normal',
                        lineHeight: 2
                    },
                }
            },
        },
    },
});
