/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    person-create.component.ts                            */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { ChangeDetectorRef, Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { Person } from 'src/app/models/person';
import { BaseService } from 'src/app/services/base.service';

@Component({
  selector: 'app-person-create',
  templateUrl: './person-create.component.html',
  styleUrls: ['./person-create.component.scss']
})
export class PersonCreateComponent implements OnInit {

  @Input()
  person: Person;

  @Input('selectedRelationship')
  selectedRelationship: number;

  startDate = new Date(1900, 0, 1);

  areDatesValid: boolean = true;
  isFormValid: boolean = false;
  @Output() formValid = new EventEmitter<boolean>();

  constructor(
    protected cdr: ChangeDetectorRef,
    private baseService: BaseService) { }

  ngOnInit(): void {
  }

  // Callback if form is valid
  checkForm(value: boolean) {
    this.formValid.emit(value);
  }

  // Transform date to czech format
  transformDate(dateString: string, type: number) {

    var date = this.baseService.dateFormatTransformation(dateString);

    if (date)
      this.saveDate(date, type);

    this.checkDatesChronology();
  }

  // Method provides validation that dates are in chronological order
  checkDatesChronology() {
    if (this.baseService.datesChronologyValidation(this.person.birthDate, this.person.baptismDate, this.person.deathDate)) {
      // Chronologically Valid
      this.areDatesValid = true;
      if (this.person.personNames[0].name != "" && this.person.personNames[1].name != "")
        this.checkForm(true);
    }
    else {
      // Invalid
      this.areDatesValid = false;
      this.checkForm(false);
    }
  }

  // Saves date by type 
  saveDate(date: Date, type: number) {
    if (type == 0)
      this.person.birthDate = date;
    else if (type == 1)
      this.person.baptismDate = date;
    else if (type == 2)
      this.person.deathDate = date;
  }

}
