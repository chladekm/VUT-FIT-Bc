<?php
/*********************************************************************************
 * Project:   3BIT ITU, Project - Evidence system                                *
 *            Faculty of Information Technology                                  *
 *            Brno University of Technology                                      *
 * File:      add_device.php                                                     *
 * Date:      08.12.2019                                                         *
 * Authors:   Peter Kruty, <xkruty00@stud.fit.vutbr.cz>                          *
 *********************************************************************************/

$device_name = $_POST["add-device-name"];
$device_type = $_POST["add-device-type"];
$device_status = $_POST["add-device-status"];
if ($device_status == "option1") {
    $device_status = "Volný";
}
else if ($device_status == "option2") {
    $device_status = "Přirazený";
}
else if ($device_status == "option3") {
    $device_status = "Vyřazený";
}
$device_disponent = $_POST["add-device-disponent"];
$device_room = $_POST["add-device-room"];
$device_img_name = $_FILES["add-device-img"]['name'];


if ($_POST["add-device-disponent"] == "Vyberte disponenta...") {
    $device_disponent = "";
}
if ($_POST["add-device-room"] == "Vyberte místnost...") {
    $device_room = "";
}

// Uploaded picture must be from this source
$device_img = "../files/" . $device_img_name;

$new_table_record = array('id' => "dev000",
                            'name' => $device_name,
                            'type' => $device_type,
                            'status' => $device_status,
                            'implemdate' => "2019-12-18",
                            'assigndate' => "2019-12-18",
                            'discarddate' => "2019-12-18",
                            'disponent' => $device_disponent,
                            'room' => $device_room,
                            'image' => $device_img
                            );

$inp = file_get_contents('../database/devices.json');
$temp_json = json_decode($inp);
$temp_json->row_count++;


array_push($temp_json->devices, $new_table_record);
$jsonData = json_encode($temp_json);
file_put_contents('../database/devices.json', $jsonData);

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0");
header("Location: ../devices.php");
die();
?>