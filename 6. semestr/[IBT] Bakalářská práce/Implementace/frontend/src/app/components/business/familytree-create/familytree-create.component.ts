/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    familytree-create.component.ts                        */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Component, OnInit } from '@angular/core';
import { MatDialogRef } from '@angular/material';
import { Router } from '@angular/router';
import { FamilyTree, FamilyTreeTypesEnum } from 'src/app/models/familytree';
import { Person } from 'src/app/models/person';
import { FamilyTreeService } from 'src/app/services/familytree.service';
import { AuthenticationService } from 'src/app/services/http/authentication.service';
import { BirthRecordService } from 'src/app/services/birthRecords/birth-record.service';

@Component({
  selector: 'app-familytree-create',
  templateUrl: './familytree-create.component.html',
  styleUrls: ['./familytree-create.component.scss']
})
export class FamilytreeCreateComponent implements OnInit {

  familytree: FamilyTree;
  person: Person = null;
  isFormValid: boolean = false;
  personFromExistingRecords: Person = null;

  constructor(
    private authenticationService: AuthenticationService,
    private dialogRef: MatDialogRef<FamilytreeCreateComponent>,
    private familytreeService: FamilyTreeService,
    private birthRecordService: BirthRecordService,
    private router: Router
  ) { }

  ngOnInit(): void {
    this.familytree = this.initFamilyTree();
    this.person = this.initPerson();
  }

  // Provides creating process of new FamilyTree
  createTree(person: Person): void {
    // Creating new person with the familytree 
    if (person.id == null) {
      this.familytree.startPerson = person;
    }
    // Person already exists in db
    else {
      this.familytree.startPersonId = person.id;
    }

    // Get birth records
    var birthRecord = this.familytreeService.buildBirthRecord(person);

    this.birthRecordService.getBirthRecordIds(birthRecord).subscribe(ids => {
      if (ids) {

        // Starting familyTree with new person
        if(person.id == null)
        {
          // There are 1-5 ids
          if (ids.length > 0 && ids.length < 6) {
  
            person.originalRecords = [];
            // Add originalIds to Person
            ids.forEach(currentId => person.originalRecords.push({ id: null, recordId: currentId, personId: null }));
          }
        }

        this.familytreeService.createFamilyTree(this.familytree).subscribe(familytree => {
          if (familytree) {
            this.familytree = familytree;
            if (this.person.id == null) {
              this.familytree.startPersonId = this.familytree.familyTreePerson[0].personId;
              this.updateTree();
            }
            else {
              this.dialogRef.close();
              var url = "familytree/" + this.familytree.id;
              this.router.navigate([url]);
            }
          }
        });

      };
    });

  }

  // Updates tree with startPerson information and redirects to the familytree
  updateTree(): void {
    this.familytreeService.updateFamilyTree(this.familytree).subscribe(familytree => {
      if (familytree) {
        this.dialogRef.close();
        var url = "familytree/" + this.familytree.id;
        this.router.navigate([url]);
      }
    }
    )
  }

  // Callback from person-create.component
  formValid(isValid: boolean) {
    this.isFormValid = isValid;
  }


  // Method will emit the detector change in person-select-similiar component
  callSimiliarPersonSearch(): void {
    if (this.isFormValid)
      this.person = { ...this.person };
  }

  // Callback from person-select-similiar.component
  newSelectedExistingPerson(person: Person) {
    this.personFromExistingRecords = person;
  }

  // Closes the modal window
  close(): void {
    this.dialogRef.close(false);
  }

  // Initialize Person
  private initPerson(): Person {
    var person: Person = {
      id: null,
      isFemale: false,
      isPrivate: (this.familytree.type == FamilyTreeTypesEnum._private) ? true : false,
      isUndefined: false,
      personNames: [
        {
          id: null,
          isFirstName: true,
          name: "",
          person: null,
          personId: null
        },
        {
          id: null,
          isFirstName: false,
          name: "",
          person: null,
          personId: null
        }
      ],
      originalRecords: null,
      ancestorOrHusbandRelationship: [],
      descendantOrWifeRelationship: [],
      mainInFamilyTrees: [],
      familyTreePerson: []
    }
    return person;
  }

  // Initialize FamilyTree
  private initFamilyTree(): FamilyTree {
    var familytree: FamilyTree = {
      id: null,
      title: null,
      type: 1,
      familyTreeCollision: [],
      familyTreePerson: [],
      familyTreeRelationship: [],
      startPersonId: null,
      startPerson: null,
      user: null,
      userId: this.authenticationService.getHash('auth', 'id')
    }
    return familytree;
  }
}
