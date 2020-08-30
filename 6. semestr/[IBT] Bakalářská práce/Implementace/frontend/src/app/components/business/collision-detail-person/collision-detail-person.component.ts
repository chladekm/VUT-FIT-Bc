/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    collision-detail-person.component.ts                  */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material';
import { Person } from 'src/app/models/person';
import { PersonService } from 'src/app/services/person.service';
import { BirthRecordService } from 'src/app/services/birthRecords/birth-record.service';

@Component({
  selector: 'app-collision-detail-person',
  templateUrl: './collision-detail-person.component.html',
  styleUrls: ['./collision-detail-person.component.scss']
})
export class CollisionDetailPersonComponent implements OnInit {

  person: Person;
  currentFamilyTreeId;
  familytreeIds: number[];
  birthRecordDatabaseSearchUrl : string;

  constructor(
    @Inject(MAT_DIALOG_DATA) public data: any,
    private personService: PersonService,
    private birthRecordService: BirthRecordService

  ) { }
  
  ngOnInit() {
    this.person = this.data.person;
    this.currentFamilyTreeId = this.data.familyTreeId;
    this.personService.getPublicTreesForPerson(this.person.id, this.currentFamilyTreeId).subscribe(ids => this.familytreeIds = ids);
    
    // If person has no references to records, build url to search
    if(this.person.originalRecords.length == 0)
    {
      var birthRecord = this.personService.buildBirthRecord(this.person);
      this.birthRecordDatabaseSearchUrl = this.birthRecordService.getUrlForPersonSearch(birthRecord);
    }
  }
}
