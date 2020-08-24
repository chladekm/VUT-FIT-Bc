<?php
/*********************************************************************************
 * Project:   3BIT ITU, Project - Evidence system                                *
 *            Faculty of Information Technology                                  *
 *            Brno University of Technology                                      *
 * File:      add_employee.php                                                   *
 * Date:      08.12.2019                                                         *
 * Authors:   Borek Reich, <xreich06@stud.fit.vutbr.cz>                          *
 *********************************************************************************/

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0");
header("Location: ../employees.php");

echo $_POST["add-employees-name"];
echo $_POST["add-employees-typ"];
echo $_POST["add-employees-specialization"];
echo $_POST["add-employees-tel"];
echo $_POST["add-employees-address"];
echo $_POST["add-employees-psc"];
echo $_POST["add-employees-room"];

//echo $_FILES['add-device-img']['name']; 

$employee_name = $_POST["add-employees-name"];
$employee_type = $_POST["add-employees-typ"];
$employee_specialization = $_POST["add-employees-specialization"];
$employee_tel = $_POST["add-employees-tel"];
$employee_address = $_POST["add-employees-address"];
$employee_psc = $_POST["add-employees-psc"];
$employee_room = $_POST["add-employees-room"];

if ($employee_type == "option1") {
    $employee_type = "ucitel";
}
else if ($employee_type == "option2") {
    $employee_type = "ostatni";
}
else if ($employee_type == "option3") {
    $employee_type = "poradce";
}
else if ($employee_type == "option4") {
	$employee_type = "reditel";
}
else if ($employee_type == "option5") {
    $employee_type = "zastupce";
}

// Uploaded picture must be from this source
$device_img = "../files/" . $device_img_name;

$new_table_record = array('id' => rand(),
                            'name' => $employee_name,
                            'typ' => $employee_type,
                            'specialization' => $employee_specialization,
                            'tel' => $employee_tel,
                            'address' => $employee_address,
                            'psc' => $employee_psc,
                            'room' => $employee_room
                            );

$inp = file_get_contents('../database/workers.json');
$temp_json = json_decode($inp);
$temp_json->row_count++;

array_push($temp_json->workers, $new_table_record);
$jsonData = json_encode($temp_json);

file_put_contents('../database/workers.json', $jsonData);

require ("../connection_credentials.php");
echo $_SERVER['SERVER_NAME'];
echo $username;

?>
<script type="text/javascript">
	window.location.href = "<?php echo 'http://', $_SERVER['SERVER_NAME'], '/~',  $username, '/employees.php"'; ?>
</script>
<?php

die();

?>