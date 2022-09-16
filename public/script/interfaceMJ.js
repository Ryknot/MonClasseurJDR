$(document).ready(function()
{

//--------------- Creation CarteMJ -----------

    //changement du fond de la carte suivant PNJ ou bestiaire
    $("#carte_mj_form_type").change(function (){
        if ($(this).val() === "PNJ"){
            $("#apercuCard").removeClass('bestiaire').addClass('pnj');
        }
        else if ($(this).val() === "bestiaire"){
            $("#apercuCard").removeClass('pnj').addClass('bestiaire');
        }
    });

    //affichage du nom dans l'apercu de la carte
    $("#carte_mj_form_nom").on("input", function (){
        var nom = $(this).val();
        $('#nomCarteMj').text(nom);
    });

    //affichage de l'image dans l'apercu de la carte
    $("#carte_mj_form_image").change(function (){
        var img = $(this).get(0).files;
        if (img){
            var reader = new FileReader();
            reader.addEventListener("load", function (){
                $('#imgCarteMJ').removeAttr("src").attr("src",reader.result);
            });
            reader.readAsDataURL(img[0]);
        }
    });

    //affichage des PV dans l'apercu de la carte
    $("#carte_mj_form_PV").on("input", function (){
        var pv = $(this).val();
        $("#rangeCarteMJ").removeAttr('max').attr('max', pv).removeAttr('value').attr('value',pv);
        $("#pvCarteMj").text(pv + " / " + pv);
    });

    //affichage de la note dans l'apercu de la carte
    $("#carte_mj_form_note").on("input", function (){
        var note = $(this).val();
        $("#noteCarteMj").html(note.replace(/\n/g, '<br />'));
    });


//----------- plateau MJ --------------

    $(".onBoard").ready(function() {

        //effacer une carte du Board
        $('.deleteCarteMJ').click(function () {
            var countcarte = $(this).data('count');
            var carteId = $(".infoCarte" + countcarte).data('carteid');
            var page = $(".infoCarte" + countcarte).data('page');

            //check pour éviter la duplication de la modal entre les cartes onBoard et celle de la page listAll
            if (page === "onBoard") {
                 //mise a jour de la quantité onBoard de la carteMJ
                var urlController = '/CarteMJ/'+carteId+'/deleteOnBoard';
                $.ajax({
                    type: 'GET',
                    url: urlController,
                    success: function () {
                        //mettre un message success sur la vue.
                    },
                    error: function () {
                        //mettre un message error sur la vue.
                    }
                });
                $("#CarteMJ" + countcarte).remove();
            }
        });

        //icone + css quand pv = 0
        $('.pvCarteMj').click(function () {
            var countcarte = $(this).data('count');
            var pv = parseInt($(this).val());

            $(this).removeAttr('value').attr('value', pv);
            if (pv == 0) {
                $('.mdiCarte' + countcarte).addClass('mdi mdi-skull').addClass('dead');
            } else {
                $('.mdiCarte' + countcarte).removeClass('mdi mdi-skull').removeClass('dead');
            }
        });

        //icone pvMoins
        $('.pvMoinsCarteMJ').click(function () {
            var countcarte = $(this).data('count');
            var pv = $("#rangeCarteMJ"+countcarte).val() - 1;
            if(pv >= 0){
                if (pv == 0) {
                    $('.mdiCarte' + countcarte).addClass('mdi mdi-skull').addClass('dead');
                    $('#rangeCarteMJ' + countcarte).removeAttr('value').attr('value', pv);
                    $('.pvValue' + countcarte).text(pv);
                } else {
                    $('.mdiCarte' + countcarte).removeClass('mdi mdi-skull').removeClass('dead');
                    $('#rangeCarteMJ' + countcarte).removeAttr('value').attr('value', pv);
                    $('.pvValue' + countcarte).text(pv);
                }
            }

        });
        //icone pvPlus
        $('.pvPlusCarteMJ').click(function () {
            var countcarte = $(this).data('count');
            var pv = parseInt($("#rangeCarteMJ"+countcarte).val()) + 1;
            var pvMax = $('#rangeCarteMJ' + countcarte).data('pvmax');
            if(pv <= pvMax) {
                if (pv == pvMax) {
                    $('#rangeCarteMJ' + countcarte).removeAttr('value').attr('value', pv);
                    $('.pvValue' + countcarte).text(pv);
                } else {
                    $('.mdiCarte' + countcarte).removeClass('mdi mdi-skull').removeClass('dead');
                    $('#rangeCarteMJ' + countcarte).removeAttr('value').attr('value', pv);
                    $('.pvValue' + countcarte).text(pv);
                }
            }
        });

        //ajouter une carte sur le plateau
        count = $("#countCarte").data('carteonboard'); //nombre de carte déjà sur le plateau au chargement

        $("#btnAddCarteMJ").click(function () {
            if ($("#addCarteMJ").find(':selected').data("count") != 0) {
                count = count + 1;
                var nom = $("#addCarteMJ").val();
                var image = $("#addCarteMJ").find(':selected').data("img");
                var pv = $("#addCarteMJ").find(':selected').data("pv");
                var note = $("#addCarteMJ").find(':selected').data("note").replace(/\n/g, '<br />');
                var type = $("#addCarteMJ").find(':selected').data("type");
                var userId = $("#addCarteMJ").find(':selected').data("userid");
                var carteId = $("#addCarteMJ").find(':selected').data("carteid");

                var carte =
                    "<div id='CarteMJ" + count + "' class='card-content p-2 mt-2 cardTypeInfo col-xl-3 col-lg-4 col-md-6 col-sm-6 onBoard CardCarteMJ " + type + "'>" +
                        "<div class='col m-2 cardChamp'><i class='mdiCarte" + count + "'><span class='nomCarteMj'>" + nom + "</span></i></div>" +
                        "<div class='col m-2 text-center cardChamp champImageCarteMJ'><img class=\"miniature\" src=\"{{ asset('uploads/'" + image + ")}}\"></div>" +
                        "<div class='col py-1 m-2 text-center cardChamp' style='height: 40px'>" +
                            "<span className='pvCarteMj'>"+
                                "<input id='pvValueCarteMJ" + count + "' class='pvCarteMj col-4' data-Count='" + count + "' data-Pvmax='" + pv + "' type='number' min='0' max='" + pv + "' value='" + pv + "'>" +
                                "/" + pv + "</span>" +
                        "</div>" +
                        "<div id='noteCarteMj' class='col m-2 cardChamp'><span class='noteCarteMj'>" + note + "</span></div>" +
                        "<div class='m-4'></div>" +
                        "<div class='col m-2 px-4 d-flex justify-content-between cardChamp'>" +
                            "<i class='deleteNewCarteMJ mdi mdi-delete' data-Count='" + count + "'></i>" +
                            "<i class='updateNewCarteMJ mdi mdi-application-edit' data-Count='" + count + "'></i>" +
                        "</div>" +
                        "<div class='infoCarte" + count + "' data-Userid='" + userId + "' data-Carteid='" + carteId + "' data-Page='new'></div>" +
                    "</div>";

                $("#insertCard").append(carte);


                //mise a jour de la quantité onBoard de la carteMJ
                var urlController = '/CarteMJ/'+carteId+'/addOnBoard';
                $.ajax({
                    type: 'GET',
                    url: urlController,
                    success: function () {
                        //mettre un message success sur la vue.
                    },
                    error: function () {
                        //mettre un message error sur la vue.
                    }
                });

                //rechargement du board pour inclure la nouvelle carte
                $(".onBoard").ready(function () {

                    //icone + css quand pv = 0
                    $('.pvCarteMj').change(function () {
                        var countcarte = $(this).data('count');
                        var pv = $(this).val();

                        $(this).removeAttr('value').attr('value', pv);
                        if (pv == 0) {
                            $('.mdiCarte' + countcarte).addClass('mdi mdi-skull').addClass('dead');
                        } else {
                            $('.mdiCarte' + countcarte).removeClass('mdi mdi-skull').removeClass('dead');
                        }
                    });

                    //effacer une nouvelle carte
                    $('.deleteNewCarteMJ').click(function () {
                        var countcarte = $(this).data('count');
                        var carteid = $(".infoCarte" + countcarte).data('carteid');

                        //mise a jour de la quantité onBoard de la carteMJ
                        var urlController = '/CarteMJ/'+carteId+'/deleteOnBoard';
                        $.ajax({
                            type: 'GET',
                            url: urlController,
                            success: function () {
                            },
                            error: function () {
                            }
                        })
                        $("#CarteMJ" + countcarte).remove();
                    });



                    //modifier une nouvelle carte
                    $('.updateNewCarteMJ').click(function () {
                        var countcarte = $(this).data('count');
                        var carteid = $(".infoCarte" + countcarte).data('carteid');

                        //modal
                        $.confirm({
                            title: "<span class='modifier'><i class='mdi mdi-file-edit-outline'> </i> Modifier</span>",
                            content: 'Voulez vous modifier la carte ?',
                            buttons: {
                                Confirmer: function () {
                                    window.location.href = "/CarteMJ/" + carteid + "/update";
                                },
                                Annuler: function () {
                                },
                            }
                        });
                    });

                    //icone pvMoins
                    $('.pvMoinsCarteMJ').click(function () {
                        var countcarte = $(this).data('count');
                        var pv = $("#rangeCarteMJ"+countcarte).val() - 1;
                        if(pv >= 0){
                            if (pv == 0) {
                                $('.mdiCarte' + countcarte).addClass('mdi mdi-skull').addClass('dead');
                                $('#rangeCarteMJ' + countcarte).removeAttr('value').attr('value', pv);;
                                $('.pvValue' + countcarte).text(pv);
                            } else {
                                $('.mdiCarte' + countcarte).removeClass('mdi mdi-skull').removeClass('dead');
                                $('#rangeCarteMJ' + countcarte).removeAttr('value').attr('value', pv);
                                $('.pvValue' + countcarte).text(pv);
                            }
                        }

                    });
                    //icone pvPlus
                    $('.pvPlusCarteMJ').click(function () {
                        var countcarte = $(this).data('count');
                        var pv = parseInt($("#rangeCarteMJ"+countcarte).val()) + 1;
                        var pvMax = $('#rangeCarteMJ' + countcarte).data('pvmax');
                        if(pv <= pvMax) {
                            if (pv == pvMax) {
                                $('#rangeCarteMJ' + countcarte).removeAttr('value').attr('value', pv);
                                $('.pvValue' + countcarte).text(pv);
                            } else {
                                $('.mdiCarte' + countcarte).removeClass('mdi mdi-skull').removeClass('dead');
                                $('#rangeCarteMJ' + countcarte).removeAttr('value').attr('value', pv);
                                $('.pvValue' + countcarte).text(pv);
                            }
                        }
                    });

                }); //FIN rechargement du board

            }
        });



    //----------- liste des cartesMJ --------------

        //suppression définitive de la carteMJ
        $('.deleteCarteMJ').click(function () {
            var countcarte = $(this).data('count');
            var carteid = $(".infoCarte" + countcarte).data('carteid');
            var page = $(".infoCarte" + countcarte).data('page');

            //check pour ne pas supprimer une carte définitivement en dehors de la page listAll
            if (page === "listAll") {
                //modal
                $.confirm({
                    title: "<span class='dead'><i class='mdi mdi-alert-outline'> </i> Supprimer</span>",
                    content: 'Voulez vous supprimer <span class=\'dead\'>définitivement</span> la carte ?',
                    buttons: {
                        Confirmer: function () {
                            window.location.href = "/CarteMJ/"+carteid+"/delete";
                        },
                        Annuler: function () {
                        },
                    }
                });
            }
        });

        //modifier une carte onBoard ou dans la page listAll
        $('.updateCarteMJ').click(function (){
            var countcarte = $(this).data('count');
            var carteid = $(".infoCarte"+countcarte).data('carteid');
            var page = $(".infoCarte" + countcarte).data('page');

            if (page != "new") {
                //modal
                $.confirm({
                    title: "<span class='modifier'><i class='mdi mdi-file-edit-outline'> </i> Modifier</span>",
                    content: 'Voulez vous modifier la carte ?',
                    buttons: {
                        Confirmer: function () {
                            window.location.href = "/CarteMJ/" + carteid + "/update";
                        },
                        Annuler: function () {
                        },
                    }
                });
            }
        });

    });

})