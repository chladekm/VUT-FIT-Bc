/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    person.service.ts                                     */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { Person } from '../models/person';
import { BaseService } from './base.service';
import { QueryService } from './http/query.service';


@Injectable({
  providedIn: 'root'
})


export class PersonService extends BaseService {

  personUrl = "person";

  constructor(protected queryService: QueryService) { super(queryService); }

  getPersonById(id: number): Observable<Person> {
    const url = `${this.personUrl}/${id}`;
    return this.queryService.get<Person>(url).pipe(map(result => result['personGet']));
  }
  
  getPublicTreesForPerson(personId: number, familyTreeId: number): Observable<number[]> {
    const url = `${this.personUrl}/publicTrees/${personId}-${familyTreeId}`;
    return this.queryService.get<number[]>(url).pipe(map(result => result['personGetPublicTrees']));
  }

  getSimiliarPersons(person: Person): Observable<Person[]> {
    const url = `${this.personUrl}/similiar`;
    return this.queryService.post<Person[]>(url, person).pipe(map(result => result['personsGetSimiliar']));
  }

  createPerson(person: Person): Observable<Person> {
    return this.queryService.post<Person>(this.personUrl, person).pipe(map(result => result['personCreate']));
  }

  replacePersonInFamilyTree(oldPersonId: number, newPersonId: number, familyTreeId: number): Observable<Person> {
    const url = `${this.personUrl}/replace/${oldPersonId}-${newPersonId}-${familyTreeId}`;
    return this.queryService.put<Person>(url, null).pipe(map(result => result['personReplace']));
  }

  replaceUndefinedPersonInFamilyTree(person: Person): Observable<Person> {
    const url = `${this.personUrl}/replace-undefined`;
    return this.queryService.put<Person>(url, person).pipe(map(result => result['personReplaceUndefined']));
  }

  updatePersonCredentials(person: Person, familytreeId: number): Observable<Person> {
    const url = `${this.personUrl}/${familytreeId}`;
    return this.queryService.put<Person>(url, person).pipe(map(result => result['personUpdate']));
  }

  updatePerson(person: Person): Observable<Person> {
    const url = `${this.personUrl}/familytree`;
    return this.queryService.put<Person>(url, person).pipe(map(result => result['personUpdate']));
  }

  removePersonFromFamilyTree(personId: number, familytreeId: number): Observable<Person> {
    const url = `${this.personUrl}/${personId}-${familytreeId}`;
    return this.queryService.delete<Person>(url).pipe(map(result => result['personRemove']));
  }
}
