$(document).on('submit', '#privacy-policy-form', function (e) {
    e.preventDefault();
    let myForm = document.getElementById('privacy-policy-form');
    let formData = new FormData(myForm);
    let url = myForm?.getAttribute('action');
    AjaxFromSubmit('privacy-policy-form',formData, url,'POST',false,'','','',false);

})


$(document).on('submit', '#term-condtion-form', function (e) {
    e.preventDefault();
    let myForm = document.getElementById('term-condtion-form');
    let formData = new FormData(myForm);
    let url = myForm?.getAttribute('action');
    AjaxFromSubmit('term-condtion-form',formData, url,'POST',false,'','','',false);

})


