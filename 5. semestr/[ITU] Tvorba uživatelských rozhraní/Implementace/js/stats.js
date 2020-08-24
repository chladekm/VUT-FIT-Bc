/*********************************************************************************
 * Project:   3BIT ITU, Project - Evidence system                                *
 *            Faculty of Information Technology                                  *
 *            Brno University of Technology                                      *
 * File:      stats.js                                                           *
 * Date:      08.12.2019                                                         *
 * Authors:   Martin Chladek <xchlad1600@stud.fit.vutbr.cz>                      *
 *********************************************************************************/

$(document).ready(function() {
    renderTime();

    var notebooks=0;
    var printers = 0;
    var phones = 0;
    var keyboards = 0;
    var projectors = 0;
    var monitor = 0;
    var mouses = 0;

    $.getJSON( "database/devices.json", function(result)
    {

        // console.log(notebooks);
        // console.log("Notebooks: " + notebooks + "\nTiskárny: " + printers + "\nKlávesnice: " + keyboards);


        $.each(result, function(name, content)
        {
            if(Array.isArray(content))
            {
                $.each(content, function(key, content) {
                  // console.log(content.type);

                    if(content.type == "Notebook")
                        notebooks++;
                    else if(content.type == "Tiskárna")
                        printers++;
                    else if(content.type == "Klávesnice")
                        keyboards++;
                    else if(content.type == "Monitor")
                        monitor++;
                    else if(content.type == "Telefon")
                        phones++;
                    else if(content.type == "Myš")
                        mouses++;
                    else if(content.type == "Projektor")
                        projectors++;
                });
            }
        });
    

        // console.log("Notebooks: " + notebooks + "\nTiskárny: " + printers + "\nKlávesnice: " + keyboards);
   

        /**************************** 1st graph ****************************/

        var ctx = document.getElementById('Graph_one').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Notebooky', 'Tiskárny', 'Klávesnice', 'Monitory', 'Myši', 'Telefony', 'Projektory'],
                datasets: [{
                    label: 'Počet zařízení',
                    data: [notebooks, printers, keyboards, monitor, mouses, phones, projectors],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(114, 171, 0, 0.6)',
                        'rgba(97, 71, 11, 0.6)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(114, 171, 0, 1)',
                        'rgba(97, 71, 11, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                legend: {
                    display: false,
                    position: 'bottom',
                },
                title: {
                    display: false,
                    text: 'Počet zařízení'
                }
            }
        });
     });

/************************************************************************************/

    var ucitele=0;
    var reditele = 0;
    var zastupce = 0;
    var skolnici = 0;
    var ostatni = 0;
    var poradce = 0;

    $.getJSON( "database/workers.json", function(result)
    {
        $.each(result, function(name, content)
        {
            if(Array.isArray(content))
            {
                $.each(content, function(key, content) {
                  
                  console.log(content.typ);
                    
                    if(content.typ == "ucitel")
                        ucitele++;
                    else if((content.typ == "ostatni") && (content.specialization == "školník"))
                        skolnici++;
                    else if(content.typ == "ostatni")
                        ostatni++;
                    else if(content.typ == "poradce")
                        poradce++;
                    else if(content.typ == "reditel")
                        reditele++;
                    else if(content.typ == "zastupce")
                        zastupce++;
                });
            }
        });
       

        /**************************** 2st graph ****************************/

        var ctx = document.getElementById('Graph_two').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Učitelé', 'Výchovní poradci', 'Školníci', 'Ředitelé', 'Zástupci', 'Ostatní'],
                datasets: [{
                    label: 'Počet zaměstnanců',
                    data: [ucitele, poradce, skolnici, reditele, zastupce, ostatni],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(114, 171, 0, 0.6)',
                        'rgba(97, 71, 11, 0.6)',
                        'rgba(153, 102, 255, 0.6)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(114, 171, 0, 1)',
                        'rgba(97, 71, 11, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: false,
                    text: 'Zaměstnanci'
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
     });

/************************************************************************************/

    var leden = 0;
    var unor = 0;
    var brezen = 0;
    var duben = 0;
    var kveten = 0;
    var cerven = 0;
    var cervenec = 0;
    var srpen = 0;
    var zari = 0;
    var rijen = 0;
    var listopad = 0;
    var prosinec = 0;

    var tmp;


    $.getJSON( "database/repairs.json", function(result)
    {
        $.each(result, function(name, content)
        {
            console.log("content");

            if(Array.isArray(content))
            {
                $.each(content, function(key, content) {
                
                  console.log("This");
                  console.log(content.startdate);

                  tmp = content.startdate.split('-')
                  tmp = tmp[1];

                    if(tmp == "01")
                        leden++;
                    else if(tmp == "02")
                        unor++;
                    else if(tmp == "02")
                        unor++;
                    else if(tmp == "03")
                        brezen++;
                    else if(tmp == "04")
                        duben++;
                    else if(tmp == "05")
                        kveten++;
                    else if(tmp == "06")
                        cerven++;
                    else if(tmp == "07")
                        cervenec++;
                    else if(tmp == "08")
                        srpen++;
                    else if(tmp == "09")
                        zari++;
                    else if(tmp == "10")
                        rijen++;
                    else if(tmp == "11")
                        listopad++;
                    else if(tmp == "12")
                        prosinec++;
                });
            }
        });
    
        console.log("Leden: " + leden + "\nUnor: " + unor);
   

        /**************************** 3st graph ****************************/

        var ctx = document.getElementById('Graph_three').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
                datasets: [{
                    label: 'Počet nahlášených oprav',
                    data: [leden, unor, brezen, duben, kveten, cerven, cervenec, srpen, zari, rijen, listopad, prosinec],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.32)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 99, 132, 1)',
                        
                    ],
                    borderWidth: 2,
                    lineTension: 0
                }]
            },
            options: {
                responsive: true,
                spanGaps: false,
                elements: {
                    line: {
                        tension: 0.000001
                    }
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            autoSkip: false,
                            maxRotation: 0
                        }
                    }]
                },
                legend: {
                    display: false,
                    position: 'bottom',
                },
                title: {
                    display: false,
                    text: 'Počet nahlášených oprav'
                }
            }
        });
     });
});

function renderTime()
{
    var date = new Date();
    var year = date.getYear();
    var day = date.getDay();
    var month = date.getMonth();
    var daym = date.getDate();

    if(year < 1000){
        year += 1900;
    }
    
    var day_array = new Array("Neděle", "Pondělí","Úterý","Středa","Čtvrtek","Pátek","Sobota");
    var month_array = new Array("leden","únor","březen","duben","květen","červen","červenec","srpen","září","říjen","listopad","prosinec");

    var date_two = new Date();
    var hours = date_two.getHours();
    var minutes = date_two.getMinutes();
    var seconds = date_two.getSeconds();

    if(hours < 10){
        hours = "0" + hours;
    }

    if(minutes < 10){
        minutes = "0" + minutes;
    }

    if(seconds < 10){
        seconds = "0" + seconds;
    }

    var myClock = document.getElementById("display_clock");
    myClock.innerHTML = "" + day_array[day] + ", " + daym + ". " + month_array[month] + " " + year + " | " + hours + ":" + minutes + ":" + seconds;

    setTimeout("renderTime()", 1000);
}