<!-- ----------------------------------------------------------------------------!
 ! Project:   3BIT ITU, Project - Evidence system                                !
 !            Faculty of Information Technology                                  !
 !            Brno University of Technology                                      !
 ! File:      repairs.php                                                        !
 ! Date:      08.12.2019                                                         !
 ! Authors:   Peter Kruty, <xkruty00@stud.fit.vutbr.cz>                          !
 !            Martin Chladek, <xchlad16@stud.fit.vutbr.cz>                       !
 !------------------------------------------------------------------------------->

<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>EvidIT - Systém pre evidenciu techniky</title>

        <?php require('links.php') ?>

        <!-- Own style -->
        <link rel="stylesheet" type="text/css" href="css/repairs.css">
        <link rel="stylesheet" type="text/css" href="css/widgets.css">

        <!-- Own script -->
        <script type="text/javascript" src="js/script.js"></script>
        <script type="text/javascript" src="js/repairs.js" defer></script>

    </head>
    <?php require('template_header.php') ?>

        <div class="container-fluid p-5 animated fadeIn">
            <h2 id="main_title">SPRÁVA OPRAV</h2>
            <hr id="main_title_hr">
             <div id="widgets" class="row mt-5">
                <div class="col-md-3">
                    <div class="widget widget_0">
                        <p class="float-left h-100"><i class="align-middle fas fa-cogs"></i></p>
                        <p class="float-right"><span id="repairs-counter"></span>oprav</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget_1">
                        <p class="float-left"><i class="align-middle fas fa-check-double"></i></i></p>
                        <p class="float-right"><span id="success-repairs-counter"></span>úspěšných oprav</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget_2">
                        <p class="float-left"><i class="align-middle fas fa-ban"></i></p>
                        <p class="float-right"><span id="unsuccess-repairs-counter"></span>neúspěšných oprav</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget_3">
                        <p class="float-left"><i class="align-middle fas fa-laptop"></i></p>
                        <p class="float-right"><span id="inrepair-repairs-counter"></span>zařízení v opravě</p>
                    </div>
                </div>
            </div>

            <div class="row control_panel">
                <div class="col-lg-5 col-12">
                    <form id="search_form" class="p-0 m-0 h-100">
                        <button class="btn btn-dark h-100 pl-3 pr-3" id="filter-button" type="button" >
                            <i class="fa fa-filter"></i>
                        </button>
                        <span class="search_box btn btn-dark h-100"><input id="search_input" class="w-100 form-control" type="text" placeholder="Vyhledat..." name="search"></span>
                        <button id="search_button" type="button" class="btn btn-dark h-100" onclick="search_repair()" type="submit"><i class="fa fa-search"></i></button>
                    </form>
                </div> 
                <div class="col-lg-7 col-12 text-right">
                    <!-- <button type="button" class="btn btn-secondary h-100 px-3 py-2 control_buttons">Upravit</button> -->
                    <button type="button" class="btn btn-success h-100 px-3 py-2 control_buttons" data-toggle="modal" data-target="#addrepairModal"><i class="fas fa-plus"></i> Přidat</button>
                    <button id="remove-repairs" type="button" class="btn btn-danger h-100 px-3 py-2 control_buttons" disabled title="Nejprve označte položku" data-toggle="modal" data-target="#remove-repair-modal" title="Nejprve označte položku"><i class="fas fa-trash"></i> Odstranit</button>
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
                            <label class="col-2 mt-2" for="filter-reason">Závada</label>
                            <input type="text" class="form-control col-4" id="filter-reason" placeholder="Zadejte jméno disponenta...">
                        </div>                 
                        <div class="form-group row">
                            <div class="col-sm-2">Stav opravy </div>
                            <div class="col-sm-10">
                                <div class="row ml-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="filter-status-1">
                                        <label class="form-check-label mr-3" for="filter-status-1">
                                            V opravě
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="filter-status-2">
                                        <label class="form-check-label mr-3" for="filter-status-2">
                                            Opraveno
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="filter-status-3">
                                        <label class="form-check-label mr-3" for="filter-status-3">
                                            Zamítnuto
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="filter-search-button" class="btn btn-dark">Vyhledat</button>
                        </form>
                    </div>
                </div>
            <table class="table table-striped table-bordered table-hover" id="repairs_table">
                <thead class="thead-dark">
                    <tr>
                      <th><input class="form-check-input ml-3 my-tablecheck" id="tablerowcheckheader" title="Označiť všetko" type="checkbox">&nbsp;</input></th>
                      <th>Zařízení</th>
                      <th>Typ</th>
                      <th>Závada</th>
                      <th>Stav</th>
                    </tr>
                  </thead>
                  <tbody id="repairs_table_body">
                 
                  </tbody>
            </table>
        </div>

        <!----------------------------------------------- Modals ------------------------------------------------->
        <!-- repair detail modal window -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"  aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content my-modal">
                    <div class="modal-header">
                        <h5 class="modal-title text-center" id="exampleModalLongTitle"> <strong>Detail opravy</strong></h5> 
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-2">
                                <p class="text-right"><strong>Zařízení: </strong></p>
                                <p class="text-right"><strong>Typ: </strong></p>
                                <p class="text-right"><strong>Závada: </strong></p>
                                <p class="text-right"><strong>Stav: </strong></p>
                            </div>
                            <div class="col-4">
                                <p id="repair-detail-device"></p>
                                <p id="repair-detail-type"></p>
                                <p id="repair-detail-reason"></p>
                                <p id="repair-detail-status"></p>
                            </div>
                            <div class="col-3">
                                <p class="text-right"><strong>Datum nahlášení: </strong></p>
                                <p class="text-right"><strong>Datum vyřešení: </strong></p>
                            </div>
                            <div class="col-2">
                                <p id="repair-detail-startdate"></p>
                                <p id="repair-detail-enddate"></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">                                
                        <button type="button" id="update-repair" class="btn btn-outline-primary" data-toggle="modal" data-target="#update-repair-modal">Upravit</button>
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Zrušit</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End repair detail modal window -->
        <!-- Update repair modal window -->
        <div class="modal" id="update-repair-modal" tabindex="-1" role="dialog" aria-labelledby="update-repair-modalTitle" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content my-modal">
                    <div class="modal-header">
                        <h5 class="modal-title text-center" id="update-repair-modalTitle"> <strong>Úprava opravy</strong></h5> 
                        <button id="update-repair-close-button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="./logic/update_repair.php" method="post">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col-sm-4 mt-2 text-sm-right text-left">
                                            <label for="add-repair-device"><strong>Zařízení *</strong></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <select class="custom-select" id="update-repair-device" name="update-repair-device">
                                                <option disabled selected value="" id="update-repair-device-selected"></option>
                                                <option value="Cherry MX-BOARD 3.1">Cherry MX-BOARD 3.1</option>
                                                <option value="Lenovo IdeaPad 330">Lenovo IdeaPad 330</option>
                                                <option value="HP Deskjet 3545">HP Deskjet 3545</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4 text-right">
                                            <label for="update-repair-reason"><strong>Závada: </strong></label>
                                        </div>
                                        <div class="col-8">
                                            <input class="form-control" id="update-repair-reason" name="update-repair-reason" type="text">    
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4 text-right">
                                            <label for="update-repair-status"><strong>Stav: </strong></label>
                                        </div>
                                        <div class="col-8">
                                            <select class="custom-select" id="update-repair-status" name="update-repair-status">
                                                <option value="" id="update-repair-status-selected" selected disabled></option>
                                                <option value="V opravě">V opravě</option>
                                                <option value="Opraveno">Opraveno</option>
                                                <option value="Zamítnuto">Zamítnuto</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row form-group">
                                        <div class="col-12 text-left">
                                            <label for="update-repair-startdate"><strong>Datum nahlášení (m-d-R): </strong></label>
                                        </div>
                                        <div class="col-12">
                                            <input class="form-control" type="date" id="update-repair-startdate" name="update-repair-startdate">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-12 text-left">
                                            <label for="update-repair-enddate"><strong>Datum vyřešení (m-d-R): </strong></label>
                                        </div>
                                        <div class="col-12">
                                            <input class="form-control" type="date" id="update-repair-enddate" name="update-repair-enddate">
                                        </div>
                                    </div>
                                    <input type="text" id="update-repair-id" name="update-repair-id" class="d-none">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">                                
                            <button type="submit" class="btn btn-outline-success" >Uložit úpravu</button>
                            <button id="update-repair-cancel" type="button" class="btn btn-outline-danger" data-dismiss="modal">Zrušit úpravu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End update repair modal window -->
        <!-- Add repair modal window -->
        <div class="modal fade" id="addrepairModal" tabindex="-1" role="dialog" aria-labelledby="addrepairModalTitle"  aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content my-modal">
                <form action="./logic/add_repair.php" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title text-center" id="addrepairModalTitle"> <strong>Přidání opravy</strong></h5> 
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
                                <label for="add-repair-device"><strong>Zařízení *</strong></label>
                            </div>
                            <div class="col-sm-10">
                                <select class="custom-select" id="add-repair-device" name="add-repair-device">
                                    <option selected>Vyberte zařízení...</option>
                                    <option value="Cherry MX-BOARD 3.1">Cherry MX-BOARD 3.1</option>
                                    <option value="Lenovo IdeaPad 330">Lenovo IdeaPad 330</option>
                                    <option value="HP Deskjet 3545">HP Deskjet 3545</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-2 mt-2 text-sm-right text-left">
                                <label for="add-repair-reason"><strong>Závada *</strong></label>
                            </div>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="add-repair-reason" name="add-repair-reason" placeholder="Zadejte popis závady...">
                            </div>
                        </div>
                        <fieldset class="form-group">
                            <div class="row">
                            <legend class="col-form-label col-sm-2 text-sm-right text-left pt-0"><strong>Stav *</strong></legend>
                            <div class="col-sm-10">
                                <div class="form-check">
                                <input class="form-check-input" type="radio" name="add-repair-status" id="add-repair-status1" value="option1" checked>
                                <label class="form-check-label" for="add-repair-status1">
                                    V opravě
                                </label>
                                </div>
                                <div class="form-check">
                                <input class="form-check-input" type="radio" name="add-repair-status" id="add-repair-status2" value="option2">
                                <label class="form-check-label" for="add-repair-status2">
                                    Opraveno
                                </label>
                                </div>
                                <div class="form-check">
                                <input class="form-check-input" type="radio" name="add-repair-status" id="add-repair-status3" value="option3">
                                <label class="form-check-label" for="add-repair-status3">
                                    Zamítnuto
                                </label>
                                </div>
                            </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="modal-footer">                                
                        <button type="submit" class="btn btn-outline-success" id="modal-add-repair-button">Přidat</button>
                        <button type="button" class="btn btn-outline-secondary" id="modal-add-repair-cancel" data-dismiss="modal">Zrušit</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Add repair model window -->
        <!-- Remove repair confirmation model window -->
        <div class="modal fade" id="remove-repair-modal" tabindex="-1" role="dialog" aria-labelledby="remove-repair-modalTitle"  aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content my-modal">
                    <div class="modal-header">
                        <h5 class="modal-title text-center" id="remove-repair-modalTitle"> <strong>Odstranění opravy</strong></h5> 
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 text-center">
                                <p><strong>POZOR!</strong><br> Po odstranění se záznam z databáze vymaže natvralo. <br> <strong>Přejete si opravu vymazat?</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">                                
                        <button type="button" class="btn btn-outline-success" id="remove-repair-confirm">Ano</button>
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Ne</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End remove repair confirmation model window -->
        <!--------------------------------------------- Hidden forms ------------------------------------------------------>
            <!-- Remove repair form -->
            <form action="./logic/remove_repair.php" method="post" class="d-none">
                <input type="text" name="remove-repair-ids" id="remove-repair-ids">
                <button id="php-remove-repair" type="submit">Odstranit</button>
            </form>
                    

    <?php require('template_footer.php') ?>