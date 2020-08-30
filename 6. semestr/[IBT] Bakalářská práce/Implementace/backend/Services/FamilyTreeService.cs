/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    FamilyTreeService.cs                                  */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using AutoMapper;
using Entity;
using Entity.Enums;
using Entity.Relationships;
using Microsoft.EntityFrameworkCore;
using Newtonsoft.Json;
using Services.Base;
using Services.Exceptions;
using Services.Interfaces;
using Services.Interfaces.RelationshipInterfaces;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace Services
{
    public class FamilyTreeService : BaseService<FamilyTree>, IFamilyTreeService
    {
        public FamilyTreeService(IServiceDependencies _serviceDependencies) : base(_serviceDependencies)
        {
        }

        #region Methods for getting specific familytree(s)

        public async Task<IEnumerable<FamilyTree>> GetFamilyTreesByUserIdAsync(int Id)
        {
            var result = await _serviceDependencies._context.FamilyTrees
                                                            .Where(familytree => familytree.UserId == Id)
                                                            .OrderBy(familytree => familytree.Title)
                                                            .ToListAsync();

            if (result.Count() == 0)
                throw new ObjectNotFoundException(nameof(Entity.FamilyTree), Id);
            else
            {
                return result;
            }
        }

        public async Task<FamilyTree> GetFamilyTreeByIdAsync(int id)
        {
            var result = await _serviceDependencies._context.FamilyTrees
                                                            .AsNoTracking()
                                                            .Include(familyTree => familyTree.StartPerson)
                                                            .ThenInclude(person => person.PersonNames)
                                                            .FirstOrDefaultAsync(familytree => familytree.Id == id);

            if (result == null)
                throw new ObjectNotFoundException(nameof(Entity.FamilyTree), id);

            return result;
        }

        public async Task<FamilyTree> GetFamilyTreeForDuplicationByIdAsync(int id)
        {
            var result = await _serviceDependencies._context.FamilyTrees
                                                            .AsNoTracking()
                                                            .Include(familyTree => familyTree.FamilyTreeCollisions)
                                                            .ThenInclude(fc => fc.Collision)
                                                            .ThenInclude(collision => collision.CollisionRelationship)
                                                            .Include(familyTree => familyTree.FamilyTreePerson)
                                                            .ThenInclude(fp => fp.Person)
                                                            .Include(familyTree => familyTree.FamilyTreeRelationship)
                                                            .FirstOrDefaultAsync(familytree => familytree.Id == id);

            if (result == null)
                throw new ObjectNotFoundException(nameof(Entity.FamilyTree), id);

            return result;
        }

        #endregion

        #region CRUD operations

        public async Task<FamilyTree> CreateFamilyTreeAsync(FamilyTree treeToCreate)
        {

            if (!(Enum.IsDefined(typeof(FamilyTreeTypesEnum), treeToCreate.Type)))
                throw new InvalidObjectException("Invalid type of FamilyTree", nameof(Entity.FamilyTree), 0);

            if (!(ExistsInDb<User>(treeToCreate.UserId)))
                throw new ObjectNotFoundException(nameof(Entity.User), treeToCreate.UserId);


            var result = await Create(treeToCreate);

            var familytreePersonservice = ResolveService<IFamilyTreePersonService>();

            FamilyTreePerson reference = new FamilyTreePerson { FamilyTree = null, FamilyTreeId = result.Id, Person = null, PersonId = result.StartPersonId };
            await familytreePersonservice.PutPersonToFamilyTreeAsync(reference);

            return result;
        }

        public async Task<FamilyTree> UpdateFamilyTreeAsync(FamilyTree treeToUpdate)
        {
            if (!(Enum.IsDefined(typeof(FamilyTreeTypesEnum), treeToUpdate.Type)))
                throw new InvalidObjectException("Invalid type of FamilyTree", nameof(Entity.FamilyTree), 0);

            return await Update(treeToUpdate);
        }

        public async Task<FamilyTree> DeleteFamilyTreeAsync(int id)
        {
            var personIdList = await _serviceDependencies._context.FamilyTreePerson
                                                                .AsNoTracking()
                                                                .Where(fp => fp.FamilyTreeId == id)
                                                                .Select(fp => fp.PersonId)
                                                                .ToListAsync();

            var familyTree = await GetFamilyTreeByIdAsync(id);
            var startPersonId = familyTree.StartPersonId;

            // StartPerson will be removed as last
            personIdList.Remove(startPersonId);

            var personService = ResolveService<IPersonService>();

            // Remove every person from FamilyTree
            foreach (var personId in personIdList)
            {
                await personService.RemovePersonFromFamilyTreeAsync(personId, id);
            }

            // Removing last person will cascade removing FamilyTree
            await personService.RemovePersonFromFamilyTreeAsync(startPersonId, id);

            // If last person still exists in database, we have to delete familytree manually
            try
            {
                await Delete(id);
            }
            catch (ObjectNotFoundException) { }

            return familyTree;
        }

        #endregion

        #region Methods for contatenating FamilyTrees

        /// <summary>
        /// Method returns all Users FamilyTrees, that can be concatenated with source FamilyTree
        /// </summary>
        public async Task<IEnumerable<FamilyTree>> FamilyTreesThanCanBeConcatenatedForUserAsync(int sourceTreeId, int userId)
        {
            try
            {
                var userFamilyTrees = await GetFamilyTreesByUserIdAsync(userId);
                var validFamilyTrees = new List<FamilyTree>();

                foreach (var tree in userFamilyTrees)
                {
                    tree.StartPerson = null;
                    _serviceDependencies._context.DetachAllEntities();

                    if (await CanConcatenateAsync(sourceTreeId, tree.Id))
                        validFamilyTrees.Add(tree);
                }

                return validFamilyTrees;
            }
            // User have no FamilyTrees
            catch (ObjectNotFoundException)
            {
                return new List<FamilyTree>();
            }
        }

        /// <summary>
        /// Method provides concatenating of two FamilyTrees (copies content from source to destination)
        /// </summary>
        public async Task<FamilyTree> ConcatenateFamilyTreesAsync(int sourceTreeId, int destinationTreeId)
        {
            if (!(await CanConcatenateAsync(sourceTreeId, destinationTreeId)))
                throw new InvalidObjectException("Cannot concatenate", nameof(FamilyTree), destinationTreeId);

            var relationshipService = ResolveService<IRelationshipService>();
            var familytreeRelationshipService = ResolveService<IFamilyTreeRelationshipService>();
            var familytreeCollisionService = ResolveService<IFamilyTreeCollisionService>();
            var familytreePersonService = ResolveService<IFamilyTreePersonService>();
            var personService = ResolveService<IPersonService>();

            FamilyTree sourceTree = await GetFamilyTreeForDuplicationByIdAsync(sourceTreeId);
            FamilyTree destinationTree = await GetFamilyTreeForDuplicationByIdAsync(destinationTreeId);

            var sourceTreePersonsIds = sourceTree.FamilyTreePerson.Select(fp => fp.PersonId);

            // Get mutual references
            var (commonPersonsIds, commonCollisionsIds, commonRelationshipsIds) = GetMutualReferencesForTwoFamilyTrees(sourceTree, destinationTree);

            // Get relationships for both FamilyTrees
            var srcTreeRelationships = await relationshipService.GetRelationshipsByFamilyTreeIdAsync(sourceTreeId);
            var dstTreeRelationships = await relationshipService.GetRelationshipsByFamilyTreeIdAsync(destinationTreeId);

            Dictionary<int, int> mappedExistingUndefinedPersonsIds = new Dictionary<int, int>();
            var newRelationshipsForUndefinedPersons = new List<Relationship>();

            // Create references on relationships
            foreach (var relationship in srcTreeRelationships)
            {
                // Relationship already is in the familyTree --> can skip
                if (commonRelationshipsIds.Contains(relationship.Id))
                    continue;

                // Check if already marriage between those two persons exists (has different id because of the marraige details)
                if (relationship.Type == RelationshipTypesEnum.isInMarriageWith)
                {

                    // Check if exists similiar relationship (has different id because of marriage details)
                    var similiarRelationship = dstTreeRelationships.FirstOrDefault(rel => rel.AncestorOrHusbandPersonId == relationship.AncestorOrHusbandPersonId
                                                                                       && rel.DescendantOrWifePersonId == relationship.DescendantOrWifePersonId
                                                                                       && rel.Type == relationship.Type);

                    // Exists -> can skip
                    if (similiarRelationship != null)
                        continue;
                }

                if (relationship.AncestorOrHusbandPerson.IsUndefined || relationship.DescendantOrWifePerson.IsUndefined)
                {
                    Relationship similiarRelationship;
                    int undefinedPersonId;

                    // Get similiar relationship of exists
                    if (relationship.AncestorOrHusbandPerson.IsUndefined)
                    {
                        undefinedPersonId = relationship.AncestorOrHusbandPersonId;

                        similiarRelationship = dstTreeRelationships.FirstOrDefault(rel => rel.DescendantOrWifePersonId == relationship.DescendantOrWifePersonId
                                                                                       && rel.AncestorOrHusbandPerson.IsUndefined
                                                                                       && rel.Type == relationship.Type);
                    }
                    else
                    {
                        undefinedPersonId = relationship.DescendantOrWifePersonId;

                        similiarRelationship = dstTreeRelationships.FirstOrDefault(rel => rel.AncestorOrHusbandPersonId == relationship.AncestorOrHusbandPersonId
                                                                                       && rel.DescendantOrWifePerson.IsUndefined
                                                                                       && rel.Type == relationship.Type);
                    }

                    // Register and map undefined person and skip creating of relationship
                    if (similiarRelationship != null)
                    {
                        var currentUndefinedPersonId = (similiarRelationship.AncestorOrHusbandPerson.IsUndefined) ? similiarRelationship.AncestorOrHusbandPersonId : similiarRelationship.DescendantOrWifePersonId;

                        if (!mappedExistingUndefinedPersonsIds.ContainsKey(undefinedPersonId))
                            mappedExistingUndefinedPersonsIds.Add(undefinedPersonId, currentUndefinedPersonId);

                        continue;
                    }
                    // Register that it is new relationship for undefined person
                    else
                    {
                        newRelationshipsForUndefinedPersons.Add(relationship);
                        continue;
                    }
                }

                // put relationship to the new FamilyTree
                await familytreeRelationshipService.PutRelationshipToFamilyTreeAsync(new FamilyTreeRelationship { FamilyTreeId = destinationTreeId, RelationshipId = relationship.Id });
            }

            // Create references on collisions
            foreach (var reference in sourceTree.FamilyTreeCollisions)
            {
                await familytreeCollisionService.CreateAsync(new FamilyTreeCollision { FamilyTreeId = destinationTreeId, CollisionId = reference.CollisionId, IsSolved = false });
            }

            // Add persons to familyTree
            foreach (var personId in sourceTreePersonsIds)
            {
                var person = await personService.GetPersonByIdAsync(personId);

                // Person already exists in the destination FamilyTree
                if (commonPersonsIds.Contains(person.Id))
                    continue;

                if (person.IsUndefined)
                {

                    // Undefined person was mapped, so it already exists in FamilyTree
                    if (mappedExistingUndefinedPersonsIds.ContainsKey(person.Id))
                    {
                        // Get current undefined person, that was mapped
                        var newUndefinedPersonId = mappedExistingUndefinedPersonsIds[person.Id];

                        // Get all new relationships for current undefined person
                        ICollection<Relationship> newPersonsRelationships = newRelationshipsForUndefinedPersons.Where(rel => rel.AncestorOrHusbandPersonId == person.Id || rel.DescendantOrWifePersonId == person.Id).ToList();

                        // Set all relationship and marriage ids to null, so they will be created again
                        newPersonsRelationships = relationshipService.NullIdsOfRelationshipsAndMarriages(newPersonsRelationships);

                        foreach (var relationship in newPersonsRelationships)
                        {
                            // Duplicate relationship and create it for the "new" (already existing) Person
                            if (relationship.AncestorOrHusbandPersonId == person.Id)
                                relationship.AncestorOrHusbandPersonId = newUndefinedPersonId;
                            else
                                relationship.DescendantOrWifePersonId = newUndefinedPersonId;

                            // Do not copy informations about marriages
                            relationship.Marriages = null;

                            // Push reference
                            relationship.FamilyTreeRelationship = new List<FamilyTreeRelationship>
                            {
                                new FamilyTreeRelationship { FamilyTreeId = destinationTreeId }
                            };

                            _serviceDependencies._context.DetachAllEntities();
                            await relationshipService.CreateNewRelationshipInFamilyTreeAsync(relationship, destinationTreeId);
                        }

                        // Undefined Person already is in the FamilyTree, no need to add reference
                        continue;
                    }
                    // Undefined person is new for this FamilyTree
                    else
                    {
                        person.AncestorOrHusbandRelationship = null;
                        person.DescendantOrWifeRelationship = null;

                        Person newUndefinedPerson = JsonConvert.DeserializeObject<Person>(JsonConvert.SerializeObject(person));
                        newUndefinedPerson.Id = 0;
                        newUndefinedPerson.FamilyTreePerson = new List<FamilyTreePerson>()
                        {
                            new FamilyTreePerson { FamilyTreeId = destinationTreeId }
                        };

                        // Get relationships for this undefined person
                        newUndefinedPerson.AncestorOrHusbandRelationship = newRelationshipsForUndefinedPersons.Where(rel => rel.AncestorOrHusbandPersonId == person.Id).ToList();
                        newUndefinedPerson.DescendantOrWifeRelationship = newRelationshipsForUndefinedPersons.Where(rel => rel.DescendantOrWifePersonId == person.Id).ToList();

                        // Null all ids in them
                        newUndefinedPerson.AncestorOrHusbandRelationship = relationshipService.NullIdsOfRelationshipsAndMarriages(newUndefinedPerson.AncestorOrHusbandRelationship);
                        newUndefinedPerson.DescendantOrWifeRelationship = relationshipService.NullIdsOfRelationshipsAndMarriages(newUndefinedPerson.DescendantOrWifeRelationship);

                        foreach (var relationship in newUndefinedPerson.AncestorOrHusbandRelationship.Concat(newUndefinedPerson.DescendantOrWifeRelationship))
                        {
                            relationship.FamilyTreeRelationship = null;

                            // Do not copy informations about marriages
                            relationship.Marriages = null;

                            relationship.FamilyTreeRelationship = new List<FamilyTreeRelationship>() { new FamilyTreeRelationship { FamilyTreeId = destinationTreeId } };
                        }

                        _serviceDependencies._context.DetachAllEntities();
                        newUndefinedPerson = await personService.CreatePersonAsync(newUndefinedPerson);
                        var newUndefinedPersonId = newUndefinedPerson.Id;

                        continue;
                    }

                }

                await familytreePersonService.PutPersonToFamilyTreeAsync(new FamilyTreePerson { FamilyTreeId = destinationTreeId, PersonId = person.Id });

            }

            // Check for replacing undefined persons
            await ReplaceUndefinedPersonsByNewlyDefined(destinationTreeId);

            // Change startPerson if necessary
            destinationTree = await ChangeStartPersonAfterConcatenate(sourceTree, destinationTree);

            // Delete persons that can not be displayed
            await DeletePersonsThatCannotBeDisplayed(destinationTreeId);

            return destinationTree;
        }

        /// <summary>
        /// Method decides and provide changing the StartPerson after concatenation
        /// </summary>
        private async Task<FamilyTree> ChangeStartPersonAfterConcatenate(FamilyTree sourceTree, FamilyTree destinationTree)
        {
            var relationshipService = ResolveService<IRelationshipService>();

            var currentStartPersonRelationships = await relationshipService.GetRelationshipsInFamilyTreeForSpecificPersonAsync(destinationTree.Id, destinationTree.StartPersonId);

            // current startPerson has newly ancestors --> actualize startPerson
            if (currentStartPersonRelationships.Any(rel => rel.DescendantOrWifePersonId == destinationTree.StartPersonId && rel.Type != RelationshipTypesEnum.isInMarriageWith))
            {
                destinationTree.StartPersonId = sourceTree.StartPersonId;
                _serviceDependencies._context.DetachAllEntities();
                await UpdateFamilyTreeAsync(destinationTree);
            }
            else
            {
                var marriages = currentStartPersonRelationships.Where(rel => rel.Type == RelationshipTypesEnum.isInMarriageWith).ToList();
                // New Start Person may be ancestor of the spouse
                if (marriages.Count == 1)
                {
                    int spouseId;

                    // Get Id of spouse
                    if (marriages.First().DescendantOrWifePersonId == destinationTree.StartPersonId)
                        spouseId = marriages.First().AncestorOrHusbandPersonId;
                    else
                        spouseId = marriages.First().DescendantOrWifePersonId;

                    var currentSpousePersonRelationships = await relationshipService.GetRelationshipsInFamilyTreeForSpecificPersonAsync(destinationTree.Id, spouseId);

                    // check if spouse has any new ancestor
                    if (currentSpousePersonRelationships.Any(rel => rel.DescendantOrWifePersonId == spouseId && rel.Type != RelationshipTypesEnum.isInMarriageWith))
                    {
                        destinationTree.StartPersonId = sourceTree.StartPersonId;
                        _serviceDependencies._context.DetachAllEntities();
                        await UpdateFamilyTreeAsync(destinationTree);
                    }
                }
            }

            return destinationTree;
        }

        /// <summary>
        /// Method will "replace" undefined person by defined person if old relationships are subset of the new ones
        /// </summary>
        private async Task ReplaceUndefinedPersonsByNewlyDefined(int destinationTreeId)
        {
            var relationshipService = ResolveService<IRelationshipService>();
            var personService = ResolveService<IPersonService>();

            var destinationTree = await GetFamilyTreeForDuplicationByIdAsync(destinationTreeId);

            var allUndefinedPersons = destinationTree.FamilyTreePerson.Where(fp => fp.Person.IsUndefined).Select(fp => fp.Person).ToList();

            foreach (var undefinedPerson in allUndefinedPersons)
            {
                var undefinedPersonRelationships = await relationshipService.GetRelationshipsInFamilyTreeForSpecificPersonAsync(destinationTreeId, undefinedPerson.Id);

                int defaultSpouseId;
                IEnumerable<Relationship> defaultSpouseRelationships;
                ICollection<Relationship> defaultSpouseMarriages;
                ICollection<Person> definedSpouses;

                if (undefinedPerson.IsFemale)
                {
                    // Get source person of marriage (defined person that has marriage with undefined)
                    defaultSpouseId = undefinedPersonRelationships.Where(rel => rel.Type == RelationshipTypesEnum.isInMarriageWith).Select(rel => rel.AncestorOrHusbandPersonId).FirstOrDefault();

                    defaultSpouseRelationships = await relationshipService.GetRelationshipsInFamilyTreeForSpecificPersonAsync(destinationTreeId, defaultSpouseId);
                    defaultSpouseMarriages = defaultSpouseRelationships.Where(rel => rel.Type == RelationshipTypesEnum.isInMarriageWith && !rel.DescendantOrWifePerson.IsUndefined).ToList();

                    definedSpouses = defaultSpouseMarriages.Select(rel => rel.DescendantOrWifePerson).ToList();
                }
                else
                {
                    // Get source person of marriage (defined person that has marriage with undefined)
                    defaultSpouseId = undefinedPersonRelationships.Where(rel => rel.Type == RelationshipTypesEnum.isInMarriageWith).Select(rel => rel.DescendantOrWifePersonId).FirstOrDefault();

                    defaultSpouseRelationships = await relationshipService.GetRelationshipsInFamilyTreeForSpecificPersonAsync(destinationTreeId, defaultSpouseId);
                    defaultSpouseMarriages = defaultSpouseRelationships.Where(rel => rel.Type == RelationshipTypesEnum.isInMarriageWith && !rel.AncestorOrHusbandPerson.IsUndefined).ToList();

                    definedSpouses = defaultSpouseMarriages.Select(rel => rel.AncestorOrHusbandPerson).ToList();
                }

                foreach (var spouse in definedSpouses)
                {
                    var undefinedPersonIsSubset = true;

                    var spouseRel = await relationshipService.GetRelationshipsInFamilyTreeForSpecificPersonAsync(destinationTreeId, spouse.Id);

                    foreach (var undefinedRelationship in undefinedPersonRelationships)
                    {
                        // Undefined person is Ancestor
                        if (undefinedRelationship.AncestorOrHusbandPersonId == undefinedPerson.Id && !undefinedRelationship.DescendantOrWifePerson.IsUndefined)
                        {
                            // Existing person does not have alternative for this relationship
                            if (!spouseRel.Any(rel => rel.DescendantOrWifePersonId == undefinedRelationship.DescendantOrWifePersonId
                                                   && rel.AncestorOrHusbandPersonId == spouse.Id && rel.Type == undefinedRelationship.Type))
                            {
                                undefinedPersonIsSubset = false;
                                break;
                            }
                        }
                        // Undefined person is Descendant
                        else if (undefinedRelationship.DescendantOrWifePersonId == undefinedPerson.Id && !undefinedRelationship.AncestorOrHusbandPerson.IsUndefined)
                        {
                            // Existing person does not have alternative for this relationship
                            if (!spouseRel.Any(rel => rel.AncestorOrHusbandPersonId == undefinedRelationship.AncestorOrHusbandPersonId
                                                   && rel.DescendantOrWifePersonId == spouse.Id && rel.Type == undefinedRelationship.Type))
                            {
                                undefinedPersonIsSubset = false;
                                break;
                            }
                        }
                    }

                    // Relationships of undefined person are only subset of existing person, that was in source --> will replace undefined person
                    if (undefinedPersonIsSubset)
                    {
                        await personService.RemovePersonFromFamilyTreeAsync(undefinedPerson.Id, destinationTreeId);
                    }
                }
            }

        }

        /// <summary>
        /// Method validates if two FamilyTrees can be concatenated
        /// </summary>
        private async Task<bool> CanConcatenateAsync(int sourceTreeId, int destinationTreeId)
        {
            FamilyTree sourceTree = await GetFamilyTreeForDuplicationByIdAsync(sourceTreeId);
            FamilyTree destinationTree = await GetFamilyTreeForDuplicationByIdAsync(destinationTreeId);

            var (commonPersonsIds, commonCollisionsIds, commonRelationshipsIds) = GetMutualReferencesForTwoFamilyTrees(sourceTree, destinationTree);

            // FamilyTrees need to have at least one common person
            if (commonPersonsIds.Count() == 0)
                return false;

            var relationshipService = ResolveService<IRelationshipService>();
            var personService = ResolveService<IPersonService>();

            if (commonPersonsIds.Count() != 1)
            {
                var differentTribePersonId = 0;

                foreach (var personId in commonPersonsIds)
                {
                    _serviceDependencies._context.DetachAllEntities();

                    // Person belongs to the tribe in source familytree
                    if (await BelongPersonToTribeAsync(personId, sourceTreeId))
                    {
                        // Person does not belong to the tribe in destination familytree
                        if (await BelongPersonToTribeAsync(personId, destinationTreeId))
                            continue;
                        else
                        {
                            if (differentTribePersonId != 0)
                            {
                                var personRelationship = await relationshipService.GetRelationshipsInFamilyTreeForSpecificPersonAsync(destinationTreeId, personId);

                                // Only marriages for one person are common (allowed)
                                if (personRelationship.Any(rel => rel.Type == RelationshipTypesEnum.isInMarriageWith && (rel.AncestorOrHusbandPersonId == differentTribePersonId || rel.DescendantOrWifePersonId == differentTribePersonId)))
                                    continue;
                                else
                                    return false;
                            }
                            else
                                differentTribePersonId = personId;
                        }
                    }
                    // Person does not belong to the tribe in source familytree
                    else
                    {
                        // Person does not belong to the tribe in destination familytree
                        if (!await BelongPersonToTribeAsync(personId, destinationTreeId))
                            continue;
                        else
                        {
                            if (differentTribePersonId != 0)
                            {
                                var personRelationship = await relationshipService.GetRelationshipsInFamilyTreeForSpecificPersonAsync(destinationTreeId, personId);

                                // Only marriages for one person are common (allowed)
                                if (personRelationship.Any(rel => rel.Type == RelationshipTypesEnum.isInMarriageWith && (rel.AncestorOrHusbandPersonId == differentTribePersonId || rel.DescendantOrWifePersonId == differentTribePersonId)))
                                    continue;
                                else
                                    return false;
                            }
                            else
                                differentTribePersonId = personId;
                        }
                    }
                }
            }

            // Source FamilyTree must be public and Destination public or nonpublic
            if (sourceTree.Type != FamilyTreeTypesEnum._public || destinationTree.Type == FamilyTreeTypesEnum._private)
                return false;

            foreach (var id in commonCollisionsIds)
            {
                // Get the collision
                var collision = sourceTree.FamilyTreeCollisions.Where(fc => fc.CollisionId == id).Select(fc => fc.Collision).FirstOrDefault();

                // Both familytrees does not have the same relationship in collision -> can not concatenate
                if (!collision.CollisionRelationship.Any(cr => commonRelationshipsIds.Contains(cr.RelationshipId)))
                {
                    return false;
                }
            }

            return true;
        }

        /// <summary>
        /// Method after concatenating deletes persons, that can not be displayed
        /// </summary>
        private async Task DeletePersonsThatCannotBeDisplayed(int destinationTreeId)
        {
            var personService = ResolveService<IPersonService>();
            var relationshipService = ResolveService<IRelationshipService>();

            var allRelationships = await relationshipService.GetRelationshipsByFamilyTreeIdAsync(destinationTreeId);
            var destinationTree = await GetFamilyTreeByIdAsync(destinationTreeId);

            // Get persons that can be displayed
            var displayedPersonIds = await GetDisplayedPersonsIdsInFamilyTree(destinationTree.StartPerson, allRelationships);
            var allPersonIds = await _serviceDependencies._context.FamilyTreePerson.AsNoTracking().Where(fp => fp.FamilyTreeId == destinationTreeId).Select(fp => fp.PersonId).ToListAsync();

            // Get ids of persons that will be removed
            var personsToRemove = allPersonIds.Where(id => !displayedPersonIds.Contains(id));

            // Remove persons that can not be displayed in current familytree
            foreach (var personId in personsToRemove)
                await personService.RemovePersonFromFamilyTreeAsync(personId, destinationTreeId);
        }

        /// <summary>
        /// Method returns ids of persons that can be displayed in FamilyTree
        /// </summary>
        private async Task<List<int>> GetDisplayedPersonsIdsInFamilyTree(Person person, IEnumerable<Relationship> allRelationships)
        {
            var ids = new List<int>();
            ids.Add(person.Id);

            var spousesIds = allRelationships.Where(rel => rel.Type == RelationshipTypesEnum.isInMarriageWith
                                                     && (rel.AncestorOrHusbandPersonId == person.Id || rel.DescendantOrWifePersonId == person.Id))
                                             .Select(rel => (person.IsFemale == true ? rel.AncestorOrHusbandPersonId : rel.DescendantOrWifePersonId));

            foreach (var spouseId in spousesIds)
            {
                ids.Add(spouseId);

                var motherChildren = allRelationships.Where(rel => rel.AncestorOrHusbandPersonId == (person.IsFemale ? person.Id : spouseId) && rel.Type == RelationshipTypesEnum.isMotherOf)
                                                     .Select(rel => rel.DescendantOrWifePersonId);
                var fatherChildren = allRelationships.Where(rel => rel.AncestorOrHusbandPersonId == (person.IsFemale ? spouseId : person.Id) && rel.Type == RelationshipTypesEnum.isFatherOf)
                                                     .Select(rel => rel.DescendantOrWifePersonId); ;

                var commonChildren = motherChildren.Where(rel => fatherChildren.Contains(rel));

                foreach (var childId in commonChildren)
                {
                    var child = allRelationships.Where(rel => rel.DescendantOrWifePersonId == childId).Select(rel => rel.DescendantOrWifePerson).FirstOrDefault();

                    var innerIds = await GetDisplayedPersonsIdsInFamilyTree(child, allRelationships);
                    ids = ids.Concat(innerIds).ToList();
                }
            }

            return ids;
        }

        /// <summary>
        /// Method returns arrays with common references for two FamilyTrees
        /// </summary>
        private (List<int> commonPersonsIds, List<int> commonRelationshipsIds, List<int> commonCollisionsIds) GetMutualReferencesForTwoFamilyTrees(FamilyTree sourceTree, FamilyTree destinationTree)
        {
            // Get ids of all persons, that have both familytrees in common
            var sourceTreePersonsIds = sourceTree.FamilyTreePerson.Select(person => person.PersonId).ToList();
            var destinationTreePersonsIds = destinationTree.FamilyTreePerson.Select(person => person.PersonId).ToList();

            var commonPersonsIds = destinationTreePersonsIds.Where(id => sourceTreePersonsIds.Contains(id)).ToList();

            // Get ids of all collisions, that have both familytrees in common
            var sourceTreeCollisionsIds = sourceTree.FamilyTreeCollisions.Select(collision => collision.CollisionId).ToList();
            var destinationTreeCollisionsIds = destinationTree.FamilyTreeCollisions.Select(collision => collision.CollisionId).ToList();

            var commonCollisionsIds = destinationTreeCollisionsIds.Where(id => sourceTreeCollisionsIds.Contains(id)).ToList();

            // Get ids of all relationships, that have both familytrees in common
            var sourceTreeRelationshipsIds = sourceTree.FamilyTreeRelationship.Select(relationship => relationship.RelationshipId).ToList();
            var destinationTreeRelationshipsIds = destinationTree.FamilyTreeRelationship.Select(relationship => relationship.RelationshipId).ToList();

            var commonRelationshipsIds = destinationTreeRelationshipsIds.Where(id => sourceTreeRelationshipsIds.Contains(id)).ToList();

            return (commonPersonsIds, commonCollisionsIds, commonRelationshipsIds);
        }

        /// <summary>
        /// Method tests if person belongs to the tribe of concrete familytree
        /// </summary>
        private async Task<bool> BelongPersonToTribeAsync(int personId, int familyTreeId)
        {
            var relationshipService = ResolveService<IRelationshipService>();
            var personService = ResolveService<IPersonService>();

            var person = await personService.GetPersonByIdAsync(personId);

            // Get all relationships in the familytree for tested person
            var personRelationshipsInFamilyTree = await relationshipService.GetRelationshipsInFamilyTreeForSpecificPersonAsync(familyTreeId, personId);

            var hasAncestorInFamilyTree = personRelationshipsInFamilyTree.Any(rel => (rel.Type == RelationshipTypesEnum.isMotherOf
                                                                                   || rel.Type == RelationshipTypesEnum.isFatherOf)
                                                                                   && rel.DescendantOrWifePersonId == personId);
            // Has mother or father --> belongs to the tribe 
            if(hasAncestorInFamilyTree)
                return true;

            var allSpouses = personRelationshipsInFamilyTree.Where(rel => rel.Type == RelationshipTypesEnum.isInMarriageWith);

            // Has 0 or more than 1 spouses --> belongs to the tribe
            if (allSpouses.Count() > 1 || allSpouses.Count() == 0)
                return true;

            // Get Id of spouse
            int? personsSpouseId = personRelationshipsInFamilyTree.Where(rel => rel.Type == RelationshipTypesEnum.isInMarriageWith)
                                                                 .Select(rel => (person.IsFemale) ? rel.AncestorOrHusbandPersonId : rel.DescendantOrWifePersonId)
                                                                 .FirstOrDefault();

            if (personsSpouseId != null)
            {
                var spouseRelationships = await relationshipService.GetRelationshipsInFamilyTreeForSpecificPersonAsync(familyTreeId, (int)personsSpouseId);

                var spouseAllMarriages = spouseRelationships.Where(rel => rel.Type == RelationshipTypesEnum.isInMarriageWith).ToList();
                
                // Tested spouse has more spouses (not only tested person) --> does not belong to the tribe
                if (spouseAllMarriages.Count() != 1)
                {
                    return false;
                }
                // Tested person belongs to the tribe or is a single spouse
                else
                {
                    var spouseHasAncestorsInFamilyTree = spouseRelationships.Any(rel => (rel.Type == RelationshipTypesEnum.isMotherOf
                                                                                      || rel.Type == RelationshipTypesEnum.isFatherOf)
                                                                                      && rel.DescendantOrWifePersonId == personsSpouseId);
                    
                    // Spouse of the tested person has parents --> tested person does not belong to the tribe
                    if(spouseHasAncestorsInFamilyTree)
                    {
                        return false;
                    }
                    else
                    {
                        return true;
                    }
                }
            }

            return false;

        }

        #endregion
    }
}
