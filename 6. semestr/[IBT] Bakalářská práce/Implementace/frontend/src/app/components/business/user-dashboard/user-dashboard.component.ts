/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    user-dashboard.component.ts                           */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { User } from 'src/app/models/user';
import { AuthenticationService } from 'src/app/services/http/authentication.service';
import { UserService } from 'src/app/services/user.service';

@Component({
  selector: 'app-user-dashboard',
  templateUrl: './user-dashboard.component.html',
  styleUrls: ['./user-dashboard.component.scss']
})
export class UserDashboardComponent implements OnInit {

  userId: number;
  user: User;
  selectedComponent: string = "user-familytrees";

  constructor(
    private userService: UserService,
    private authenticationService: AuthenticationService,
    private router: Router
  ) { }

  ngOnInit(): void {
    this.userId = this.authenticationService.getHash('auth', 'id');
    this.getUserById();
  }

  // Gets info about user
  getUserById(): void {
    this.userService.getUser(this.userId).subscribe(user => {
      if (user) {
        this.user = user;
      }
    })
  }

  // Sign out from application (remove saved data from browser)
  signOut(): void {
    this.authenticationService.removeAuth();
    this.router.navigate(['']);
  }

  // Callback from editing personal informations
  returnFromEdit(value: boolean) {
    value ? this.selectedComponent = "user-detail" : "";
  }

}
