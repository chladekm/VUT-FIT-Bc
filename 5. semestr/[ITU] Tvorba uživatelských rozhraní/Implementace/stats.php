<!-- ----------------------------------------------------------------------------!
 ! Project:   3BIT ITU, Project - Evidence system                                !
 !            Faculty of Information Technology                                  !
 !            Brno University of Technology                                      !
 ! File:      stats.php                                                          !
 ! Date:      08.12.2019                                                         !
 ! Authors:   Martin Chladek, <xchlad16@stud.fit.vutbr.cz>                       !
 !------------------------------------------------------------------------------->

<!doctype html>
<html lang="cs">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>EvidIT - Systém pre evidenciu techniky</title>

        <!-- Start server
        sudo /opt/lampp/lampp start

        Stop server
        sudo /etc/init.d/apache2 stop -->

        <?php require('links.php') ?>

        <!-- Own style -->
        <link rel="stylesheet" type="text/css" href="css/stats.css">


        <!-- Own script -->
        <script type="text/javascript" src="js/script.js"></script>
        <script type="text/javascript" src="js/up_button.js"></script>


        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.js" integrity="sha256-qSIshlknROr4J8GMHRlW3fGKrPki733tLq+qeMCR05Q=" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js" integrity="sha256-xKeoJ50pzbUGkpQxDYHD7o7hxe0LaOGeguUidbq6vis=" crossorigin="anonymous"></script>

        <script type="text/javascript" src="js/stats.js"></script>

    </head>
    <?php require('template_header.php') ?>
    <div class="container-fluid p-5 animated fadeIn">
            <h2 id="main_title"> STATISTIKY </h2>
            <hr id="main_title_hr">

        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="p-5">
                    <h5 id="main_title" class="text-center pb-2">Počet zařízení</h5>
                    <!-- <hr id="main_title_hr"> -->
                    <canvas id="Graph_one" width="100" height="80"></canvas>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="p-5">
                    <h5 id="main_title" class="text-center pb-2">Přehled zaměstnanců</h5>
                    <!-- <hr id="main_title_hr"> -->
                    <canvas id="Graph_two" width="100" height="80"></canvas>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="p-5 graph_tree">
                    <h5 id="main_title" class="text-center pb-2">Počet nahlášených oprav</h5>
                    <!-- <hr id="main_title_hr"> -->
                    <canvas id="Graph_three" width="100" height="30"></canvas>
                </div>
            </div>
        </div>
                        
        </div>
                    
<?php require('template_footer.php') ?>
