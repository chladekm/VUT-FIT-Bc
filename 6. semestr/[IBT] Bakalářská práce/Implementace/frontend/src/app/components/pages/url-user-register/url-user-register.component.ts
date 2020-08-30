/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    url-user-register.component.ts                        */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';

@Component({
  selector: 'app-url-user-register',
  templateUrl: './url-user-register.component.html',
  styleUrls: ['./url-user-register.component.scss']
})
export class UrlUserRegisterComponent implements OnInit {

  constructor(private titleService: Title) { }


  ngOnInit(): void {
    this.titleService.setTitle("Registrace");
  }

}
