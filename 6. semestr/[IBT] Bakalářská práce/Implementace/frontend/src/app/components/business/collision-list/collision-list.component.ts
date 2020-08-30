/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    collision-list.component.ts                           */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Component, Inject, OnInit, ViewChild } from '@angular/core';
import { MatDialogRef, MatPaginator, MatSort, MatTableDataSource, MAT_DIALOG_DATA } from '@angular/material';
import { Collision, CollisionTypesEnum } from 'src/app/models/collision';
import { FamilyTree } from 'src/app/models/familytree';
import { Relationship } from 'src/app/models/relationship';
import { CollisionService } from 'src/app/services/collision.service';
import { RelationshipService } from 'src/app/services/relationship.service';

export interface collisionItem {
  id: number;
  solved: boolean;
  type: CollisionTypesEnum;
  descendantName: string;
  ancestorName: string;
  currentPersonId: number;
}

@Component({
  selector: 'app-collision-list',
  templateUrl: './collision-list.component.html',
  styleUrls: ['./collision-list.component.scss']
})
export class CollisionListComponent implements OnInit {

  familytree: FamilyTree;
  relationships: Relationship[];
  collisions: Collision[];
  collisionRows: collisionItem[] = [];

  selectedCollision: collisionItem;
  refreshFamilyTreeViewAfterClose: boolean = false;

  displayedColumns: string[] = ['solved', 'type', 'descendantName', 'ancestorName', ' '];
  dataSource: MatTableDataSource<collisionItem>;

  @ViewChild(MatPaginator, { static: true }) paginator: MatPaginator;
  @ViewChild(MatSort, { static: true }) sort: MatSort;

  constructor(
    @Inject(MAT_DIALOG_DATA) public data: any,
    private collisionService: CollisionService,
    private relationshipService: RelationshipService,
    public dialogRef: MatDialogRef<CollisionListComponent>,
  ) { }


  ngOnInit(): void {
    this.familytree = this.data.familytree;
    this.relationships = this.data.relationships;

    this.getCollisions();
  }

  // Gets collisions from database
  public getCollisions() {
    this.collisionService.getCollisionsByFamilyTreeId(this.familytree.id).subscribe(col => {
      if (col) {
        this.collisions = col;
        this.setCollisionRows();
      }
    });
  }

  // Processes collisions to format that will be displayed
  public setCollisionRows() {
    this.collisions.forEach(item => {

      var row: Relationship = null;

      // Find relationship that causes collision in this familytree
      item.collisionRelationship.forEach(rel => {

        var currentRelationship = this.relationships.find(relationship => relationship.id == rel.relationshipId);
        if (currentRelationship != undefined) {
          row = currentRelationship;
        }
      });

      // Check if collision is already solved 
      if (item.familyTreeCollision.find(x => x.familyTreeId == this.familytree.id && x.isSolved == true))
        var isSolved = true;
      else
        var isSolved = false;


      var descendantName = "";
      var ancestorName = "";

      row.descendantOrWifePerson.personNames.forEach(name => { descendantName += (name.name + " ") });
      row.ancestorOrHusbandPerson.personNames.forEach(name => { ancestorName += (name.name + " ") });

      this.collisionRows.push(
        {
          id: item.id,
          solved: isSolved,
          type: item.type,
          descendantName: descendantName,
          ancestorName: ancestorName,
          currentPersonId: row.ancestorOrHusbandPersonId
        }
      );
    });

    this.initTable();
  }

  // Initializes table for list of collisions
  initTable() {
    this.dataSource = new MatTableDataSource(this.collisionRows);
    this.dataSource.paginator = this.paginator;
    this.dataSource.sort = this.sort;
  }

  // Closes detail of collision
  closeDetail(refresh: boolean) {
    this.selectedCollision = null;
    if (refresh) {
      this.collisionRows = [];
      this.getRelationshipsByFamilyTreeId(this.familytree.id);
    }
  }

  // Sets argument if familytree needs to be rendered after close
  refreshViewAfterClose(refresh: boolean) {
    this.refreshFamilyTreeViewAfterClose = refresh;
  }

  // Closes modal window
  close() {
    this.dialogRef.close(this.refreshFamilyTreeViewAfterClose);
  }

  // Gets all relationships in familytree
  getRelationshipsByFamilyTreeId(id: number): void {
    this.relationshipService.getRelationshipsByFamilyTreeId(id).subscribe((relationships) => {
      if (relationships) {
        this.relationships = relationships;
        this.getCollisions();
      }
    });
  }
}