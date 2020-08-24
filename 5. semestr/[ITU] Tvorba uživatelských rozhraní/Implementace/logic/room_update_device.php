<?php
    /*********************************************************************************
     * Project:   3BIT ITU, Project - Evidence system                                *
     *            Faculty of Information Technology                                  *
     *            Brno University of Technology                                      *
     * File:      room_update_device.php                                             *
     * Date:      08.12.2019                                                         *
     * Authors:   Borek Reich, <xreich06@stud.fit.vutbr.cz>                          *
     *********************************************************************************/
    $update_device_id = $_POST["update-device-id"];
    $update_device_name = $_POST["update-device-name"];
    $update_device_type = $_POST["update-device-type"];
    if ($_POST["update-device-status"] != "") {
        $update_device_status = $_POST["update-device-status"];
    }
    if ($_POST["update-device-disponent"] != "") {
        $update_device_disponent = $_POST["update-device-disponent"];
    }
    if ($_POST["update-device-room"] != "") {
        $update_device_room = $_POST["update-device-room"];
    }
    $update_device_implemdate = $_POST["update-device-implemdate"];
    $update_device_assigndate = $_POST["update-device-assigndate"];
    $update_device_discarddate = $_POST["update-device-discarddate"];


    $inp = file_get_contents('../database/devices.json');
    $temp_json = json_decode($inp);

    
    $temp_json->devices[$update_device_id]->name = $update_device_name;
    $temp_json->devices[$update_device_id]->type = $update_device_type;
    
    if ($_POST["update-device-status"] != "") {
        $temp_json->devices[$update_device_id]->status = $_POST["update-device-status"];
    }
    if ($_POST["update-device-disponent"] != "") {
        $temp_json->devices[$update_device_id]->disponent = $_POST["update-device-disponent"];
    }
    if ($_POST["update-device-room"] != "") {
        $temp_json->devices[$update_device_id]->room = $_POST["update-device-room"];
    }
    if ($_POST["update-device-implemdate"] != "") {
        $temp_json->devices[$update_device_id]->implemdate = $_POST["update-device-implemdate"];
    }
    else {
        $temp_json->devices[$update_device_id]->implemdate = null;
    }
    if ($_POST["update-device-assigndate"] != "") {
        $temp_json->devices[$update_device_id]->assigndate = $_POST["update-device-assigndate"];
    }
    else {
        $temp_json->devices[$update_device_id]->assigndate = null;
    }
    if ($_POST["update-device-discarddate"] != "") {
        $temp_json->devices[$update_device_id]->discarddate = $_POST["update-device-discarddate"];
    }
    else {
        $temp_json->devices[$update_device_id]->discarddate = null;
    }

    $jsonData = json_encode($temp_json);
    file_put_contents('../database/devices.json', $jsonData);

    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    header("Pragma: no-cache"); // HTTP 1.0.
    header("Expires: 0");
    header("Location: ../rooms.php?patro=1");
    die();
?>