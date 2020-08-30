/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    collision-detail.component.ts                         */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { MatDialog, MatDialogConfig, MatDialogRef } from '@angular/material';
import { Collision, CollisionTypesEnum } from 'src/app/models/collision';
import { FamilyTree, FamilyTreeTypesEnum } from 'src/app/models/familytree';
import { Person } from 'src/app/models/person';
import { CollisionService } from 'src/app/services/collision.service';
import { PersonService } from 'src/app/services/person.service';
import { CollisionDetailPersonComponent } from '../collision-detail-person/collision-detail-person.component';
import { collisionItem } from '../collision-list/collision-list.component';

@Component({
  selector: 'app-collision-detail',
  templateUrl: './collision-detail.component.html',
  styleUrls: ['./collision-detail.component.scss']
})
export class CollisionDetailComponent implements OnInit {


  @Input('selectedCollision') collisionSummary: collisionItem;
  @Input('collisions') allCollisions: Collision[];
  @Input('familytree') familytree: FamilyTree;

  @Output() refresh = new EventEmitter<boolean>();
  @Output() personInFamilyTreeChanged = new EventEmitter<boolean>();


  collision: Collision;
  alternativePersons: Person[] = [];
  solutionDate: Date = null;
  showSelection: boolean = false;
  collisionSolutionPersonId: number = null;
  personDetailTreeDialogRef: MatDialogRef<CollisionDetailPersonComponent>;

  errorOccured: boolean = false;
  samePersonError: boolean = false;

  constructor(
    private personService: PersonService,
    private collisionService: CollisionService,
    private dialog: MatDialog

  ) { }

  ngOnInit(): void {

    this.collision = this.allCollisions.find(col => col.id == this.collisionSummary.id);
    this.initDetails();

    // Get solution date
    if (this.collisionSummary.solved)
      this.getSolutionDate();
  }

  // Initialize Collision details
  initDetails(): void {

    var personIds: number[] = [];

    if (this.collision.type == CollisionTypesEnum.differentFather || this.collision.type == CollisionTypesEnum.differentMother) {
      this.collision.collisionRelationship.forEach(colRel => personIds.push(colRel.relationship.ancestorOrHusbandPersonId));
    }
    else if (this.collision.type == CollisionTypesEnum.marriageOrAncestor) {
      personIds = [
        this.collision.collisionRelationship[0].relationship.ancestorOrHusbandPersonId,
        this.collision.collisionRelationship[0].relationship.descendantOrWifePersonId
      ]
    }

    // Get all persons and their references to PUBLIC familytrees
    personIds.forEach(id =>
      this.personService.getPersonById(id).subscribe(
        result => {
          result.familyTreePerson = [];
          var references = this.getReferences(result);
          references.forEach(treeId =>
            result.familyTreePerson.push({ familyTreeId: treeId, familyTree: null, personId: id, person: null })
          );

          this.alternativePersons.push(result)
        },
        error => this.errorOccured = true
      )
    );
  }

  // Method returns references on familytrees, where concrete person is in concrete collision
  getReferences(person: Person): number[] {

    var relationshipIds = this.collision.collisionRelationship.map(({ relationshipId }) => relationshipId);
    var familytreeIdArray = [];


    // Find all references to familytree in AncestorOrHusbandRelationship
    person.ancestorOrHusbandRelationship.filter(x => relationshipIds.includes(x.id)).forEach(rel => {

      rel.familyTreeRelationship.forEach(fr => {

        if (fr.familyTree.type == FamilyTreeTypesEnum._public && fr.familyTreeId != this.familytree.id)
          familytreeIdArray.push(fr.familyTreeId);
      });

    });

    // Find all references to familytree in DescendantOrWifeRelationship
    person.descendantOrWifeRelationship.filter(x => relationshipIds.includes(x.id)).forEach(rel => {

      rel.familyTreeRelationship.forEach(fr => {

        if (fr.familyTree.type == FamilyTreeTypesEnum._public && fr.familyTreeId != this.familytree.id)
          familytreeIdArray.push(fr.familyTreeId);
      });

    });

    return familytreeIdArray;
  }

  // Method provides replacing person that was in collision for another one
  SolveCollision(): void {

    // Sollution is different person than the current one 
    if (this.collisionSolutionPersonId != this.collisionSummary.currentPersonId) {

      var oldPersonId = this.collisionSummary.currentPersonId;
      var newPersonId = this.collisionSolutionPersonId;
      var familyTreeId = this.familytree.id;

      this.personService.replacePersonInFamilyTree(oldPersonId, newPersonId, familyTreeId).subscribe(
        person => {
          if (person) {
            this.toggleCollisionSolution(true);
            this.personInFamilyTreeChanged.emit(true);
          }
        },
        error => this.samePersonError = true
      );
    }
    // Sollution is current person
    else {
      this.toggleCollisionSolution(true);
    }

  }

  // Marks collision as solved/not solved
  toggleCollisionSolution(solved: boolean) {
    var reference = this.collision.familyTreeCollision.find(fc => fc.familyTreeId == this.familytree.id);
    reference.isSolved = solved;

    this.collisionService.toggleCollisionSolvedAttribute(reference).subscribe(col => {
      if (col) {
        if (solved == true && this.collision.type != CollisionTypesEnum.marriageOrAncestor) {
          this.close(true);
        }
        else {

          if (solved)
          {
            this.solutionDate = new Date (col.solutionDate);
          }

          this.collisionSummary.solved = col.isSolved;
          this.alternativePersons = [];
          this.initDetails();
        }
      }
    });
  }

  // Opens dialog window with details of person in collision
  openPersonDetails(person: Person) {
    const dialogConfig = new MatDialogConfig();
    dialogConfig.minWidth = "300px",
      dialogConfig.maxWidth = "600px",
      dialogConfig.data = {
        person: person,
        familyTreeId: this.familytree.id
      }

    this.personDetailTreeDialogRef = this.dialog.open(CollisionDetailPersonComponent, dialogConfig);
  }

  // Returns date when was collision solved by user in current familytree
  getSolutionDate() {
    this.solutionDate = new Date (this.collision.familyTreeCollision.find(fc => fc.familyTreeId == this.familytree.id).solutionDate);
    this.solutionDate.setHours(this.solutionDate.getHours() - 2);
  }

  // Closes modal window
  close(refresh: boolean) {
    this.refresh.emit(refresh);
  }
}
