/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    familyTreePersons.ts                                  */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { FamilyTree } from '../familytree';
import { Person } from '../person';

export interface FamilyTreePerson {

    familyTreeId: number;
    familyTree: FamilyTree;

    personId: number;
    person: Person;
}