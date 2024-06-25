
$(document).ready(async function () {
	let columns = [
		{ title: 'Notification', data: 'message' }
	];
	await InitDataTable('notification-table', notification_datatable_url, columns);

	const statusButton = document.querySelector('.status-action');
	if (statusButton) {
		statusButton.addEventListener('change', (element) => {
			notificationStatus($(element.target).attr('data-id'), $(element.target).attr('data-status'));
		});
	}

	$(document).on('click', '.notification-list-tab', async function () {
		let $this = $(this);
		let status = $this.attr('data-type');
		await InitDataTable('notification-table', notification_datatable_url, columns, status);
	})

})
