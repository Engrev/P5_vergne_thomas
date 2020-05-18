$(document).ready(function () {
    $(document).on('click', '#generate-password', function () {
        var obj = $(this);
        $('#password, #password_confirm').val('');
        obj.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '../core/ajax/users.php',
            dataType: 'json',
            method: 'POST',
            data: {action: 'password_generator'},
            success: function (d) {
                $('#password, #password_confirm').val(d.retour);
                obj.prop('disabled', false).html('<i class="fas fa-key"></i> Générer un mot de passe');
            }
        });
    });
});