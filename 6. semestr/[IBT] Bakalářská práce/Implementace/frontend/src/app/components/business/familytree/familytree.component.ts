/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    familytree.component.ts                               */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { MatDialog, MatDialogConfig, MatDialogRef } from "@angular/material";
import { Title } from '@angular/platform-browser';
import { ActivatedRoute } from '@angular/router';
import * as d3 from 'd3';
import * as dTree from 'd3-dtree';
import { Observable } from 'rxjs';
import { map, take } from 'rxjs/operators';
import { FamilyTree } from 'src/app/models/familytree';
import { Person } from 'src/app/models/person';
import { Relationship, RelationshipTypesEnum } from 'src/app/models/relationship';
import { RenderPersonMarriage } from 'src/app/models/rendering/render-person-marriage.model';
import { RenderPerson } from 'src/app/models/rendering/render-person.model';
import { SelectedPerson } from 'src/app/models/rendering/selected-person';
import { CollisionService } from 'src/app/services/collision.service';
import { AuthenticationService } from 'src/app/services/http/authentication.service';
import { RelationshipService } from 'src/app/services/relationship.service';
import { UserService } from 'src/app/services/user.service';
import { FamilyTreeService } from '../../../services/familytree.service';
import { PersonService } from '../../../services/person.service';
import { FamilytreeHelpComponent } from '../../ui/familytree-help/familytree-help.component';
import { FamilytreeRemovingPersonDialogComponent } from '../../ui/familytree-removing-person-dialog/familytree-removing-person-dialog.component';
import { CollisionDetailPersonComponent } from '../collision-detail-person/collision-detail-person.component';
import { CollisionListComponent } from '../collision-list/collision-list.component';
import { FamilytreeConcatenateComponent } from '../familytree-concatenate/familytree-concatenate.component';
import { FamilytreeDetailComponent } from '../familytree-detail/familytree-detail.component';
import { PersonAddComponent } from '../person-add/person-add.component';
import { PersonReplaceUndefinedComponent } from '../person-replace-undefined/person-replace-undefined.component';


@Component({
  selector: 'app-familytree',
  templateUrl: './familytree.component.html',
  styleUrls: ['./familytree.component.scss']
})
export class FamilytreeComponent implements OnInit {

  familytree: FamilyTree;
  topPerson: Person;
  relationships: Relationship[] = [];
  treeData: RenderPerson[] = [];
  selectedPerson: SelectedPerson;
  showPerson;
  collisionsCount: number = 0;
  signedUserId: number = 0;
  realUserName: string = "";


  // Dialog window references
  addPersonDialogRef: MatDialogRef<PersonAddComponent>;
  editFamilyTreeDialogRef: MatDialogRef<FamilytreeDetailComponent>;
  removingPersonDialogRef: MatDialogRef<FamilytreeRemovingPersonDialogComponent>;
  showCollisionsDialogRef: MatDialogRef<CollisionListComponent>;
  replacePersonDialogRef: MatDialogRef<PersonReplaceUndefinedComponent>;
  copyFamilyTreeDialogRef: MatDialogRef<FamilytreeConcatenateComponent>;
  personDetailTreeDialogRef: MatDialogRef<CollisionDetailPersonComponent>;

  constructor(
    private route: ActivatedRoute,
    private familyTreeService: FamilyTreeService,
    private relationshipService: RelationshipService,
    private personService: PersonService,
    private userService: UserService,
    private collisionService: CollisionService,
    private authenticationService: AuthenticationService,
    private titleService: Title,
    private dialog: MatDialog,
    protected cdr: ChangeDetectorRef
  ) { }

  ngOnInit(): void {
    this.getFamilyTree();
    this.signedUserId = this.authenticationService.getHash('auth', 'id');
  }

  // -------------------- Methods for upper toolbar -------------------- //

  // Get FamilyTree from database
  getFamilyTree(): void {
    const id = +this.route.snapshot.paramMap.get('id');
    this.familyTreeService.getFamilyTreeById(id)
      .subscribe(
        (familytree) => {
          this.familytree = familytree;
          if (this.familytree) {
            this.getRelationshipsByFamilyTreeId(id);
            this.getCollisionsCount();
            this.titleService.setTitle("Rodokmen " + familytree.title);

            // If current user is not author of the tree -> get name of author
            if (!this.authorized())
              this.getRealUser();
          }
          else { throw new Error('Cannot load data from server'); }
        }
      );
  };

  // Modal Window: Display help article
  displayHelp() {
    const dialogConfig = new MatDialogConfig();
    dialogConfig.maxWidth = "700px";
    this.dialog.open(FamilytreeHelpComponent, dialogConfig);
  }

  // Modal Window: Edit informations about familytree
  editFamilyTree() {

    const dialogConfig = new MatDialogConfig();
    dialogConfig.disableClose = false,
      dialogConfig.minWidth = "300px",
      dialogConfig.width = "30%";
    dialogConfig.data = {
      familytree: this.familytree
    }

    this.editFamilyTreeDialogRef = this.dialog.open(FamilytreeDetailComponent, dialogConfig);

    this.editFamilyTreeDialogRef.afterClosed().subscribe(data => {
      if (data) {
        this.familytree = data;
        this.titleService.setTitle("Rodokmen " + this.familytree.title);
      }

    });
  }

  // Modal Window: Edit informations about familytree
  showCollisions() {

    this.getCollisionsCount();
    this.selectedPerson = null;
    this.showPerson = null;

    const dialogConfig = new MatDialogConfig();
    dialogConfig.minWidth = "300px";
    dialogConfig.maxWidth = "1000px";
    dialogConfig.disableClose = true,
      dialogConfig.data = {
        familytree: this.familytree,
        relationships: this.relationships
      }

    this.showCollisionsDialogRef = this.dialog.open(CollisionListComponent, dialogConfig);

    this.showCollisionsDialogRef.afterClosed().subscribe(refresh => {
      if (refresh)
        this.getFamilyTree();
      this.getCollisionsCount();
    });
  }

  // Gets number of unsolved collisions
  getCollisionsCount() {
    this.collisionService.getCountOfUnsolvedCollisions(this.familytree.id).subscribe(count => { this.collisionsCount = count; });
  }

  // Modal Window: Replace undefined person
  replaceUndefinedPerson(id: number) {

    if (!this.authorized())
      return;

    const dialogConfig = new MatDialogConfig();
    dialogConfig.minWidth = "60%";
    dialogConfig.maxWidth = "100%";
    dialogConfig.disableClose = true,
      dialogConfig.data = {
        selectedPerson: this.personService.getPersonDataFromFamilyTreeRelationships(id, this.relationships),
        familytree: this.familytree,
        relationships: this.relationships
      }

    this.replacePersonDialogRef = this.dialog.open(PersonReplaceUndefinedComponent, dialogConfig);

    this.replacePersonDialogRef.afterClosed().subscribe(action => {
      switch (action) {
        case 'add_relationship': {
          this.addPerson();
          break;
        }
        case 'delete_person': {
          this.removePersonValidation(id);
          break;
        }
        case 'replaced': {
          this.getRelationshipsByFamilyTreeId(this.familytree.id);
          break;
        }
        default: {
          break;
        }
      }
      this.selectedPerson = null;
    });
  }

  // Modal Window: Replace undefined person
  copyFamilyTree() {

    const dialogConfig = new MatDialogConfig();
    dialogConfig.minWidth = "350px";
    dialogConfig.width = "30%";
    dialogConfig.data = { familytreeId: this.familytree.id }

    this.copyFamilyTreeDialogRef = this.dialog.open(FamilytreeConcatenateComponent, dialogConfig);
  }

  // -------------------- Methods for lower toolbar -------------------- //

  // Display more informations about person
  displayPersonDetail(): void {
    if (!(this.showPerson)) {
      this.showPerson = this.selectedPerson;
    }
    else {
      this.showPerson = undefined;
    }

    this.renderFamilyTree();
  }

  // Show add person component to add person to familytree
  addPerson() {

    const dialogConfig = new MatDialogConfig();
    dialogConfig.minWidth = "60%";
    dialogConfig.maxWidth = "100%";
    dialogConfig.disableClose = true,
      dialogConfig.data = {
        selectedPerson: this.selectedPerson,
        familytree: this.familytree,
        relationships: this.relationships
      }

    this.addPersonDialogRef = this.dialog.open(PersonAddComponent, dialogConfig);

    this.addPersonDialogRef.afterClosed().subscribe(returnData => {
      if (returnData.refresh != undefined && returnData.refresh == true) {
        this.familytree.startPersonId = returnData.startPersonId;
        setTimeout(() => { this.getRelationshipsByFamilyTreeId(this.familytree.id); }, 500);
      }
    });
  }

  // Validation and processing of removing process (core method for removing)
  removePersonValidation(id: number) {
    var person = this.familyTreeService.getPersonDataFromFamilyTreeRelationships(id, this.relationships);

    if (person == null && id == this.familytree.startPersonId) {
      const message = "Nelze smazat jedinou osobu v rodokmenu. Provést smazání celého rodokmenu můžete na svém profilu.";
      this.openRemovingPersonDialog(false, message);
      return;
    }

    // Person does not have descendants
    if ((this.familyTreeService.getAllDescendants(person)).length == 0 && this.familytree.startPersonId != id) {

      // Person has spouses but belongs to tribe -> remove him and all his spouses
      if (this.familyTreeService.belongPersonToTribe(person, this.familytree, this.relationships)) {

        var spouses = this.familyTreeService.getAllSpouses(person);
        const message = "Budou smazány i všechny manželky/manželé";
        this.openRemovingPersonDialog(true, (spouses.length != 0) ? message : null).subscribe(result => {
          if (result) {
            spouses.forEach(spouse => {
              if (spouse.id != this.familytree.startPersonId) {
                this.removePerson(spouse.id);
              }
            });
            this.removePerson(id);
            this.finishRemoving();
          }
          else {
            return;
          }
        });
      }
      // Person does not belong to tribe -> remove him
      else {
        this.openRemovingPersonDialog(true).subscribe(result => {
          if (result) {
            this.removePerson(id);
            this.finishRemoving();
          }
          else
            return;
        });
      }
    }
    // Person does have descendants but is on top of the familytree
    else if (this.familyTreeService.isPersonTopPersonOrMarriedToIt(person, this.familytree, this.relationships)) {
      // Person has only one descendant -> delete him and spouses -> starperson will be descendant

      if ((this.familyTreeService.getAllDescendants(person)).length == 1) {
        var newStartPersonId = this.familyTreeService.getAllDescendants(person)[0].id;

        var spouses = this.familyTreeService.getAllSpouses(person);
        const message = "Budou smazány i všechny manželky/manželé";
        this.openRemovingPersonDialog(true, (spouses.length != 0) ? message : null).subscribe(result => {
          if (result) {
            spouses.forEach(spouse => {
              this.removePerson(spouse.id);
            });
            this.removePerson(id);
            this.finishRemoving(newStartPersonId);
          }
          else {
            return;
          }
        });
      }
      // Person has no descendants
      else if (this.familyTreeService.getAllDescendants(person).length == 0) {

        var spouses = this.familyTreeService.getAllSpouses(person);

        // Person is founder of the family
        if (spouses.length != 1 || (spouses.length == 1 && spouses[0].isUndefined == true)) {
          const message = "Osobu nelze odstranit, neboť je zakladatelem rodu";
          this.openRemovingPersonDialog(false, message);
        }
        // Person may not be founder of the family but has only one spouse, so the spouse will be
        else {
          // Spouse is going to be the new founder of the family
          var newStartPersonId = spouses[0].id;

          this.openRemovingPersonDialog(true).subscribe(result => {
            if (result) {
              this.removePerson(id);
              this.finishRemoving(newStartPersonId);
            }
          });
        }

      }
      else {
        const message = "Osobu nelze odstranit, neboť má více než jednoho potomka. Nebyl by tak jednoznačný nový zakladatel rodu."
        this.openRemovingPersonDialog(false, message);
      }
    }
    // Person does have descendants and is not on the top of familytree --> cannot delete
    else {
      // Cannot delete
      const message = "Osoba má potomky. Pokud ji chcete skutečně odstranit, odstraňte nejprve prosím potomky"
      this.openRemovingPersonDialog(false, message);
    }
  }

  // Helper method that opens modal window, where user decides if deleting will be executed
  openRemovingPersonDialog(canDelete: boolean, message?: string): Observable<boolean> {

    const dialogConfig = new MatDialogConfig();
    dialogConfig.disableClose = false,
      dialogConfig.minWidth = "300px",
      dialogConfig.maxWidth = "400px",
      dialogConfig.data = {
        canDelete: canDelete,
        message: message
      }

    this.removingPersonDialogRef = this.dialog.open(FamilytreeRemovingPersonDialogComponent, dialogConfig);

    return this.removingPersonDialogRef.afterClosed().pipe(take(1), map(data => {
      return data;
    }
    ));
  }

  // Method provides validation for new startPerson and detaches selected user
  finishRemoving(newStartPersonId?: number): void {

    if (newStartPersonId != null) {
      this.familytree.startPersonId = newStartPersonId;
      this.familyTreeService.updateFamilyTree(this.familytree).subscribe(tree => {
        if (tree) {
          this.selectedPerson = null;
          this.showPerson = null;
          this.getFamilyTree();
          return;
        }
      });
    }

    this.selectedPerson = null;
    this.showPerson = null;
    this.getFamilyTree();
  }

  // Method sending request to delete person by id
  removePerson(id: number): void {

    this.personService.removePersonFromFamilyTree(id, this.familytree.id).subscribe(removedPerson => {
      if (removedPerson) {
        this.getRelationshipsByFamilyTreeId(this.familytree.id);
      }
    });
  }


  // -------------------- Methods for rendering familytree -------------------- //

  // Gets all relationships in specific familytree
  getRelationshipsByFamilyTreeId(id: number): void {

    this.relationshipService.getRelationshipsByFamilyTreeId(id)
      .subscribe(
        (relationships) => {
          this.relationships = relationships;
          if (this.relationships) {

            if (this.relationships.length == 0)
              this.topPerson = this.familytree.startPerson;
            else
              this.topPerson = this.personService.getPersonDataFromFamilyTreeRelationships(this.familytree.startPersonId, this.relationships);

            this.getCollisionsCount();
            this.generateFamilyTree();

            // Actualize relationships for selected person
            if (this.selectedPerson)
              this.selectedPerson.data = (this.relationships.length == 0) ? this.topPerson : this.personService.getPersonDataFromFamilyTreeRelationships(this.selectedPerson.id, this.relationships);


          }
          error => {
            throw new Error('Cannot load data from server');
          }
        }
      );
  };

  // Ensure tree generating
  async generateFamilyTree() {

    this.treeData = [];

    // 50 tries before error will be executed (10 seconds)
    for (var i = 0; i < 50; i++) {
      if ((typeof this.relationships !== 'undefined') && (this.topPerson)) {
        this.treeData = [];
        this.treeData.push(this.createDataTreeContent(this.topPerson));
        this.renderFamilyTree();
        return;
      }
      else
        await this.delay(200);
    }

    if (this.familytree == null && this.relationships == null)
      throw new Error('Cannot load data from server');
  }

  // Generate formatted content for familytree rendering
  private createDataTreeContent(person: Person): RenderPerson {

    var spousesRelationship = this.relationships.filter(rel => (rel.type == RelationshipTypesEnum.isInMarriageWith && (rel.ancestorOrHusbandPersonId == person.id || rel.descendantOrWifePersonId == person.id)));

    if (person.isFemale)
      var spouses = spousesRelationship.map(spouse => spouse.ancestorOrHusbandPerson);
    else
      var spouses = spousesRelationship.map(spouse => spouse.descendantOrWifePerson);

    var currentPerson: RenderPerson = {
      name: "",
      class: (person.isUndefined ? 'undefined' : ((person.isFemale) ? 'woman' : 'man')),
      marriages: [],
      extra: {
        id: person.id,
        birthYear: (person.birthDate) ? (new Date(person.birthDate).getFullYear()) : undefined,
        deathYear: (person.deathDate) ? (new Date(person.deathDate).getFullYear()) : undefined
      }
    }

    // Sort person names
    this.familyTreeService.sortNamesByType(person.personNames).forEach(pname => { currentPerson.name += pname.name + " " });

    // Iterate through marriages
    spouses.forEach(spouse => {

      var spouseName = "";
      this.familyTreeService.sortNamesByType(spouse.personNames).forEach(pname => { spouseName += pname.name + " " });

      var currentMarriage: RenderPersonMarriage = {
        spouse: {
          name: spouseName,
          class: (spouse.isUndefined ? 'undefined' : ((spouse.isFemale) ? 'woman' : 'man')),
          marriages: [],
          extra: {
            id: spouse.id,
            birthYear: (spouse.birthDate) ? (new Date(spouse.birthDate).getFullYear()) : undefined,
            deathYear: (spouse.deathDate) ? (new Date(spouse.deathDate).getFullYear()) : undefined
          }
        },
        children: []
      }

      // Filter children by mother
      var motherChildren = this.relationships.filter(rel => (rel.ancestorOrHusbandPersonId == ((spouse.isFemale) ? spouse.id : currentPerson.extra.id)) && (rel.type == RelationshipTypesEnum.isMotherOf)).map(c => c.descendantOrWifePersonId);

      // Filter children by father
      var fatherChildren = this.relationships.filter(rel => (rel.ancestorOrHusbandPersonId == ((spouse.isFemale) ? currentPerson.extra.id : spouse.id)) && (rel.type == RelationshipTypesEnum.isFatherOf)).map(c => c.descendantOrWifePersonId);

      // Lets make intersect of those
      var children: number[];
      children = motherChildren.filter(x => fatherChildren.includes(x));

      // Iterate through children
      children.forEach(childId => {

        // Find child person by id
        var child = this.relationships.map(rel => rel.descendantOrWifePerson).find(child => child.id == childId);

        // Push Children to original person
        currentMarriage.children.push(this.createDataTreeContent(child));
      });

      // Push Marriage to original person
      currentPerson.marriages.push(currentMarriage);
    });

    return currentPerson;
  }

  // Renders the tree
  public renderFamilyTree(): void {

    const that = this;

    d3.select("svg").remove();

    dTree.init(this.treeData, {
      target: "#familytree",
      debug: false,
      height: (window.innerHeight),
      width: this.showPerson ? (window.innerWidth) * 0.75 : (window.innerWidth),
      callbacks: {
        nodeHeightSeperation: (nodeWidth, nodeMaxHeight) => {
          return nodeMaxHeight + 70;
        },
        nodeClick: (name, extra, id) => {

          var personData = (this.relationships.length == 0) ? this.topPerson : this.personService.getPersonDataFromFamilyTreeRelationships(extra.id, this.relationships);

          if (personData.isUndefined) {
            this.selectedPerson = null;
            this.replaceUndefinedPerson(extra.id);
          }

          // Define selected person
          this.selectedPerson =
          {
            id: extra.id,
            name: name,
            data: personData
          };

          // If person is not author -> show him detail for person
          if (!this.authorized() && !this.selectedPerson.data.isUndefined) {
            this.displayPersonDetailModalWindow();
            return;
          }

          // If is sidebar with person info opened, actualize it to new person
          if (this.showPerson) {
            if (this.selectedPerson.data.isUndefined) {
              this.selectedPerson = null;
              this.showPerson = null;
            }
            else {
              this.showPerson = this.selectedPerson;
            }
          }

          that.cdr.detectChanges();
        },
        textRenderer: (name, extra, textClass) => {

          var text = '<p align="center" class="node-text-style' + textClass + '">' + name + '</p>';
          text += '<p class="years">';

          if (extra && extra.birthYear)
            text += '<span class="node-text-birthyear">' + "*" + extra.birthYear + '</span>';
          if (extra && extra.deathYear) {
            text += ' <span class="node-text-deathyear">' + '<span style="font-size:90%;">&#x271D;</span>' + extra.deathYear + '</span>';
          }

          text += '</p>';

          return text;
        },
        nodeRenderer: (name, x, y, height, width, extra, id, nodeClass, textClass, textRenderer) => {

          let node = '';
          node += '<div style="width:100%; height:100%;" class="' + nodeClass + '" id="' + id + '">\n';
          node += textRenderer(name, extra, textClass);
          node += '</div>';

          return node;

        }
      },
      margin: {
        top: 0,
        right: 0,
        bottom: 0,
        left: 0
      }
    });
  }

  // Put a thread to sleep
  private delay(ms: number) {
    return new Promise(resolve => setTimeout(resolve, ms));
  }

  // -------------------- Methods for unauthorized user -------------------- //

  // Checks if user is author of the familytree
  authorized(): boolean {
    return this.familytree.userId === this.authenticationService.getHash('auth', 'id');
  }

  // Get name of familytree author
  getRealUser(): void {
    this.userService.getUser(this.familytree.userId).subscribe(result => {
      if (result) {
        if (result.name && result.surname)
          this.realUserName = result.name + " " + result.surname;
        else
          this.realUserName = result.nickname;
      }
    })
  }

  // Display more informations about person
  displayPersonDetailModalWindow(): void {

    const dialogConfig = new MatDialogConfig();
    dialogConfig.minWidth = "300px",
      dialogConfig.maxWidth = "600px",
      dialogConfig.data = {
        person: this.selectedPerson.data,
        familyTreeId: this.familytree.id
      }

    this.personDetailTreeDialogRef = this.dialog.open(CollisionDetailPersonComponent, dialogConfig);
  }


}
