$(document).ready(function() {
  	/*Code json qui teste les droits*/
  	
  	function Confirm(title, msg, $true, $false, $link) { /*change*/
        var $content =  "<div class='dialog-ovelay'>" +
                        "<div class='dialog'><header>" +
                         " <h3> " + title + " </h3> " +
                         "<i class='fa fa-close'></i>" +
                     "</header>" +
                     "<div class='dialog-msg'>" +
                         " <p> " + msg + " </p> " +
                     "</div>" +
                     "<footer>" +
                         "<div class='controls'>" +
                             " <button class='button button-danger doAction'>" + $true + "</button> " +
                             " <button class='button button-default cancelAction'>" + $false + "</button> " +
                         "</div>" +
                     "</footer>" +
                  "</div>" +
                "</div>";
         $('body').prepend($content);
  	};
  	
	$(document).on("click",".etape-actuelle",function(){

		//if droits
		var r = confirm("Etes vous sûr de vouloir valider cette étape? ");
		if(r == true)
		{
    		$(this).removeClass('etape-actuelle').addClass('etape-valide');
    		var $etapesSuivantes = $('.etape').not('.etape-valide');
    		var $etapeSuivante = $etapesSuivantes.first();
    		$etapeSuivante.addClass('etape-actuelle');
		}
		//endif
	});

	$(document).on("click",".etape-valide",function(){

		//if droits
		console.log("click");
		var r = confirm("Etes vous sûr de vouloir invalider cette étape et les suivantes? ");
		if(r == true)
		{
    		$(this).removeClass('etape-valide').addClass('etape-actuelle');
    		var $etapes = $('.etape');
    		var $bool = 0;
    		for(var i = 0; i<$etapes.length; i++)
    		{
    			if($bool == 0)
    			{
    				if($($etapes[i]).hasClass('etape-actuelle'))
    				{
    					$bool = 1;
    				}
    			}
    			else
    			{
    				$($etapes[i]).removeClass('etape-valide');
    				$($etapes[i]).removeClass('etape-actuelle');
    			}
    		}
		}
		//endif
	});
});








