const configN = {
	toolbar: [
		"heading",
		"|",
		"undo",
		"redo",
		"|",
		"bold",
		"italic",
		"|",
		"bulletedList",
		"numberedList",
	],
};

ClassicEditor
	.create(document.querySelector('#ck_editor'), configN)
	.catch(error => {
		console.error(error);
	});
