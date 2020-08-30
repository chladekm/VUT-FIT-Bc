/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    render-person.model.ts                                */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { RenderPersonMarriage } from './render-person-marriage.model';

export class RenderPerson {
    name : string;
    class : string;
    marriages : RenderPersonMarriage[];
    extra : {
        id: number,
        birthYear: number,
        deathYear: number
    }
}
