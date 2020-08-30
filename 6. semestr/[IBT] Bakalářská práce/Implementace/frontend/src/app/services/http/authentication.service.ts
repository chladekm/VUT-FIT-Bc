/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    authentication.service.ts                             */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { Injectable } from '@angular/core';
import { StorageService } from 'src/libraries/storage';

@Injectable({
  providedIn: 'root'
})
export class AuthenticationService {

  constructor(private storageService: StorageService) { }

  getHash(source = 'auth', name = 'hash'): any {
    return this.storageService.get(source, name);
  }

  setHash(hash: string, id: number): void {
    this.storageService.set('auth', 'hash', hash);
    this.storageService.set('auth', 'id', id);
  }

  removeAuth(): void {
    this.storageService.remove('auth', 'hash');
    this.storageService.remove('auth', 'id');
  }
}