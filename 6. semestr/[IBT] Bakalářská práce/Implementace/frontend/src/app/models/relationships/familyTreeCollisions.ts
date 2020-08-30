/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    familyTreeCollisions.ts                               */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Collision } from '../collision';
import { FamilyTree } from '../familytree';

export interface FamilyTreeCollision {

    familyTreeId: number;
    familytree: FamilyTree;

    collisionId: number;
    collision: Collision;

    isSolved: boolean;
    solutionDate: Date;
}