/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    app-routing.module.ts                                 */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { UrlFamilytreeComponent } from './components/pages/url-familytree/url-familytree.component';
import { HomeComponent } from './components/pages/home/home.component';
import { ErrorComponent } from './components/pages/error/error.component';
import { UrlLoginComponent } from './components/pages/url-login/url-login.component';
import { UrlUserRegisterComponent } from './components/pages/url-user-register/url-user-register.component';
import { UrlUserDashboardComponent } from './components/pages/url-user-dashboard/url-user-dashboard.component';
import { ErrorAuthorizationComponent } from './components/pages/error-authorization/error-authorization.component';


const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'register', component: UrlUserRegisterComponent },
  { path: 'login', component: UrlLoginComponent },
  { path: 'dashboard', component: UrlUserDashboardComponent },
  { path: 'familytree/:id', component: UrlFamilytreeComponent },
  { path: 'error', component: ErrorComponent },
  { path: 'error-auth', component: ErrorAuthorizationComponent },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
