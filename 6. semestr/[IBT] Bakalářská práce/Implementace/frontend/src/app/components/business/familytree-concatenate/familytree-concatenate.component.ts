/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    familytree-concatenate.component.ts                   */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { Router } from '@angular/router';
import { FamilyTree } from 'src/app/models/familytree';
import { FamilyTreeService } from 'src/app/services/familytree.service';
import { AuthenticationService } from 'src/app/services/http/authentication.service';

@Component({
  selector: 'app-familytree-concatenate',
  templateUrl: './familytree-concatenate.component.html',
  styleUrls: ['./familytree-concatenate.component.scss']
})
export class FamilytreeConcatenateComponent implements OnInit {

  srcFamilyTreeId: number;
  dstFamilyTreeId: number = 0;
  validFamilyTrees: FamilyTree[];
  clicked: boolean;
  returned: boolean = false;


  constructor(
    @Inject(MAT_DIALOG_DATA) public data: any,
    public dialogRef: MatDialogRef<FamilytreeConcatenateComponent>,
    private familyTreeService: FamilyTreeService,
    private authenticationService: AuthenticationService,
    public router: Router
  ) { }

  ngOnInit(): void {
    this.srcFamilyTreeId = this.data.familytreeId;
    this.getValidFamilyTrees();
  }

  // Gets familytrees, that can be concatenated with
  getValidFamilyTrees() {
    var userId = this.authenticationService.getHash('auth', 'id');
    this.familyTreeService.getValidFamilyTreesForConcatenation(this.srcFamilyTreeId, userId).subscribe(familyTrees => {
      if (familyTrees) {
        this.validFamilyTrees = familyTrees;
        this.returned = true;
      }
    });
  }

  // Provides concatenating
  concatenate() {
    this.familyTreeService.concatenateFamilyTrees(this.srcFamilyTreeId, this.dstFamilyTreeId).subscribe(familytree => {
      if (familytree) {
        this.dialogRef.close();
        var returnUrl = 'familytree/' + this.dstFamilyTreeId;
        this.redirectTo(returnUrl);
      }
    });
  }

  // Method provides redirecting to the target familytree
  redirectTo(returnUrl: string) {
    this.router.navigateByUrl('/', { skipLocationChange: true }).then(() =>
      this.router.navigate([returnUrl]));
  }
}
