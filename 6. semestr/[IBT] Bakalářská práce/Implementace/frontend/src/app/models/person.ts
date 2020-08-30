/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    person.ts                                             */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { FamilyTree } from './familytree';
import { OriginalRecord } from './originalRecord';
import { PersonName } from './personName';
import { Relationship } from './relationship';
import { FamilyTreePerson } from './relationships/familyTreePerson';

export interface Person {

    id: number;
    isFemale: boolean;
    birthDate?: Date;
    baptismDate?: Date;
    deathDate?: Date;
    birthPlace?: string;
    isPrivate: boolean;
    isUndefined: boolean;

    // References

    // 1 to Many - with PersonName
    personNames: PersonName[];

    // 1 to Many - with OriginalRecord
    originalRecords: OriginalRecord[];

    // 1 to Many - with FamilyTree
    mainInFamilyTrees: FamilyTree[];

    // 1 to Many - with Relationship (Ancestor or Husband)
    ancestorOrHusbandRelationship: Relationship[];

    // 1 to Many - with Relationship (Descendant or Wife)
    descendantOrWifeRelationship: Relationship[];

    // Many to many - with FamilyTree,
    familyTreePerson: FamilyTreePerson[];
}
