<?php
/*********************************************************************************
 * Project:   3BIT ITU, Project - Evidence system                                *
 *            Faculty of Information Technology                                  *
 *            Brno University of Technology                                      *
 * File:      add_repair.php                                                     *
 * Date:      08.12.2019                                                         *
 * Authors:   Peter Kruty, <xkruty00@stud.fit.vutbr.cz>                          *
 *********************************************************************************/

$add_repair_device = $_POST["add-repair-device"];
$add_repair_reason = $_POST["add-repair-reason"];
$add_repair_status = $_POST["add-repair-status"];
if ($add_repair_status == "option1") {
    $add_repair_status = "V opravě";
}
else if ($add_repair_status == "option2") {
    $add_repair_status = "Opraveno";
}
else if ($add_repair_status == "option3") {
    $add_repair_status = "Zamítnuto";
}

$new_table_record = array('id' => "rep000",
                            'device' => $add_repair_device,
                            'reason' => $add_repair_reason,
                            'type' => "Zariadenie",
                            'status' => $add_repair_status,
                            'startdate' => "2019-12-18",
                            'enddate' => "2019-12-18"
                            );

$inp = file_get_contents('../database/repairs.json');
$temp_json = json_decode($inp);
$temp_json->row_count++;


array_push($temp_json->repairs, $new_table_record);
$jsonData = json_encode($temp_json);
file_put_contents('../database/repairs.json', $jsonData);

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0");
header("Location: ../repairs.php");
die();
?>