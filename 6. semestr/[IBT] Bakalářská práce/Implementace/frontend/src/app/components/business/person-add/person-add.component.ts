/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    person-add.component.ts                               */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { ChangeDetectionStrategy, Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { FamilyTree, FamilyTreeTypesEnum } from 'src/app/models/familytree';
import { Marriage } from 'src/app/models/marriage';
import { Person } from 'src/app/models/person';
import { Relationship, RelationshipTypesEnum } from 'src/app/models/relationship';
import { SelectedPerson } from 'src/app/models/rendering/selected-person';
import { FamilyTreeService } from 'src/app/services/familytree.service';
import { PersonService } from 'src/app/services/person.service';
import { BirthRecordService } from 'src/app/services/birthRecords/birth-record.service';

enum selectedRelationshipEnum {
  mother,
  father,
  descendant,
  sibling,
  husband,
  wife
}

@Component({
  changeDetection: ChangeDetectionStrategy.OnPush,
  selector: 'app-person-add',
  templateUrl: './person-add.component.html',
  styleUrls: ['./person-add.component.scss'],
})
export class PersonAddComponent implements OnInit {

  selectedPerson: SelectedPerson;
  selectedRelationship: selectedRelationshipEnum;
  selectedSpouseId: number = null;
  person: Person = null;

  familytree: FamilyTree;
  allRelationships: Relationship[];

  sourcePerson: Person = null;
  sourcePersonSpouses: Person[] = [];

  isFormValid: boolean = false;

  personFromExistingRecords: Person;

  marriage: Marriage;
  marriageStartDate = new Date(1900, 0, 1);

  constructor(
    @Inject(MAT_DIALOG_DATA) public data: any,
    private dialogRef: MatDialogRef<PersonAddComponent>,
    public personService: PersonService,
    private familytreeService: FamilyTreeService,
    private birthRecordService: BirthRecordService
  ) { }

  ngOnInit(): void {

    // Get data from parent
    this.allRelationships = this.data.relationships;
    this.selectedPerson = this.data.selectedPerson;
    this.sourcePerson = this.data.selectedPerson.data;
    this.familytree = this.data.familytree;

    // Initialize new person
    this.person = this.initPerson();

    // Initialize potencial marriage
    this.marriage = this.initMarriage();
  }

  // ------------------------ Communication with server -------------------- //

  // Creates record of Person in database
  createPerson(person: Person) {
    this.personService.createPerson(person)
      .subscribe(person => {
        if (person) {
          this.person = person;
          if (this.selectedRelationship == selectedRelationshipEnum.mother || this.selectedRelationship == selectedRelationshipEnum.father) {
            this.familytree.startPersonId = person.id;
          }
          this.updateFamilyTree(this.familytree);
        }
      });
  }

  // Updates record of Person in database
  updatePerson(person: Person) {

    this.personService.updatePerson(person)
      .subscribe(person => {
        if (person) {
          this.person = person;
          if (this.selectedRelationship == selectedRelationshipEnum.mother || this.selectedRelationship == selectedRelationshipEnum.father) {
            this.familytree.startPersonId = person.id;
          }
          this.updateFamilyTree(this.familytree);
        }
      });
  }

  // Updates familyTree in case that startPerson changes
  updateFamilyTree(familytree: FamilyTree): void {
    this.familytreeService.updateFamilyTree(familytree)
      .subscribe(tree => {
        if (tree) {
          this.finishAddingPersonToFamilyTree(this.person);
        }
      })
  }

  // -------------------- Process of adding new person -------------------- //

  // General function that handles adding person to FamilyTree
  addPersonToFamilyTree(): void {

    // If user chose from similiar persons
    if (this.personFromExistingRecords)
      this.person = this.personFromExistingRecords;

    switch (this.selectedRelationship) {
      case selectedRelationshipEnum.mother:
      case selectedRelationshipEnum.father:

        // If creating undefined father (user is creating mother) -> condition will be false -> argument isFemale = false
        var undefinedPerson = this.initUndefinedPerson(this.selectedRelationship != selectedRelationshipEnum.mother);

        if (undefinedPerson.isFemale)
          undefinedPerson.ancestorOrHusbandRelationship.push(this.generateRelationship(undefinedPerson.id, this.sourcePerson.id, RelationshipTypesEnum.isMotherOf));
        else
          undefinedPerson.ancestorOrHusbandRelationship.push(this.generateRelationship(undefinedPerson.id, this.sourcePerson.id, RelationshipTypesEnum.isFatherOf));

        this.createUndefinedPerson(undefinedPerson, false);

        return;

      case selectedRelationshipEnum.descendant:

        // We need to generate new undefined person
        if (this.selectedSpouseId == null || this.selectedSpouseId == -1) {

          this.selectedSpouseId = null;
          var undefinedPerson = this.initUndefinedPerson(this.sourcePerson.isFemale ? false : true);

          if (undefinedPerson.isFemale)
            undefinedPerson.descendantOrWifeRelationship.push(this.generateRelationship(this.sourcePerson.id, undefinedPerson.id, RelationshipTypesEnum.isInMarriageWith));
          else
            undefinedPerson.ancestorOrHusbandRelationship.push(this.generateRelationship(undefinedPerson.id, this.sourcePerson.id, RelationshipTypesEnum.isInMarriageWith));

          this.createUndefinedPerson(undefinedPerson, true);
        }
        // We know who is mother/father
        else {
          this.createDescendantRelationship();
        }

        return;

      // Create Sibling relationship
      case selectedRelationshipEnum.sibling:

        var motherRelationship = this.generateRelationship(this.getMother(this.sourcePerson), this.person.id, RelationshipTypesEnum.isMotherOf);
        var fatherRelationship = this.generateRelationship(this.getFather(this.sourcePerson), this.person.id, RelationshipTypesEnum.isFatherOf);

        this.person.descendantOrWifeRelationship.push(motherRelationship, fatherRelationship);
        break;

      // Create relationship with Husband
      case selectedRelationshipEnum.husband:
      case selectedRelationshipEnum.wife:

        // Creating husband
        if (this.sourcePerson.isFemale) {
          var marriageRelationship = this.generateRelationship(this.person.id, this.sourcePerson.id, RelationshipTypesEnum.isInMarriageWith);

          // Insert informations about marriage
          if (this.marriage.marriageAddress != '' || this.marriage.marriageDate != null)
            marriageRelationship.marriages.push(this.marriage);

          this.person.ancestorOrHusbandRelationship.push(marriageRelationship);
        }
        // Creating wife
        else {
          var marriageRelationship = this.generateRelationship(this.sourcePerson.id, this.person.id, RelationshipTypesEnum.isInMarriageWith);

          // Insert informations about marriage
          if (this.marriage.marriageAddress != '' || this.marriage.marriageDate != null)
            marriageRelationship.marriages.push(this.marriage);

          this.person.descendantOrWifeRelationship.push(marriageRelationship);
        }


        // Marriage is happening on the top of FamilyTree -> need to change startPerson
        if ((this.familytree.startPersonId != this.sourcePerson.id) && this.personService.isPersonTopPersonOrMarriedToIt(this.sourcePerson, this.familytree, this.allRelationships)) {
          this.familytree.startPersonId = this.sourcePerson.id;
          this.updateFamilyTree(this.familytree);
          return;
        }

        break;

      default:
        throw new Error("Invalid relationship type");

    }

    this.finishAddingPersonToFamilyTree(this.person);
  }

  // Saves undefined person to database 
  createUndefinedPerson(person: Person, creatingDescendant: boolean) {
    this.personService.createPerson(person).subscribe(undefinedPerson => {
      if (undefinedPerson) {
        if (creatingDescendant) {
          this.selectedSpouseId = undefinedPerson.id;
          this.createDescendantRelationship();
        }
        else {
          this.createMotherOrFatherRelationship(undefinedPerson);
        }

      }
    });
  }

  // Creates relationships descendant -> ancestors
  createMotherOrFatherRelationship(undefinedPerson: Person): void {

    if (undefinedPerson.isFemale) {
      var ancestorRelationship = this.generateRelationship(this.person.id, this.sourcePerson.id, RelationshipTypesEnum.isFatherOf);
      var marriageRelationship = this.generateRelationship(this.person.id, undefinedPerson.id, RelationshipTypesEnum.isInMarriageWith);
      this.person.ancestorOrHusbandRelationship.push(marriageRelationship);
    }
    else {
      var ancestorRelationship = this.generateRelationship(this.person.id, this.sourcePerson.id, RelationshipTypesEnum.isMotherOf);
      var marriageRelationship = this.generateRelationship(undefinedPerson.id, this.person.id, RelationshipTypesEnum.isInMarriageWith);
      this.person.descendantOrWifeRelationship.push(marriageRelationship);
    }

    this.person.ancestorOrHusbandRelationship.push(ancestorRelationship);

    this.finishAddingPersonToFamilyTree(this.person);
  }

  // Creates relationships ancestors -> descendant
  createDescendantRelationship(): void {
    var motherRelationship = this.generateRelationship((this.sourcePerson.isFemale) ? this.sourcePerson.id : this.selectedSpouseId, this.person.id, RelationshipTypesEnum.isMotherOf);
    var fatherRelationship = this.generateRelationship((this.sourcePerson.isFemale) ? this.selectedSpouseId : this.sourcePerson.id, this.person.id, RelationshipTypesEnum.isFatherOf);

    this.person.descendantOrWifeRelationship.push(motherRelationship, fatherRelationship);

    this.finishAddingPersonToFamilyTree(this.person);
  }

  // Finaly creates person and closes the modal window
  finishAddingPersonToFamilyTree(person: Person): void {
    // It is the whole new person
    if (person.id == null) {

      // Get birth records
      var birthRecord = this.personService.buildBirthRecord(person);

      this.birthRecordService.getBirthRecordIds(birthRecord).subscribe(ids => {
        if (ids) {

          // There are 1-5 ids
          if (ids.length > 0 && ids.length < 6) {

            person.originalRecords = [];
            // Add originalIds to Person
            ids.forEach(currentId => person.originalRecords.push({ id: null, recordId: currentId, personId: null }));
          }
          this.createPerson(person);
        };
      })


    }
    else if (this.personFromExistingRecords && (person.id == this.personFromExistingRecords.id)) {
      person.familyTreePerson.push({ familyTreeId: this.familytree.id, familyTree: null, personId: person.id, person: null });

      this.personFromExistingRecords = null;
      this.updatePerson(person);
    }
    else {
      this.clearAll();
      this.dialogRef.close({ refresh: true, startPersonId: this.familytree.startPersonId });
    }

  }

  // Creates Relationship based on arguments
  private generateRelationship(ancestorOrHusbandPersonId: number, descendantOrWifePersonId: number, type: RelationshipTypesEnum): Relationship {
    var relationship = this.initRelationship();
    relationship.type = type;
    relationship.ancestorOrHusbandPersonId = ancestorOrHusbandPersonId;
    relationship.descendantOrWifePersonId = descendantOrWifePersonId;
    relationship.familyTreeRelationship[relationship.familyTreeRelationship.length - 1].familyTreeId = this.familytree.id;
    return relationship;
  }

  // -------------------- Helper methods -------------------- //

  // If user selected Wife or Husband, all his spouses are needed to be found to choose ancestor of child later
  prepareWifesOrHusbands(person: Person) {
    if (this.selectedRelationship == selectedRelationshipEnum.descendant) {
      // Prepares potencional mothers (for wife) or fathers (for husband)
      this.sourcePersonSpouses = this.personService.getAllSpouses(person);
    }
  }

  // Closes the modal window
  close(): void {
    this.clearAll();
    this.dialogRef.close({ refresh: false });
  }

  // Callback from person-create.component
  formValid(isValid: boolean) {
    this.isFormValid = isValid;
  }

  callSimiliarPersonSearch(): void {
    // this will emit the detector change in person-select-similiar component
    if (this.isFormValid)
      this.person = { ...this.person };
  }

  // Callback from person-select-similiar.component
  newSelectedExistingPerson(person: Person) {
    this.personFromExistingRecords = person;
  }

  // Transform date to czech format
  transformDate(dateString: string) {

    var date = this.personService.dateFormatTransformation(dateString);

    if (date)
      this.marriage.marriageDate = date;

  }

  // -------------------- Validation methods for adding new relationship to familytree -------------------- //

  // Get person's father (implemented here because acces from html is needed)
  getFather(person: Person): number {
    return this.personService.getFather(person);
  }

  // Get person's mother (implemented here because acces from html is needed)
  getMother(person: Person): number {
    return this.personService.getMother(person);
  }

  // Finds if source person already has undefined spouse
  sourcePersonHasUndefinedSpouse(): boolean {
    return this.sourcePersonSpouses.some(s => s.isUndefined == true)
  }

  // If relationship is type: Mother, Father, Husband or Wife, the gender of new person must be set (user cannot choose)
  setGender(selected: number) {

    if (selected == selectedRelationshipEnum.mother
      || selected == selectedRelationshipEnum.wife) {
      this.person.isFemale = true;
    }
    else if (selected == selectedRelationshipEnum.father
      || selected == selectedRelationshipEnum.husband) {
      this.person.isFemale = false;
    }
  }

  // -------------------- Initializations -------------------- //

  // Initialize undefined person
  private initUndefinedPerson(isFemale: boolean): Person {
    var person = this.initPerson();
    person.personNames = [];
    person.isFemale = isFemale;
    person.isPrivate = true;
    person.isUndefined = true;
    return person;
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
      familyTreePerson: [
        {
          familyTreeId: this.familytree.id,
          familyTree: null,
          personId: null,
          person: null
        }
      ]
    }
    return person;
  }

  // Initialize Relationship
  private initRelationship(): Relationship {
    var relationship: Relationship = {
      id: null,
      type: null,
      marriages: [],
      ancestorOrHusbandPersonId: null,
      ancestorOrHusbandPerson: null,
      descendantOrWifePersonId: null,
      descendantOrWifePerson: null,
      familyTreeRelationship: [
        {
          familyTreeId: null,
          familyTree: null,
          relationshipId: null,
          relationship: null
        }
      ],
      collisionRelationship: null
    }
    return relationship;
  }

  // Initialize Marriage
  private initMarriage(): Marriage {
    var relationship: Marriage = {
      id: null,
      marriageAddress: '',
      marriageDate: null,
      relationship: null,
      relationshipId: null
    }
    return relationship;
  }

  // Reset all properties
  private clearAll(): void {
    this.sourcePerson = null;
    this.person = this.initPerson();
    this.sourcePersonSpouses = null;
    this.selectedSpouseId = null;
  }
}