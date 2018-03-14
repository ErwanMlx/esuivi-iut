$(document).ready(function() {
	var i = 1;
	$(".form_entreprise").hide();
	$(".form_maitre").hide();
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
			$(".form_entreprise").show();
		}
		else
		{
			$(".form_entreprise").hide();
		}
	});
	$(".select_maitre").change(function(){
		if($(this).val() == 'Autre')
		{
			$(".form_maitre").show();
		}
		else
		{
			$(".form_maitre").hide();
		}
	});
});