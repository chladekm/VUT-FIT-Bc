/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    person-replace-undefined.component.ts                 */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { ChangeDetectionStrategy, Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { FamilyTree, FamilyTreeTypesEnum } from 'src/app/models/familytree';
import { Person } from 'src/app/models/person';
import { Relationship } from 'src/app/models/relationship';
import { PersonService } from 'src/app/services/person.service';

@Component({
  changeDetection: ChangeDetectionStrategy.OnPush,
  selector: 'app-person-replace-undefined',
  templateUrl: './person-replace-undefined.component.html',
  styleUrls: ['./person-replace-undefined.component.scss']
})
export class PersonReplaceUndefinedComponent implements OnInit {

  person: Person = null;

  familytree: FamilyTree;
  allRelationships: Relationship[];

  undefinedPerson: Person = null;

  isFormValid: boolean = false;

  personFromExistingRecords: Person;

  smallDisplayContinue: boolean = false;

  constructor(
    @Inject(MAT_DIALOG_DATA) public data: any,
    private dialogRef: MatDialogRef<PersonReplaceUndefinedComponent>,
    public personService: PersonService
  ) { }

  ngOnInit(): void {
    // Get data from parent
    this.allRelationships = this.data.relationships;
    this.undefinedPerson = this.data.selectedPerson;
    this.familytree = this.data.familytree;

    // Remove circular dependency
    this.undefinedPerson.ancestorOrHusbandRelationship.forEach(rel => rel.ancestorOrHusbandPerson = null);
    this.undefinedPerson.descendantOrWifeRelationship.forEach(rel => rel.descendantOrWifePerson = null);

    // Set person
    this.person = this.personService.copyObject(this.undefinedPerson);
    this.person.isUndefined = false;
    this.person.isPrivate = (this.familytree.type == FamilyTreeTypesEnum._private) ? true : false;
    this.person.personNames = [];

    // Push Firstname & Lastname to edit
    this.person.personNames.push({ name: "", isFirstName: true, personId: this.undefinedPerson.id, id: null });
    this.person.personNames.push({ name: "", isFirstName: false, personId: this.undefinedPerson.id, id: null });
  }

  replacePerson(person: Person): void {
    // Updating undefined person
    if (person.id == this.undefinedPerson.id) {
      this.personService.replaceUndefinedPersonInFamilyTree(person).subscribe(rp => {
        if(rp){
          this.close('replaced');
        }
      })
    }
    // Replacing by existing person
    else {
      this.personService.replacePersonInFamilyTree(this.undefinedPerson.id, person.id, this.familytree.id).subscribe(rp => {
        if (rp) {
          this.close('replaced');
        }
      }
      );
    }

  }

  // -------------------- Communication with child components -------------------- //

  // Callback from person-create.component
  formValid(isValid: boolean) {
    this.isFormValid = isValid;
  }

  callSimiliarPersonSearch(): void {
    // this will emit the detector change in person-select-similiar component
    if (this.isFormValid) {
      this.person = { ...this.person };
    }
  }

  // Callback from person-select-similiar.component
  newSelectedExistingPerson(person: Person) {
    this.personFromExistingRecords = person;
  }

  // -------------------- Communication with parent component -------------------- //

  // Closes the modal window
  close(action?: string): void {
    this.dialogRef.close((action) ? action : null);
  }
}
