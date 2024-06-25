$(document).on('submit', '#walkthrough-screen-form', async function (e) {
	e.preventDefault();
	let myForm = document.getElementById('walkthrough-screen-form');
	let formData = new FormData(myForm);
	let url = myForm?.getAttribute('action');
	await AjaxFromSubmit('walkthrough-screen-form', formData, url, 'POST', false, '', '', '', false);

})


$(document).on('submit', '#walkthrough-video-form', async function (e) {
	e.preventDefault();
	let myForm = document.getElementById('walkthrough-video-form');
	let formData = new FormData(myForm);
	let url = myForm?.getAttribute('action');
	await AjaxFromSubmit('walkthrough-video-form', formData, url, 'POST', false, '', '', '', false);

})


$(document).on('click', '.walkthorugh-tab', async function () {
	let $this = $(this);
	let url = $this.attr('data-action');
	await urlNavigatorHistory(url);
	const options = {
		type: "GET",
		url: url,
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
			$('.walkthorugh-tab-inner').html(response?.data);
			await initDropZone();
		}

	} catch (error) {
		console.log(error);
	}
})