<!-- ----------------------------------------------------------------------------!
 ! Project:   3BIT ITU, Project - Evidence system                                !
 !            Faculty of Information Technology                                  !
 !            Brno University of Technology                                      !
 ! File:      rooms.php                                                          !
 ! Date:      08.12.2019                                                         !
 ! Authors:   Martin Chladek, <xchlad16@stud.fit.vutbr.cz>                       !
 ! Authors:   Borek Reich, <xreich06@stud.fit.vutbr.cz>                          !
 !------------------------------------------------------------------------------->

<!doctype html>
<html lang="cs">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>EvidIT - Systém pro evidenci</title>

        <?php require('links.php') ?>        

        <!-- Own style -->
        <link rel="stylesheet" type="text/css" href="css/rooms.css">
        <link rel="stylesheet" type="text/css" href="css/widgets.css">



        <!-- Own script -->
        <script type="text/javascript" src="js/devices.js"></script>
        <script type="text/javascript" src="js/rooms.js" defer></script>

    </head>
        <?php require('template_header.php') ?>
        <div class="container-fluid p-5 <?php if(isset($_GET["patro"]) and$_GET["patro"]==1){ echo'animated fadeIn';} ?>">
            <h2 id="main_title"> SPRÁVA MÍSTNOSTÍ </h2>
            <hr id="main_title_hr">
            
            <div id="widgets" class="row mt-5">
                <div class="col-md-4">
                    <div class="widget widget_0">
                        <p class="float-left h-100"><i class="fas fa-chair align-middle"></i></p>
                        <p class="float-right">
                            <span>
                            <?php 
                                $url = './database/rooms.json';
                                $data = file_get_contents($url);
                                $rooms = json_decode($data); 
                                echo $rooms->row_count;
                            ?>
                            </span>místností</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="widget widget_1">
                        <p class="float-left"><i class="fas fa-ban align-middle"></i></i></i></p>
                        <p class="float-right">
                            <span>
                            <?php 
                                $url = './database/rooms.json';
                                $data = file_get_contents($url);
                                $rooms = json_decode($data);
                                $url = './database/devices.json';
                                $data = file_get_contents($url);
                                $devices = json_decode($data);
                                $count = $rooms->row_count;
                                foreach ($rooms->rooms as $room) {
                                    foreach ($devices->devices as $item) {
                                        if ($room->name == $item->room)
                                            $count--;
                                    }
                                }
                                echo $count;
                            ?>
                            </span>místností bez zařízení</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="widget widget_2">
                        <p class="float-left"><i class="fas fa-book align-middle"></i></p>
                        <p class="float-right">
                            <span>
                            <?php 
                                $url = './database/rooms.json';
                                $data = file_get_contents($url);
                                $rooms = json_decode($data);
                                $count = 0;
                                foreach ($rooms->rooms as $room) {
                                    if($room->ucebna == 'true')
                                        $count++;
                                }
                                echo $count;
                            ?>                                
                            </span>učeben</p>
                    </div>
                </div>
            </div>

<!--             <div class="row control_panel">
                <div class="col-lg-5 col-12">
                        <span class="search_box btn btn-dark h-100">
                        <input id="search_input" type="text" class="w-100" placeholder="&nbsp;&nbsp;Vyhledat..." name="search">
                        </span>
                        <button id="search_button" class="btn btn-dark h-100" type="submit"><i class="fa fa-search"></i></button>
                    </form>
                </div> 
                <div class="col-lg-7 col-12 text-right">
                    <button type="button" class="btn btn-secondary h-100 px-3 py-2 control_buttons">Vyřadit</button>
                    <button type="button" class="btn btn-secondary h-100 px-3 py-2 control_buttons">Přidělit</button>
                    <button type="button" class="btn btn-success h-100 px-3 py-2 control_buttons"><i class="fas fa-plus"></i> Přidat</button>
                    <button type="button" class="btn btn-danger h-100 px-3 py-2 control_buttons"><i class="fas fa-trash"></i> Odstranit</button>
                </div>
            </div> -->
            <div id="floor_buttons" class="row p-0 mb-4"> 
                	<?php  
                        echo '<div class="row w-75 p-0 mx-auto mb-1">'; 
                        echo '<div class="col-lg-6 col-12 p-0 ">';

                        if(isset($_GET["patro"]) and$_GET["patro"]==2)
                        {
                                // echo '<script>window.location = "#floor_buttons";</script>';
                                echo '
                                <a class="link_button btn btn-secondary" href="'.  $_SERVER['PHP_SELF'] .'?patro=1">1. patro</a>
                                <a class="link_button btn btn-dark" href="'.  $_SERVER['PHP_SELF'] .'?patro=2">2. patro</a>';
                                echo '</div>
                                <div class="col-lg-6 col-12 p-0 text-right align-bottom"><h2 class="d-inline-block"><span class="align-bottom mr-4">2. patro</span></h2></div></div>';
                        }
                        else
                        {
                                echo '
                                <a class="link_button btn btn-dark" href="'.  $_SERVER['PHP_SELF'] .'?patro=1">1. patro</a>
                                <a class="link_button btn btn-secondary" href="'.  $_SERVER['PHP_SELF'] .'?patro=2">2. patro</a>';
                                echo '</div>
                                <div class="col-lg-6 col-12 p-0 text-right align-bottom"><h2 class="d-inline-block"><span class="align-bottom mr-4">1. patro</span></h2></div></div>';
                        }
                	?>
            </div>     

            <?php

                    // <h2 id="main_title" class="d-block">1. patro</h2>
                    // <hr id="main_title_hr">
            if (isset($_GET["patro"]) and $_GET["patro"]==1) {
                echo '
                <div class="mx-auto d-block map_div">
                    <img class="img_map" name="rooms1" src="img/patro1.jpg" usemap="#rooms1" border="0" width="100%">
                    <map name="rooms1">
                        <area shape="rect" coords="0,0,425,425" href="'. $_SERVER['PHP_SELF'] .'?select=1A" title="1.A">
                        <area shape="rect" coords="0,425,425,850" href="'. $_SERVER['PHP_SELF'] .'?select=1B" title="1.B">
                        <area shape="rect" coords="425,0,850,300" href="'. $_SERVER['PHP_SELF'] .'?select=zas_mist1" title="zasedací místnost 1">
                        <area shape="rect" coords="1280,0,1700,420" href="'. $_SERVER['PHP_SELF'] .'?select=2A" title="2.A">
                        <area shape="rect" coords="1280,420,1700,850" href="'. $_SERVER['PHP_SELF'] .'?select=2B" title="2.B">
                        <area shape="rect" coords="1080,680,1280,850" href="'. $_SERVER['PHP_SELF'] .'?select=knihovna" title="knihovna">
                        <area shape="rect" coords="880,680,1080,850" href="'. $_SERVER['PHP_SELF'] .'?select=kab1" title="kabinet 1">
                        <area shape="rect" coords="560,680,780,850" href="'. $_SERVER['PHP_SELF'] .'?select=reditelna" title="ředitelna">
                        <area shape="rect" coords="425,680,560,850" href="'. $_SERVER['PHP_SELF'] .'?select=sklad1" title="sklad 1">

                    </map>
                </div>';
            }
                        // <h2 id="main_title" class="d-block">2. patro</h2>
                        // <hr id="main_title_hr">
            if (isset($_GET["patro"]) and $_GET["patro"]==2) {
                echo '
                    <div class="mx-auto d-block map_div">
                        <img class="img_map" name="rooms2" src="img/patro2.jpg" usemap="#rooms2" border="0" width="100%">
                        <map name="rooms2">
                            <area shape="rect" coords="0,0,425,425" href="'. $_SERVER['PHP_SELF'] .'?select=3A" title="3.A">
                            <area shape="rect" coords="0,425,425,850" href="'. $_SERVER['PHP_SELF'] .'?select=3B" title="3.B">
                            <area shape="rect" coords="425,0,545,310" href="'. $_SERVER['PHP_SELF'] .'?select=archiv" title="archív">
                            <area shape="rect" coords="545,0,715,310" href="'. $_SERVER['PHP_SELF'] .'?select=jidelna" title="jídelna">
                            <area shape="rect" coords="715,0,860,310" href="'. $_SERVER['PHP_SELF'] .'?select=kuchyn" title="kuchyň">
                            <area shape="rect" coords="1300,1,1720,440" href="'. $_SERVER['PHP_SELF'] .'?select=4A" title="4.A">
                            <area shape="rect" coords="1300,440,1720,850" href="'. $_SERVER['PHP_SELF'] .'?select=4B" title="4.B">
                            <area shape="rect" coords="900,690,1300,860" href="'. $_SERVER['PHP_SELF'] .'?select=kab2" title="kabinet 2">
                            <area shape="rect" coords="430,690,660,860" href="'. $_SERVER['PHP_SELF'] .'?select=kab3" title="kabinet 3">
                            <area shape="rect" coords="660,690,795,860" href="'. $_SERVER['PHP_SELF'] .'?select=sklad2" title="sklad 2">
                        </map>
                    </div>
                </div>';
            }
            if (isset($_GET["select"])) {

               if (($_GET["select"]=="1A") 
                or ($_GET["select"]=="1B")
            	or ($_GET["select"]=="2A")
            	or ($_GET["select"]=="2B")
                or ($_GET["select"]=="zas_mist1")
                or ($_GET["select"]=="knihovna")
                or ($_GET["select"]=="kab1")
                or ($_GET["select"]=="reditelna") 
                or ($_GET["select"]=="sklad1")
                or ($_GET["select"]=="3A") 
                or ($_GET["select"]=="3B") 
                or ($_GET["select"]=="4A") 
                or ($_GET["select"]=="4B") 
                or ($_GET["select"]=="archiv") 
                or ($_GET["select"]=="jidelna") 
                or ($_GET["select"]=="kuchyn") 
                or ($_GET["select"]=="kab2") 
                or ($_GET["select"]=="kab3") 
                or ($_GET["select"]=="sklad2"))
                {

                    if ($_GET["select"]=="1A") {             $name='1.A';}
                    elseif ($_GET["select"]=="1B"){          $name='1.B';}
                    elseif ($_GET["select"]=="2A"){          $name='2.A';}
                    elseif ($_GET["select"]=="2B"){          $name='2.B';}
                    elseif ($_GET["select"]=="zas_mist1"){   $name='Zasedací místnost 1';}
                    elseif ($_GET["select"]=="knihovna"){    $name='Knihovna';}
                    elseif ($_GET["select"]=="kab1"){        $name='Kabinet 1';}
                    elseif ($_GET["select"]=="reditelna") {  $name='Ředitelna';}
                    elseif ($_GET["select"]=="sklad1"){      $name='Sklad 1';}
                    elseif ($_GET["select"]=="3A") {         $name='3.A';}
                    elseif ($_GET["select"]=="3B") {         $name='3.B';}
                    elseif ($_GET["select"]=="4A") {         $name='4.A';}
                    elseif ($_GET["select"]=="4B") {         $name='4.B';}
                    elseif ($_GET["select"]=="archiv") {     $name='Archiv';}
                    elseif ($_GET["select"]=="jidelna") {    $name='Jídlena';}
                    elseif ($_GET["select"]=="kuchyn") {     $name='Kuchyně';}
                    elseif ($_GET["select"]=="kab2") {       $name='Kabinet 2';}
                    elseif ($_GET["select"]=="kab3") {       $name='Kabinet 3';}
                    elseif ($_GET["select"]=="sklad2"){      $name='Sklad 2';}

                	echo '
                            <a class="link_button btn btn-dark w-auto" href="rooms.php?patro=1"><i class="fas fa-long-arrow-alt-left"></i>&nbsp;&nbsp;Zpět na mapu</a>
                        <h2 id="main_title" class="d-block">'.$name.'</h2>
                        <hr id="main_title_hr">
                        <table class="table table-striped table-bordered table-hover" id="device_table">
                        <thead class="thead-dark">
                            <tr>
                              <th>Zařízení</th>
                              <th>Typ</th>
                              <th>Disponent</th>
                            </tr>
                          </thead>
                          <tbody class="device_table_body" id="rooms_table_body'.$_GET["select"].'">
                            <!-- Content generated by devices.js -->
                          </tbody>
                    </table>';
                }
            }
            ?>    


            <!-- Modal windows section -->
                <!-- Employee detail modal window -->
                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"  aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content my-modal">
                            <div class="modal-header">
                                <h5 class="modal-title text-center" id="exampleModalLongTitle"> <strong>Detail zařízení</strong></h5> 
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-2">
                                        <p class="text-right"><strong>Název: </strong></p>
                                        <p class="text-right"><strong>Typ: </strong></p>
                                        <p class="text-right"><strong>Stav: </strong></p>
                                        <p class="text-right"><strong>Disponent: </strong></p>
                                        <p class="text-right"><strong>Místnost: </strong></p>
                                    </div>
                                    <div class="col-4">
                                        <p id="device-detail-name"></p>
                                        <p id="device-detail-type"></p>
                                        <p id="device-detail-status"></p>
                                        <p id="device-detail-disponent"></p>
                                        <p id="device-detail-room"></p>
                                    </div>
                                    <div class="col-3">
                                        <p class="text-right"><strong>Datum zavedení: </strong></p>
                                        <p class="text-right"><strong>Datum přidelení: </strong></p>
                                        <p class="text-right"><strong>Datum vyřazení: </strong></p>
                                    </div>
                                    <div class="col-2">
                                        <p id="device-detail-implemdate"></p>
                                        <p id="device-detail-assigndate"></p>
                                        <p id="device-detail-discarddate"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">                                
                                <button type="button" id="update-device" class="btn btn-outline-primary" data-toggle="modal" data-target="#update-device-modal">Upravit</button>
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Zrušit</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End employee detail modal window -->
                
                <!-- Update employee modal window -->
                <div class="modal" id="update-device-modal" tabindex="-1" role="dialog" aria-labelledby="update-device-modalTitle" aria-hidden="true" data-backdrop="false">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content my-modal">
                            <div class="modal-header">
                                <h5 class="modal-title text-center" id="update-device-modalTitle"> <strong>Úprava zařízení</strong></h5> 
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="./logic/room_update_device.php" method="post">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="row form-group">
                                                <div class="col-4 text-right">
                                                    <label for="update-device-name"><strong>Název: </strong></label>
                                                </div>
                                                <div class="col-8">
                                                    <input class="form-control" id="update-device-name" name="update-device-name" type="text">    
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-4 text-right">
                                                    <label for="update-device-type"><strong>Typ: </strong></label>
                                                </div>
                                                <div class="col-8">
                                                    <input class="form-control" id="update-device-type" name="update-device-type" type="text">    
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-4 text-right">
                                                    <label for="update-device-status"><strong>Stav: </strong></label>
                                                </div>
                                                <div class="col-8">
                                                    <select class="custom-select" id="update-device-status" name="update-device-status">
                                                        <option value="" id="update-device-status-selected" selected></option>
                                                        <option value="Volný">Volný</option>
                                                        <option value="Přidělený">Přidělený</option>
                                                        <option value="Vyřazený">Vyřazený</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-4 text-right">
                                                    <label for="update-device-disponent"><strong>Disponent: </strong></label>
                                                </div>
                                                <div class="col-8">
                                                    <select class="custom-select" id="update-device-disponent" name="update-device-disponent">
                                                        <option value="" id="update-device-disponent-selected" selected></option>
                                                        <option value="Petra Veselá">Petra Veselá</option>
                                                        <option value="Martin Pokorný">Martin Pokorný</option>
                                                        <option value="Viktor Kučera">Viktor Kučera</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-4 text-right">
                                                    <label for="update-device-room"><strong>Místnost: </strong></label>
                                                </div>
                                                <div class="col-8">
                                                    <select class="custom-select" id="update-device-room" name="update-device-room">
                                                        <option value="" id="update-device-room-selected" selected></option>
                                                        <option value="1A">1.A</option>
                                                        <option value="1B">1.B</option>
                                                        <option value="2A">2.A</option>
                                                        <option value="2B">2.B</option>
                                                        <option value="zas_mist1">Zasedací místnost 1</option>
                                                        <option value="knihovna">Knihovna</option>
                                                        <option value="kab1">Kabinet 1</option>
                                                        <option value="reditelna">Ředitelna</option>
                                                        <option value="sklad1">Sklad 1</option>
                                                        <option value="3A">3.A</option>
                                                        <option value="3B">3.B</option>
                                                        <option value="4A">4.A</option>
                                                        <option value="4B">4.B</option>
                                                        <option value="archiv">Archiv</option>
                                                        <option value="jidelna">Jídlena</option>
                                                        <option value="kuchyn">Kuchyně</option>
                                                        <option value="kab2">Kabinet 2</option>
                                                        <option value="kab3">Kabinet 3</option>
                                                        <option value="sklad2"></Sklad 2option>  
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="row form-group">
                                                <div class="col-12 text-left">
                                                    <label for="update-device-implemdate"><strong>Datum nasazení (m-d-R): </strong></label>
                                                </div>
                                                <div class="col-12">
                                                    <input class="form-control" type="date" id="update-device-implemdate" name="update-device-implemdate">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-12 text-left">
                                                    <label for="update-device-assigndate"><strong>Datum přirazení (m-d-R): </strong></label>
                                                </div>
                                                <div class="col-12">
                                                    <input class="form-control" type="date" id="update-device-assigndate" name="update-device-assigndate">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-12 text-left">
                                                    <label for="update-device-discarddate"><strong>Datum vyřazení (m-d-R): </strong></label>
                                                </div>
                                                <div class="col-12">
                                                    <input class="form-control" type="date" id="update-device-discarddate" name="update-device-discarddate">
                                                </div>
                                            </div>
                                            <input type="text" id="update-device-id" name="update-device-id" class="d-none">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">                                
                                    <button type="submit" class="btn btn-outline-success" >Uložit úpravu</button>
                                    <button id="update-device-cancel" type="button" class="btn btn-outline-danger" data-dismiss="modal">Zrušit úpravu</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End update employee modal window -->
              


    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript" src="image-map-resizer-master/js/imageMapResizer.min.js"></script>
    <script type="text/javascript">

        $('map').imageMapResize();

    </script>

    </div>