$(document).ready(function()
{
    $(".form-control").on("change keyup paste", function()
    {
        var text = $(this).val();
        $.ajax({
                        url: '/liste/recherche/',
                        type: 'GET',
                        dataType: 'json',
                        data: 'search=' + text,
                        
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
                                <p>"+element.prenom+" "+element.nom+"</p>\
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