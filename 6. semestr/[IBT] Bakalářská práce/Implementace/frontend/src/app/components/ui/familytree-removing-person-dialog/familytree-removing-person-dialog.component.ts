/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    familytree-removing-person-dialog.component.ts        */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Component, OnInit, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material';

@Component({
  selector: 'app-familytree-removing-person-dialog',
  templateUrl: './familytree-removing-person-dialog.component.html',
  styleUrls: ['./familytree-removing-person-dialog.component.scss']
})
export class FamilytreeRemovingPersonDialogComponent implements OnInit {

  canDelete: boolean;
  message: string;

  constructor(
    @Inject(MAT_DIALOG_DATA) public data: any,
    private dialogRef: MatDialogRef<FamilytreeRemovingPersonDialogComponent>,
  ) { }

  ngOnInit(): void {
    this.canDelete = this.data.canDelete;
    this.message = this.data.message;
  }

  public close(willDelete: boolean){
    this.dialogRef.close(willDelete);
  }

}
