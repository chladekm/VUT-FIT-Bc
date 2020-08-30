/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    collisionRelationship.ts                              */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Collision } from '../collision';
import { Relationship } from '../relationship';

export interface CollisionRelationship {

    collisionId: number;
    collision: Collision;

    relationshipId: number;
    relationship: Relationship;
}