/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    birth-record.service.ts                               */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Injectable } from '@angular/core';
import { BirthRecord } from 'src/app/models/birthRecords/birth-record';
import { Observable } from 'rxjs';
import { HttpClient, HttpParams, HttpHeaders } from '@angular/common/http';
import { map } from 'rxjs/operators';


@Injectable({
  providedIn: 'root'
})
export class BirthRecordService {

  constructor(private httpClient: HttpClient, ) { }

  url = "http://perun.fit.vutbr.cz/demos/api/search/?";

  getBirthRecordIds(birthRecord: BirthRecord): Observable<number[]> {

    // Data are required in JSON format 
    birthRecord.json = true;

    // Convert to GTM +0 
    if(birthRecord.birthDate != null && birthRecord.birthDate != undefined && typeof birthRecord.birthDate == "object")
      birthRecord.birthDate.setHours(birthRecord.birthDate.getHours()+1); 
    
    const headers = this.setHeaders();
    const params = this.setParams(birthRecord);
    
    return this.httpClient.get<string>(this.url + params, {headers}).pipe(map(result => JSON.parse(result)));;
  }

  getUrlForPersonSearch(birthRecord: BirthRecord) : string {

    // Data are required in HTML format 
    birthRecord.json = false;
    const params = this.setParams(birthRecord);

    return this.url + params;
  }

  private setHeaders() : HttpHeaders {
    return new HttpHeaders()
    .append('Content-Type', 'application/json')
    .append('Access-Control-Allow-Headers', 'Content-Type')
    .append('Access-Control-Allow-Methods', 'GET')
    .append('Access-Control-Allow-Origin', '*');
  }

  private setParams(birthRecord: BirthRecord): HttpParams {

    if (birthRecord.birthDate) {
      var date = JSON.stringify(birthRecord.birthDate);
      var birthDate = date.substring(1, 11);
    }

    let params = new HttpParams()
      .set('acode', 'd396173a554a4ab8a379cb988f3c5ce8')
      .set('name', birthRecord.name.normalize("NFD").replace(/[\u0300-\u036f]/g, ""))
      .set('surname', birthRecord.surname.normalize("NFD").replace(/[\u0300-\u036f]/g, ""))
      .set('json', birthRecord.json ? '1' : '0');

    if (birthRecord.birthDate)
      params = params.set('birthDate', birthDate);

    if (birthRecord.domicile)
      params = params.set('domicile', birthRecord.domicile.normalize("NFD").replace(/[\u0300-\u036f]/g, ""));

    return params;
  }
}
