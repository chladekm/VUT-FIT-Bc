<?php
/*********************************************************************************
 * Project:   3BIT ITU, Project - Evidence system                                *
 *            Faculty of Information Technology                                  *
 *            Brno University of Technology                                      *
 * File:      remove_device.php                                                  *
 * Date:      08.12.2019                                                         *
 * Authors:   Peter Kruty, <xkruty00@stud.fit.vutbr.cz>                          *
 *********************************************************************************/

$indexes_from_js = $_POST["remove-device-ids"];


$inp = file_get_contents('../database/devices.json');
$temp_json = json_decode($inp);

$devices_to_remove = explode(" ", $indexes_from_js);
foreach($devices_to_remove as $device_to_remove){
    $device_to_remove = intval($device_to_remove);
    $temp_json->row_count--;
    unset($temp_json->devices[$device_to_remove]);    
}

$t = $temp_json->devices;
$temp_json->devices = [];
foreach($t as $k => $v) {
    array_push($temp_json->devices, $v);
}

$jsonData = json_encode($temp_json);
file_put_contents('../database/devices.json', $jsonData);

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0");
header("Location: ../devices.php");
die();
?>