/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    personName.ts                                         */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Person } from './person';

export interface PersonName {

    id: number;
    name: string;
    isFirstName: boolean;

    //References

    // 1 to Many - with Person
    personId: number;
    person?: Person;
}
