/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    person-detail-marriage.component.ts                   */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { Marriage } from 'src/app/models/marriage';
import { MarriageService } from 'src/app/services/marriage.service';

@Component({
  selector: 'app-person-detail-marriage',
  templateUrl: './person-detail-marriage.component.html',
  styleUrls: ['./person-detail-marriage.component.scss']
})
export class PersonDetailMarriageComponent implements OnInit {

  constructor(
    private dialogRef: MatDialogRef<PersonDetailMarriageComponent>,
    private marriageService: MarriageService,
    @Inject(MAT_DIALOG_DATA) public data: any
  ) { }

  spouseName: string;
  relationshipId: number;
  familyTreeId: number;

  marriages: Marriage[] = [];
  newMarriage: Marriage = null;

  marriageStartDate = new Date(1900, 0, 1);

  ngOnInit(): void {
    this.spouseName = this.data.marriage.name;
    this.relationshipId = this.data.marriage.id;
    this.familyTreeId = this.data.familyTreeId;
    this.getMarriages(this.relationshipId);
  }

  // Gets all marriages for current relationship
  getMarriages(relId: number): void {
    this.marriageService.getMarriagesByRelationshipId(relId).subscribe(marriages => this.marriages = marriages);
  }

  // In UI will be shown form for new marriage
  showAddingMarriage(): void {
    this.newMarriage = this.initMarriage();
  }

  // Provides creating of the marriage
  createNewMarriage(marriage: Marriage): void {
    this.marriageService.createMarriage(marriage, this.familyTreeId).subscribe(createdMarriage => {
      if (createdMarriage) {
        this.relationshipId = createdMarriage.relationshipId;
        this.marriages.push(createdMarriage);
        this.newMarriage = null;
      }
    })
  }

  // Closes modal window
  close(): void {
    this.dialogRef.close(this.relationshipId);
  }

  // Transform date to czech format
  transformDate(dateString: string) {

    var date = this.marriageService.dateFormatTransformation(dateString);

    if (date)
      this.newMarriage.marriageDate = date;
  }
  
  // Initialize new marriage
  public initMarriage(): Marriage {
    var marriage: Marriage = {
      id: null,
      marriageAddress: '',
      marriageDate: null,
      relationship: null,
      relationshipId: this.relationshipId
    }
    return marriage;
  }
}
