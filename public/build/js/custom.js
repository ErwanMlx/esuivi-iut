$(document).ready(function() {
    /*Code json qui teste les droits*/
    $(document).on("click",".etape-actuelle",function(){

        //if droits
        $(this).removeClass('etape-actuelle').addClass('etape-valide');
        var $etapesSuivantes = $('.etape').not('.etape-valide');
        var $etapeSuivante = $etapesSuivantes.first();
        $etapeSuivante.addClass('etape-actuelle');
        //endif
    });

    $(document).on("click",".etape-valide",function(){

        //if droits
        console.log("click");
        $(this).removeClass('etape-valide').addClass('etape-actuelle');
        var $etapes = $('.etape');
        var $bool = 0;
        console.log($etapes);
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
        //endif
    });
});