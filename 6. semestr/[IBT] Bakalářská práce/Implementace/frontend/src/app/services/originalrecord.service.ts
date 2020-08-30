/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    originalrecord.service.ts                             */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { BaseService } from './base.service';
import { QueryService } from './http/query.service';
import { OriginalRecord } from '../models/originalRecord';


@Injectable({
  providedIn: 'root'
})
export class OriginalRecordService extends BaseService{

  originalrecordUrl = "originalrecord";

  constructor(protected queryService: QueryService) { super(queryService);}
  
  getOriginalRecordsByPersonId(id: number): Observable<OriginalRecord[]> {
    const url = `${this.originalrecordUrl}/${id}`;
    return this.queryService.get<OriginalRecord[]>(url).pipe(map(result => result['recordsGet']));
  }
}
