$(document).ready(function() {
    var i = 1;
    // $(".form_entreprise").addClass('hide');
    // $(".form_maitre").addClass('hide');
    // $(".infos_entreprise").addClass('hide');
    // $(".select_maitre").prop('readonly',true);
    // $(".select_maitre").addClass("disable");
    $(document).on("click",".ajout",function(){
        console.log("test");
        i+=1;

        $(".maitre_stage").append("<div class='container'>\
			    <div class='col-xs-4 col-sm-4'>\
			    </div>\
				  <div class='form-group row col-xs-4 col-sm-4'>\
			      <p>Entrer le nom de votre maître de stage :<p>\
			      <input name='autre' type='text' class='form-control' placeholder='Autre maître de stage " + i +"'>\
			    </div>\
			  </div>\
			  <div class='container'>\
			    <div class='col-xs-4 col-sm-4'>\
			    </div>\
				  <div class='form-group row col-xs-4 col-sm-4'>\
			      <p>Entrer l\'email de votre maître de stage :<p>\
			      <input name='autre' type='email' class='form-control' placeholder='Email du maître de stage " + i +"'>\
			    </div>\
			  </div>");
    });
    $(document).on("click",".retirer",function(){
        console.log("test");
        if (i>1)
        {
            i-=1;
            $(".maitre_stage > .container").last().remove();
            $(".maitre_stage > .container").last().remove();
        }
    });
    $(".select_entreprise").change(function(){
        if($(this).val() == 'Autre')
        {
            console.log("totototototot");

            $(".form_entreprise").removeClass('hide');
            $(".infos_entreprise").addClass('hide');
            $(".select_maitre").prop('readonly',true);
            $(".select_maitre").addClass("disable");
            $(".select_maitre option[value=\"Autre\"]").prop("selected", true);
            $(".form_maitre").hide();
            $(".form_maitre").removeClass('hide');
            $(".form_maitre").fadeIn("fast");
        }
        else
        {
            $(".form_entreprise").addClass('hide');
            $(".form_maitre").addClass('hide');
            if($(this).val() != '')
            {
                $(".infos_entreprise").hide();
                $(".infos_entreprise").removeClass('hide');
                $(".select_maitre").prop('readonly',false);
                $(".select_maitre").removeClass("disable");
                
                $.ajax({
                    url: 'informations/',
                    type: 'GET',
                    dataType: 'json',
                    data: 'id_entreprise=' + $(this).val(),
                    
                    success: function(resultat, statut)
                    {
                        console.log(resultat);
                        $(".infos_entreprise").hide();
                        $(".infos_entreprise").html("<p>Adresse de l'entreprise :</p>\
                        <p>"+resultat.entreprise.adresse+"</p>\
                        <p>Code postal de l'entreprise :</p>\
                        <p>"+resultat.entreprise.cp+"</p>\
                        <p>Ville de l'entreprise :</p>\
                        <p>"+resultat.entreprise.ville+"</p>\
                        <div class='infos_ma'></div>");
                        $(".infos_entreprise").fadeIn("fast");
                        
                        $(".select_maitre").html("<option value=''>-- Selectionner le maitre d'apprentissage --</option>");
                        $.each(resultat.liste_ma,function(index,element)
                        {
                            $(".select_maitre").append("<option value='"+element.id+"'>"+element.nom + " " + element.prenom+"</option>");
                        });
                        $(".select_maitre").append("<option value='Autre'>Autre</option>");
                        $(".select_maitre").append("<div class ='infos_ma'></div>");
                        
                    },
                    error: function(resultat, statut, erreur)
                    {
                        console.log('erreur');
                    }
                });
            }
            else
            {
                $(".infos_entreprise").addClass('hide');
                $(".select_maitre").prop('readonly',true);
                $(".select_maitre").addClass("disable");
            }
        }

    });
    $(".select_maitre").change(function(){
        if($(this).val() == 'Autre')
        {
            $(".form_maitre").hide();
            $(".form_maitre").removeClass('hide');
            $(".form_maitre").fadeIn("fast");
            $(".infos_ma").html("");
        }
        else
        {
            if($(this).val() != '')
            {
                $.ajax({
                        url: 'informations_ma/',
                        type: 'GET',
                        dataType: 'json',
                        data: 'id_ma=' + $(this).val(),
                        
                        success: function(resultat, statut)
                        {
                            console.log(resultat);
                            $(".infos_ma").hide();
                            
                            var html_ma = "<p>Nom et prénom du maître d'apprentissage :</p>\
                            <p>"+resultat.maitre_app.nom+" "+resultat.maitre_app.prenom+"</p>";
                            if(resultat.maitre_app.email != null)
                            {
                                html_ma += "<p>Adresse email du maître d'apprentissage :</p>\
                                <p>"+resultat.maitre_app.email+"</p>";
                            }
                            if(resultat.maitre_app.tel != null)
                            {
                                html_ma += "<p>Telephone du maître d'appentissage :</p>\
                                <p>"+resultat.maitre_app.tel+"</p>";
                            }
                            if(resultat.maitre_app.fonction != null)
                            {
                                html_ma += "<p>Fonction du maître d'apprentissage :</p>\
                                <p>"+resultat.maitre_app.fonction+"</p>";
                            }
                            $(".infos_ma").html(html_ma);
                            /*$(".infos_ma").html("<p>Nom et prénom du maître d'apprentissage :</p>\
                            <p>"+resultat.maitre_app.nom+" "+resultat.maitre_app.prenom+"</p>\
                            <p>Adresse email du maître d'apprentissage :</p>\
                            <p>"+resultat.maitre_app.email+"</p>\
                            <p>Telephone du maître d'appentissage :</p>\
                            <p>"+resultat.maitre_app.tel+"</p>\
                            <p>Fonction du maître d'apprentissage :</p>\
                            <p>"+resultat.maitre_app.fonction+"</p>");*/
                            $(".infos_ma").fadeIn("fast");
    
                        },
                        error: function(resultat, statut, erreur)
                        {
                            console.log('erreur');
                        }
                    });
                $(".form_maitre").addClass('hide');
            }
            else
            {
                $(".form_maitre").addClass('hide');
                $(".infos_ma").html("");
            }
        }
    });
});