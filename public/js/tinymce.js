$(document).ready(function () {
    if ($('textarea.mce').length) {
        tinymce.init({
            selector: 'textarea.mce',
            language: 'fr_FR',
            branding: false,
            height: 500,
            plugins: 'lists,quickbars,image,imagetools,media,link,code,emoticons',
            toolbar: 'undo redo copy cut paste | fontsizeselect forecolor backcolor | bold italic underline | alignleft aligncenter alignright alignjustify bullist numlist | quickimage editimage media | link code emoticons'
        });
    }
});