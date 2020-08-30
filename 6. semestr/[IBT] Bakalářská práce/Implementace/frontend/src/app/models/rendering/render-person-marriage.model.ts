/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    render-person-marriage.model.ts                       */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { RenderPerson } from './render-person.model';

export class RenderPersonMarriage {
    spouse : RenderPerson;
    children : RenderPerson[];
}
