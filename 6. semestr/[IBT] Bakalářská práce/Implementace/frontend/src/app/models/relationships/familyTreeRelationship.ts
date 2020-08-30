/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    familyTreeRelationship.ts                             */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { FamilyTree } from '../familytree';
import { Relationship } from '../relationship';

export interface FamilyTreeRelationship {

    familyTreeId: number;
    familyTree: FamilyTree;

    relationshipId: number;
    relationship: Relationship;
}