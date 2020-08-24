/*********************************************************************************
 * Project:   3BIT ITU, Project - Evidence system                                *
 *            Faculty of Information Technology                                  *
 *            Brno University of Technology                                      *
 * File:      repairs.js                                                         *
 * Date:      08.12.2019                                                         *
 * Authors:   Peter Kruty, <xkruty00@stud.fit.vutbr.cz>                          *
 *********************************************************************************/

$("#filter-search-button").click(function () {
    var request = new XMLHttpRequest();
    request.open("GET", "database/repairs.json", true);
    request.send(null);
    request.onreadystatechange = function() {
        if (request.readyState === 4 && request.status === 200) {
            $("#repairs_table_body tr").remove();

            var filter_repair_status1 = "";
            var filter_repair_status2 = "";
            var filter_repair_status3 = "";
            
            if (document.getElementById("filter-status-1").checked) {
                filter_repair_status1 = "v opravě";
            }
            else {
                filter_repair_status1 = "VALUEVALUE";
            }
            if (document.getElementById("filter-status-2").checked) {
                filter_repair_status2 = "opraveno";
            }
            else {
                filter_repair_status2 = "VALUEVALUE";
            }
            if (document.getElementById("filter-status-3").checked) {
                filter_repair_status3 = "zamítnuto";
            }
            else {
                filter_repair_status3 = "VALUEVALUE";
            }

            var filter_device_type = document.getElementById("filter-device-type").value;
            filter_device_type = filter_device_type.toLowerCase();
            if (filter_device_type == "all") {
                filter_device_type = "";
            }
            var filter_repair_reason = document.getElementById("filter-reason").value;
            if (!filter_repair_reason) {
                filter_repair_reason = "";
            }
            filter_repair_reason = filter_repair_reason.toLowerCase();
            
            var repairs_json = JSON.parse(request.responseText);            
            var table_body = document.getElementById("repairs_table_body");
            var j = 0;
            for (var i in repairs_json.repairs) {
                if (repairs_json.repairs[i].type.toLowerCase().includes(filter_device_type) && 
                    (filter_repair_reason == "" || repairs_json.repairs[i].reason.toLowerCase().includes(filter_repair_reason)) &&
                    (repairs_json.repairs[i].status.toLowerCase().includes(filter_repair_status1) || 
                    repairs_json.repairs[i].status.toLowerCase().includes(filter_repair_status2) || 
                    repairs_json.repairs[i].status.toLowerCase().includes(filter_repair_status3) || 
                    (filter_repair_status1.includes("VALUEVALUE") &&
                    filter_repair_status2.includes("VALUEVALUE") && 
                    filter_repair_status3.includes("VALUEVALUE")))) {
                        var row  = table_body.insertRow(j);
                    
                        row.setAttribute("id", "table_row" + j);
                        row.setAttribute("onclick", "show_detail(" + i + "); show_edit_detail(" + i + ");");
                        
                        var cell0 = row.insertCell(0);
                        var cell1 = row.insertCell(1);
                        var cell2 = row.insertCell(2);
                        var cell3 = row.insertCell(3);
                        var cell4 = row.insertCell(4);
        
                        cell0.innerHTML = '<input title="Označiť" class="form-check-input ml-3 my-tablecheck" id="tablerowcheck' + j + '"  type="checkbox">&nbsp;</input>';
                        cell1.innerHTML = repairs_json.repairs[i].device;
                        cell2 .innerHTML = repairs_json.repairs[i].type;
                        cell3.innerHTML = repairs_json.repairs[i].reason;
                        cell4.innerHTML = repairs_json.repairs[i].status;
                        
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

window.onload = function(){
    this.loadTableData();
    this.renderTime();
}


function loadTableData() {

    var request = new XMLHttpRequest();
    request.open("GET", "database/repairs.json", true);
    request.send(null);
    request.onreadystatechange = function() {
        if (request.readyState === 4 && request.status === 200) {
            var repairs_json = JSON.parse(request.responseText);            
            var table_body = document.getElementById("repairs_table_body");
            
            for (var i in repairs_json.repairs) {
                var row  = table_body.insertRow(i);
                
                row.setAttribute("id", "table_row" + i);
                row.setAttribute("onclick", "show_detail(" + i + "); show_edit_detail(" + i + ");");
                
                var cell0 = row.insertCell(0);
                var cell1 = row.insertCell(1);
                var cell2 = row.insertCell(2);
                var cell3 = row.insertCell(3);
                var cell4 = row.insertCell(4);
                
                cell0.innerHTML = '<input title="Označiť" class="form-check-input ml-3 my-tablecheck" id="tablerowcheck' + i + '"  type="checkbox">&nbsp;</input>';
                cell1.innerHTML = repairs_json.repairs[i].device;
                cell2 .innerHTML = repairs_json.repairs[i].type;
                cell3.innerHTML = repairs_json.repairs[i].reason;
                cell4.innerHTML = repairs_json.repairs[i].status;
                

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
                
                
                var rowcheck = document.getElementById("tablerowcheck" + i);
                rowcheck.setAttribute("onclick", "activate_row(\"table_row" + i + "\")");
            }
            var repairs_counter = 0;
            var success_repairs_counter = 0;
            var unsuccess_repairs_counter = 0;
            var inrepair_repairs_counter = 0;
    
            for (var i in repairs_json.repairs) {
                repairs_counter++;
                if (repairs_json.repairs[i].status == "Opraveno") {
                    success_repairs_counter++;
                }
                if (repairs_json.repairs[i].status == "Zamítnuto") {
                    unsuccess_repairs_counter++;
                }
                if (repairs_json.repairs[i].status == "V opravě") {
                    inrepair_repairs_counter++;
                }
            }
            $("#repairs-counter").text(repairs_counter);
            $("#success-repairs-counter").text(success_repairs_counter);
            $("#unsuccess-repairs-counter").text(unsuccess_repairs_counter);
            $("#inrepair-repairs-counter").text(inrepair_repairs_counter);        
        
        }
    }
}

/* Searching */
function search_repair() {
    console.log("search_repair was executed");
    var request = new XMLHttpRequest();
    request.open("GET", "database/repairs.json", true);
    request.send(null);
    request.onreadystatechange = function() {
        if (request.readyState === 4 && request.status === 200) {
            $("#repairs_table_body tr").remove();
            var search_input_value = document.getElementById("search_input").value;
            search_input_value = search_input_value.toLowerCase();

            var repairs_json = JSON.parse(request.responseText);            
            var table_body = document.getElementById("repairs_table_body");
            var j = 0;
            for (var i in repairs_json.repairs) {
                if (repairs_json.repairs[i].device.toLowerCase().includes(search_input_value)) {
                    var row  = table_body.insertRow(j);
                    
                    row.setAttribute("id", "table_row" + j);
                    row.setAttribute("onclick", "show_detail(" + i + "); show_edit_detail(" + i + ");");
                    
                    var cell0 = row.insertCell(0);
                    var cell1 = row.insertCell(1);
                    var cell2 = row.insertCell(2);
                    var cell3 = row.insertCell(3);
                    var cell4 = row.insertCell(4);
    
                    cell0.innerHTML = '<input title="Označiť" class="form-check-input ml-3 my-tablecheck" id="tablerowcheck' + j + '"  type="checkbox">&nbsp;</input>';
                    cell1.innerHTML = repairs_json.repairs[i].device;
                    cell2 .innerHTML = repairs_json.repairs[i].type;
                    cell3.innerHTML = repairs_json.repairs[i].reason;
                    cell4.innerHTML = repairs_json.repairs[i].status;
                    
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
                    
                    var rowcheck = document.getElementById("tablerowcheck" + j);
                    rowcheck.setAttribute("onclick", "activate_row(\"table_row" + j + "\")");
                    j++;
                }
            }
        }
    }
}

// Show repair detail
function show_detail(id) {
    var request = new XMLHttpRequest();
    request.open("GET", "database/repairs.json", true);
    request.send(null);
    request.onreadystatechange = function() {        
        var repairs_json = JSON.parse(request.responseText);  

        $("#repair-detail-repair").text(repairs_json.repairs[id].repair);
        $("#repair-detail-type").text(repairs_json.repairs[id].type);
        $("#repair-detail-reason").text(repairs_json.repairs[id].reason);
        $("#repair-detail-status").text(repairs_json.repairs[id].status);
        $("#repair-detail-startdate").text(repairs_json.repairs[id].startdate);
        $("#repair-detail-enddate").text(repairs_json.repairs[id].enddate);
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


// Select/Unselect all rows
$("#tablerowcheckheader").click(function select_all_rows(params) {  
    if (document.getElementById("tablerowcheckheader").checked) {
        // Select all rows
        var table = document.getElementById("repairs_table");
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
        var table = document.getElementById("repairs_table");
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

// Change toolbar butons activity
function change_buttons_activity() {
    var table = document.getElementById("repairs_table");
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
        $("#remove-repairs").removeAttr("disabled");
    }
    else {
        $("#remove-repairs").attr("disabled", "");
    }
}

// Remove repair
$("#remove-repair-confirm").click(remove_repair);

function remove_repair() {
    console.log("remove_repair");
    
    var table = document.getElementById("repairs_table");
    var repairs_to_remove = "";
    
    for (var i = 0, row; row = table.rows[i]; i++) {
        if (document.getElementById("tablerowcheck" + i) == null) {
            repairs_to_remove = repairs_to_remove.slice(0, -1); 
            break;
        }
        if (document.getElementById("tablerowcheck" + i).checked) {
            repairs_to_remove += i;
            repairs_to_remove += " ";
        }
    }
    console.log(repairs_to_remove);

    $("#remove-repair-ids").val(repairs_to_remove);

    $("#php-remove-repair").click();
}

// Update repair
$("#update-repair").click(update_repair);

function update_repair() {
    $("#exampleModalCenter").addClass("d-none");
}

// Update repair cancel
$("#update-repair-cancel").click(update_repair_cancel);
$("#update-repair-close-button").click(update_repair_cancel);

function update_repair_cancel() {
    $("#exampleModalCenter").removeClass("d-none");
}

// Show edit detail
function show_edit_detail(id) {
    console.log("show edit detail");
    var request = new XMLHttpRequest();
    request.open("GET", "database/repairs.json", true);
    request.send(null);
    request.onreadystatechange = function() {        
        var repairs_json = JSON.parse(request.responseText);  
        console.log(repairs_json.repairs[id].name);
        
        $("#update-repair-id").val(id);  
        $("#update-repair-repair-selected").text(repairs_json.repairs[id].repair);
        $("#update-repair-reason").val(repairs_json.repairs[id].reason);
        $("#update-repair-status-selected").text(repairs_json.repairs[id].status);
        $("#update-repair-startdate").val(repairs_json.repairs[id].startdate);
        $("#update-repair-enddate").val(repairs_json.repairs[id].enddate);
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