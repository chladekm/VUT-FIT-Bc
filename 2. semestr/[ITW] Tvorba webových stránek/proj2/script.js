    /* ---------- COOKIE SET ----------*/

    var cname = 'DIV_NEWHOME';

    var cookies = document.cookie;

    var c_find_home='0';

    var pos1 = cookies.indexOf(escape(cname) + '=');

    if (pos1 != -1)
    {
        pos1 = pos1 + (escape(cname) + '=').length;
        pos2 = cookies.indexOf(';', pos1);
        if (pos2 == -1) pos2 = cookies.length;

    c_find_home = cookies.substring(pos1, pos2);
    }

$(document).ready(function() {
      
    /* ------------ ON LOAD ----------- */

    $(window).load(function() {
      
      $('.photocontainer').hide();

    });

    if(c_find_home == '0'){

      $('#find_your_new_home').hide();
      $(".bg2").css("margin-bottom", "-20px");
   
    }

    // vymena sipky, pokud je box pri nacteni otevreny 
    if(c_find_home == '1'){

       $(".float_right_button").each(function(e){
       $(this).html("<i class='fas fa-chevron-circle-up'></i>");
          });
    }

    /* --------- SEARCH --------*/

    $(".search_click").click(function (event){
			   event.preventDefault();
			   $("#search").toggle();
			     
			 });

    $("#main").click(function(e){
    		$("#search").hide();
    });

    $(".box, .title").click(function(e){
    		$("#search").hide();
    });

    /* ------- FIND YOUR NEW HOME -------- */   

    var opened = c_find_home; 

    $(".float_right_button").click(function FindBox(e){

      $("#find_your_new_home").slideToggle(350,"linear");
       
       if(opened == 1){
           $(this).html("<i class='fas fa-chevron-circle-down'></i>");
           $(".bg2").css("margin-bottom", "-20px");
           opened = 0
           document.cookie = "DIV_NEWHOME=0";
           alert(DIV_NEWHOME);
       } else {
           $(this).html("<i class='fas fa-chevron-circle-up'></i>");
           $(".bg2").css("margin-bottom", "20px");
           opened = 1
           document.cookie = "DIV_NEWHOME=1";
       }      
    });

});

function toggleme(param)
	{
		$(".photocontainer:eq("+param+")").slideToggle(350,"linear");
		
		/*
		param=param+1;

		if($(".photocontainer").is(":visible"))
 		{
			$(".minicontainer:eq("+param+")").css("background-color", "yellow !important");
  		}
  		
  		if($(".photocontainer").is(":not(:visible)"))
  		{
  			$(".minicontainer:eq("+param+")").css("margin-top", "-20px");
  		}
  		*/
	};

function searchfooter()
{
	$("#search").show();
}

function RealButton()
{
	alert("I want to be real button :(");
}

