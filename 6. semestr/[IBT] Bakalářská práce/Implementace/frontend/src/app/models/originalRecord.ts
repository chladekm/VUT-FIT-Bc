/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    originalRecord.ts                                     */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Person } from './person';

export interface OriginalRecord {

    id: number;
    recordId: number;

    //References

    // 1 to Many - with Person
    personId: number;
    person?: Person;
}
