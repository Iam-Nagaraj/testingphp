$(document).ready(function() {
	let columns = [
		{ data: 'id', name: 'id', orderable: false, searchable: false },
		{ data: 'image', name: 'image', orderable: false, searchable: false },
		{ data: 'title', name: 'title', orderable: false, searchable: false },
		{ data: 'action', name: 'action', orderable: false, searchable: false },
		// { data: 'tag', name: 'tag', orderable: false, searchable: false },


	];
	InitDataTable('design-table', design_datatable_url, columns);

	$(document).on('submit', '#design-add-from', function(e) {
		e.preventDefault();
		let myForm = document.getElementById('design-add-from');
		let formData = new FormData(myForm);
		
		let url = myForm?.getAttribute('action');
		AjaxFromSubmit('design-add-from',formData, url, 'POST',false,'design-table',design_datatable_url,columns);

	})

	$(document).on('submit', '#design-update-from', function(e) {
		e.preventDefault();
		let myForm = document.getElementById('design-update-from');
		let formData = new FormData(myForm);
		let url = myForm?.getAttribute('action');
		AjaxFromSubmit('design-add-from',formData, url, 'POST',false,'design-table',design_datatable_url,columns);

	});


	$('#tagItemModal').on('shown.bs.modal', function() {
		$(document).off('focusin.modal');
	});
	window.tagImage = function(event) {
		event.preventDefault();
		var x = event.offsetX;
		var y = event.offsetY;
		var postId = $("#imageToTag").attr('data-id');
		const tagInput = document.getElementById('tagInput');

		tagInput.style.left = x + 'px';
		tagInput.style.top = y + 'px';
		tagInput.style.display = 'inline';
		tagInput.value = ''; // Clear any previous input

		tagInput.focus();
		tagInput.addEventListener('blur', function() {
			// Hide the input field when it loses focus (e.g., user clicks outside)
			tagInput.style.display = 'none';

			const tagName = tagInput.value.trim();
			if (tagName != '' && tagName != 'undefined') {
				createTag(x, y, postId, tagName);

			}


		});
		//  var tagName = prompt('Enter the tag name:');		
		//createTag(x, y, postId, tagName);

		/*Swal.fire({
			title: 'Enter tag name',
			html: "<input type='text'>",
			showCancelButton: true,
			confirmButtonText: 'Submit',
			showLoaderOnConfirm: true,
		}).then((result) => {
			if (result.value) {
				createTag(x, y, postId, result.value);
			}
		})*/

	}


	$(document).on('click', '.tag-action', function() {


		var url = $(this).attr('data-url');
		var target = $(this).attr('data-target');
		var designId = $(this).attr('data-id');
		$.ajax({
			type: "GET",
			url: url,
			success: function(response) {
				$('.' + target).html(response?.html);
				$('.' + target).closest('.modal').modal('show');
				initDropZone();
				renderTags(response.tag);
			}, error: function(error) {

			}
		})
	})


	function renderTags(tags) {
		const imageElem = document.getElementById('imageToTag');
		const imageContainer = imageElem.parentElement;

		console.log(imageContainer);

		tags.forEach(tag => {
			const tagElem = document.createElement('div');
			tagElem.className = 'tag';
			tagElem.style.left = tag.x_cordinate + 'px';
			tagElem.style.top = tag.y_cordinate + 'px';
			tagElem.title = tag.tag_name;
			const editButton = document.createElement('button');
			editButton.className = 'edit-button';
			editButton.innerText = 'Edit';
			editButton.addEventListener('click', function() {
				const tagInput = document.getElementById('tagInput');
				tagInput.style.left = tag.x_cordinate + 'px';
				tagInput.style.top = tag.y_cordinate + 'px';
				tagInput.style.display = 'inline';
				tagInput.value = tag.tag_name; // Clear any previous input

				tagInput.focus();
				tagInput.addEventListener('blur', function() {
					// Hide the input field when it loses focus (e.g., user clicks outside)
					tagInput.style.display = 'none';

					const tagName = tagInput.value.trim();
					if (tagName != '' && tagName != 'undefined') {
						createTag(x, y, postId, tagName);

					}


				});
				var postId = $("#imageToTag").attr('data-id');
				createTag(tag.x_cordinate, tag.y_cordinate, postId, tagName);
			});

			const deleteButton = document.createElement('button');
			deleteButton.className = 'delete-button';
			deleteButton.innerText = 'Delete';
			deleteButton.addEventListener('click', function() {
				// Implement delete functionality here
				// You can ask for confirmation and then delete the tag
				alert('Delete button clicked for tag: ' + tag.tag_name);
			});

			tagElem.appendChild(editButton);
			//tagElem.appendChild(deleteButton);

			imageContainer.appendChild(tagElem);
		});


	}

	function createTag(x, y, postId, tagName) {

		$.ajax({
			type: "POST",
			data: {
				x: x,
				y: y,
				tagName: tagName,
				post_id: postId
			},

			url: design_tag_items_url,
			headers: { Accept: "application/json", 'X-CSRF-TOKEN': csrf_token },
			success: function(response) {
				if (response.success == true) {
					alert(response.message);
                         renderTags(response.tags);
				} else {
					alert('There was a problem saving the tag.');

				}

			},
			beforeSend: function() {
				$(".loader_box").show();
			},
			complete: function() {
				$(".loader_box").hide();
			},
			error: function(response) {

				console.log(response);
			}
		});
	}

})
