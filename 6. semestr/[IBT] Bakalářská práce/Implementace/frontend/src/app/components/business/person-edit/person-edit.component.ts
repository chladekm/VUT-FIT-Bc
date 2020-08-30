/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    person-edit.component.ts                              */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { Person } from 'src/app/models/person';
import { PersonName } from 'src/app/models/personName';
import { PersonService } from 'src/app/services/person.service';
import { BirthRecordService } from 'src/app/services/birthRecords/birth-record.service';

@Component({
  selector: 'app-person-edit',
  templateUrl: './person-edit.component.html',
  styleUrls: ['./person-edit.component.scss']
})
export class PersonEditComponent implements OnInit {

  person: Person;
  personName: PersonName = null;
  familyTreeId: number;

  editMode: boolean = false;
  startDate = new Date(1900, 0, 1);

  areDatesValid: boolean = true;

  constructor(
    @Inject(MAT_DIALOG_DATA) public data: any,
    private dialogRef: MatDialogRef<PersonEditComponent>,
    private personService: PersonService,
    private birthRecordService: BirthRecordService
  ) { }

  ngOnInit(): void {
    this.person = this.personService.copyObject(this.data.person);
    this.familyTreeId = this.data.familyTreeId;
  }

  // Pushes new name to current person
  pushNewName(): void {
    this.person.personNames.push(this.personName);
    this.personName = null;
  }

  // Deletes existing name
  deleteName(name: PersonName): void {
    const index: number = this.person.personNames.indexOf(name);
    if (index !== -1) {
      this.person.personNames.splice(index, 1);
    }
  }

  // Filters names by type
  filterNamesOfType(type: boolean): PersonName[] {
    return this.person.personNames.filter(x => x.isFirstName == type);
  }

  // Checks if none of names is empty
  allNamesSet(): boolean {

    var BreakException = {};

    try {

      this.person.personNames.forEach(name => {
        if (name.name == '') {
          throw BreakException
        }
      });

    } catch (e) {
      return false;
    }

    return true;
  }

  // Save changes
  save(): void {

    // Object is the same -> no need to update in database
    if (JSON.stringify(this.person) === JSON.stringify(this.data.person)) {
      this.dialogRef.close(this.person);
      return;
    }

    var birthRecord = this.personService.buildBirthRecord(this.person);
    this.person.originalRecords = [];

    this.birthRecordService.getBirthRecordIds(birthRecord).subscribe(ids => {
      if (ids) {
        // There are 1-5 ids
        if (ids.length > 0 && ids.length < 6) {

          this.person.originalRecords = [];
          // Add originalIds to Person
          ids.forEach(currentId => this.person.originalRecords.push({ id: null, recordId: currentId, personId: this.person.id }));
        }

        this.person.ancestorOrHusbandRelationship = null;
        this.person.descendantOrWifeRelationship = null;

        this.personService.updatePersonCredentials(this.person, this.familyTreeId).subscribe(person => {
          if (person) {
            this.person = person;
            this.dialogRef.close(person);
          }
        })
      };
    })
  }

  // Transform date to czech format
  transformDate(dateString: string, type: number) {

    var date = this.personService.dateFormatTransformation(dateString);

    if (date)
      this.saveDate(date, type);

    this.checkDatesChronology();
  }

  // Method provides validation that dates are in chronological order
  checkDatesChronology() {
    // Transform to same format
    var birthDate = this.person.birthDate ? new Date(this.personService.copyObject(this.person.birthDate)) : null;
    var baptismDate = this.person.baptismDate ? new Date(this.personService.copyObject(this.person.baptismDate)) : null;
    var deathDate = this.person.deathDate ? new Date(this.personService.copyObject(this.person.deathDate)) : null;

    birthDate ? birthDate.setHours(0,0,0,0) : null; 
    baptismDate ? baptismDate.setHours(0,0,0,0) : null;
    deathDate ? deathDate.setHours(0,0,0,0) : null;

    if (this.personService.datesChronologyValidation(birthDate, baptismDate, deathDate)) {
      // Chronologically Valid
      this.areDatesValid = true;
    }
    else {
      // Invalid
      this.areDatesValid = false;
    }
  }

  // Saves date by type 
  saveDate(date: Date, type: number) {
    if (type == 0)
      this.person.birthDate = date;
    else if (type == 1)
      this.person.baptismDate = date;
    else if (type == 2)
      this.person.deathDate = date;
  }

  // Initialize new person's name
  public initPersonName(isFirstName: boolean): void {
    var personName: PersonName = {
      id: null,
      name: '',
      isFirstName: isFirstName,
      personId: this.person.id
    }
    this.personName = personName;
  }
}
