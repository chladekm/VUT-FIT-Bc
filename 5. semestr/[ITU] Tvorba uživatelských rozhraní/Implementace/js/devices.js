/*********************************************************************************
 * Project:   3BIT ITU, Project - Evidence system                                *
 *            Faculty of Information Technology                                  *
 *            Brno University of Technology                                      *
 * File:      devices.js                                                         *
 * Date:      08.12.2019                                                         *
 * Authors:   Peter Kruty, <xkruty00@stud.fit.vutbr.cz>                          *
 *********************************************************************************/


$("#filter-search-button").click(function () {
    var request = new XMLHttpRequest();
    request.open("GET", "database/devices.json", true);
    request.send(null);
    request.onreadystatechange = function() {
        if (request.readyState === 4 && request.status === 200) {
            $("#device_table_body tr").remove();

            var filter_device_status1 = "";
            var filter_device_status2 = "";
            var filter_device_status3 = "";
            var filter_device_status4 = "";
            
            if (document.getElementById("filter-status-1").checked) {
                filter_device_status1 = "volný";
            }
            else {
                filter_device_status1 = "VALUEVALUE";
            }
            if (document.getElementById("filter-status-2").checked) {
                filter_device_status2 = "přidělený";
            }
            else {
                filter_device_status2 = "VALUEVALUE";
            }
            if (document.getElementById("filter-status-3").checked) {
                filter_device_status3 = "v opravě";
            }
            else {
                filter_device_status3 = "VALUEVALUE";
            }
            if (document.getElementById("filter-status-4").checked) {
                filter_device_status4 = "vyřazený";
            }
            else {
                filter_device_status4 = "VALUEVALUE";
            }

            var filter_device_type = document.getElementById("filter-device-type").value;
            filter_device_type = filter_device_type.toLowerCase();
            if (filter_device_type == "all") {
                filter_device_type = "";
            }
            var filter_device_disponent = document.getElementById("filter-disponent").value;
            if (!filter_device_disponent) {
                filter_device_disponent = "";
            }
            filter_device_disponent = filter_device_disponent.toLowerCase();
            var filter_device_room = document.getElementById("filter-room").value;
            if (!filter_device_room ) {
                filter_device_room = "";
            }
            filter_device_room = filter_device_room.toLowerCase();

            var devices_json = JSON.parse(request.responseText);            
            var table_body = document.getElementById("device_table_body");
            var j = 0;
            for (var i in devices_json.devices) {
                if (devices_json.devices[i].type.toLowerCase().includes(filter_device_type) && 
                    (filter_device_disponent == "" || devices_json.devices[i].disponent.toLowerCase().includes(filter_device_disponent)) &&
                    (filter_device_room == "" || devices_json.devices[i].room.toLowerCase().includes(filter_device_room)) &&
                    (devices_json.devices[i].status.toLowerCase().includes(filter_device_status1) || 
                    devices_json.devices[i].status.toLowerCase().includes(filter_device_status2) || 
                    devices_json.devices[i].status.toLowerCase().includes(filter_device_status3) || 
                    devices_json.devices[i].status.toLowerCase().includes(filter_device_status4) || 
                    (filter_device_status1.includes("VALUEVALUE") &&
                    filter_device_status2.includes("VALUEVALUE") && 
                    filter_device_status3.includes("VALUEVALUE") &&
                    filter_device_status4.includes("VALUEVALUE")))) {
                    var row  = table_body.insertRow(j);
                    
                    row.setAttribute("id", "table_row" + j);
                    row.setAttribute("onclick", "show_detail(" + i + "); show_edit_detail(" + i + ");");
                    var cell0 = row.insertCell(0);
                    var cell1 = row.insertCell(1);
                    var cell2 = row.insertCell(2);
                    var cell3 = row.insertCell(3);
                    var cell4 = row.insertCell(4);
                    var cell5 = row.insertCell(5);
    
                    cell0.innerHTML = '<input title="Označiť" class="form-check-input ml-3 my-tablecheck" id="tablerowcheck' + j + '"  type="checkbox">&nbsp;</input>';
                    cell1.innerHTML = devices_json.devices[i].name;
                    cell2.innerHTML = devices_json.devices[i].type;
                    cell3.innerHTML = devices_json.devices[i].status;
                    cell4.innerHTML = devices_json.devices[i].disponent;
                    cell5.innerHTML = devices_json.devices[i].room;

                    cell1.setAttribute("data-toggle", "modal");
                    cell1.setAttribute("data-target", "#exampleModalCenter");
                    cell1.setAttribute("title", "Zobrazit detail zařízení");
                    

                    cell2.setAttribute("data-toggle", "modal");
                    cell2.setAttribute("data-target", "#exampleModalCenter");
                    cell2.setAttribute("title", "Zobrazit detail zařízení");
                    

                    cell3.setAttribute("data-toggle", "modal");
                    cell3.setAttribute("data-target", "#exampleModalCenter");
                    cell3.setAttribute("title", "Zobrazit detail zařízení");
                    

                    cell4.setAttribute("data-toggle", "modal");
                    cell4.setAttribute("data-target", "#exampleModalCenter");
                    cell4.setAttribute("title", "Zobrazit detail zařízení");
                    

                    cell5.setAttribute("data-toggle", "modal");
                    cell5.setAttribute("data-target", "#exampleModalCenter");
                    cell5.setAttribute("title", "Zobrazit detail zařízení");    

                    var rowcheck = document.getElementById("tablerowcheck" + j);
                    rowcheck.setAttribute("onclick", "activate_row(\"table_row" + j + "\")");
                    j++;
                }
            }
        }
    }
})

$("#filter-button").click(function () {
    if ($("#filter-collapse").hasClass("show")) {
        $("#filter-collapse").collapse("hide");
    }
    else {
        $("#filter-collapse").collapse("show");
    }
})

/* Device Table */
window.onload = function(){
    this.loadTableData();
    this.renderTime();
}


function loadTableData() {
    console.log("Skuska loadTableData");

    var request = new XMLHttpRequest();
    request.open("GET", "database/devices.json", true);
    request.send(null);
    request.onreadystatechange = function() {
        if (request.readyState === 4 && request.status === 200) {
            var devices_json = JSON.parse(request.responseText);            
            var table_body = document.getElementById("device_table_body");
            
            for (var i in devices_json.devices) {
                var row  = table_body.insertRow(i);
                
                row.setAttribute("id", "table_row" + i);
                row.setAttribute("onclick", "show_detail(" + i + "); show_edit_detail(" + i + ");");
                var cell0 = row.insertCell(0);
                var cell1 = row.insertCell(1);
                var cell2 = row.insertCell(2);
                var cell3 = row.insertCell(3);
                var cell4 = row.insertCell(4);
                var cell5 = row.insertCell(5);
                
                cell0.innerHTML = '<input title="Označiť" class="form-check-input ml-3 my-tablecheck" id="tablerowcheck' + i + '"  type="checkbox">&nbsp;</input>';
                cell1.innerHTML = devices_json.devices[i].name;
                cell2.innerHTML = devices_json.devices[i].type;
                cell3.innerHTML = devices_json.devices[i].status;
                cell4.innerHTML = devices_json.devices[i].disponent;
                cell5.innerHTML = devices_json.devices[i].room;

                cell1.setAttribute("data-toggle", "modal");
                cell1.setAttribute("data-target", "#exampleModalCenter");
                cell1.setAttribute("title", "Zobrazit detail zařízení");
                cell2.setAttribute("data-toggle", "modal");
                cell2.setAttribute("data-target", "#exampleModalCenter");
                cell2.setAttribute("title", "Zobrazit detail zařízení");
                cell3.setAttribute("data-toggle", "modal");
                cell3.setAttribute("data-target", "#exampleModalCenter");
                cell3.setAttribute("title", "Zobrazit detail zařízení");
                cell4.setAttribute("data-toggle", "modal");
                cell4.setAttribute("data-target", "#exampleModalCenter");
                cell4.setAttribute("title", "Zobrazit detail zařízení");
                cell5.setAttribute("data-toggle", "modal");
                cell5.setAttribute("data-target", "#exampleModalCenter");
                cell5.setAttribute("title", "Zobrazit detail zařízení");
                
                var rowcheck = document.getElementById("tablerowcheck" + i);
                rowcheck.setAttribute("onclick", "activate_row(\"table_row" + i + "\")");


                
            }
            var devices_counter = 0;
            var available_devices_counter = 0;
            var inrepair_devices_counter = 0;
            var discard_devices_counter = 0;
    
            for (var i in devices_json.devices) {
                devices_counter++;
                if (devices_json.devices[i].status == "Volný") {
                    available_devices_counter++;
                }
                if (devices_json.devices[i].status == "V opravě") {
                    inrepair_devices_counter++;
                }
                if (devices_json.devices[i].status == "Vyřazený") {
                    discard_devices_counter++;
                }
            }
            $("#devices-counter").text(devices_counter);
            $("#available-devices-counter").text(available_devices_counter);
            $("#inrepair-devices-counter").text(inrepair_devices_counter);
            $("#discard-devices-counter").text(discard_devices_counter);        
        }
    }
}

/* Searching */
function search_device() {
    console.log("search_device was executed");
    var request = new XMLHttpRequest();
    request.open("GET", "database/devices.json", true);
    request.send(null);
    request.onreadystatechange = function() {
        if (request.readyState === 4 && request.status === 200) {
            $("#device_table_body tr").remove();
            var search_input_value = document.getElementById("search_input").value;
            search_input_value = search_input_value.toLowerCase();

            var devices_json = JSON.parse(request.responseText);            
            var table_body = document.getElementById("device_table_body");
            var j = 0;
            for (var i in devices_json.devices) {
                if (devices_json.devices[i].name.toLowerCase().includes(search_input_value)) {
                    var row  = table_body.insertRow(j);
                    
                    row.setAttribute("id", "table_row" + j);
                    row.setAttribute("onclick", "show_detail(" + i + "); show_edit_detail(" + i + ");");
                    var cell0 = row.insertCell(0);
                    var cell1 = row.insertCell(1);
                    var cell2 = row.insertCell(2);
                    var cell3 = row.insertCell(3);
                    var cell4 = row.insertCell(4);
                    var cell5 = row.insertCell(5);
    
                    cell0.innerHTML = '<input title="Označiť" class="form-check-input ml-3 my-tablecheck" id="tablerowcheck' + j + '"  type="checkbox">&nbsp;</input>';
                    cell1.innerHTML = devices_json.devices[i].name;
                    cell2.innerHTML = devices_json.devices[i].type;
                    cell3.innerHTML = devices_json.devices[i].status;
                    cell4.innerHTML = devices_json.devices[i].disponent;
                    cell5.innerHTML = devices_json.devices[i].room;

                    cell1.setAttribute("data-toggle", "modal");
                    cell1.setAttribute("data-target", "#exampleModalCenter");
                    cell1.setAttribute("title", "Zobrazit detail zařízení");
                    

                    cell2.setAttribute("data-toggle", "modal");
                    cell2.setAttribute("data-target", "#exampleModalCenter");
                    cell2.setAttribute("title", "Zobrazit detail zařízení");
                    

                    cell3.setAttribute("data-toggle", "modal");
                    cell3.setAttribute("data-target", "#exampleModalCenter");
                    cell3.setAttribute("title", "Zobrazit detail zařízení");
                    

                    cell4.setAttribute("data-toggle", "modal");
                    cell4.setAttribute("data-target", "#exampleModalCenter");
                    cell4.setAttribute("title", "Zobrazit detail zařízení");
                    

                    cell5.setAttribute("data-toggle", "modal");
                    cell5.setAttribute("data-target", "#exampleModalCenter");
                    cell5.setAttribute("title", "Zobrazit detail zařízení");    

                    var rowcheck = document.getElementById("tablerowcheck" + j);
                    rowcheck.setAttribute("onclick", "activate_row(\"table_row" + j + "\")");
                    j++;
                }
            }
        }
    }
}

// Select/Unselect all rows
$("#tablerowcheckheader").click(function select_all_rows(params) {  
    if (document.getElementById("tablerowcheckheader").checked) {
        // Select all rows
        var table = document.getElementById("device_table");
        for (var i = 0, row; row = table.rows[i]; i++) {
            if (document.getElementById("tablerowcheck" + i) == null) {
                break;
            }
            if (!document.getElementById("tablerowcheck" + i).checked) {
                $("#tablerowcheck" + i).trigger( "click");
            }
        }
    }
    else {
        // Unselect all rows
        var table = document.getElementById("device_table");
        for (var i = 0, row; row = table.rows[i]; i++) {
            if (document.getElementById("tablerowcheck" + i) == null) {
                break;
            }
            if (document.getElementById("tablerowcheck" + i).checked) {
                $("#tablerowcheck" + i).trigger( "click");
            }
        }
    }
})

// Show device detail
function show_detail(id) {
    var request = new XMLHttpRequest();
    request.open("GET", "database/devices.json", true);
    request.send(null);
    request.onreadystatechange = function() {        
        var devices_json = JSON.parse(request.responseText);  

        $("#device-detail-name").text(devices_json.devices[id].name);
        $("#device-detail-type").text(devices_json.devices[id].type);
        $("#device-detail-status").text(devices_json.devices[id].status);
        $("#device-detail-disponent").text(devices_json.devices[id].disponent);
        $("#device-detail-room").text(devices_json.devices[id].room);
        $("#device-detail-implemdate").text(devices_json.devices[id].implemdate);
        $("#device-detail-assigndate").text(devices_json.devices[id].assigndate);
        $("#device-detail-discarddate").text(devices_json.devices[id].discarddate);
    }
}


/* Active table row */
function activate_row(id) {
    var table_row = document.getElementById(id);
    
    change_buttons_activity();

    if (table_row.style.backgroundColor == "lightsteelblue") {
        if ((id.slice(-1).charCodeAt(0) % 2) == 0) {
            table_row.style.backgroundColor = "lightgray";
            table_row.onmouseout = function() {
            table_row.style.backgroundColor = "lightgray";
        }
            table_row.onmouseover = function() {
            table_row.style.backgroundColor = "lightgray";
        }
        }
        else {
            table_row.style.backgroundColor = "transparent";
            table_row.onmouseout = function() {
            table_row.style.backgroundColor = "transparent";
        }
            table_row.onmouseover = function() {
            table_row.style.backgroundColor = "lightgray";
        }
        }
    }
    else {
        table_row.style.backgroundColor = "lightsteelblue";
        table_row.onmouseout = function() {
            table_row.style.backgroundColor = "lightsteelblue";
        }
        table_row.onmouseover = function() {
            table_row.style.backgroundColor = "lightsteelblue";
        }
    }
}


// Change toolbar butons activity
function change_buttons_activity() {
    var table = document.getElementById("device_table");
    var active_flag = false;
    for (var i = 0, row; row = table.rows[i]; i++) {
        if (document.getElementById("tablerowcheck" + i) == null) {
            break;
        }
        if (document.getElementById("tablerowcheck" + i).checked) {
            active_flag = true;
        }
    }
    if (active_flag) {
        console.log("active_flag");
        $("#remove-device").removeAttr("disabled");
        $("#discard-device").removeAttr("disabled");
        $("#assign-device").removeAttr("disabled");
    }
    else {
        console.log("active_flag false");
        $("#remove-device").attr("disabled", "");
        $("#discard-device").attr("disabled", "");
        $("#assign-device").attr("disabled", "");
    }
}

// Remove device
$("#remove-device-confirm").click(remove_device);

function remove_device() {
    console.log("remove_device");
    
    var table = document.getElementById("device_table");
    var devices_to_remove = "";
    
    for (var i = 0, row; row = table.rows[i]; i++) {
        if (document.getElementById("tablerowcheck" + i) == null) {
            devices_to_remove = devices_to_remove.slice(0, -1); 
            break;
        }
        if (document.getElementById("tablerowcheck" + i).checked) {
            devices_to_remove += i;
            devices_to_remove += " ";
        }
    }
    console.log(devices_to_remove);

    $("#remove-device-ids").val(devices_to_remove);

    $("#php-remove-device").click();
}

// Discard device
$("#discard-device").click(discard_device);

function discard_device() {
    console.log("discard_device");
    
    var table = document.getElementById("device_table");
    var devices_to_discard = "";
    
    for (var i = 0, row; row = table.rows[i]; i++) {
        if (document.getElementById("tablerowcheck" + i) == null) {
            devices_to_discard = devices_to_discard.slice(0, -1); 
            break;
        }
        if (document.getElementById("tablerowcheck" + i).checked) {
            devices_to_discard += i;
            devices_to_discard += " ";
        }
    }
    console.log(devices_to_discard);

    $("#discard-device-ids").val(devices_to_discard);


    $("#php-discard-device").click();
}

// Assign device
$("#assign-device").click(assign_device);

function assign_device() {
    console.log("assign_device");
    
    var table = document.getElementById("device_table");
    var devices_to_assign = "";
    
    for (var i = 0, row; row = table.rows[i]; i++) {
        if (document.getElementById("tablerowcheck" + i) == null) {
            devices_to_assign = devices_to_assign.slice(0, -1); 
            break;
        }
        if (document.getElementById("tablerowcheck" + i).checked) {
            devices_to_assign += i;
            devices_to_assign += " ";
        }
    }
    console.log(devices_to_assign);

    $("#assign-device-ids").val(devices_to_assign);
}

// Update device
$("#update-device").click(update_device);

function update_device() {
    $("#exampleModalCenter").addClass("d-none");
}

// Update device cancel
$("#update-device-cancel").click(update_device_cancel);
$("#update-device-close-button").click(update_device_cancel);

function update_device_cancel() {
    $("#exampleModalCenter").removeClass("d-none");
}

// Show edit detail
function show_edit_detail(id) {
    console.log("show edit detail");
    var request = new XMLHttpRequest();
    request.open("GET", "database/devices.json", true);
    request.send(null);
    request.onreadystatechange = function() {        
        var devices_json = JSON.parse(request.responseText);  
        console.log(devices_json.devices[id].name);
        
        $("#update-device-id").val(id);  
        $("#update-device-name").val(devices_json.devices[id].name);
        $("#update-device-type").val(devices_json.devices[id].type);
        $("#update-device-status-selected").text(devices_json.devices[id].status);
        $("#update-device-disponent-selected").text(devices_json.devices[id].disponent);
        $("#update-device-room-selected").text(devices_json.devices[id].room);
        $("#update-device-implemdate").val(devices_json.devices[id].implemdate);
        $("#update-device-assigndate").val(devices_json.devices[id].assigndate);
        $("#update-device-discarddate").val(devices_json.devices[id].discarddate);
    }    
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
