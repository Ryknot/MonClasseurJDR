$(document).ready(function()
{

//---------------------------------------------DELETE------------------------------------------

    //Admin/userList -> delete user (confirmation modal)
    $('.deleteUser').click(function () {
        var id = $(this).data('user');
        var email = $(this).data('email');

        //modal
        $.confirm({
            title: "<span class='dead'><i class='mdi mdi-alert-outline'> </i> Supprimer</span>",
            content: "Voulez vous supprimer définitivement le user <span class='dead'>" + email + "</span> ?",
            buttons: {
                Confirmer: function () {
                    window.location.href = "http://localhost/projet_perso/public/admin/user/"+id+"/delete";
                },
                Annuler: function () {
                },
            }
        });
    });

    //Admin/log -> delete log (confirmation modal)
    $('.deleteLog').click(function () {
        var id = $(this).data('log');

        //modal
        $.confirm({
            title: "<span class='dead'><i class='mdi mdi-alert-outline'> </i> Supprimer</span>",
            content: "Voulez vous supprimer définitivement ce log ?",
            buttons: {
                Confirmer: function () {
                    window.location.href = "http://localhost/projet_perso/public/admin/log/"+id+"/delete";
                },
                Annuler: function () {
                },
            }
        });
    });

    //Admin/log -> delete log (confirmation modal)
    $('.deleteMessage').click(function () {
        var id = $(this).data('message');

        //modal
        $.confirm({
            title: "<span class='dead'><i class='mdi mdi-alert-outline'> </i> Supprimer</span>",
            content: "Voulez vous supprimer définitivement ce message ?",
            buttons: {
                Confirmer: function () {
                    window.location.href = "http://localhost/projet_perso/public/admin/message/"+id+"/delete";
                },
                Annuler: function () {
                },
            }
        });
    });

//---------------------------------------------VALIDATED USER------------------------------------------

    //Admin/listUser -> validated/unvalidated user (confirmation modal)
    $('.validatedUser').click(function () {
        var id = $(this).data('user');
        var validated = $(this).data('validated');

        if (validated === 1)
        {
            //modal
            $.confirm({
                title: "<span class='dead'><i class='mdi mdi-alert-outline'> </i> Annulation de la validité du compte</span>",
                content: "Voulez vous annuler la validité de ce user?",
                buttons: {
                    Confirmer: function () {
                        window.location.href = "http://localhost/projet_perso/public/admin/user/"+id+"/unvalidated";
                    },
                    Annuler: function () {
                    },
                }
            });
        }
        else if (validated === "")
        {
            //modal
            $.confirm({
                title: "<span class='modifier'><i class='mdi mdi-alert-outline'> </i> Validation du compte</span>",
                content: "Voulez vous valider le compte de ce user?",
                buttons: {
                    Confirmer: function () {
                        window.location.href = "http://localhost/projet_perso/public/admin/user/"+id+"/validated";
                    },
                    Annuler: function () {
                    },
                }
            });
        }

    });

//---------------------------------------------Active USER------------------------------------------

    //Admin/listUser -> active/desactive user (confirmation modal)
    $('.activeUser').click(function () {
        var id = $(this).data('user');
        var active = $(this).data('active');

        if (active === 1)
        {
            //modal
            $.confirm({
                title: "<span class='dead'><i class='mdi mdi-alert-outline'> </i> Désactivation du compte</span>",
                content: "Voulez vous désactiver le compte de ce user?",
                buttons: {
                    Confirmer: function () {
                        window.location.href = "http://localhost/projet_perso/public/admin/user/"+id+"/desactive";
                    },
                    Annuler: function () {
                    },
                }
            });
        }
        else if (active === "")
        {
            //modal
            $.confirm({
                title: "<span class='modifier'><i class='mdi mdi-alert-outline'> </i> Activation du compte</span>",
                content: "Voulez vous activer le compte de ce user?",
                buttons: {
                    Confirmer: function () {
                        window.location.href = "http://localhost/projet_perso/public/admin/user/"+id+"/active";
                    },
                    Annuler: function () {
                    },
                }
            });
        }
    });

//--------------------------------------------- MAIL ------------------------------------------
    //Admin/listUser -> envoi mail code validation (confirmation modal)
        $('.mailCodeValidation').click(function () {
            var id = $(this).data('user');

            //modal
            $.confirm({
                title: "<span class='modifier'><i class='mdi mdi-alert-outline'> </i> Envoi mail code validation</span>",
                content: "Voulez vous envoyer un nouveau code de validation pour ce user ?",
                buttons: {
                    Confirmer: function () {
                        window.location.href = "http://localhost/projet_perso/public/admin/user/"+id+"/mailCodeValidation";
                    },
                    Annuler: function () {
                    },
                }
            });
         });


})