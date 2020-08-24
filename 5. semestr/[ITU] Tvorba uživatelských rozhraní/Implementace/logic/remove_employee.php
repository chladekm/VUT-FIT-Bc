<?php
	/*********************************************************************************
	 * Project:   3BIT ITU, Project - Evidence system                                *
	 *            Faculty of Information Technology                                  *
	 *            Brno University of Technology                                      *
	 * File:      remove_employee.php                                                *
	 * Date:      08.12.2019                                                         *
	 * Authors:   Borek Reich, <xreich06@stud.fit.vutbr.cz>                          *
	 *********************************************************************************/

	header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
	header("Pragma: no-cache"); // HTTP 1.0.
	header("Expires: 0");
	header("Location: ../employees.php");

	$remove_item = $_POST['remove-employee-id'];

	$inp = file_get_contents('../database/workers.json');
	$temp_json = json_decode($inp);

	$temp_json->row_count--;

	for($i=0; $i<$temp_json->row_count; $i++) {
		if ($temp_json->workers[$i]->id == $remove_item)
			unset($temp_json->workers[$i]);
	}

	$t = $temp_json->workers;
	$temp_json->workers = [];
	foreach($t as $k => $v) {
	    array_push($temp_json->workers, $v);
	}

	//$temp_json = array_values($temp_json);
	$jsonData = json_encode($temp_json);
	file_put_contents('../database/workers.json', $jsonData);

	die();
?>