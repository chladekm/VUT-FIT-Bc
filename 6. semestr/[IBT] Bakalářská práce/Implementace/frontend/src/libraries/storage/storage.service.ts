/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    storage.service.ts                                    */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Injectable } from '@angular/core';
import storage from 'local-storage-fallback';
import { StorageConfig } from './storage.models';

@Injectable({
    providedIn: 'root',
})
export class StorageService {

    private storageObject: any;

    constructor(private config: StorageConfig) {
        
        const storageObject = storage.getItem(config.name);
        
        if (!storageObject) {
            // Initialize storage object
            storage.setItem(config.name, JSON.stringify({}));
        }

        const storageValue = storage.getItem(config.name);

        if (!storageValue)
            throw Error(`Invalid storage value`);

        this.storageObject = JSON.parse(storageValue);
    }

    // Set (Create or Update) object 
    set<T>(source: string, name: string, value: T): void {
        
        const existingObject = this.storageObject[source];
        let storeObject: any;

        if (!existingObject) {
            // Create new object
            storeObject = {};
            this.storageObject[source] = storeObject;
        } else {
            storeObject = existingObject;
        }

        // Store property value
        storeObject[name] = value;
        this.saveCurrentObject();
    }

    // Get object from storage
    get<T>(source: string, name: string): T | undefined {
        const sourceObject = this.storageObject[source];

        if (!sourceObject)
            return undefined;

        return sourceObject[name];
    }

    // Remove object from storage
    removeSource(source: string): void {
        delete this.storageObject[source];
        this.storageObject[source] = undefined;
    }

    // Remove object property
    remove(source: string, name: string): void {
        const sourceObject = this.storageObject[source];

        if (!sourceObject)
            return undefined;

        delete this.storageObject[source][name];
        this.saveCurrentObject();
    }

    // Save current object
    private saveCurrentObject(): void {
        storage.setItem(this.config.name, JSON.stringify(this.storageObject));
    }

}
