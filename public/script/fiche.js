
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
            console.log(id);
            //modal
           $.confirm({
               title: 'Supprimer',
               content: 'Voulez vous supprimer le champ ?',
               buttons: {
                   Confirmer: function () {
                       window.location.href = "http://localhost/projet_perso/public/champ/delete/" + id;
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
                var input = "<input type='text' id='newValeur'>";
                var oldValeur = valeurTexte;
            }
            else if (valeurArea !== ""){
                typeChamp = "area";
                var input = "<textarea id='newValeur'></textarea>";
                var oldValeur = valeurArea;
            }

            //modal
            $.confirm({
                title: "<h3>Modification du champ ?</h3>",
                content:
                    "<p>Ancienne valeur: " + oldValeur + "</p>" +
                    "<label>Nouvelle valeur: </label>" + input,
                buttons: {
                    Confirmer: function () {
                        var newValeur = $("#newValeur").val();
                        if (newValeur === "" || newValeur == null || newValeur.length === 0){
                            newValeur = " texte vide !!";
                        }
                        //appel controller
                        window.location.href = "http://localhost/projet_perso/public/champ/update/" + id + "/" + newValeur + "/" + page

                        //update du champ en ajax (en cours)
                        /*
                        $.ajax({
                            type:'POST',
                            url: '/projet_perso/public/ajax/champUpdateAjax.php',
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
            window.location.href = "http://localhost/projet_perso/public/champ/previous/" + champId;
        }
    );

    $(".buttonNext").click(function()
        {
            var champId = $(this).data('champ')
            window.location.href = "http://localhost/projet_perso/public/champ/next/" + champId;
        }
    );


//************************ fin : champ ********************


//*****************debut : ressource **************

    //gestion couleur valeur range au chargement
    for(let i = 1; i<=3; i++){
        var valeurGlissante = $("#range"+i).val();
        var valeurMax = $("#range"+i).data("max");
        var color = formateCSSSuivantValeurMax(valeurGlissante, valeurMax);
        $('.colorValue'+i).addClass("text-" + color);
    }

    //modification d'une valeur glissante d'une ressource
    $(".range").change(function()
        {
            //récupération des datas
            var count = parseInt($(this).data("count"));
            var id = parseInt($(this).data("id"));
            var valeurGlissante = parseInt($(this).val());
            var valeurMax = parseInt($(this).data("max"));

            //annulation des couleurs css
            $('.colorValue'+count).removeClass(
                "text-success \n\
                text-warning \n\
                text-danger"
            ).addClass("text-info");

            //update de la ressource en ajax
            $.ajax({
                type:'POST',
                url: '/projet_perso/public/ajax/ressourceUpdateAjax.php',
                data: {valeurGlissante: valeurGlissante, id: id},
                success: function (){
                    //changement de la valeur affichée
                    $('.refreshValue'+count).text(valeurGlissante);

                    //changement de la couleur d'affichage
                    var color = formateCSSSuivantValeurMax(valeurGlissante, valeurMax);
                    $('.colorValue'+count).removeClass("text-info").addClass("text-" + color);

                    //changement valeur info bulle  ***********(en cours)
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

            console.log(valeurMax);

            //annulation des couleurs css
            $('.colorValue'+count).removeClass(
                "text-success \n\
                text-warning \n\
                text-danger"
            ).addClass("text-info");

            //modal
            $.confirm({
                title: "<h3>Modification de la valeur max de la ressource ?</h3>",
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
                        window.location.href = "http://localhost/projet_perso/public/ressource/updateRangeMax/" + id + "/" + newRangeMax + "/" + page
                    },
                    Annuler: function ()
                    {

                    },
                },
            });
            //url: updateRangeMax/{id}/{newRangeMax}

        }
    );

    //gestion couleur valeur glissante
    function formateCSSSuivantValeurMax(valeurGlissante, valeurMax){
        var color = "";
        if (valeurGlissante > (valeurMax/2))
            color = "success";
        else if (valeurGlissante > (valeurMax/4) && valeurGlissante >= (valeurMax/2))
            color = "warning";
        else if (valeurGlissante <= (valeurMax/4))
            color = "danger";
        return color;
    }

    //suppression d'une ressource
    $(".cliquableRessource").click(function()
        {
            //récupération des datas
            var id = $(this).data('ressource');

            //modal
            $.confirm({
                title: 'Supprimer',
                content: 'Voulez vous supprimer le champ ?',
                buttons: {
                    Confirmer: function () {
                        window.location.href = "http://localhost/projet_perso/public/ressource/delete/" + id;
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
        console.log("click btn");
        //récupération des datas
        var id = $(this).data('fiche');

        //modal
        $.confirm({
            title: 'Supprimer',
            content: 'Voulez vous supprimer supprimer cette fiche ?',
            buttons: {
                Confirmer: function () {
                    window.location.href = "http://localhost/projet_perso/public/fiche/" + id + "/delete" ;
                },
                Annuler: function () {
                    console.log('annuler');
                },
            }
        });
    });





//fin document Ready
})



