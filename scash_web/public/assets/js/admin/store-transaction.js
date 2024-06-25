$(document).ready(async function () {
	$('.is-invalid').hide();
	let country_code_symbol = '+';
	let active_status = 1;
	let inactive_status = 2;
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
                return '$'+data.amount;
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
	await InitDataTable('store-table', store_transaction_datatable_url, columns);


	$(document).on('click', '.cashback-list-tab', async function () {
		let $this = $(this);
		let status = $this.attr('data-type');

		await InitDataTable('cashback-table', store_transaction_datatable_url, columns, status);
	})

});
