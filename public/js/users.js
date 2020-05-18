$(document).ready(function () {
    $(document).on('click', '.user-active', function () {
        var obj = $(this);
        obj.tooltip('hide');
        var state = obj.data('state').split('#');
        var active = state[0];
        var id_user = state[1];
        var parent = obj.parent();
        parent.html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: 'core/ajax/users.php',
            dataType: 'json',
            method: 'POST',
            data: {action: 'activate', active: active, id_user: id_user},
            success: function (d) {
                switch (active) {
                    case '0':
                        parent.html('<i class="fas fa-check text-success user-active" data-state="1#'+id_user+'" data-toggle="tooltip" data-placement="bottom" title="Activer ?"></i>');
                        break;
                    case '1':
                        parent.html('<i class="fas fa-times text-danger user-active" data-state="0#'+id_user+'" data-toggle="tooltip" data-placement="bottom" title="Désactiver ?"></i>');
                        break;
                }
                parent.children().tooltip('enable');
            }
        });
    });
});