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
            $(".form_maitre").removeClass('hide');
        }
        else
        {
            $(".form_entreprise").addClass('hide');
            if($(this).val() != '')
            {
                $(".infos_entreprise").removeClass('hide');
                $(".select_maitre").prop('readonly',false);
                $(".select_maitre").removeClass("disable");
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
            $(".form_maitre").removeClass('hide');

        }
        else
        {
            $(".form_maitre").addClass('hide');
        }
    });
});