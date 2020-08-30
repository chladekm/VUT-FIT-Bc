/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    user-familytrees.component.ts                         */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Component, Input, OnInit } from '@angular/core';
import { MatDialog, MatDialogConfig, MatDialogRef } from '@angular/material';
import { FamilyTree } from 'src/app/models/familytree';
import { Person } from 'src/app/models/person';
import { User } from 'src/app/models/user';
import { FamilyTreeService } from 'src/app/services/familytree.service';
import { YesNoDialogComponent } from '../../ui/yes-no-dialog/yes-no-dialog.component';
import { FamilytreeCreateComponent } from '../familytree-create/familytree-create.component';
import { FamilytreeDetailComponent } from '../familytree-detail/familytree-detail.component';
import { FamilyTreeCollision } from 'src/app/models/relationships/familyTreeCollisions';

@Component({
  selector: 'app-user-familytrees',
  templateUrl: './user-familytrees.component.html',
  styleUrls: ['./user-familytrees.component.scss']
})
export class UserFamilytreesComponent implements OnInit {

  @Input()
  user: User;

  person: Person;

  showEditRow: boolean = false;
  isFormValid: boolean = false;

  editFamilyTreeDialogRef: MatDialogRef<FamilytreeDetailComponent>;
  deleteFamilyTreeDialogRef: MatDialogRef<YesNoDialogComponent>;


  constructor(
    private dialog: MatDialog,
    private familytreeService: FamilyTreeService
  ) { }

  ngOnInit(): void {
  }

  addNewFamilyTree(): void {
    const dialogConfig = new MatDialogConfig();
    dialogConfig.minWidth = "60%";
    dialogConfig.maxWidth = "100%";
    this.dialog.open(FamilytreeCreateComponent, dialogConfig);
  }

  // Modal Window: Edit informations about familytree
  editFamilyTree(tree: FamilyTree) {

    const dialogConfig = new MatDialogConfig();
    dialogConfig.disableClose = true,
      dialogConfig.minWidth = "300px",
      dialogConfig.data = {
        familytree: tree
      }

    this.editFamilyTreeDialogRef = this.dialog.open(FamilytreeDetailComponent, dialogConfig);

    this.editFamilyTreeDialogRef.afterClosed().subscribe(data => {
      if (data) {
        tree.title = data.title;
        tree.type = data.type;
      }
    });
  }

  // Provides deleting of familytree
  deleteFamilyTree(familytree: FamilyTree) {
    this.familytreeService

    const dialogConfig = new MatDialogConfig();
    dialogConfig.disableClose = false,

      // Open deciding modal window
      this.deleteFamilyTreeDialogRef = this.dialog.open(YesNoDialogComponent, dialogConfig);

    this.deleteFamilyTreeDialogRef.afterClosed().subscribe(positive => {
      if (positive == true) {
        this.familytreeService.deleteFamilyTree(familytree.id).subscribe(tree => {
          if (tree) {
            const index: number = this.user.familyTrees.indexOf(familytree);
            if (index !== -1) {
              this.user.familyTrees.splice(index, 1);
            }
            else { }
          }
        });


      }
    });
  }

  // Counts unsolved collusions for the view
  getCountOfUnsolvedCollisions(references : FamilyTreeCollision[]) : number
  {
    return references.filter(x => !x.isSolved).length;
  }

  // Callback from person-create.component if form is valid 
  formValid(isValid: boolean) {
    this.isFormValid = isValid;
  }

}