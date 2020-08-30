/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    marriage.ts                                           */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Relationship } from './relationship';

export interface Marriage{
    
    id: number;
    marriageDate?: Date;
    marriageAddress?: string;
    
    // References
    
    // 1 to Many - with Relationship
    relationshipId: number;
    relationship: Relationship;
}