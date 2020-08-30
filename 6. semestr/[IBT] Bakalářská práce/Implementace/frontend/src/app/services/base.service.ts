/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    base.service.ts                                       */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Injectable } from '@angular/core';
import { FamilyTree } from '../models/familytree';
import { Person } from '../models/person';
import { PersonName } from '../models/personName';
import { Relationship, RelationshipTypesEnum } from '../models/relationship';
import { QueryService } from './http/query.service';
import { BirthRecord } from '../models/birthRecords/birth-record';


@Injectable({
  providedIn: 'root'
})

export abstract class BaseService {
  constructor(protected queryService: QueryService) { }

  // Get person's father
  getFather(person: Person): number {
    const father = person.descendantOrWifeRelationship.find(rel => rel.type == RelationshipTypesEnum.isFatherOf);
    return (father) ? father.ancestorOrHusbandPersonId : null;
  }

  // Get person's mother
  getMother(person: Person): number {
    const mother = person.descendantOrWifeRelationship.find(rel => rel.type == RelationshipTypesEnum.isMotherOf);
    return (mother) ? mother.ancestorOrHusbandPersonId : null;
  }

  // Returns Person with all relationships (in current familytree)
  getPersonDataFromFamilyTreeRelationships(id: number, relationships: Relationship[]): Person {

    var baseRelationship = relationships.find(rel => (rel.ancestorOrHusbandPersonId == id || rel.descendantOrWifePersonId == id));

    if (baseRelationship == null)
      return null;

    var person: Person;
    if (baseRelationship.ancestorOrHusbandPersonId == id)
      person = baseRelationship.ancestorOrHusbandPerson;
    else
      person = baseRelationship.descendantOrWifePerson;

    person.ancestorOrHusbandRelationship = [];
    person.descendantOrWifeRelationship = [];

    relationships.forEach(rel => {
      if (rel.ancestorOrHusbandPersonId == id) {
        person.ancestorOrHusbandRelationship.push(this.copyObject(rel));
      }
      else if (rel.descendantOrWifePersonId == id) {
        person.descendantOrWifeRelationship.push(this.copyObject(rel));
      }
    });

    return person;
  }

  // Sorts names by types
  sortNamesByType(personNames: PersonName[]) {
    personNames.sort((a, b) => {
      return (a.isFirstName === b.isFirstName) ? 0 : a.isFirstName ? -1 : 1;
    });

    return personNames;
  }

  // If Person is ancestor (true) or just marrried with someone (false)
  belongPersonToTribe(person: Person, familytree: FamilyTree, allRelationships: Relationship[]): boolean {
    if (this.isPersonTopPersonOrMarriedToIt(person, familytree, allRelationships) || this.personHasMotherOrFatherInFamilyTree(person))
      return true;
  }

  // Check if person is on the top of familytree 
  isPersonTopPersonOrMarriedToIt(person: Person, familytree: FamilyTree, allRelationships: Relationship[]): boolean {
    if (person.id == familytree.startPersonId)
      return true;

    // Pokud top person má pouze jedno manzelstvi
    const topPerson = this.getPersonDataFromFamilyTreeRelationships(familytree.startPersonId, allRelationships);
    var spouses = this.getAllSpouses(topPerson);

    // person is married to topPerson and is the only partner
    if ((spouses.some(spouse => spouse.id == person.id)) && (spouses.length <= 1))
      return true;

    return false;
  }

  // Function checks if person has any direct ancestor
  personHasMotherOrFatherInFamilyTree(person: Person): boolean {
    if (this.getMother(person) != null || this.getFather(person) != null)
      return true;
    else
      return false;
  }

  // Function returns all spouses of passed person
  getAllSpouses(person: Person): Person[] {
    if (person.isFemale)
      return person.descendantOrWifeRelationship.filter(rel => rel.type == RelationshipTypesEnum.isInMarriageWith).map(rel => rel.ancestorOrHusbandPerson);
    else
      return person.ancestorOrHusbandRelationship.filter(rel => rel.type == RelationshipTypesEnum.isInMarriageWith).map(rel => rel.descendantOrWifePerson);
  }

  // Function returns all descendants of passed person
  getAllDescendants(person: Person): Person[] {
    return person.ancestorOrHusbandRelationship.filter(rel => ((rel.type == RelationshipTypesEnum.isMotherOf) || (rel.type == RelationshipTypesEnum.isFatherOf))).map(rel => rel.descendantOrWifePerson);
  }

  // Function provides date transformation to czech format
  dateFormatTransformation(dateString: string): Date {

    var ddMMyyyy = dateString.toString().split(".");

    if (ddMMyyyy != null && ddMMyyyy.length == 3) {

      // Take only four digits
      if (ddMMyyyy[2].trim().length > 4)
        ddMMyyyy[2] = ddMMyyyy[2].trim().substring(0, 4);

      if (ddMMyyyy[2].trim().length == 4 && +ddMMyyyy[1] < 13 && +ddMMyyyy[0] <= 32) {

        var date = new Date(ddMMyyyy[2] + "-" + ddMMyyyy[1] + "-" + ddMMyyyy[0]);
        var currentDate = new Date;

        // compare with actual DateTime
        if (date < currentDate)
          return date;
      }
    }

    return null;
  }

  // Checks if dates are chronologically correct
  datesChronologyValidation(birthDate: Date, baptismDate: Date, deathDate: Date): boolean {

    if (((birthDate && baptismDate) && (baptismDate < birthDate)) ||
      ((baptismDate && deathDate) && (deathDate < baptismDate)) ||
      ((deathDate && birthDate) && (deathDate < birthDate))) {
      return false;
    }
    else {
      return true;
    }
  }

  // Method builds object of birthRecord 
  buildBirthRecord(person: Person): BirthRecord {
    var name = (person.personNames.find(name => name.isFirstName == true)).name;

    var allSurnames = (person.personNames.filter(name => name.isFirstName == false));
    var surname = "";

    allSurnames.forEach(name => surname += name.name + " ");

    // Remove space after last sruname
    var surname = surname.slice(0, -1);

    var birthRecord: BirthRecord = {
      name: name,
      surname: surname,
      birthDate: person.birthDate ? person.birthDate : null,
      domicile: person.birthPlace ? person.birthPlace : null
    }

    return birthRecord;
  }

  // Function copies the object
  copyObject(item: any): any {
    return JSON.parse(JSON.stringify(item));
  }

}
