/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    storage.module.ts                                     */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { CommonModule } from '@angular/common';
import { ModuleWithProviders, NgModule } from '@angular/core';

import { StorageConfig } from './storage.models';

@NgModule({
    imports: [
        CommonModule,
    ],
    providers: [
    ]
})
export class StorageModule {
    static forRoot(config: StorageConfig): ModuleWithProviders {
        return {
            ngModule: StorageModule,
            providers: [
                {
                    provide: StorageConfig,
                    useValue: config,
                },
            ]
        };
    }
}
