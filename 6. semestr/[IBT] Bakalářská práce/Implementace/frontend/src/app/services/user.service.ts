/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    user.service.ts                                       */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { LoginCredentials } from '../models/authentication/login-credentials';
import { User } from '../models/user';
import { BaseService } from './base.service';
import { QueryService } from './http/query.service';



@Injectable({
  providedIn: 'root'
})
export class UserService extends BaseService {

  userUrl = "user";

  constructor(protected queryService: QueryService) { super(queryService);}

  authenticate(user : LoginCredentials): Observable<User> {
    const url = `${this.userUrl}/login`;
    return this.queryService.post<LoginCredentials>(url, user).pipe(map(result => result['login']));
  }

  checkUniqueCredentials(credentials : LoginCredentials): Observable<boolean> {
    const url = `${this.userUrl}/check`;
    return this.queryService.post(url, credentials);
  }

  getUser(id: number): Observable<User> {
    const url = `${this.userUrl}/${id}`;
    return this.queryService.get<User>(url).pipe(map(result => result['userGet']));
  }

  createUser(user: User): Observable<any> {
    return this.queryService.post(this.userUrl, user).pipe(map(result => result['userCreate']));
  }

  updateUser(user: User): Observable<any> {
    return this.queryService.put(this.userUrl, user).pipe(map(result => result['userUpdate']));
  }

}
