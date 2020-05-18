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

    $(document).on('click', '.post-published', function () {
        var obj = $(this);
        obj.tooltip('hide');
        var state = obj.data('state').split('#');
        var published = state[0];
        var id_post = state[1];
        var parent = obj.parent();
        parent.html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: 'core/ajax/posts.php',
            dataType: 'json',
            method: 'POST',
            data: {action: 'activate', published: published, id_post: id_post},
            success: function (d) {
                switch (published) {
                    case '0':
                        parent.html('<i class="fas fa-check text-success post-published" data-state="1#'+id_post+'" data-toggle="tooltip" data-placement="bottom" title="Publié ?"></i>');
                        break;
                    case '1':
                        parent.html('<i class="fas fa-times text-danger post-published" data-state="0#'+id_post+'" data-toggle="tooltip" data-placement="bottom" title="Dépublié ?"></i>');
                        break;
                }
                parent.children().tooltip('enable');
            }
        });
    });
});