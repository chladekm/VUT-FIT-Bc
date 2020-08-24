/*********************************************************************************
 * Project:   3BIT ITU, Project - Evidence system                                *
 *            Faculty of Information Technology                                  *
 *            Brno University of Technology                                      *
 * File:      script.js                                                          *
 * Date:      08.12.2019                                                         *
 * Authors:   Martin Chladek <xchlad16@stud.fit.vutbr.cz>                      *
 *********************************************************************************/

/** -------------------- Time for top bar -------------------- **/

export function renderTime()
{
	var day_array = new Array("Pondělí","Úterý","Středa","Čtvrtek","Pátek","Sobota","Neděle");
	var month_array = new Array("leden","únor","březen","duben","květen","červen","červenec","srpen","září","říjen","listopad","prosinec");
	
	var date = new Date();
	var year = date.getYear();
	var day = date.getDay();
	var month = date.getMonth();
	var daym = date.getDate();

	var hours = date.getHours();
	var minutes = date.getMinutes();
	var seconds = date.getSeconds();

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
	myClock.innerHTML = "" + day_array[day-1] + ", " + daym + ". " + month_array[month] + " " + year + " | " + hours + ":" + minutes + ":" + seconds;

	setTimeout("renderTime()", 1000);
}

 	/* Click somewhere to hide menu */
 	// Studijni zdroj: https://recalll.co/?q=Collapse%20jquery%20tab%20when%20click%20outside%20of%20the%20div&type=code
    $(document).click(function(e)
    {
	    var target = e.target;

	    if (!$(target).is('.collapse') && !$(target).parents().is('.navbar'))
	    {
	        $('.collapse').collapse("hide");
    	}
	});
