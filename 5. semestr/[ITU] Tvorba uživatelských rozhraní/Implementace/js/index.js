/*********************************************************************************
 * Project:   3BIT ITU, Project - Evidence system                                *
 *            Faculty of Information Technology                                  *
 *            Brno University of Technology                                      *
 * File:      index.js                                                           *
 * Date:      08.12.2019                                                         *
 * Authors:   Peter Kruty, <xkruty00@stud.fit.vutbr.cz>                          *
 *********************************************************************************/

$("#login_button").click(function() {
    login();
});

function reload_login_state() {
    if (user_logged == false) {
        login_form = document.getElementsByClassName("login_form")[0];
        nav_buttons = document.getElementsByClassName("nav_buttons");
        
        login_form.style.visibility = "visible";
        nav_buttons[0].style.visibility = "hidden";
        nav_buttons[1].style.visibility = "hidden";
    }
    else if (user_logged == true) {
        login_form = document.getElementsByClassName("login_form")[0];
        nav_buttons = document.getElementsByClassName("nav_buttons");
        loader = document.getElementsByClassName("loader")[0];
        loader_background = document.getElementsByClassName("loader_background")[0];
        
        $('#home_username_block').removeClass("d-none");
        loader.style.visibility = "hidden";
        loader_background.style.visibility = "hidden";
        login_form.style.visibility = "hidden";
        nav_buttons[0].style.visibility = "visible";
        nav_buttons[1].style.visibility = "visible"; 
    }
}



function login() {    
    console.log("Uzivatel je prihlaseny");
    loader = document.getElementsByClassName("loader")[0];
    loader_background = document.getElementsByClassName("loader_background")[0];
    login_form = document.getElementsByClassName("login_form")[0];
    
    user_logged = true;


    loader.style.visibility = "visible";
    loader_background.style.visibility = "visible";
    login_form.style.visibility = "hidden";

    setTimeout(function(){
        reload_login_state();
    },1000); //delay is in milliseconds 
}

function logout() {
    console.log("Uzivatel nieje prihlaseny");
    user_logged = false;
    reload_login_state();
}