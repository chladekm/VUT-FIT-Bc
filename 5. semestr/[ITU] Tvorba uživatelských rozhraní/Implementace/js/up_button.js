/*********************************************************************************
 * Project:   3BIT ITU, Project - Evidence system                                *
 *            Faculty of Information Technology                                  *
 *            Brno University of Technology                                      *
 * File:      uo_button.js                                                       *
 * Date:      08.12.2019                                                         *
 * Authors:   Peter Kruty, <xkruty00@stud.fit.vutbr.cz>                          *
 *********************************************************************************/

function checkPosition() {
    var up_button = document.getElementById("up_button");
    let windowY = window.scrollY;
  
  if (windowY == 0) {
    up_button.classList.remove("animated", "slideInUp");
    up_button.classList.add('animated', 'slideOutDown');
    
  } else {
    up_button.style.visibility='visible';
    up_button.classList.remove("animated", "slideOutDown");
    up_button.classList.add('animated', 'slideInUp');
  }
}

window.addEventListener('scroll', checkPosition);