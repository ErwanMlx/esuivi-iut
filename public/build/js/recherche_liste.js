$(document).ready(function()
{
    $("#form-search").on("change keyup paste", function()
    {
        var text = $(".form-control").val();
        var params = 'search=' + text + '&etat=' + $('input[name=etat]:checked', '#form-search').val();

        //Mets à jour l'URL sans rafraichir la page (utile pour revenir à la recherche en cours après avoir consulté un suivi)
        if (history.pushState) {
            var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + params;
            window.history.pushState({path:newurl},'',newurl);
        }

        $.ajax({
            url: '/liste/recherche/',
            type: 'GET',
            dataType: 'json',
            data: params,

            success: function(resultat, statut)
            {
                console.log(resultat);
                $(".contenu a").remove();
                if(resultat.liste.length === 0)
                {
                    $("#noapp").removeClass('hide');
                }
                else
                {
                    $("#noapp").addClass('hide');
                }
                $.each(resultat.liste,function(index,element)
                {
                    $(".contenu").append("<a href='/suivi/"+element.id+"'>\
                                <div class='bordure col-sm-8 col-sm-offset-2'>\
                                <p>"+element.nom+" "+element.prenom+"</p>\
                                <ul>\
                                <li>Nom entreprise : "+element.entreprise+"</li>\
                                <li>Mission : "+element.mission+"</li>\
                                <li>Etat d'avancement : "+element.etat_avancement+"</li>\
                                </ul>\
                                </div>\
                                </a>");
                });
            },
            error: function(resultat, statut, erreur)
            {
                console.log('erreur');
            }
        });
    });
});