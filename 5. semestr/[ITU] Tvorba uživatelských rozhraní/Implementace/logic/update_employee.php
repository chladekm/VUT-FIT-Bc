<?php
    /*********************************************************************************
     * Project:   3BIT ITU, Project - Evidence system                                *
     *            Faculty of Information Technology                                  *
     *            Brno University of Technology                                      *
     * File:      update_empolyee.php                                                *
     * Date:      08.12.2019                                                         *
     * Authors:   Borek Reich, <xreich06@stud.fit.vutbr.cz>                          *
     *********************************************************************************/

    $update_item = $_POST['update-employee-id-send'];

    $inp = file_get_contents('../database/workers.json');
    $temp_json = json_decode($inp);

    for($i=0; $i<$temp_json->row_count; $i++) {
        if ($temp_json->workers[$i]->id == $temp_json->workers[$update_item]->id) {
            $temp_json->workers[$i]->name = $_POST['update-employee-name-send'];
            if ($_POST['update-employee-typ-send'] != "null")
                $temp_json->workers[$i]->typ = $_POST['update-employee-typ-send'];
            $temp_json->workers[$i]->specialization = $_POST['update-employee-specialization-send'];
            $temp_json->workers[$i]->address = $_POST['update-employee-address-send'];
            $temp_json->workers[$i]->psc = $_POST['update-employee-psc-send'];
            $temp_json->workers[$i]->tel = $_POST['update-employee-tel-send'];
            $temp_json->workers[$i]->room = $_POST['update-employee-room-send'];
            //echo $temp_json->workers[$i]->name;
        }
    }

    $t = $temp_json->workers;
    $temp_json->workers = [];
    foreach($t as $k => $v) {
        array_push($temp_json->workers, $v);
    }

    //$temp_json = array_values($temp_json);
    $jsonData = json_encode($temp_json);

    file_put_contents('../database/workers.json', $jsonData);

?>