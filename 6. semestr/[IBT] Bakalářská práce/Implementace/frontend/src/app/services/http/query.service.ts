/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    query.service.ts                                      */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { AuthenticationService } from './authentication.service';

@Injectable({
  providedIn: 'root'
})
export class QueryService {

  private baseUrl = "https://localhost:44358/";

  httpOptions = {
    headers: new HttpHeaders({ 'Content-Type': 'application/json' })
  };

  constructor(protected httpClient: HttpClient, private authenticationService: AuthenticationService) { }

  get<T>(
    action: string
  ): Observable<T> {
    return this.httpClient
      .get(this.baseUrl + action, {
        headers: this.getHeaders()
      })
      .pipe(
        map((response: any) => response)
      );
  }

  put<T>(
    action: string,
    data: any
  ): Observable<T> {
    return this.httpClient
      .put(this.baseUrl + action, data, {
        headers: this.getHeaders()
      })
      .pipe(
        map((response: any) => response)
      );
  }

  post<T>(
    action: string,
    data: any
  ): Observable<T> {
    return this.httpClient
      .post<T>(this.baseUrl + action, data, {
        headers: this.getHeaders()
      })
      .pipe(
        map((response: any) => response)
      );

  }

  delete<T>(
    action: string
  ): Observable<T> {
    return this.httpClient
      .delete<T>(this.baseUrl + action, {
        headers: this.getHeaders()
      })
      .pipe(
        map((response: any) => response)
      );

  }

  private getHeaders(): HttpHeaders {
    var id = this.authenticationService.getHash('auth', 'id');
    var hash = this.authenticationService.getHash();

    if(id && hash)
    {
      return new HttpHeaders({
        'Content-Type': 'application/json',
        'auth': JSON.stringify(
          { 
            'Id' : id, 
            'Hash' : hash
          })
      })
    }
    else
    {
      return new HttpHeaders({
        'Content-Type': 'application/json'
      })
    }
  }

}
