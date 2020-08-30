/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    user.ts                                               */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { FamilyTree } from './familytree';

export interface User {

    id: number;
    name?: string;
    surname?: string;
    nickname: string;
    email: string;
    password: string;
    registerDate: Date;

    // References

    // 1 to Many - with FamilyTree
    familyTrees: FamilyTree[];
}
