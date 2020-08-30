/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    user-register.component.ts                            */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { LoginCredentials } from 'src/app/models/authentication/login-credentials';
import { User } from 'src/app/models/user';
import { AuthenticationService } from 'src/app/services/http/authentication.service';
import { UserService } from 'src/app/services/user.service';


@Component({
  selector: 'app-user-register',
  templateUrl: './user-register.component.html',
  styleUrls: ['./user-register.component.scss']
})
export class UserRegisterComponent implements OnInit {

  registerForm: FormGroup;
  user: User;
  usernameUnique: boolean = true;
  passwordsIdentic: boolean = true;

  constructor(
    private router: Router,
    private authenticationService: AuthenticationService,
    private userService: UserService,
    private formBuilder: FormBuilder
  ) { }

  ngOnInit(): void {
    if (this.authenticationService.getHash())
      this.router.navigate(['']);

    this.registerForm = this.formBuilder.group({
      name: ['', [Validators.pattern('^[A-Za-zá-žÁ-Ž- ]+$')]],
      surname: ['', [Validators.pattern('^[A-Za-zá-žÁ-Ž- ]*$')]],
      email: ['', [Validators.pattern('^[a-zA-Z0-9.!#$%&*+/=?^_{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$')]],
      nickname: ['', [Validators.required, Validators.pattern('^[A-Za-z0-9]+$')]],
      password: ['', [Validators.required, Validators.minLength(5), Validators.pattern('^[A-Za-z0-9-_]+$')]],
      passwordAgain: ['', [Validators.required, Validators.minLength(5), Validators.pattern('^[A-Za-z0-9-_]+$')]]
    });
  }

  get f() { return this.registerForm.controls; }

  // Check if username does not exist in database
  checkUniqueUsername(): void {
    if(!this.f.nickname.value)
    {
      this.usernameUnique = false;
      return;
    }
    
    var credentials: LoginCredentials = { id: null, nickname: this.f.nickname.value, password: null }
    this.userService.checkUniqueCredentials(credentials).subscribe(unique =>
       { 
         this.usernameUnique = unique;
         
         if(!unique)
            this.f.nickname.setErrors({'incorrect': true}); 
        });
  }

  // Compare two password input fields if are identic
  comparePasswords(): void {

    if(this.f.password.value && this.f.password.valid && this.f.passwordAgain.value && this.f.passwordAgain.valid)
    {
      if(this.f.password.value == this.f.passwordAgain.value)
      {
        this.passwordsIdentic = true;
      }
      else
      {
        this.passwordsIdentic = false;
      }
    }
  }

  // Provides registration process
  onSubmit() {

    if (this.registerForm.invalid) {
      return;
    }

    this.user = this.registerForm.value;
    this.user.registerDate = new Date(Date.now());
    this.userService.createUser(this.user).subscribe(user => 
      {
        this.authenticationService.setHash(user.password, user.id);
        this.router.navigate(["/dashboard"]);
      });
  }
}
