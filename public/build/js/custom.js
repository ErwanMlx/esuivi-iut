$(document).ready(function() {
    /*Code json qui teste les droits*/
    // var blocked = 0;

    // function Confirm(title, msg, $true, $false, $elem, n) { /*change*/
    //     var $content =  "<div class='dialog-ovelay'>" +
    //         "<div class='dialog'><header>" +
    //         " <h3> " + title + " </h3> " +
    //         "<i class='fa fa-close'></i>" +
    //         "</header>" +
    //         "<div class='dialog-msg'>" +
    //         " <p> " + msg + " </p> " +
    //         "</div>" +
    //         "<footer>" +
    //         "<div class='controls'>" +
    //         " <button class='button button-danger doAction'>" + $true + "</button> " +
    //         " <button class='button button-default cancelAction'>" + $false + "</button> " +
    //         "</div>" +
    //         "</footer>" +
    //         "</div>" +
    //         "</div>";
    //     $('body').append($content);
    //
    //     $('.doAction').click(function () {
    //         $(this).parents('.dialog-ovelay').fadeOut(500, function () {
    //             blocked = 0
    //             $(this).remove();
    //         });
    //         if(n == 1)
    //         {
    //             $.ajax({
    //                 type: "POST",
    //                 datatype : "application/json",
    //                 url: "valider_etape",
    //                 data: { id:$("#id_dossier").text() },
    //                 success: function(data){
    //                     if(data.error != "ok") {
    //                         alert(data.error);
    //                     }
    //                 }
    //             });
    //
    //             console.log("ok");
    //             $elem.removeClass('etape-actuelle').addClass('etape-valide');
    //             var $etapesSuivantes = $('.etape').not('.etape-valide');
    //             var $etapeSuivante = $etapesSuivantes.first();
    //             $etapeSuivante.addClass('etape-actuelle');
    //         }
    //         if(n == 2)
    //         {
    //             console.log("ok2");
    //             $elem.removeClass('etape-valide').addClass('etape-actuelle');
    //
    //             $.ajax({
    //                 type: "POST",
    //                 datatype : "application/json",
    //                 url: "annuler_etape",
    //                 data: { id:$("#id_dossier").text(), id_etape:$elem.attr('id') },
    //                 success: function(data){
    //                     if(data.error != "ok") {
    //                         alert(data.error);
    //                     }
    //                 }
    //             });
    //
    //             var $etapes = $('.etape');
    //             var $bool = 0;
    //             for(var i = 0; i<$etapes.length; i++)
    //             {
    //                 if($bool == 0)
    //                 {
    //                     if($($etapes[i]).hasClass('etape-actuelle'))
    //                     {
    //                         $bool = 1;
    //                     }
    //                 }
    //                 else
    //                 {
    //                     $($etapes[i]).removeClass('etape-valide');
    //                     $($etapes[i]).removeClass('etape-actuelle');
    //                 }
    //             }
    //         }
    //     });
    //     $('.cancelAction, .fa-close').click(function () {
    //         $(this).parents('.dialog-ovelay').fadeOut(500, function () {
    //             blocked = 0;
    //             $(this).remove();
    //         });
    //     });
    // };

    function Confirm(title, msg, $true, $false, $elem, n) { /*change*/
        $('#pop').find('.modal-title').text(title);
        $('#pop').find('.modal-text').text(msg);
        $('#pop').find('.cancelAction').text($false);
        $('#pop').find('.doAction').text($true);
        $('#pop').modal('show');

        $('.doAction').click(function () {
            // blocked = 0;
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
        });
        // $('.cancelAction, .fa-close').click(function () {
        //     // blocked = 0;
        // });
    };

    $(document).on("click",".etape-actuelle",function(){

        //if droits
        // if(blocked == 0)
        // {
        //     blocked = 1;
            Confirm("Confirmation","Etes vous sûr de vouloir valider cette étape?",
                "Oui","Non",$(this),1);
        // }
        // //endif
    });

    $(document).on("click",".etape-valide",function(){

        //if droits
        // if(blocked == 0)
        // {
        //     blocked = 1;
            Confirm("Confirmation","Etes vous sûr de vouloir annuler cette étape et les étapes suivantes?",
                "Oui","Non",$(this),2);
        // }
        //endif
    });
});