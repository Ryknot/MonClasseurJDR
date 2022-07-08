
$(document).ready(function()
{

//**************** debut : formulaire *******************
    //chargement de la page
    $("#champArea").hide();
    $("#champ_form_label").val('');
    $("#champ_form_valeurTexte").val('');
    $("#champ_form_valeurArea").val('');

    //choix type de champs visible
    $("#champ_form_typeChamp").change(function ()
        {
            if ($(this).val() == "text")
            {
                $("#champTexte").show();
                $("#champArea").hide();
            } else
            {
                $("#champArea").show();
                $("#champTexte").hide();
            }
        }
    );

//**************** fin : formulaire *******************


//******************* debut : champ ********************

    //suppression d'un champs
    $(".cliquableChamp").click(function()
        {
            //récupération des datas
            var id = $(this).data('champ');

            //modal
           $.confirm({
               title: "<span class='dead'><i class='mdi mdi-alert-outline'> </i> Supprimer</span>",
               content: 'Voulez vous supprimer le champ ?',
               buttons: {
                   Confirmer: function () {
                       window.location.href = "/public/champ/delete/" + id;
                   },
                   Annuler: function () {
                       console.log('annuler');
                   },
               }
           });

        }
    );

    //modification d'un champ
    $(".modifiable").click(function ()
        {
            //récupération des datas
            var count = parseInt($(this).data("count"));
            var id = $(this).data('champ');
            var valeurTexte = $(this).data('valeurtexte');
            var valeurArea = $(this).data('valeurarea');
            var page = $(this).data('page');

            //choix du type d'input (texte ou area)
            var typeChamp = "";
            if (valeurTexte !== ""){
                typeChamp = "texte";
                var oldValeur = valeurTexte;
                var input = "<input type='text' id='newValeur' value=''"+ oldValeur +">";
            }
            else if (valeurArea !== ""){
                typeChamp = "area";
                var oldValeur = valeurArea;
                var input = "<textarea id='newValeur'>"+ oldValeur +"</textarea>";
            }

            //modal
            $.confirm({
                title: "<span class='modifier'><i class='mdi mdi-file-edit-outline'> </i> Modifier</span>",
                content:
                    "<p>Ancienne valeur: " + oldValeur + "</p>" +
                    "<label>Nouvelle valeur: </label>" + input,
                buttons: {
                    Confirmer: function () {
                        var newValeur = $("#newValeur").val().replace(/\n/g, '¤');

                        if (newValeur === "" || newValeur == null || newValeur.length === 0){
                            newValeur = " texte vide !!";
                        }
                        //appel controller
                        window.location.href = "/public/champ/update/" + id + "/" + newValeur + "/" + page

                        //update du champ en ajax (en cours)
                        /*
                        $.ajax({
                            type:'POST',
                            url: '/public/ajax/champUpdateAjax.php',
                            data: {newValeur: newValeur, id: id, typeChamp: typeChamp},
                            success: function (){
                                //changement de la valeur affichée
                                $("#champ"+count).text(newValeur);
                            },
                            error: function (){
                                console.log("ajax erreur");
                            }
                        });
                         */
                    },
                    Annuler: function () {

                    },
                },
            });

        }
    );

    //changement de l'ordre des champs
    $(".buttonPrevious").click(function()
        {
            var champId = $(this).data('champ')
            window.location.href = "/public/champ/previous/" + champId;
        }
    );

    $(".buttonNext").click(function()
        {
            var champId = $(this).data('champ')
            window.location.href = "/public/champ/next/" + champId;
        }
    );


//************************ fin : champ ********************


//*****************debut : ressource **************

    //modification d'une valeur glissante d'une ressource (input)
        $(".inputPV").change(function()
            {
                //récupération des datas
                var count = parseInt($(this).data("count"));
                var id = parseInt($(this).data("id"));
                var valeurGlissante = parseInt($(this).val());
                var valeurMax = parseInt($(this).data("max"));

                //valeur et css progress-bar
                var progressBarValue = valeurGlissante*100/valeurMax;
                if(progressBarValue > 50)
                {
                    var cssBar = "bg-success";
                }
                else if(progressBarValue <= 50 && progressBarValue > 25)
                {
                    var cssBar = "bg-warning";
                }
                else if(progressBarValue <= 25 && progressBarValue > 0)
                {
                    var cssBar = "bg-danger";
                }
                else if(progressBarValue <= 0)
                {
                    progressBarValue = 100;
                    var cssBar = "bg-secondary progress-bar-striped";
                }

                //update de la ressource en ajax
                $.ajax({
                    type:'POST',
                    url: '/public/ajax/ressourceUpdateAjax.php',
                    data: {valeurGlissante: valeurGlissante, id: id},
                    success: function (){
                        //changement de la progress-bar
                        $('.progress-bar'+count)
                            .removeAttr('aria-valuenow')
                            .attr('aria-valuenow', valeurGlissante)
                            .removeAttr('style')
                            .attr('style', "width:"+progressBarValue+"%")
                            .removeClass('bg-success').removeClass('bg-warning').removeClass('bg-danger').removeClass('bg-secondary progress-bar-striped')
                            .addClass(cssBar)
                        ;
                    },
                    error: function (){
                        console.log("ajax controller erreur");
                    }
                });
            }
        );

    //modification de la rangeMax d'une ressource
    $(".modifiableRangeMax").click(function ()
        {
            //récupération des datas
            var count = parseInt($(this).data("count"));
            var id = parseInt($(this).data("id"));
            var valeurMax = parseInt($(this).data("rangemax"));
            var page = $(this).data('page');

            //modal
            $.confirm({
                title: "<span class='modifier'><i class='mdi mdi-file-edit-outline'> </i> Modifier</span>",
                content:
                    "<p>Ancienne valeur: " + valeurMax + "</p>" +
                    "<label>Nouvelle valeur: </label>" +
                    "<input type=\"number\" id=\"newRangeMax\">",
                buttons: {
                    Confirmer: function ()
                    {
                        var newRangeMax = $("#newRangeMax").val();
                        if (newRangeMax === "" || newRangeMax == null || newRangeMax.length === 0){
                            newRangeMax = valeurMax;
                        }
                        //appel controller
                        window.location.href = "/public/ressource/updateRangeMax/" + id + "/" + newRangeMax + "/" + page
                    },
                    Annuler: function ()
                    {

                    },
                },
            });
        }
    );

    //suppression d'une ressource
    $(".cliquableRessource").click(function()
        {
            //récupération des datas
            var id = $(this).data('ressource');

            //modal
            $.confirm({
                title: "<span class='dead'><i class='mdi mdi-alert-outline dead'> </i> Supprimer</span>",
                content: 'Voulez vous supprimer le champ ?',
                buttons: {
                    Confirmer: function () {
                        window.location.href = "/public/ressource/delete/" + id;
                    },
                    Annuler: function () {
                        console.log('annuler');
                    },
                }
            });

        });


//***************** fin : ressource **************

//**************** debut : fichePerso ************

    //suppression d'une fichePerso
    $("#btnDeleteFiche").click(function ()
    {
        //récupération des datas
        var id = $(this).data('fiche');

        //modal
        $.confirm({
            title: "<span class='dead'><i class='mdi mdi-alert-outline dead'> </i> Supprimer</span>",
            content: 'Voulez vous supprimer <span class="dead">définitivement</span> cette fiche ?',
            buttons: {
                Confirmer: function () {
                    window.location.href = "/public/fiche/" + id + "/delete" ;
                },
                Annuler: function () {
                    console.log('annuler');
                },
            }
        });
    });

//**************** debut : volet gauche ************

    //lancement de dé classique
    $(".btnDe").click(function ()
    {
        var de = $(this).val();

        $.ajax({
            type:'POST',
            url: '/public/ajax/lancerDe.php',
            data: {de: de},
            success: function (response){
                //changement de la valeur affichée + animation couleur
                $("#scoreDe").text(response).css({background: 'green', color: 'white'}).delay(1000).queue(function (){
                    $(this).css({
                        background: 'none',
                        color: 'black',
                        transition: '2s',
                    });
                    $(this).dequeue();
                });
                $('#btnDe'+de).css({background: 'green', color: 'white'}).delay(1000).queue(function (){
                    $(this).css({
                        background: 'none',
                        color: 'black',
                        transition: '2s',
                    });
                    $(this).dequeue();
                });
            },
            error: function (){
                $("#scoreDe").css({background: 'red', color: 'white'}).delay(1000).queue(function (){
                    $(this).css({
                        background: 'none',
                        color: 'black',
                        transition: '2s',
                    });
                    $(this).dequeue();
                });
            }
        });

    });

    //lancement de dé choisi
    $("#btnDeInput").click(function ()
    {
        var de = $("#inputDe").val();

        if (de.length === 0 || de < 1 || de > 1000){
            //ajouter un effet rouge
            $("#scoreDe").text(0).css({background: 'red', color: 'white'}).delay(1000).queue(function (){
                $(this).css({
                    background: 'none',
                    color: 'black',
                    transition: '2s',
                });
                $(this).dequeue();
            });
        }else{
            $.ajax({
                type:'POST',
                url: '/public/ajax/lancerDe.php',
                data: {de: de},
                success: function (response){
                    //changement de la valeur affichée + animation couleur
                    $("#scoreDe").text(response).css({background: 'green', color: 'white'}).delay(1000).queue(function (){
                        $(this).css({
                            background: 'none',
                            color: 'black',
                            transition: '2s',
                        });
                        $(this).dequeue();
                    });

                },
                error: function (){
                    $("#scoreDe").text(0).css({background: 'red', color: 'white'}).delay(1000).queue(function (){
                        $(this).css({
                            background: 'none',
                            color: 'black',
                            transition: '2s',
                        });
                        $(this).dequeue();
                    });
                }
            });
        }

    });



//fin document Ready
})



