<?php
    /*********************************************************************************
     * Project:   3BIT ITU, Project - Evidence system                                *
     *            Faculty of Information Technology                                  *
     *            Brno University of Technology                                      *
     * File:      update_repair.php                                                  *
     * Date:      08.12.2019                                                         *
     * Authors:   Peter Kruty, <xkruty00@stud.fit.vutbr.cz>                          *
     *********************************************************************************/
    $inp = file_get_contents('../database/repairs.json');
    $temp_json = json_decode($inp);
    
    $update_repair_id = $_POST["update-repair-id"];
    
    if ($_POST["update-repair-device"] != "") {
        $temp_json->repairs[$update_repair_id]->device = $_POST["update-repair-device"];
    }

    if ($_POST["update-repair-reason"] != "") {
        $temp_json->repairs[$update_repair_id]->reason = $_POST["update-repair-reason"];
    }
    if ($_POST["update-repair-status"] != "") {
        $temp_json->repairs[$update_repair_id]->status = $_POST["update-repair-status"];
    }
    if ($_POST["update-repair-startdate"] != "") {
        $temp_json->repairs[$update_repair_id]->startdate = $_POST["update-repair-startdate"];
    }
    else {
        $temp_json->repairs[$update_repair_id]->startdate = null;
    }
    if ($_POST["update-repair-enddate"] != "") {
        $temp_json->repairs[$update_repair_id]->enddate = $_POST["update-repair-enddate"];
    }
    else {
        $temp_json->repairs[$update_repair_id]->enddate = null;
    }
   
    $jsonData = json_encode($temp_json);
    file_put_contents('../database/repairs.json', $jsonData);

    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    header("Pragma: no-cache"); // HTTP 1.0.
    header("Expires: 0");
    header("Location: ../repairs.php");
    die();
?>