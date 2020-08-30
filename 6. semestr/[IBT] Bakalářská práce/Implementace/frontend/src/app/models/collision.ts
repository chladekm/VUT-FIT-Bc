/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    collision.ts                                          */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { CollisionRelationship } from './relationships/collisionRelationship';
import { FamilyTreeCollision } from './relationships/familyTreeCollisions';

export enum CollisionTypesEnum {
    differentMother,
    differentFather,
    marriageOrAncestor
}

export interface Collision {

    id: number;
    type: CollisionTypesEnum;

    // References

    // Many to many - with Relationship
    collisionRelationship: CollisionRelationship[];

    // Many to many - with FamilyTree
    familyTreeCollision: FamilyTreeCollision[];
}
