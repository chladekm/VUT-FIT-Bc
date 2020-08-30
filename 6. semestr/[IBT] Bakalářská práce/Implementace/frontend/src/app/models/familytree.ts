/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    familytree.ts                                         */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { User } from './user';
import { FamilyTreePerson } from './relationships/familyTreePerson';
import { FamilyTreeCollision } from './relationships/familyTreeCollisions';
import { FamilyTreeRelationship } from './relationships/familyTreeRelationship';
import { Person } from './person';

export enum FamilyTreeTypesEnum {
    _private,
    _public,
    _nonpublic
}

export interface FamilyTree {

    id: number;
    type: FamilyTreeTypesEnum;
    title: string;

    //References

    // 1 to Many - with User
    userId: number;
    user: User;

    // 1 to Many - with Person
    startPersonId: number;
    startPerson: Person;

    // Many to many - with Collision
    familyTreeCollision: FamilyTreeCollision[];

    // Many to many - with Person
    familyTreePerson: FamilyTreePerson[];

    // Many to many - with Relationship
    familyTreeRelationship: FamilyTreeRelationship[];
}