$(document).ready(function () {


    $(document).on('submit', '#design-render-form', function (e) {
        e.preventDefault();
        let myForm = document.getElementById('design-render-form');
        let formData = new FormData(myForm);
        let url = myForm?.getAttribute('action');
        AjaxFromSubmitRenderDesign('design-render-form', formData, url, 'POST', false, 'none', '', {}, false);

    })


})
