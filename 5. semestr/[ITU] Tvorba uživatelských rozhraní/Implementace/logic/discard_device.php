<?php
/*********************************************************************************
 * Project:   3BIT ITU, Project - Evidence system                                *
 *            Faculty of Information Technology                                  *
 *            Brno University of Technology                                      *
 * File:      discard_device.php                                                 *
 * Date:      08.12.2019                                                         *
 * Authors:   Peter Kruty, <xkruty00@stud.fit.vutbr.cz>                          *
 *********************************************************************************/

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0");
header("Location: ../devices.php");

$indexes_from_js = $_POST["discard-device-ids"];

$inp = file_get_contents('../database/devices.json');
$temp_json = json_decode($inp);

$devices_to_discard = explode(" ", $indexes_from_js);
foreach($devices_to_discard as $device_to_discard){
    $device_to_discard = intval($device_to_discard);
    echo $device_to_discard;
    $temp_json->devices[$device_to_discard]->status = "Vyřazený";
    $temp_json->devices[$device_to_discard]->disponent = null;
    $temp_json->devices[$device_to_discard]->room = null;
    $temp_json->devices[$device_to_discard]->discarddate = date("Y-m-d");
}


$jsonData = json_encode($temp_json);
file_put_contents('../database/devices.json', $jsonData);
die();
?>