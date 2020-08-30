/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    app.module.ts                                         */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { BrowserModule } from '@angular/platform-browser';
import { NgModule, ErrorHandler } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';

// Added modules
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { LayoutModule } from '@angular/cdk/layout';
import { FlexLayoutModule } from '@angular/flex-layout';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { MAT_DATE_LOCALE } from '@angular/material';

// Custom modules
import { MaterialModule } from './material/material.module';
import { StorageModule } from 'src/libraries/storage';
import { GlobalErrorHandler } from './global-error-handler';

// Created components
// Business
import { CollisionListComponent } from './components/business/collision-list/collision-list.component';
import { CollisionDetailComponent } from './components/business/collision-detail/collision-detail.component';
import { FamilytreeComponent } from './components/business/familytree/familytree.component';
import { FamilytreeDetailComponent } from './components/business/familytree-detail/familytree-detail.component';
import { FamilytreeCreateComponent } from './components/business/familytree-create/familytree-create.component';
import { FamilytreeConcatenateComponent } from './components/business/familytree-concatenate/familytree-concatenate.component';
import { UserDetailComponent } from './components/business/user-detail/user-detail.component';
import { UserLoginComponent } from './components/business/user-login/user-login.component';
import { UserRegisterComponent } from './components/business/user-register/user-register.component';
import { UserDashboardComponent } from './components/business/user-dashboard/user-dashboard.component';
import { UserDetailEditComponent } from './components/business/user-detail-edit/user-detail-edit.component';
import { UserFamilytreesComponent } from './components/business/user-familytrees/user-familytrees.component';
import { PersonSelectSimiliarComponent } from './components/business/person-select-similiar/person-select-similiar.component';
import { PersonCreateComponent } from './components/business/person-create/person-create.component';
import { PersonAddComponent } from './components/business/person-add/person-add.component';
import { PersonDetailComponent } from './components/business/person-detail/person-detail.component';
import { PersonDetailMarriageComponent } from './components/business/person-detail-marriage/person-detail-marriage.component';
import { PersonEditComponent } from './components/business/person-edit/person-edit.component';
import { PersonReplaceUndefinedComponent } from './components/business/person-replace-undefined/person-replace-undefined.component';

// UI
import { MainNavComponent } from './components/ui/main-nav/main-nav.component';
import { JumbotronComponent } from './components/ui/jumbotron/jumbotron.component';
import { InfoBlocksComponent } from './components/ui/info-blocks/info-blocks.component';
import { PromoComponent } from './components/ui/promo/promo.component';
import { FamilytreeHelpComponent } from './components/ui/familytree-help/familytree-help.component';
import { FamilytreeRemovingPersonDialogComponent } from './components/ui/familytree-removing-person-dialog/familytree-removing-person-dialog.component';
import { YesNoDialogComponent } from './components/ui/yes-no-dialog/yes-no-dialog.component';

// Pages
import { HomeComponent } from './components/pages/home/home.component';
import { ErrorComponent } from './components/pages/error/error.component';
import { ErrorAuthorizationComponent } from './components/pages/error-authorization/error-authorization.component';
import { UrlFamilytreeComponent } from './components/pages/url-familytree/url-familytree.component';
import { UrlLoginComponent } from './components/pages/url-login/url-login.component';
import { UrlUserRegisterComponent } from './components/pages/url-user-register/url-user-register.component';
import { UrlUserDashboardComponent } from './components/pages/url-user-dashboard/url-user-dashboard.component';
import { CollisionDetailPersonComponent } from './components/business/collision-detail-person/collision-detail-person.component';

@NgModule({
  declarations: [
    AppComponent,
    UrlFamilytreeComponent,
    UrlLoginComponent,
    UrlUserRegisterComponent,
    UrlUserDashboardComponent,
    UserDetailComponent,
    MainNavComponent,
    HomeComponent,
    JumbotronComponent,
    InfoBlocksComponent,
    FamilytreeDetailComponent,
    FamilytreeComponent,
    FamilytreeHelpComponent,
    PersonDetailComponent,
    PersonCreateComponent,
    PersonAddComponent,
    ErrorComponent,
    UserLoginComponent,
    UserRegisterComponent,
    UserDashboardComponent,
    UserDetailEditComponent,
    UserFamilytreesComponent,
    FamilytreeCreateComponent,
    PersonSelectSimiliarComponent,
    YesNoDialogComponent,
    PersonDetailMarriageComponent,
    PersonEditComponent,
    ErrorAuthorizationComponent,
    FamilytreeRemovingPersonDialogComponent,
    CollisionListComponent,
    CollisionDetailComponent,
    PersonReplaceUndefinedComponent,
    FamilytreeConcatenateComponent,
    PromoComponent,
    CollisionDetailPersonComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    FormsModule,
    ReactiveFormsModule,
    HttpClientModule,
    BrowserAnimationsModule,
    LayoutModule,
    FlexLayoutModule,
    MaterialModule,

    StorageModule.forRoot({
      name: 'familytree'
    })
  ],
  providers: [
    { provide: MAT_DATE_LOCALE, useValue: 'cs-CZ' },
    { provide: ErrorHandler, useClass: GlobalErrorHandler }
  ],
  bootstrap: [AppComponent],
  entryComponents: [FamilytreeDetailComponent]
})
export class AppModule { }