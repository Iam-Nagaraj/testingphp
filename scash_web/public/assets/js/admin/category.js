$(document).ready(function () {
	let columns = [
		{ data: 'id', name: 'id', orderable: false, searchable: false },
		{ data: 'logo', name: 'logo', orderable: false, searchable: false },
		{ data: 'name', name: 'name', orderable: false, searchable: false },
        { data: 'parent', name: 'parent', orderable: false, searchable: false },
		{ data: 'action', name: 'action', orderable: false, searchable: false },

	];
	InitDataTable('category-table', category_datatable_url, columns);

	$(document).on('submit', '#category-add-from', function (e) {
		e.preventDefault();
		let myForm = document.getElementById('category-add-from');
		let formData = new FormData(myForm);
		let url = myForm?.getAttribute('action');
		AjaxFromSubmit('category-add-from',formData, url,'POST',false,'category-table',category_datatable_url,columns);

	})

    $(document).on('submit', '#category-update-from', function (e) {
		e.preventDefault();
		let myForm = document.getElementById('category-update-from');
		let formData = new FormData(myForm);
		let url = myForm?.getAttribute('action');
		AjaxFromSubmit('category-add-from',formData, url,'POST',false,'category-table',category_datatable_url,columns);

	})
})
