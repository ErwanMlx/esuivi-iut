$(document).ready(function(){
    var ctx = document.getElementById("BarChart");
    // var labels = ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"];
    var labels = JSON.parse($('#labels').text());
    var dataTmpMoyen = JSON.parse($('#tmpMoyenEtapes').text());
    var dataTauxAbandon = JSON.parse($('#tauxAbandonEtape').text());
    // var dataTauxRetour = [12, 19, 3, 5, 2, 5, 2, 3, 4];

    // var poolColors = function (a) {
    //     var pool = [];
    //     for(i=0;i<a;i++){
    //         pool.push(dynamicColors());
    //     }
    //     return pool;
    // };
    //
    // var dynamicColors = function() {
    //     var r = Math.floor(Math.random() * 255);
    //     var g = Math.floor(Math.random() * 255);
    //     var b = Math.floor(Math.random() * 255);
    //     return "rgb(" + r + "," + g + "," + b + ")";
    // };

    // var backgroundColor = poolColors(9);
    // var borderColor = backgroundColor;

    var backgroundColor = ['#3366CC','#DC3912','#FF9900','#109618','#990099','#3B3EAC','#0099C6','#DD4477','#66AA00','#B82E2E','#316395','#994499','#22AA99','#AAAA11','#6633CC','#E67300','#8B0707','#329262','#5574A6','#3B3EAC'];
    borderColor = backgroundColor;

    var datasets = [];

    for (var i = 0; i < labels.length; i++) {
        datasets[i] =
            {
                label: labels[i],
                data: [dataTmpMoyen[i]],
                backgroundColor: backgroundColor[i],
                borderColor: borderColor[i],
                borderWidth: 1
            }
    };


    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true,
                        stepSize: 1
                    }
                }],
                xAxes: [{
                    ticks: {
                        autoSkip: false,
                        display: false
                        // lineHeight: 20
                        // maxRotation: 90,
                        // minRotation: 90
                    }
                }]
            },
            title:{
              display: true,
              text: "Temps moyen (en jours) passé à chaque étape",
              position: "top",
              fontSize: 23,
              fontColor: "#22519C",
              fontWeight: "normal"
            },
            // legend: {
            //     display: false,
            // }
        }
    });

    var ctx = document.getElementById("PieChart1");
    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: dataTauxAbandon,
                backgroundColor: backgroundColor,
                borderColor: borderColor,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            },
            title:{
              display: true,
              text: "Taux d'abandon du dossier par étape",
              position: "top",
              fontSize: 23,
              fontColor: "#22519C",
              fontWeight: "normal"
            }
        }
    });
    // var ctx = document.getElementById("PieChart2");
    // var myChart = new Chart(ctx, {
    //     type: 'pie',
    //     data: {
    //         labels: labels,
    //         datasets: [{
    //             label: 'Etapes',
    //             data: dataTauxRetour,
    //             backgroundColor: backgroundColor,
    //             borderColor: borderColor,
    //             borderWidth: 1
    //         }]
    //     },
    //     options: {
    //         responsive: true,
    //         maintainAspectRatio: false,
    //         scales: {
    //             yAxes: [{
    //                 ticks: {
    //                     beginAtZero:true
    //                 }
    //             }]
    //         },
    //         title:{
    //           display: true,
    //           text: "Taux de retour en arrière par étape",
    //           position: "top",
    //           fontSize: 23,
    //           fontColor: "#22519C",
    //           fontWeight: "normal"
    //         }
    //     }
    // });
});
