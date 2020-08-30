/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    marriage.service.ts                                   */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { Marriage } from '../models/marriage';
import { BaseService } from './base.service';
import { QueryService } from './http/query.service';


@Injectable({
  providedIn: 'root'
})
export class MarriageService extends BaseService{

  marriageUrl = "marriage";

  constructor(protected queryService: QueryService) { super(queryService);}
  
  getMarriagesByRelationshipId(id: number): Observable<Marriage[]> {
    const url = `${this.marriageUrl}/relationship/${id}`;
    return this.queryService.get<Marriage[]>(url).pipe(map(result => result['marriagesGet']));
  }

  createMarriage(marriage: Marriage, familytreeId: number): Observable<Marriage> {
    const url = `${this.marriageUrl}/${familytreeId}`;
    return this.queryService.post<Marriage[]>(url, marriage).pipe(map(result => result['marriageCreate']));
  }
}
