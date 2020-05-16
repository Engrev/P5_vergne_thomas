$(document).ready(function () {
    $(document).on('click', '.link-delete', function (e) {
        var href = $(this).attr('href');
        var parent = $(this).closest('table').attr('id');
        var content;
        switch (parent) {
            case 'posts':
                content = 'Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible !<br>Tous les commentaires liés à cet article seront supprimées.';
                break;
            case 'categories':
                content = 'Êtes-vous sûr de vouloir supprimer cette catégorie ? Cette action est irréversible !<br>Tous les articles présents dans cette catégorie seront déplacés.';
                break;
        }
        e.preventDefault();
        $.confirm({
            icon: 'fas fa-exclamation-triangle',
            title: 'Suppression du compte',
            content: content,
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
                        $(location).attr('href', href);
                    }
                }
            }
        });
    });
});