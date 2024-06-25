$(document).ready(function () {
	let columns = [
		{ data: 'id', name: 'id', orderable: false, searchable: false },
		{ data: 'image', name: 'image', orderable: false, searchable: false },
		{ data: 'title', name: 'title', orderable: false, searchable: false },
		{ data: 'design', name: 'design', orderable: false, searchable: false },
		{ data: 'category', name: 'category', orderable: false, searchable: false },
		{ data: 'action', name: 'action', orderable: false, searchable: false },

	];
	InitDataTable('product-table', product_datatable_url, columns);

	$(document).on('submit', '#product-add-from', function (e) {
		e.preventDefault();
		let myForm = document.getElementById('product-add-from');
		let formData = new FormData(myForm);
		let url = myForm?.getAttribute('action');
		AjaxFromSubmit('product-add-from',formData, url,'POST',false,'product-table',product_datatable_url,columns);

	})

    $(document).on('submit', '#product-update-from', function (e) {
		e.preventDefault();
		let myForm = document.getElementById('product-update-from');
		let formData = new FormData(myForm);
		let url = myForm?.getAttribute('action');
		AjaxFromSubmit('product-add-from',formData, url,'POST',false,'product-table',product_datatable_url,columns);

	})
})