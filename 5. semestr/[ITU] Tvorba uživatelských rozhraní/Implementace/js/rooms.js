/*********************************************************************************
 * Project:   3BIT ITU, Project - Evidence system                                *
 *            Faculty of Information Technology                                  *
 *            Brno University of Technology                                      *
 * File:      rooms.js                                                           *
 * Date:      08.12.2019                                                         *
 * Authors:   Borek Reich, <xreich06@stud.fit.vutbr.cz>                          *
 *********************************************************************************/


window.onload = function () {
    this.load_table_data();    
    this.renderTime();    
}

function load_table_data() {
    var request = new XMLHttpRequest();
    request.open("GET", "database/devices.json", true);
    request.send(null);
    request.onreadystatechange = function() {
        if (request.readyState === 4 && request.status === 200) {
            var devices_json = JSON.parse(request.responseText);            
            var table_body;
            
            if (table_body = document.getElementById("rooms_table_body1A"))
            	selected="1A";
            else if (table_body = document.getElementById("rooms_table_body1B"))
            	selected="1B";
            else if (table_body = document.getElementById("rooms_table_body2A"))
            	selected="2A";
            else if (table_body = document.getElementById("rooms_table_body2B"))
            	selected="2B";
            else if (table_body = document.getElementById("rooms_table_body3A"))
            	selected="3A";
            else if (table_body = document.getElementById("rooms_table_body3B"))
            	selected="3B";
            else if (table_body = document.getElementById("rooms_table_body4A"))
            	selected="4A";
            else if (table_body = document.getElementById("rooms_table_body4B"))
            	selected="4B";
            else if (table_body = document.getElementById("rooms_table_bodyzas_mist1"))
            	selected="zas_mist1";
            else if (table_body = document.getElementById("rooms_table_bodyknihovna"))
            	selected="knihovna";
            else if (table_body = document.getElementById("rooms_table_bodykab1"))
            	selected="kab1";
            else if (table_body = document.getElementById("rooms_table_bodyreditelna"))
            	selected="reditelna";
            else if (table_body = document.getElementById("rooms_table_bodysklad1"))
            	selected="sklad1";
            else if (table_body = document.getElementById("rooms_table_bodyarchiv"))
            	selected="archiv";
            else if (table_body = document.getElementById("rooms_table_bodyjidelna"))
            	selected="jidelna";
            else if (table_body = document.getElementById("rooms_table_bodykuchyn"))
            	selected="kuchyn";
            else if (table_body = document.getElementById("rooms_table_bodykab2"))
            	selected="kab2";
            else if (table_body = document.getElementById("rooms_table_bodykab3"))
            	selected="kab3";
            else if (table_body = document.getElementById("rooms_table_bodysklad2"))
            	selected="sklad2";

	        console.log (selected);

	        var table_row=0;
            for (var i in devices_json.devices) {
                $('#floor_buttons').hide();
            	console.log (devices_json.devices[i].room);
            	if (devices_json.devices[i].room == selected) {
	                var row  = table_body.insertRow(table_row);
	                table_row++;

	                row.setAttribute("id", "table_row" + i);
                    row.setAttribute("onclick", "show_detail(" + i + "); show_edit_detail(" + i + ");");
	                var cell0 = row.insertCell(0);
	                var cell1 = row.insertCell(1);
	                var cell2 = row.insertCell(2);
	                //var cell3 = row.insertCell(3);
	                
	                cell0.innerHTML = devices_json.devices[i].name;
	                cell1.innerHTML = devices_json.devices[i].type;
	                cell2.innerHTML = devices_json.devices[i].disponent;
	                //cell3.innerHTML = devices_json.devices[i].room;
                    

                    cell0.setAttribute("data-toggle", "modal");
                    cell0.setAttribute("data-target", "#exampleModalCenter");
                    cell0.setAttribute("title", "Zobrazit detail zařízení");
                    cell1.setAttribute("data-toggle", "modal");
                    cell1.setAttribute("data-target", "#exampleModalCenter");
                    cell1.setAttribute("title", "Zobrazit detail zařízení");
                    cell2.setAttribute("data-toggle", "modal");
                    cell2.setAttribute("data-target", "#exampleModalCenter");
                    cell2.setAttribute("title", "Zobrazit detail zařízení");
                    /*cell3.setAttribute("data-toggle", "modal");
                    cell3.setAttribute("data-target", "#exampleModalCenter");
                    cell3.setAttribute("title", "Zobrazit detail zařízení");*/
	            }    
            }

        }
    }
}

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
        if (devices_json.devices[id].discarddate)
            $("#device-detail-discarddate").text(devices_json.devices[id].discarddate);
    }
}

// Update device
$("#update-device").click(update_device);

function update_device() {
    $("#exampleModalCenter").addClass("d-none");
}

// Update device cancel
$("#update-device-cancel").click(update_device_cancel);

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