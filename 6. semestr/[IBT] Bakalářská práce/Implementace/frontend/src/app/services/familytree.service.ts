/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    familytree.service.ts                                 */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { FamilyTree } from '../models/familytree';
import { BaseService } from './base.service';
import { QueryService } from './http/query.service';


@Injectable({
  providedIn: 'root'
})
export class FamilyTreeService extends BaseService{

  familyTreeUrl = "familytree";

  constructor(protected queryService: QueryService) { super(queryService);}

    getFamilyTreeById(id: number): Observable<FamilyTree> {
      const url = `${this.familyTreeUrl}/${id}`;
      return this.queryService.get<FamilyTree>(url).pipe(map(result => result['familyTreeGet']));
    }

    updateFamilyTree(familytree: FamilyTree): Observable<any> {
      return this.queryService.put<FamilyTree>(this.familyTreeUrl, familytree).pipe(map(result => result['familyTreeUpdate']));
    }

    createFamilyTree(familytree: FamilyTree): Observable<any> {
      return this.queryService.post<FamilyTree>(this.familyTreeUrl, familytree).pipe(map(result => result['familyTreeCreate']));
    }

    deleteFamilyTree(id: number): Observable<any> {
      const url = `${this.familyTreeUrl}/${id}`;
      return this.queryService.delete<FamilyTree>(url).pipe(map(result => result['familyTreeDelete']));
    }
    
    getValidFamilyTreesForConcatenation(srcId : number, userId : number): Observable<FamilyTree[]> {
      const url = `${this.familyTreeUrl}/concat-valid/${srcId}-${userId}`;
      return this.queryService.get<FamilyTree[]>(url).pipe(map(result => result['familyTreeConcatValid']));
    }

    concatenateFamilyTrees(srcId : number, dstId : number): Observable<FamilyTree> {
      const url = `${this.familyTreeUrl}/concatenate/${srcId}-${dstId}`;
      return this.queryService.put<FamilyTree>(url, null).pipe(map(result => result['familyTreeConcatenate']));
    }
  
}
