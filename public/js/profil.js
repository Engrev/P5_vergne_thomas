$(document).ready(function () {
    $(document).on('click', '#btn-delete', function (e) {
        var obj = $(this);
        e.preventDefault();
        $.confirm({
            icon: 'fas fa-exclamation-triangle',
            title: 'Suppression du compte',
            content: 'Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible !<br>Toutes les informations liées à votre compte seront supprimées.',
            columnClass: 'medium',
            type: 'red',
            buttons: {
                cancel: {
                    text: 'Non',
                    btnClass: 'btn-default'
                },
                confirm: {
                    text: 'Oui',
                    btnClass: 'btn-red',
                    action: function() {
                        obj.parent().submit();
                    }
                }
            }
        });
    });
});