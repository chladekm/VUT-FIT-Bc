/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    user-login.component.ts                               */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { LoginCredentials } from 'src/app/models/authentication/login-credentials';
import { AuthenticationService } from 'src/app/services/http/authentication.service';
import { UserService } from 'src/app/services/user.service';

@Component({
  selector: 'app-user-login',
  templateUrl: './user-login.component.html',
  styleUrls: ['./user-login.component.scss']
})
export class UserLoginComponent implements OnInit {

  loginForm: FormGroup;
  returnUrl: string;

  loading = false;
  loginFailed = false;


  constructor(
    private userService: UserService,
    private authenticationService: AuthenticationService,
    private formBuilder: FormBuilder,
    public router: Router
  ) { }

  ngOnInit(): void {

    this.loginForm = this.formBuilder.group({
      username: ['', [Validators.required, Validators.pattern('^[A-Za-z0-9]+$')]],
      password: ['', [Validators.required, Validators.minLength(5)]]
    });

  }

  get f() { return this.loginForm.controls; }

  // Login to system
  onSubmit() {

    if (this.loginForm.invalid) {
      return;
    }

    var credentials: LoginCredentials =
    {
      id: null,
      nickname: this.f.username.value,
      password: this.f.password.value
    }

    this.userService.authenticate(credentials).subscribe(
      data => {
        this.authenticationService.setHash(data.password, data.id);
        this.returnUrl = "/dashboard";
        this.router.navigate([this.returnUrl]);
      },
      error => {
        this.loginFailed = true;
      }
    )
  }
}
