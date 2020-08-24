<!-- ----------------------------------------------------------------------------!
 ! Project:   3BIT ITU, Project - Evidence system                                !
 !            Faculty of Information Technology                                  !
 !            Brno University of Technology                                      !
 ! File:      employees.php                                                      !
 ! Date:      08.12.2019                                                         !
 ! Authors:   Martin Chladek, <xchlad16@stud.fit.vutbr.cz>                       !
 !            Borek Reich, <xreich06@stud.fit.vutbr.cz>                          !
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
        <link rel="stylesheet" type="text/css" href="css/employees.css">
        <link rel="stylesheet" type="text/css" href="css/widgets.css">

        <!-- Own script -->
        <script type="text/javascript" src="js/script.js"></script>
        <script type="text/javascript" src="js/employees.js" defer></script>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

    </head>

    <?php require('template_header.php') ?>
        
    <div class="container-fluid p-5 animated fadeIn">
        <h2 id="main_title"> SPRÁVA ZAMĚSTNANCŮ </h2>
        <hr id="main_title_hr">
    

        <div id="widgets" class="row mt-5">
            <div class="col-md-3">
                <div class="widget widget_0">
                    <p class="float-left h-100"><i class="fas fa-users align-middle"></i></p>
                    <p class="float-right"><span>
                    <?php 
                        $url = './database/workers.json';;
                        $data = file_get_contents($url);
                        $employees = json_decode($data);
                        echo $employees->row_count;
                    ?>
                        
                    </span>zaměstnanců</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget_1">
                    <p class="float-left"><i class="fas fa-chalkboard-teacher align-middle"></i></p>
                    <p class="float-right"><span>
                        <?php 
                        $url = './database/workers.json';
                        $data = file_get_contents($url);
                        $employees = json_decode($data);
                        $count = 0;
                        foreach ($employees->workers as $worker) {
                            if($worker->typ == 'ucitel')
                                $count++;
                        }
                        echo $count;
                    ?>
                    </span>učitelů</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget_2">
                    <p class="float-left"><i class="fas fa-hands-helping align-middle"></i></p>
                    <p class="float-right"><span>
                    <?php 
                        $url = './database/workers.json';
                        $data = file_get_contents($url);
                        $employees = json_decode($data);
                        $count = 0;
                        foreach ($employees->workers as $worker) {
                            if($worker->typ == 'poradce')
                                $count++;
                        }
                        echo $count;
                    ?></span>výchovných poradců</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget_3">
                    <p class="float-left"><i class="fas fa-briefcase align-middle"></i></p>
                    <p class="float-right"><span>
                    <?php 
                        $url = './database/workers.json';
                        $data = file_get_contents($url);
                        $employees = json_decode($data);
                        $count = 0;
                        foreach ($employees->workers as $worker) {
                            if($worker->typ == 'poradce')
                                $count++;
                        }
                        echo $count;
                    ?></span>ostatních zaměstnanců</p>
                </div>
            </div>
        </div>

      <div class="row control_panel">
            <div class="col-lg-8 col-12">
                    <span class="search_box btn btn-dark h-100">
                        <input id="search_input" type="text" class="w-100" placeholder="&nbsp;&nbsp;Vyhledat zaměstnance..." name="search">
                    </span>
                    <button id="search_button" class="btn btn-dark h-100" type="submit" onclick="search_employee()"><i class="fa fa-search"></i></button>
            </div>
            <div class="col-lg-4 col-12 text-right">
                <button id="add_employee_button" type="button" class="btn btn-success text-right h-100 px-3 py-2 control_buttons" data-toggle="modal" data-target="#addEmployeeModal"><i class="fas fa-plus"></i> Přidat</button>
            </div>
        </div>
        
        <div class="m-auto" id="employees-search">
                <h2 id="label_vedeni" class="mt-5">Vedení</h2>
                <hr class="employees_hr">
                <div class="row p-1" id="div_for_employees_managers">
                </div>

                <h2 id="label_ucitele" class="mt-5">Učitelé</h2>
                <hr class="employees_hr">
                <div class="row p-1" id="div_for_employees_teachers">                           </div>

                <h2 id="label_poradce" class="mt-5">Výchovní poradcové a asistenti</h2>
                <hr class="employees_hr">
                <div class="row p-1" id="div_for_employees_advisers">
                </div>

                <h2 id="label_ostatni" class="mt-5">Ostatní</h2>
                <hr class="employees_hr">
                <div class="row p-1" id="div_for_employees_others">
                </div>    
                
<!--                 <div class="row" id="div_for_employees">
                </div> -->
    	</div>
    </div>

    <div class="modal fade" id="remove-employee-modal" tabindex="-1" role="dialog" aria-labelledby="remove-employee-modalTitle"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content my-modal">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="remove-employee-modalTitle"> <strong>Odstranění zaměstnance</strong></h5> 
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            <p><strong>POZOR!</strong><br> Po odstranění se záznam z databáze vymaže natvralo. <br> <strong>Přejete si zaměstnance vymazat?</strong></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">                                
                    <button type="button" class="btn btn-outline-success" id="remove-employee-confirm">Ano</button>
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal" id="remove-employee-cancel">Ne</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal windows section -->
    <!-- Employee detail modal window -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content my-modal">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="exampleModalLongTitle"> <strong>Detail zaměstnance</strong></h5> 
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-4">
                            <p class="text-right"><strong>Jméno: </strong></p>
                            <p class="text-right"><strong>Specializace: </strong></p>
                            <p class="text-right"><strong>Tel. číslo: </strong></p>
                            <p class="text-right"><strong>Adresa: </strong></p>
                            <p class="text-right"><strong>PSČ: </strong></p>
                            <p class="text-right"><strong>Místnost: </strong></p>
                        </div>
                        <div class="col-8">
                            <p id="employees-detail-name"></p>
                            <p id="employees-detail-type"></p>
                            <p id="employees-detail-specialization"></p>
                            <p id="employees-detail-tel"></p>
                            <p id="employees-detail-address"></p>
                            <p id="employees-detail-psc"></p>
                            <p id="employees-detail-room"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">   
                    <button type="button" id="show-devices" class="btn btn-outline-primary" data-toggle="modal" data-target="#show-employee-devices-modal" >Zařízení</button>  
                    <button type="button" id="update-employee" class="btn btn-outline-primary" data-toggle="modal" data-target="#update-employee-modal">Upravit</button>
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Zrušit</button>
                    <button type="button" id="remove-employee" class="btn btn-outline-danger" data-toggle="modal" data-target="#remove-employee-modal"><i class="fas fa-trash"></i>Smazat</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End employee detail modal window -->

    <div class="modal fade" id="show-employee-devices-modal" tabindex="-1" role="dialog" aria-labelledby="show-employee-devices-modal-title"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content my-modal">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="show-employee-device-modal-long-title"> <strong id="title-device-employee"></strong></h5> 
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="div-for-device">
                        <table class="table table-striped table-bordered" id="device_table">
                        <thead class="thead-dark">
                            <tr>
                              <th>Zařízení</th>
                              <th>Typ</th>
                              <th>Stav</th>
                              <th>Místnost</th>
                            </tr>
                          </thead>
                          <tbody class="device_table_body" id="employee_table_body">
                            <!-- Content generated by devices.js -->
                          </tbody>
                    </table>
                </div>
                <div class="modal-footer">   
                    <button type="button" class="btn btn-outline-secondary" id="dismiss-device" data-dismiss="modal">Zavřít</button>                
                </div>
            </div>
        </div>
    </div>
    
    <!-- Update employee modal window -->
    <div class="modal" id="update-employee-modal" tabindex="-1" role="dialog" aria-labelledby="update-employee-modalTitle" aria-hidden="true" data-backdrop="false">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content my-modal">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="update-employee-modalTitle"> <strong>Úprava zaměstnance</strong></h5> 
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <!-- Jmeno pole -->
                        <div class="col-md-4 text-right">
                            <label for="update-employee-name"><strong>Jméno: </strong></label>
                        </div>
                        <div class="col-md-8">
                            <input class="form-control" id="update-employees-name" name="update-employees-name" type="text">    
                        </div>

                        <!-- Specializace pole -->
                        <div class="col-md-4 text-right mt-3">
                            <label for="update-employee-specialization"><strong>Typ: </strong></label>
                        </div>
                        <div class="col-md-8 mt-3">
                            <select class="custom-select" id="update-employees-typ" name="update-employees-typ">
                                <option value="null" id="update-employees-typ-selected" disabled selected></option>
                                <option value="ucitel">Učitel</option>
                                <option value="ostatni">Ostatní</option>
                                <option value="poradce">Poradce</option>
                                <option value="reditel">Ředitel</option>
                                <option value="zastupce">Zástupce ředitele</option>
                            </select>  
                        </div>

                        <!-- Specializace pole -->
                        <div class="col-md-4 text-right mt-3">
                            <label for="update-employees-specialization"><strong>Specializace: </strong></label>
                        </div>
                        <div class="col-md-8 mt-3">
                            <input class="form-control" id="update-employees-specialization" name="update-employees-specialization" type="text">    
                        </div>
                    
                        <!-- Telefon pole -->
                        <div class="col-md-4 text-right mt-3">
                            <label for="update-employees-tel"><strong>Tel. číslo: </strong></label>
                        </div>
                        <div class="col-md-8 mt-3">
                            <input class="form-control" id="update-employees-tel" name="update-employees-tel" type="text">    
                        </div>

                         <!-- Adresa pole -->
                        <div class="col-md-4 text-right mt-3">
                            <label for="update-employees-address"><strong>Adresa: </strong></label>
                        </div>
                        <div class="col-md-8 mt-3">
                            <input class="form-control" id="update-employees-address" name="update-employees-address" type="text">    
                        </div>
              
                        <!-- PSC pole -->
                        <div class="col-md-4 text-right mt-3">
                            <label for="update-employees-psc"><strong>PSČ: </strong></label>
                        </div>
                        <div class="col-md-8 mt-3">
                            <input class="form-control" id="update-employees-psc" name="update-employees-psc" type="text">    
                        </div>

                        <!-- Mistnost pole -->
                        <div class="col-md-4 text-right mt-3">
                            <label for="update-employees-psc"><strong>Místnost: </strong></label>
                        </div>
                        <div class="col-md-8 mt-3">
                            <input class="form-control" id="update-employees-room" name="update-employees-room" type="text">    
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">                                
                    <button type="button" class="btn btn-outline-success" id="update-save" data-dismiss="modal">Uložit úpravu</button>
                    <button id="update-employee-cancel" type="button" class="btn btn-outline-danger" data-dismiss="modal">Zrušit úpravu</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End employee detail modal window -->

    <!-- Add employee modal window -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="addEmployeeModalTitle"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content my-modal">
            <form action="./logic/add_employee.php" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="addDeviceModalTitle"> <strong>Přidání zaměstnance</strong></h5> 
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">                            

                        <!-- Jmeno pole -->
                        <div class="col-md-4 text-right">
                            <label for="add-employees-name"><strong>Jméno: </strong></label>
                        </div>
                        <div class="col-md-8">
                            <input class="form-control" id="add-employees-name" name="add-employees-name" type="text">    
                        </div>

                        <!-- Typ pole -->
                        <div class="col-md-4 text-right mt-3">
                            <label for="add-employees-typ"><strong>Role: </strong></label>
                        </div>
                        <div class="col-md-8 mt-3">
                            <fieldset class="form-group">
                                <div class="row">
                                <div class="col-sm-10">
                                    <div class="form-check row">
                                        <input class="form-check-input" type="radio" name="add-employees-typ" id="add-employees-typ1" value="option1" checked>
                                        
                                        <label class="form-check-label" for="add-employees-typ1">
                                            Učitel
                                        </label>
                                    </div>
                                    <div class="form-check row">
                                        <input class="form-check-input" type="radio" name="add-employees-typ" id="add-employees-typ4" value="option2">
                                        <label class="form-check-label" for="add-employees-typ4">
                                            Ostatní
                                        </label>
                                    </div>
                                    <div class="form-check row">
                                        <input class="form-check-input" type="radio" name="add-employees-typ" id="add-employees-typ3" value="option3">
                                        <label class="form-check-label" for="add-employees-typ3">
                                            Poradce
                                        </label>
                                    </div>
                                    <div class="form-check row">
                                        <input class="form-check-input" type="radio" name="add-employees-typ" id="add-employees-typ2" value="option4">
                                        <label class="form-check-label" for="add-employees-typ2">
                                            Ředitel
                                        </label>
                                    </div>
                                    <div class="form-check row">
                                        <input class="form-check-input" type="radio" name="add-employees-typ" id="add-employees-typ2" value="option5">
                                        <label class="form-check-label" for="add-employees-typ2">
                                            Zástupce ředitele
                                        </label>
                                    </div>
                                </div>
                                </div>
                            </fieldset>
                        </div>

                        <!-- Specializace pole -->
                        <div class="col-md-4 text-right mt-3">
                            <label for="add-employees-specialization"><strong>Specializace: </strong></label>
                        </div>
                        <div class="col-md-8 mt-3">
                            <input class="form-control" id="add-employees-specialization" name="add-employees-specialization" type="text">    
                        </div>

                        <!-- Telefon pole -->
                        <div class="col-md-4 text-right mt-3">
                            <label for="add-employees-tel"><strong>Tel. číslo: </strong></label>
                        </div>
                        <div class="col-md-8 mt-3">
                            <input class="form-control" id="add-employees-tel" name="add-employees-tel" type="text">    
                        </div>

                         <!-- Adresa pole -->
                        <div class="col-md-4 text-right mt-3">
                            <label for="add-employees-address"><strong>Adresa: </strong></label>
                        </div>
                        <div class="col-md-8 mt-3">
                            <input class="form-control" id="add-employees-address" name="add-employees-address" type="text">    
                        </div>
              
                        <!-- PSC pole -->
                        <div class="col-md-4 text-right mt-3">
                            <label for="add-employees-psc"><strong>PSČ: </strong></label>
                        </div>
                        <div class="col-md-8 mt-3">
                            <input class="form-control" id="add-employees-psc" name="add-employees-psc" type="text">    
                        </div>

                        <!-- Mistnost pole -->
                        <div class="col-md-4 text-right mt-3">
                            <label for="add-employees-psc"><strong>Místnost: </strong></label>
                        </div>
                        <div class="col-md-8 mt-3">
                            <input class="form-control" id="add-employees-room" name="add-employees-room" type="text">    
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
    <!-- End Add employee model window -->

    <!-------------------------- Hidden forms ----------------------------->
    <!-- Remove employee form -->
    <form action="./logic/remove_employee.php" method="post" class="d-none">
        <input type="text" name="remove-employee-id" id="remove-employee-id">
        <button id="php-remove-employee" type="submit">Odstranit</button>
    </form>

    <form action="./logic/update_employee.php" method="post" class="d-none">
        <input type="text" name="update-employee-id-send" id="update-employee-id-send">
        <input type="text" name="update-employee-name-send" id="update-employee-name-send">
        <input type="text" name="update-employee-typ-send" id="update-employee-typ-send">
        <input type="text" name="update-employee-specialization-send" id="update-employee-specialization-send">
        <input type="text" name="update-employee-tel-send" id="update-employee-tel-send">
        <input type="text" name="update-employee-address-send" id="update-employee-address-send">
        <input type="text" name="update-employee-psc-send" id="update-employee-psc-send">
        <input type="text" name="update-employee-room-send" id="update-employee-room-send">

        <button id="php-update-employee" type="submit">Upravit</button>
    </form>

<?php require('template_footer.php') ?>