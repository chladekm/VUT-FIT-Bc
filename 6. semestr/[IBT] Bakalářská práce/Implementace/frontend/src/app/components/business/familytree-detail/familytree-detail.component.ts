/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    familytree-detail.component.ts                        */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { FamilyTree } from 'src/app/models/familytree';
import { FamilyTreeService } from '../../../services/familytree.service';

@Component({
  selector: 'app-familytree-detail',
  templateUrl: './familytree-detail.component.html',
  styleUrls: ['./familytree-detail.component.scss']
})
export class FamilytreeDetailComponent implements OnInit {

  familytree: FamilyTree;

  constructor(
    @Inject(MAT_DIALOG_DATA) public data: any,
    public dialogRef: MatDialogRef<FamilytreeDetailComponent>,
    private familyTreeService: FamilyTreeService) { }

  ngOnInit() {
    this.familytree = this.familyTreeService.copyObject(this.data.familytree);
  }

  // Closes dialog window
  cancel(): void {
    this.dialogRef.close(null);
  }

  // Saves changes in FamilyTree
  save(): void {
    this.familyTreeService.updateFamilyTree(this.familytree)
      .subscribe(familyTree => this.familytree = familyTree);

    this.dialogRef.close(this.familytree)
  }

}
