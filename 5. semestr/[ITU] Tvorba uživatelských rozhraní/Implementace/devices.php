<!-- ----------------------------------------------------------------------------!
 ! Project:   3BIT ITU, Project - Evidence system                                !
 !            Faculty of Information Technology                                  !
 !            Brno University of Technology                                      !
 ! File:      devices.php                                                        !
 ! Date:      08.12.2019                                                         !
 ! Authors:   Peter Kruty, <xkruty00@stud.fit.vutbr.cz>                          !
 !            Martin Chladek, <xchlad16@stud.fit.vutbr.cz>                       !
 !------------------------------------------------------------------------------->

<!doctype html>
<html lang="cs">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>EvidIT - Systém pre evidenciu techniky</title>
        

        <?php require('links.php') ?>


        <!-- Own style -->
        <link rel="stylesheet" type="text/css" href="css/devices.css">
        <link rel="stylesheet" type="text/css" href="css/widgets.css">

        <!-- Own script -->
        <script type="text/javascript" src="js/devices.js" defer></script>

    </head>
	<?php require('template_header.php') ?>
        <!-- Modal windows section -->
        <!-- Devide detail modal window -->
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
        <!-- End devide detail modal window -->
        <!-- Update device modal window -->
        <div class="modal" id="update-device-modal" tabindex="-1" role="dialog" aria-labelledby="update-device-modalTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content my-modal">
                    <div class="modal-header">
                        <h5 class="modal-title text-center" id="update-device-modalTitle"> <strong>Úprava zařízení</strong></h5> 
                        <button id="update-device-close-button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="./logic/update_device.php" method="post">
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
                                                <option value="A222">A222</option>
                                                <option value="B104">B104</option>
                                                <option value="C102">C102</option>
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
        <!-- End update device modal window -->
        <!-- Add device modal window -->
        <div class="modal fade" id="addDeviceModal" tabindex="-1" role="dialog" aria-labelledby="addDeviceModalTitle"  aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content my-modal">
                <form action="./logic/add_device.php" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title text-center" id="addDeviceModalTitle"> <strong>Přidání zařízení</strong></h5> 
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <div class="col-sm-12 text-right">
                                * povinná pole
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-2 mt-2 text-sm-right text-left">
                                <label for="add-device-name"><strong>Název *</strong></label>
                            </div>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="add-device-name"  id="add-device-name" placeholder="Zadejte název...">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-2 mt-2 text-sm-right text-left">
                                <label for="add-device-type"><strong>Typ *</strong></label>
                            </div>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="add-device-type" name="add-device-type" placeholder="Zadejte typ...">
                            </div>
                        </div>
                        <fieldset class="form-group">
                            <div class="row">
                            <legend class="col-form-label col-sm-2 text-sm-right text-left pt-0"><strong>Stav *</strong></legend>
                            <div class="col-sm-10">
                                <div class="form-check">
                                <input class="form-check-input" type="radio" name="add-device-status" id="add-device-status1" value="option1">
                                <label class="form-check-label" for="add-device-status1">
                                    Volný
                                </label>
                                </div>
                                <div class="form-check">
                                <input class="form-check-input" type="radio" name="add-device-status" id="add-device-status2" value="option2">
                                <label class="form-check-label" for="add-device-status2">
                                    Přidelený
                                </label>
                                </div>
                                <div class="form-check">
                                <input class="form-check-input" type="radio" name="add-device-status" id="add-device-status3" value="option3">
                                <label class="form-check-label" for="add-device-status3">
                                    Vyřazený
                                </label>
                                </div>
                            </div>
                            </div>
                        </fieldset>
                        <div class="form-group row">
                            <div class="col-sm-2 mt-2 text-sm-right text-left">
                                <label for="add-device-disponent"><strong>Disponent</strong></label>
                            </div>
                            <div class="col-sm-10">
                                <select class="custom-select" id="add-device-disponent" name="add-device-disponent">
                                    <option selected>Vyberte disponenta...</option>
                                    <option value="Petra Veselá">Petra Veselá</option>
                                    <option value="Martin Pokorný">Martin Pokorný</option>
                                    <option value="Viktor Kučera">Viktor Kučera</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-2 mt-2 text-sm-right text-left">
                                <label for="add-device-room"><strong>Místnost</strong></label>
                            </div>
                            <div class="col-sm-10">
                                <select class="custom-select" id="add-device-room" name="add-device-room">
                                    <option selected>Vyberte místnost...</option>
                                    <option value="A222">A222</option>
                                    <option value="B104">B104</option>
                                    <option value="C102">C102</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-2 text-sm-right text-left">
                                <strong>Náhledový Obrázek</strong>
                            </div>
                            <div class="col-sm-10">
                                <input type="file" name="add-device-img" id="add-device-img" lang="sk">
                            </div>
                        </div>                                        
                    </div>
                    <div class="modal-footer">                                
                        <button type="submit" class="btn btn-outline-success" id="modal-add-device-button">Přidat</button>
                        <button type="button" class="btn btn-outline-secondary" id="modal-add-device-cancel" data-dismiss="modal">Zrušit</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Add device model window -->
        <!-- Remove device confirmation model window -->
        <div class="modal fade" id="remove-device-modal" tabindex="-1" role="dialog" aria-labelledby="remove-device-modalTitle"  aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content my-modal">
                    <div class="modal-header">
                        <h5 class="modal-title text-center" id="remove-device-modalTitle"> <strong>Odstranění zařízení</strong></h5> 
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 text-center">
                                <p><strong>POZOR!</strong><br> Po odstranění se záznam z databáze vymaže natvralo. <br> <strong>Přejete si zařízení vymazat?</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">                                
                        <button type="button" class="btn btn-outline-success" id="remove-device-confirm">Ano</button>
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Ne</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End remove device confirmation model window -->
        <!-- Assign device model window -->
        <div class="modal fade" id="assign-device-modal" tabindex="-1" role="dialog" aria-labelledby="assign-device-modalTitle"  aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content my-modal">
                    <form action="./logic/assign_device.php" method="post" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title text-center" id="assign-device-modalTitle"> <strong>Přidělení zařízení</strong></h5> 
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-2">
                                <div class="col-sm-12 text-right">
                                    * povinné alespoň jedno pole
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 mt-2 text-sm-right text-left">
                                    <label for="assign-device-disponent"><strong>Disponent *</strong></label>
                                </div>
                                <div class="col-sm-10">
                                    <select class="custom-select" id="assign-device-disponent" name="assign-device-disponent">
                                        <option selected>Vyberte disponenta...</option>
                                        <option value="Petra Veselá">Petra Veselá</option>
                                        <option value="Martin Pokorný">Martin Pokorný</option>
                                        <option value="Viktor Kučera">Viktor Kučera</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 mt-2 text-sm-right text-left">
                                    <label for="assign-device-room"><strong>Místnost *</strong></label>
                                </div>
                                <div class="col-sm-10">
                                    <select class="custom-select" id="assign-device-room" name="assign-device-room">
                                        <option selected>Vyberte místnost...</option>
                                        <option value="A222">A222</option>
                                        <option value="B104">B104</option>
                                        <option value="C102">C102</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" name="assign-device-ids" id="assign-device-ids" class="d-none">
                        </div>
                        <div class="modal-footer">                                
                            <button type="submit" class="btn btn-outline-success" id="assign-device-confirm">Přidělit</button>
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Zrušit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End remove device confirmation model window -->

        <div class="container-fluid p-5 animated fadeIn">
                
                <h2 id="main_title"> SPRÁVA ZAŘÍZENÍ</h2>
                <hr id="main_title_hr">
            <div id="widgets" class="row mt-5">
                <div class="col-md-3">
                    <div class="widget widget_0">
                        <p class="float-left h-100"><i class="align-middle fas fa-cogs"></i></p>
                        <p class="float-right"><span id="devices-counter"></span>zařízení</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget_1">
                        <p class="float-left h-100"><i class="align-middle fas fa-laptop"></i></p>
                        <p class="float-right"><span id="available-devices-counter"></span>dostupných zařízení</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget_2">
                        <p class="float-left h-100"><i class="align-middle fas fa-ban"></i></p>
                        <p class="float-right"><span id="inrepair-devices-counter"></span>pokažených zařízení</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget_3">
                        <p class="float-left h-100"><i class="align-middle fas fa-trash-alt"></i></p>
                        <p class="float-right"><span id="discard-devices-counter"></span>vyřazených zařízení</p>
                    </div>
                </div>
            </div>
                <div class="row control_panel">
                    <div class="col-lg-5 col-12"> 
                        <button class="btn btn-dark h-100 pl-3 pr-3" id="filter-button" type="button" >
                            <i class="fa fa-filter"></i>
                        </button>
                        <span class="search_box btn btn-dark h-100">
                            <input id="search_input" class="w-100 form-control"  type="text" placeholder="Vyhledat zařízení..." name="search">
                        </span>
                        <button id="search_button" class="btn btn-dark h-100" type="submit" onclick="search_device()"><i class="fa fa-search"></i></button>
                    </div> 
                    <div class="col-lg-7 col-12 text-right">
                        <button id="discard-device" class="btn btn-secondary h-100 px-3 py-2 control_buttons" type="button" class="btn btn-outline-dark" disabled title="Nejprve označte položku">Vyřadit</button>
                        <button id="assign-device" class="btn btn-secondary h-100 px-3 py-2 control_buttons" type="button" class="btn btn-outline-dark" disabled title="Nejprve označte položku" data-toggle="modal" data-target="#assign-device-modal">Přidělit</button>
                        <button type="button" class="btn btn-success h-100 px-3 py-2 control_buttons" id="add_device_button"
                                data-toggle="modal" data-target="#addDeviceModal"><i class="fas fa-plus"></i>  Přidat</i></button>
                        <button id="remove-device" type="button" class="btn btn-danger h-100 px-3 py-2 control_buttons" disabled title="Nejprve označte položku" data-toggle="modal" data-target="#remove-device-modal"><i class="fas fa-trash"></i>  Odstranit</button>
                    </div>
                </div>
                <div class="collapse mb-1 mt-1" id="filter-collapse">
                     <div class="card card-body my-card border border-dark">
                        <h2 class="mb-3">Podrobné vyhledávaní</h2>
                        <form action="" method="post">
                        <div class="form-group row">
                            <label for="filter-device-type" class="col-2 mt-2">Typ zařízení</label>
                            <select class="form-control col-4" name="device-type" id="filter-device-type">
                                    <option value="all">- všechny -</option>
                                    <option value="Klávesnice">Klávesnice</option>
                                    <option value="Monitor">Monitor</option>
                                    <option value="Telefon">Telefon</option>
                                    <option value="Notebook">Notebook</option>
                            </select>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-2">Stav zařízení </div>
                            <div class="col-sm-10">
                                <div class="row ml-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="filter-status-1">
                                    <label class="form-check-label mr-3" for="filter-status-1">
                                        Volný
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="filter-status-2">
                                    <label class="form-check-label mr-3" for="filter-status-2">
                                        Přidělený
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="filter-status-3">
                                    <label class="form-check-label mr-3" for="filter-status-3">
                                        V opravě
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="filter-status-4">
                                    <label class="form-check-label mr-3" for="filter-status-4">
                                        Vyřazený
                                    </label>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 mt-2" for="filter-disponent">Disponent</label>
                            <input type="text" class="form-control col-4" id="filter-disponent" placeholder="Zadejte jméno disponenta...">
                        </div>                 
                        <div class="form-group row">
                            <label class="col-2 mt-2" for="filter-room">Místnost</label>
                            <input type="text" class="form-control col-4" id="filter-room" placeholder="Zadejte místnost...">
                        </div>                 
                        <button type="button" id="filter-search-button" class="btn btn-dark">Vyhledat</button>
                        </form>
                    </div>
                </div>
                <!-- Device table -->
                <table class="table table-striped table-bordered table-hover" id="device_table">
                    <thead class="thead-dark">
                        <tr>
                          <th><input class="form-check-input ml-3 my-tablecheck" id="tablerowcheckheader" title="Označiť všetko" type="checkbox">&nbsp;</input></th>
                          <th>Zařízení</th>
                          <th>Typ</th>
                          <th>Stav</th>
                          <th>Disponent</th>
                          <th>Místnost</th>
                        </tr>
                      </thead>
                      <tbody id="device_table_body">
                        <!-- Content generated by devices.js -->
                      </tbody>
                </table>
        </div>
                
                <!--------------------------------------------- Hidden forms ------------------------------------------------------>
                <!-- Remove device form -->
                <form action="./logic/remove_device.php" method="post" class="d-none">
                    <input type="text" name="remove-device-ids" id="remove-device-ids">
                    <button id="php-remove-device" type="submit">Odstranit</button>
                </form>
                <!-- Discard device form -->
                <form action="./logic/discard_device.php" method="post" class="d-none">
                    <input type="text" name="discard-device-ids" id="discard-device-ids">
                    <button id="php-discard-device" type="submit">Vyradit</button>
                </form>

    <?php require('template_footer.php') ?>
    