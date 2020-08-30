/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    collision.service.ts                                  */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { Collision } from '../models/collision';
import { FamilyTreeCollision } from '../models/relationships/familyTreeCollisions';
import { BaseService } from './base.service';
import { QueryService } from './http/query.service';


@Injectable({
  providedIn: 'root'
})


export class CollisionService extends BaseService{

  collisionUrl = "collision";

  constructor(protected queryService: QueryService) { super(queryService);}

  getCollisionsByFamilyTreeId (id : number): Observable<Collision[]> {
    const url = `${this.collisionUrl}/${id}`;
    return this.queryService.get<Collision[]>(url).pipe(map(result => result['collisionsGet']));
  }

  getCountOfUnsolvedCollisions (id : number): Observable<number> {
    const url = `${this.collisionUrl}/count/${id}`;
    return this.queryService.get<number>(url).pipe(map(result => result['collisionsCountGet']));
  }

  toggleCollisionSolvedAttribute(item: FamilyTreeCollision): Observable<FamilyTreeCollision> {
    const url = `${this.collisionUrl}/solved/`;
    return this.queryService.put<FamilyTreeCollision>(url, item).pipe(map(result => result['collisionSolved']));
  }
  
}
