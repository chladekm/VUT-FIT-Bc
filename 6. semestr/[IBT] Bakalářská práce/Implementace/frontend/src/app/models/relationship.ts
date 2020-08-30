/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    relationship.ts                                       */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Person } from './person';
import { Marriage } from './marriage';
import { FamilyTreeRelationship } from './relationships/familyTreeRelationship';
import { CollisionRelationship } from './relationships/collisionRelationship';

export enum RelationshipTypesEnum {
    isMotherOf,
    isFatherOf,
    isInMarriageWith
}

export interface Relationship {

    id: number;
    type: RelationshipTypesEnum;

    //References

    // 1 to Many - with Relationship
    marriages: Marriage[];

    // 1 to Many - with Person (Ancestor or Husband)
    ancestorOrHusbandPersonId: number;
    ancestorOrHusbandPerson: Person;
    
    // 1 to Many - with Person (Descendant or Wife)
    descendantOrWifePersonId: number;
    descendantOrWifePerson: Person;

    // Many to Many - with Collision
    collisionRelationship: CollisionRelationship[];

    // Many to many - with FamilyTree
    familyTreeRelationship: FamilyTreeRelationship[];
}