$(document).ready(function() {

    function Confirm(title, msg, $true, $false, $elem, n) { /*change*/
        $('#pop').find('.modal-title').text(title);
        $('#pop').find('.modal-text').text(msg);
        $('#pop').find('.cancelAction').text($false);
        $('#pop').find('.doAction').text($true);
        $('#pop').modal('show');

        $('.doAction').click(function () {
            $('#pop').modal('hide');
            if(n == 1)
            {
                $.ajax({
                    type: "POST",
                    datatype : "application/json",
                    url: "valider_etape",
                    data: { id:$("#id_dossier").text(), id_etape:$elem.attr('id') },
                    success: function(data){
                        if(data.error != "ok") {
                            alert(data.error);
                        }
                        else {
                            console.log("ok");
                            $elem.removeClass('etape-actuelle').addClass('etape-valide');
                            var $etapesSuivantes = $('.etape').not('.etape-valide');
                            var $etapeSuivante = $etapesSuivantes.first();
                            $etapeSuivante.addClass('etape-actuelle');
                        }
                    }
                });
            }
            if(n == 2)
            {
                console.log("ok2");

                $.ajax({
                    type: "POST",
                    datatype : "application/json",
                    url: "annuler_etape",
                    data: { id:$("#id_dossier").text(), id_etape:$elem.attr('id') },
                    success: function(data){
                        if(data.error != "ok") {
                            alert(data.error);
                        }
                        else {
                            $elem.removeClass('etape-valide').addClass('etape-actuelle');
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
                    }
                });
            }
            if(n == 3)
            {
                $.ajax({
                    type: "POST",
                    datatype : "application/json",
                    url: "abandon",
                    data: { id:$("#id_dossier").text() },
                    success: function(data){
                        if(data.error != "ok") {
                            alert(data.error);
                        }
                        else {
                            location.reload(true);
                        }
                    }
                });
            }
        });
    };

    $(document).on("click",".etape-actuelle.validable",function(){
            Confirm("Confirmation","Etes vous sûr de vouloir valider cette étape ?",
                "Oui","Non",$(this),1);
    });

    $(document).on("click",".etape-valide.annulable",function(){
            Confirm("Confirmation","Etes vous sûr de vouloir annuler cette étape et les étapes suivantes ?",
                "Oui","Non",$(this),2);
    });
    
    $(document).on("click","#abandon",function(){
            Confirm("Confirmation d'abandon","Etes vous sûr de vouloir abandonner le dossier ?",
                "Oui","Non",$(this),3);
    });
});