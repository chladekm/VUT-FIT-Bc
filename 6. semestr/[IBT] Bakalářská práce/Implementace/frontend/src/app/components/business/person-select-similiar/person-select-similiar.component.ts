/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    person-select-similiar.component.ts                   */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { animate, state, style, transition, trigger } from '@angular/animations';
import { Component, EventEmitter, Input, OnChanges, Output, SimpleChanges, ViewChild } from '@angular/core';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { MatTableDataSource } from '@angular/material/table';
import { Person } from 'src/app/models/person';
import { PersonService } from 'src/app/services/person.service';



@Component({
  selector: 'app-person-select-similiar',
  templateUrl: './person-select-similiar.component.html',
  styleUrls: ['./person-select-similiar.component.scss'],
  animations: [
    trigger('detailExpand', [
      state('collapsed, void', style({ height: '0px', minHeight: '0', visibility: "hidden" })),
      state('expanded', style({ height: '*' })),
      transition('expanded <=> collapsed', animate('225ms cubic-bezier(0.4, 0.0, 0.2, 1)')),
      transition('expanded <=> void', animate('225ms cubic-bezier(0.4, 0.0, 0.2, 1)'))
    ])
  ],
})
export class PersonSelectSimiliarComponent implements OnChanges {
  displayedColumns: string[] = [' ', 'name', 'surname', 'birthPlace', 'birthDate', 'baptismDate', 'deathDate'];
  dataSource: MatTableDataSource<Person>;
  expandedElement: Person | null;

  @Input('person')
  person: Person;

  selectedExistingPerson: Person;

  @Output() existingPersonChange = new EventEmitter<Person>();

  similiarPersons: Person[];

  @ViewChild(MatPaginator, { static: true }) paginator: MatPaginator;
  @ViewChild(MatSort, { static: true }) sort: MatSort;

  constructor(
    private personService: PersonService
  ) { }

  ngOnInit() {
    this.dataSource = new MatTableDataSource(this.similiarPersons);
    this.dataSource.paginator = this.paginator;
    this.dataSource.sort = this.sort;
    this.selectedExistingPerson = null;
  }

  ngOnChanges(changes: SimpleChanges) {

    // First change is initialization
    if (!(changes.person.firstChange)) {
      if (changes.person.previousValue != changes.person.currentValue) {
        this.selectedExistingPerson = null;
        this.getSimiliarPersons();
      }
    }
  }

  //  Gets similiar persons from database
  getSimiliarPersons() {

    this.personService.getSimiliarPersons(this.person).subscribe(persons => {
      this.similiarPersons = persons;
      this.dataSource = new MatTableDataSource(this.similiarPersons);
      this.dataSource.paginator = this.paginator;
      this.dataSource.sort = this.sort;

    },
      error => {
        console.error(error);
      }
    )
  }

  // Will emit callback in parent component (person was selected)
  newSelectedExistingPerson(person: Person) {
    this.existingPersonChange.emit(person);
  }
}

