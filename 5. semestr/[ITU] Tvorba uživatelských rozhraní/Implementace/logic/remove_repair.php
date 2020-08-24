<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0");
header("Location: ../repairs.php");

$indexes_from_js = $_POST["remove-repair-ids"];
 


$inp = file_get_contents('../database/repairs.json');
$temp_json = json_decode($inp);

$repairs_to_remove = explode(" ", $indexes_from_js);
foreach($repairs_to_remove as $repair_to_remove){
    $repair_to_remove = intval($repair_to_remove);
    echo $repair_to_remove;
    $temp_json->row_count--;
    unset($temp_json->repairs[$repair_to_remove]);    
}

$t = $temp_json->repairs;
$temp_json->repairs = [];
foreach($t as $k => $v) {
    array_push($temp_json->repairs, $v);
}

$jsonData = json_encode($temp_json);
file_put_contents('../database/repairs.json', $jsonData);

die();

?>