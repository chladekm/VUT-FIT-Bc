/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    person-detail.component.ts                            */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Component, EventEmitter, Input, OnInit, Output, SimpleChanges } from '@angular/core';
import { MatDialog, MatDialogConfig, MatDialogRef } from '@angular/material';
import { PersonName } from 'src/app/models/personName';
import { RelationshipTypesEnum } from 'src/app/models/relationship';
import { SelectedPerson } from 'src/app/models/rendering/selected-person';
import { Person } from '../../../models/person';
import { PersonService } from '../../../services/person.service';
import { PersonDetailMarriageComponent } from '../person-detail-marriage/person-detail-marriage.component';
import { PersonEditComponent } from '../person-edit/person-edit.component';
import { OriginalRecordService } from 'src/app/services/originalrecord.service';
import { BirthRecordService } from 'src/app/services/birthRecords/birth-record.service';

export interface MarriageIdentification {
  name: string,
  id: number
}

@Component({
  selector: 'app-person-detail',
  templateUrl: './person-detail.component.html',
  styleUrls: ['./person-detail.component.scss']
})
export class PersonDetailComponent implements OnInit {

  @Input('init')
  selectedPerson: SelectedPerson;
  @Input('treeId')
  familyTreeId: number;
  person: Person;
  mothersNames: PersonName[];
  fathersNames: PersonName[];
  marriages: MarriageIdentification[] = [];
  referenceTreeIds: number[];
  birthRecordDatabaseSearchUrl : string;

  @Output() refreshView = new EventEmitter();

  marriageDetailDialogRef: MatDialogRef<PersonDetailMarriageComponent>;
  personEditDialogRef: MatDialogRef<PersonEditComponent>;


  constructor(
    private personService: PersonService,
    private originalRecordService: OriginalRecordService,
    private birthRecordService: BirthRecordService,
    private dialog: MatDialog
  ) { }

  ngOnInit() {
    // Some person was selected or selectedPerson changed
    if (this.selectedPerson || ((this.person) && (this.person.id != this.selectedPerson.id))) {
      this.person = this.selectedPerson.data;
    }
  }

  ngOnChanges(changes: SimpleChanges) {
    if (changes.selectedPerson.previousValue != changes.selectedPerson.currentValue) {
      this.person = this.selectedPerson.data;
      this.mothersNames = null;
      this.fathersNames = null;
      this.initParents();
      this.initMarriages();
      this.initReferencesOnTrees(this.person.id, this.familyTreeId);
      this.initReferencesOnOriginalRecords(this.person.id);
    }
  }

  // Find parents for selected person
  initParents(): void {
    var motherRelationship = this.person.descendantOrWifeRelationship.find(rel => rel.type == RelationshipTypesEnum.isMotherOf);
    if (motherRelationship)
      this.mothersNames = motherRelationship.ancestorOrHusbandPerson.personNames;

    var fatherRelationship = this.person.descendantOrWifeRelationship.find(rel => rel.type == RelationshipTypesEnum.isFatherOf);
    if (fatherRelationship)
      this.fathersNames = fatherRelationship.ancestorOrHusbandPerson.personNames;
  }

  // Find all marriages for selected person
  initMarriages(): void {
    this.marriages = [];

    if (this.person.isFemale) {
      var marriages = this.person.descendantOrWifeRelationship.filter(rel => rel.type == RelationshipTypesEnum.isInMarriageWith && (!rel.ancestorOrHusbandPerson.isUndefined));
    }
    else {
      var marriages = this.person.ancestorOrHusbandRelationship.filter(rel => rel.type == RelationshipTypesEnum.isInMarriageWith && (!rel.descendantOrWifePerson.isUndefined));
    }

    var allNames: string;

    marriages.forEach(marriage => {

      allNames = "";

      if (this.person.isFemale)
        var sortedNames = this.personService.sortNamesByType(marriage.ancestorOrHusbandPerson.personNames);
      else
        var sortedNames = this.personService.sortNamesByType(marriage.descendantOrWifePerson.personNames);

      sortedNames.forEach(name => {
        allNames += name.name + " ";
      })

      this.marriages.push({ "name": allNames, "id": marriage.id })
    });
  }

  // Get references on public familytrees where person exists
  initReferencesOnTrees(personId: number, familyTreeId: number): void {
    this.personService.getPublicTreesForPerson(personId, familyTreeId).subscribe(ids => this.referenceTreeIds = ids);
  }

  // Get original records for person
  initReferencesOnOriginalRecords(personId: number): void {
    this.originalRecordService.getOriginalRecordsByPersonId(personId).subscribe(ids => {
      if (ids) {
        if(ids.length > 0)
          this.person.originalRecords = ids;
        else
          this.birthRecordDatabaseSearchUrl = this.getUrlForRecordSearch();
      }
    });
  }

  // Method gets url with parameters for search in original database
  getUrlForRecordSearch() {

    var birthRecord = this.personService.buildBirthRecord(this.person);
    return this.birthRecordService.getUrlForPersonSearch(birthRecord);
  }

  // Opens dialog window with Marriage details
  showMarriageDetail(marriage: MarriageIdentification): void {
    const dialogConfig = new MatDialogConfig();
    dialogConfig.disableClose = true,
      dialogConfig.data = {
        marriage: marriage,
        familyTreeId: this.familyTreeId
      }

    this.marriageDetailDialogRef = this.dialog.open(PersonDetailMarriageComponent, dialogConfig);

    this.marriageDetailDialogRef.afterClosed().subscribe(newRelationshipId => {
      marriage.id = newRelationshipId;
    });
  }

  // Opens dialog window to edit persons credentials
  editPerson(): void {
    const dialogConfig = new MatDialogConfig();
    dialogConfig.disableClose = true;
    dialogConfig.data = {
      person: this.person,
      familyTreeId: this.familyTreeId

    }

    this.personEditDialogRef = this.dialog.open(PersonEditComponent, dialogConfig);

    this.personEditDialogRef.afterClosed().subscribe(person => {
      if (person) {
        this.refreshView.emit();
        this.person = person;
        this.selectedPerson.name = "";
        this.initReferencesOnOriginalRecords(person.id);
        this.personService.sortNamesByType(this.person.personNames).forEach(name => {
          this.selectedPerson.name += (name.name + " ");
        });
      }
    });
  }
}
