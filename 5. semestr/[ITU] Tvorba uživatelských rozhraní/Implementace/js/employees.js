/*********************************************************************************
 * Project:   3BIT ITU, Project - Evidence system                                *
 *            Faculty of Information Technology                                  *
 *            Brno University of Technology                                      *
 * File:      employees.js                                                       *
 * Date:      08.12.2019                                                         *
 * Authors:   Borek Reich, <xreich06@stud.fit.vutbr.cz>                          *
 *********************************************************************************/
 

window.onload = function () {
    this.loadTableData();    
    this.renderTime();

	var modalId = localStorage.getItem('show_detail');
	
	if (modalId != null){
		$(document).ready(function(){
			$('#exampleModalCenter').modal('show');
		});   
		this.show_detail(modalId);
	  	localStorage.removeItem('show_detail');
	}
}

window.on('resizestart', function() {
      var modal = $modal.data('bs.modal');
      modal.ignoreBackdropClick = true;
    })

window.on('resizeend', function() {
      setTimeout(function() {
        var modal = $modal.data('bs.modal');
        modal.ignoreBackdropClick = false;
      }, 0);
    })

function loadTableData() {
    console.log("Skuska loadTableData");

    var request = new XMLHttpRequest();
    request.open("GET", "database/workers.json", true);
    request.send(null);
    request.onreadystatechange = function() {
        if (request.readyState === 4 && request.status === 200) {
            var employees_json = JSON.parse(request.responseText);         

            var element_advisers = document.getElementById("div_for_employees_advisers");   
            var element_others = document.getElementById("div_for_employees_others");
            var element_teacher = document.getElementById("div_for_employees_teachers");
            var element_managers = document.getElementById("div_for_employees_managers");

            $("#div_for_employees_managers").empty();
            $("#div_for_employees_teachers").empty();
            $("#div_for_employees_others").empty();
            $("#div_for_employees_advisers").empty();
            
            for (var i in employees_json.workers) {

                var div_block_to_insert;

                div_block_to_insert = document.createElement( 'div' );
                div_block_to_insert.setAttribute("class", "col-3 p-2");
                div_block_to_insert.setAttribute("id", "div_worker" + i);
                div_block_to_insert.setAttribute("onclick", "show_detail(" + i + "); show_edit_detail(" + i + ");");
                
                // Pro zobrazeni detailu
                div_block_to_insert.setAttribute("data-toggle", "modal");
                div_block_to_insert.setAttribute("data-target", "#exampleModalCenter");
                div_block_to_insert.setAttribute("title", "Zobrazit detail zařízení");
        

                console.log(employees_json.workers[i]);

                // Pro zobrazeni polozky - nahledu
                div_block_to_insert.innerHTML = `<div class='employee_box'><div class='employee_photo'><i class='fas fa-user'></i></div>
                    <div class='employee_desc font-weight-bold'><p class='employees_name'>` 
                    + employees_json.workers[i].name + `</p><p class='employees_specialization font-weight-normal'>` + employees_json.workers[i].specialization + "</p>" + 
                    `<p class='employees_specialization font-weight-normal'>` + employees_json.workers[i].room + "</p>" + "</div></div>";

                // Rozdeleni, kam se to zobrazi
                if (employees_json.workers[i].typ == "ucitel")
                    element_teacher.appendChild( div_block_to_insert );
                else if (employees_json.workers[i].typ == "ostatni")
                    element_others.appendChild( div_block_to_insert );
                else if (employees_json.workers[i].typ == "poradce")
                    element_advisers.appendChild( div_block_to_insert );
                else if (employees_json.workers[i].typ == "reditel")
                    element_managers.appendChild( div_block_to_insert );
                else if (employees_json.workers[i].typ == "zastupce")
                    element_managers.appendChild( div_block_to_insert );

            }
        }
    }
}


function search_employee() {
    console.log("Skuska loadTableData");

    var request = new XMLHttpRequest();
    request.open("GET", "database/workers.json", true);
    request.send(null);
    request.onreadystatechange = function() {
        if (request.readyState === 4 && request.status === 200) {

            $("#div_for_employees_managers").empty();
            $("#div_for_employees_teachers").empty();
            $("#div_for_employees_others").empty();
            $("#div_for_employees_advisers").empty();

            var employees_json = JSON.parse(request.responseText);    
            var search_input_value = document.getElementById("search_input").value;     
            search_input_value = search_input_value.toLowerCase();

            if (search_input_value == "null") loadTableData();

            var element_advisers = document.getElementById("div_for_employees_advisers");   
            var element_others = document.getElementById("div_for_employees_others");
            var element_teacher = document.getElementById("div_for_employees_teachers");
            var element_managers = document.getElementById("div_for_employees_managers");
        
            for (var i in employees_json.workers) {

                if (employees_json.workers[i].name.toLowerCase().includes(search_input_value)) {

                    var div_block_to_insert = document.createElement( 'div' );
                    div_block_to_insert.setAttribute("class", "col-3 p-2");
                    div_block_to_insert.setAttribute("id", "div_worker" + i);
                    div_block_to_insert.setAttribute("onclick", "show_detail(" + i + "); show_edit_detail(" + i + ");");
                    
                    // Pro zobrazeni detailu
                    div_block_to_insert.setAttribute("data-toggle", "modal");
                    div_block_to_insert.setAttribute("data-target", "#exampleModalCenter");
                    div_block_to_insert.setAttribute("title", "Zobrazit detail zařízení");
            

                    console.log(employees_json.workers[i]);

                    // Pro zobrazeni polozky - nahledu
                    div_block_to_insert.innerHTML = `<div class='employee_box'><div class='employee_photo'><i class='fas fa-user'></i></div>
                    <div class='employee_desc font-weight-bold'><p class='employees_name'>` 
                    + employees_json.workers[i].name + `</p><p class='employees_specialization font-weight-normal'>` + employees_json.workers[i].specialization + "</p>" + 
                    `<p class='employees_specialization font-weight-normal'>` + employees_json.workers[i].room + "</p>" + "</div></div>";

                    // Rozdeleni, kam se to zobrazi
                    if (employees_json.workers[i].typ == "ucitel")
                        element_teacher.appendChild( div_block_to_insert );
                    else if (employees_json.workers[i].typ == "ostatni")
                        element_others.appendChild( div_block_to_insert );
                    else if (employees_json.workers[i].typ == "poradce")
                        element_advisers.appendChild( div_block_to_insert );
                    else if (employees_json.workers[i].typ == "reditel")
                        element_managers.appendChild( div_block_to_insert );
                    else if (employees_json.workers[i].typ == "zastupce")
                        element_managers.appendChild( div_block_to_insert );
                }    
            }
        }
    }
}

// Show employee detail
function show_detail(id) {
	console.log(id);
    var request = new XMLHttpRequest();
    request.open("GET", "database/workers.json", true);
    request.send(null);
    request.onreadystatechange = function() {        
        var employees_json = JSON.parse(request.responseText);  

        $("#employees-detail-name").text(employees_json.workers[id].name);
        $("#employees-detail-specialization").text(employees_json.workers[id].specialization);
        $("#employees-detail-tel").text(employees_json.workers[id].tel);
        $("#employees-detail-address").text(employees_json.workers[id].address);
        $("#employees-detail-psc").text(employees_json.workers[id].psc);
        $("#employees-detail-room").text(employees_json.workers[id].room);
        
        var element_button = document.getElementById("remove-employee");
        element_button.setAttribute("onclick", "remove_employee(" +id+ ");");

        var element_button = document.getElementById("update-employee");
        element_button.setAttribute("onclick", "show_edit_detail(" +id+ ");");

        var element_button = document.getElementById("show-devices");
        element_button.setAttribute("onclick", "load_devices_data(\"" +employees_json.workers[id].name+ "\");");

    }
}

// Update employee cancel
$("#update-employee-cancel").click(update_employee_cancel);

function update_employee_cancel() {
    $("#exampleModalCenter").removeClass("d-none");
}

// Show employee detail
function show_edit_detail(id) {

    var request = new XMLHttpRequest();
    request.open("GET", "database/workers.json", true);
    request.send(null);
    request.onreadystatechange = function() {        
        var employees_json = JSON.parse(request.responseText);  

        $("#update-employees-name").val(employees_json.workers[id].name);

        if (employees_json.workers[id].typ == "ucitel")
            $("#update-employees-typ-selected").text("Učitel");
        else if (employees_json.workers[id].typ == "zastupce")
            $("#update-employees-typ-selected").text("Zástupce ředitel");
        else if (employees_json.workers[id].typ == "reditel")
            $("#update-employees-typ-selected").text("Ředitel");
        else if (employees_json.workers[id].typ == "ostatni")
            $("#update-employees-typ-selected").text("Ostatní");
        else if (employees_json.workers[id].typ == "poradce")
            $("#update-employees-typ-selected").text("Poradce");

        $("#update-employees-tel").val(employees_json.workers[id].tel);
        $("#update-employees-address").val(employees_json.workers[id].address);
        $("#update-employees-psc").val(employees_json.workers[id].psc);
        $("#update-employees-specialization").val(employees_json.workers[id].specialization);
        $("#update-employees-room").val(employees_json.workers[id].room);

        var element_button = document.getElementById("update-save");
        element_button.setAttribute("onclick", "update_employee(" +id+ ");");
    }
}

function update_employee(id) {
    
    if(document.getElementById("update-employees-name").value){
        $.ajax({
            url     : '../ITU/logic/update_employee.php',
            method  : 'post',
            data    : { 
            			'update-employee-id-send': id,
            			'update-employee-name-send': document.getElementById("update-employees-name").value,
                        'update-employee-name-send': document.getElementById("update-employees-name").value,
                        'update-employee-typ-send': document.getElementById("update-employees-typ").value, 
                        'update-employee-specialization-send': document.getElementById("update-employees-specialization").value,
                        'update-employee-tel-send': document.getElementById("update-employees-tel").value,
                        'update-employee-address-send': document.getElementById("update-employees-address").value,
                        'update-employee-psc-send': document.getElementById("update-employees-psc").value,
                        'update-employee-room-send': document.getElementById("update-employees-room").value
                    },
            success : function( response ) {
                console.log(id);
			    var request = new XMLHttpRequest();
			    request.open("GET", "database/workers.json", true);
			    request.send(null);
			    request.onreadystatechange = function() {        
			        var employees_json = JSON.parse(request.responseText);  

			        $("#employees-detail-name").text(employees_json.workers[id].name);
			        $("#employees-detail-specialization").text(employees_json.workers[id].specialization);
			        $("#employees-detail-tel").text(employees_json.workers[id].tel);
			        $("#employees-detail-address").text(employees_json.workers[id].address);
			        $("#employees-detail-psc").text(employees_json.workers[id].psc);
			        $("#employees-detail-room").text(employees_json.workers[id].room);
			        
			        var element_button = document.getElementById("remove-employee");
			        element_button.setAttribute("onclick", "remove_employee(" +id+ ");");

			        var element_button = document.getElementById("update-employee");
			        element_button.setAttribute("onclick", "show_edit_detail(" +id+ ");");

			        var element_button = document.getElementById("show-devices");
			        element_button.setAttribute("onclick", "load_devices_data(\"" +employees_json.workers[id].name+ "\");");

			        loadTableData();
			    }
            }
        });
    }
}


// Remove employee
//$("#remove-employee").click(remove_employee);

function remove_employee(id) {
    console.log("remove_employee");
    $(document).ready(function(){
        $("#exampleModalCenter").hide();
    });

    var element_button = document.getElementById("remove-employee-confirm");
    element_button.setAttribute("onclick", "remove_employee_confirm(" +id+ ");");

    element_button = document.getElementById("remove-employee-cancel");
    element_button.setAttribute("onclick", "remove_employee_cancel();");
}

//$("#remove-employee-confirm").click(remove_employee_confirm);

function remove_employee_confirm(id) {

    if ($('#remove-employee-confirm').is(':visible')) {

        var request = new XMLHttpRequest();
        request.open("GET", "database/workers.json", true);
        request.send(null);
        request.onreadystatechange = function() {        
            var employees_json = JSON.parse(request.responseText);  
        
            $("#remove-employee-modal").hide();

            $("#remove-employee-id").val(employees_json.workers[id].id);
            $("#php-remove-employee").click();
        }
    }
}

function remove_employee_cancel() {
    $("#remove-employee-modal").hide();

    $('#exampleModalCenter').modal('show');
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
}


function load_devices_data(id_name) {
    if (id_name) {

        $("#employee_table_body").empty();
        $("#exampleModalCenter").hide();

        var at_least_one = false;
        var request = new XMLHttpRequest();
        request.open("GET", "database/devices.json", true);
        request.send(null);
        request.onreadystatechange = function() {
            if (request.readyState === 4 && request.status === 200) {
                var devices_json = JSON.parse(request.responseText);            
                var table_body = document.getElementById("employee_table_body");

                var table_row=0;
                for (var i in devices_json.devices) {

                    if (devices_json.devices[i].disponent == id_name) {
                        at_least_one = true;
                        var row  = table_body.insertRow(table_row);
                        table_row++;

                        var cell0 = row.insertCell(0);
                        var cell1 = row.insertCell(1);
                        var cell2 = row.insertCell(2);
                        var cell3 = row.insertCell(3);
                        
                        cell0.innerHTML = devices_json.devices[i].name;
                        cell1.innerHTML = devices_json.devices[i].type;
                        cell2.innerHTML = devices_json.devices[i].status;
                        cell3.innerHTML = devices_json.devices[i].room;
                        
                    }    
                }

                if (!at_least_one) {
                    table_body = document.getElementById("div-for-device");
                    $("#div-for-device").empty();
                    table_body.innerHTML = "Uživatel nemá zařízení.";
                }

                table_body = document.getElementById("title-device-employee");
                table_body.innerHTML = "Zapůjčená zařízení - " + id_name;

                var element_button = document.getElementById("dismiss-device");
                element_button.setAttribute("onclick", "devices_employee_cancel();");
            }
        }
    }    
}

function devices_employee_cancel()
{
    $("#exampleModalCenter").show();    
}


function renderTime()
{
    var date = new Date();
    var year = date.getYear();
    var day = date.getDay();
    var month = date.getMonth();
    var daym = date.getDate();

    if(year < 1000){
        year += 1900;
    }
    
    var day_array = new Array("Neděle", "Pondělí","Úterý","Středa","Čtvrtek","Pátek","Sobota");
    var month_array = new Array("leden","únor","březen","duben","květen","červen","červenec","srpen","září","říjen","listopad","prosinec");

    var date_two = new Date();
    var hours = date_two.getHours();
    var minutes = date_two.getMinutes();
    var seconds = date_two.getSeconds();

    if(hours < 10){
        hours = "0" + hours;
    }

    if(minutes < 10){
        minutes = "0" + minutes;
    }

    if(seconds < 10){
        seconds = "0" + seconds;
    }

    var myClock = document.getElementById("display_clock");
    myClock.innerHTML = "" + day_array[day] + ", " + daym + ". " + month_array[month] + " " + year + " | " + hours + ":" + minutes + ":" + seconds;

    setTimeout("renderTime()", 1000);
}