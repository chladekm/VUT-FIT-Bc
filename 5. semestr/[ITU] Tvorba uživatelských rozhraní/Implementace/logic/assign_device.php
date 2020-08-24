<?php
/*********************************************************************************
 * Project:   3BIT ITU, Project - Evidence system                                *
 *            Faculty of Information Technology                                  *
 *            Brno University of Technology                                      *
 * File:      assign_device.php                                                  *
 * Date:      08.12.2019                                                         *
 * Authors:   Peter Kruty, <xkruty00@stud.fit.vutbr.cz>                          *
 *********************************************************************************/

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0");
header("Location: ../devices.php");

$indexes_from_js = $_POST["assign-device-ids"];


$inp = file_get_contents('../database/devices.json');
$temp_json = json_decode($inp);

$devices_to_assign = explode(" ", $indexes_from_js);
foreach($devices_to_assign as $device_to_assign){
    $device_to_assign = intval($device_to_assign);
    $temp_json->devices[$device_to_assign]->status = "Přidělený";
    $temp_json->devices[$device_to_assign]->disponent = $_POST["assign-device-disponent"];
    $temp_json->devices[$device_to_assign]->room = $_POST["assign-device-room"];
    $temp_json->devices[$device_to_assign]->assigndate = date("Y-m-d");
    $temp_json->devices[$device_to_assign]->discarddate = null;
}


$jsonData = json_encode($temp_json);
file_put_contents('../database/devices.json', $jsonData);
die();
?>