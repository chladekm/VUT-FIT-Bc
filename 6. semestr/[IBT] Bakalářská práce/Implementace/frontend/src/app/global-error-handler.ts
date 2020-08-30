/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    global-error-handler.ts                               */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { ErrorHandler, Injectable, NgZone } from '@angular/core';
import { MatDialog } from '@angular/material';
import { Router } from '@angular/router';

@Injectable()
export class GlobalErrorHandler implements ErrorHandler {

    constructor(
        public router: Router,
        public ngZone: NgZone,
        private dialogRef: MatDialog
    ) { }

    handleError(error: any) {
        
        var err: any;

        if (error)
            err = {
                status: error.status,
                statusText: error.statusText ? error.statusText : '',
                name: error.name ? error.name : '',
                message: error.message ? error.message : error.toString(),
            };

        // Log  the error
        console.error(error);
        console.error(err);

        // Close all modal windows
        this.dialogRef.closeAll();

        if (error.status == 401) {
            this.ngZone.run(() => this.router.navigate(['error-auth'])).then();
        }
        else {
            this.ngZone.run(() => this.router.navigate(['error'])).then();
        }

    }
}