/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    user-detail-edit.component.ts                         */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { User } from 'src/app/models/user';
import { UserService } from '../../../services/user.service';



@Component({
  selector: 'app-user-detail-edit',
  templateUrl: './user-detail-edit.component.html',
  styleUrls: ['./user-detail-edit.component.scss']
})
export class UserDetailEditComponent implements OnInit {

  @Input()
  user: User;
  userEdit : User;

  willReturn : boolean = false;
  @Output() returnNeeded = new EventEmitter<boolean>();


  constructor(
    private userService: UserService,
    ) { }

  ngOnInit() {
    this.userEdit = JSON.parse(JSON.stringify(this.user));
  }

  // Return from editing
  goBack() {
    this.returnNeeded.emit(true);
  }

  // Save changes
  save(): void {
    this.userService.updateUser(this.userEdit)
      .subscribe(user => {
        if(user)
        {
          this.user.name = user.name;
          this.user.surname = user.surname;
          this.user.email = user.email;
          this.goBack();
        }
        });
  }


}
