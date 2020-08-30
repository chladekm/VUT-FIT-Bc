/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    relationship.service.ts                               */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { Relationship } from '../models/relationship';
import { BaseService } from './base.service';
import { QueryService } from './http/query.service';


@Injectable({
  providedIn: 'root'
})
export class RelationshipService extends BaseService {

  relationshipUrl = "relationship";

  constructor(protected queryService: QueryService) { super(queryService); }

  getRelationshipsByFamilyTreeId(id: number): Observable<Relationship[]> {
    const url = `${this.relationshipUrl}/tree/${id}`;
    return this.queryService.get<Relationship[]>(url).pipe(map(result => result['relationshipsByFamilyTreeGet']));
  }
}
