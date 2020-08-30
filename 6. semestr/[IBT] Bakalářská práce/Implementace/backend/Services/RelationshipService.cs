/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    RelationshipService.cs                                */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using Entity.Enums;
using Entity.Relationships;
using Microsoft.EntityFrameworkCore;
using Services.Base;
using Services.Exceptions;
using Services.Interfaces;
using Services.Interfaces.RelationshipInterfaces;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace Services
{
    public class RelationshipService : BaseService<Relationship>, IRelationshipService
    {
        public RelationshipService(IServiceDependencies _serviceDependencies) : base(_serviceDependencies)
        {
        }

        #region Methods for getting specific relationship(s)

        /// <summary>
        /// Gets Relationship by Id
        /// </summary>
        public async Task<Relationship> GetRelationshipByIdAsync(int id)
        {
            var result = await _serviceDependencies._context.Relationships
                                                            .AsNoTracking()
                                                            .Include(rel => rel.Marriages)
                                                            .Include(rel => rel.FamilyTreeRelationship)
                                                            .FirstOrDefaultAsync(item => item.Id == id);
            if (result == null)
                throw new ObjectNotFoundException(nameof(Relationship), id);
            else
                return result;
        }

        /// <summary>
        /// Gets all relationships for specific Person (not only in one FamilyTree)
        /// </summary>
        public async Task<IEnumerable<Relationship>> GetRelationshipsListByPersonIdAsync(int Id)
        {
            var result = await _serviceDependencies._context.Relationships
                                                            .AsNoTracking()
                                                            .Where(r => (r.AncestorOrHusbandPersonId == Id)
                                                                     || (r.DescendantOrWifePersonId == Id))
                                                            .Include(marriage => marriage.Marriages)
                                                            .Include(r => r.CollisionRelationship)
                                                            .ThenInclude(cr => cr.Collision)
                                                            .Include(r => r.AncestorOrHusbandPerson)
                                                            .Include(r => r.DescendantOrWifePerson)
                                                            .Include(r => r.FamilyTreeRelationship)
                                                            .ToListAsync();
            return result;
        }

        /// <summary>
        /// Gets all relationships for concrete Person and specific FamilyTree
        /// </summary>
        public async Task<IEnumerable<Relationship>> GetRelationshipsInFamilyTreeForSpecificPersonAsync(int familyTreeId, int personId)
        {
            var allRelationshipsInTree = await _serviceDependencies._context.FamilyTreeRelationship
                                                                            .Where(ft => ft.FamilyTreeId == familyTreeId)
                                                                            .Include(ft => ft.Relationship)
                                                                            .ThenInclude(rel => rel.Marriages)
                                                                            .Include(ft => ft.Relationship)
                                                                            .ThenInclude(rel => rel.AncestorOrHusbandPerson)
                                                                            .Include(ft => ft.Relationship)
                                                                            .ThenInclude(rel => rel.DescendantOrWifePerson)
                                                                            .Select(reference => reference.Relationship)
                                                                            .AsNoTracking()
                                                                            .ToListAsync();

            var result =  allRelationshipsInTree.Where(rel => ((rel.AncestorOrHusbandPersonId == personId)
                                                            || (rel.DescendantOrWifePersonId == personId)))
                                                .ToList();

            return result;
        }

        /// <summary>
        /// Gets relationship by participated persons
        /// </summary>
        public async Task<Relationship> GetRelationshipByParticipatedPersonsAsync(Relationship item)
        {
            var result = await _serviceDependencies._context.Relationships
                                                            .AsNoTracking()
                                                            .Include(r => r.FamilyTreeRelationship)
                                                            .Include(r => r.CollisionRelationship)
                                                            .ThenInclude(cr => cr.Collision)
                                                            .FirstOrDefaultAsync(r => (r.AncestorOrHusbandPersonId == item.AncestorOrHusbandPersonId)
                                                                                   && (r.DescendantOrWifePersonId == item.DescendantOrWifePersonId)
                                                                                   && (r.Type == item.Type));

            return result;
        }

        /// <summary>
        /// Gets list of relationships using ids of participated persons
        /// </summary>
        public async Task<IEnumerable<Relationship>> GetRelationshipsListByParticipatedPersonsAsync(Relationship item)
        {
            var result = await _serviceDependencies._context.Relationships
                                                            .AsNoTracking()
                                                            .Where(r => (r.AncestorOrHusbandPersonId == item.AncestorOrHusbandPersonId)
                                                                     && (r.DescendantOrWifePersonId == item.DescendantOrWifePersonId)
                                                                     && (r.Type == item.Type))
                                                            .Include(r => r.Marriages)
                                                            .Include(r => r.FamilyTreeRelationship)
                                                            .Include(r => r.CollisionRelationship)
                                                            .ThenInclude(cr => cr.Collision)
                                                            .ToListAsync();
            return result;
        }

        /// <summary>
        /// Gets list of relationships by FamilyTree Id
        /// </summary>
        public async Task<IEnumerable<Relationship>> GetRelationshipsByFamilyTreeIdAsync(int id)
        {
            var result = await _serviceDependencies._context.FamilyTreeRelationship
                                       .Where(ft => ft.FamilyTreeId == id)
                                       .Include(reference => reference.Relationship)
                                       .ThenInclude(relationship => relationship.AncestorOrHusbandPerson)
                                       .ThenInclude(person => person.PersonNames)
                                       .Include(reference => reference.Relationship)
                                       .ThenInclude(relationship => relationship.DescendantOrWifePerson)
                                       .ThenInclude(person => person.PersonNames)
                                       .AsNoTracking().ToListAsync();

            return result.Select(fr => fr.Relationship);
        }

        #endregion

        #region CRUD operations

        public async Task<Relationship> CreateRelationshipAsync(Relationship item)
        {
            return await Create(item);
        }

        public async Task<Relationship> UpdateRelationshipAsync(Relationship item)
        {
            return await Update(item);
        }

        public async Task<Relationship> DeleteRelationshipAsync(int id)
        {
            // First of all check if deleting will affect collisions
            var collisionService = ResolveService<ICollisionService>();
            await collisionService.CheckForCollisionDeleteAsync(id);

            // Delete
            return await Delete(id);
        }

        #endregion

        /// <summary>
        /// Method controls proces of adding new Relationship, provides adding all necessary references
        /// </summary>
        public async Task<Relationship> CreateNewRelationshipInFamilyTreeAsync(Relationship item, int familyTreeId)
        {
            await GeneralRelationshipValidation(item);

            var collisionService = ResolveService<ICollisionService>();

            // Relationship is type Mother or Father
            if ((item.Type == RelationshipTypesEnum.isMotherOf) || (item.Type == RelationshipTypesEnum.isFatherOf))
            {
                // This type of relationship should not have marriage data
                if (item.Marriages != null)
                    item.Marriages = null;

                var existingRelationship = await GetRelationshipByParticipatedPersonsAsync(item);

                // Relationship already exists, just Add id to your tree
                if (existingRelationship != null)
                {
                    await AddExistingRelationshipToFamilyTreeAsync(existingRelationship.Id, familyTreeId);

                    return await GetRelationshipByIdAsync(existingRelationship.Id);
                }
                else
                {
                    _serviceDependencies._context.DetachAllEntities();
                    var result = await CreateRelationshipAsync(item);
                    await collisionService.CollisionDetectionAsync(result);
                    return result;
                }
            }
            else if (item.Type == RelationshipTypesEnum.isInMarriageWith)
            {
                //await ValidateMarriageRelationshipAsync(item);

                var existingRelationshipsMarriages = await GetRelationshipsListByParticipatedPersonsAsync(item);

                // Records for Marriage between those two persons already exists
                if (existingRelationshipsMarriages.Count() != 0)
                {
                    Relationship existingRelationship = null;

                    if (item.Marriages.Count() == 0)
                    {
                        existingRelationship = existingRelationshipsMarriages.FirstOrDefault(r => (r.Marriages.Count() == 0));
                    }
                    // Find if already exists relationship with only one and the same Marriage
                    else if (item.Marriages.Count() == 1)
                    {
                        existingRelationship = existingRelationshipsMarriages.FirstOrDefault(r => (r.Marriages.Count() == 1)
                                                                                                && (r.Marriages.First().MarriageDate == item.Marriages.First().MarriageDate)
                                                                                                && (r.Marriages.First().MarriageAddress == item.Marriages.First().MarriageAddress));
                    }

                    // Relationship exists -> just add reference to your FamilyTree
                    if (existingRelationship != null)
                    {
                        await AddExistingRelationshipToFamilyTreeAsync(existingRelationship.Id, familyTreeId);

                        return await GetRelationshipByIdAsync(existingRelationship.Id);
                    }
                    //Create Relationship
                    else
                    {
                        _serviceDependencies._context.DetachAllEntities();
                        var result = await CreateRelationshipAsync(item);
                        await collisionService.CollisionDetectionAsync(item);
                        return result;
                    }
                }
                // No records for marriage between these two persons --> create new one
                else
                {
                    var result = await CreateRelationshipAsync(item);
                    await collisionService.CollisionDetectionAsync(item);
                    return result;
                }
            }


            return item;
        }

        /// <summary>
        /// Method adds reference between existing relationship and familytree
        /// also adds references on all collisions that are part of the relationship
        /// </summary>
        public async Task AddExistingRelationshipToFamilyTreeAsync(int relationshipId, int familyTreeId)
        {
            // Services definition
            var familytreeRelationshipService = ResolveService<IFamilyTreeRelationshipService>();
            var familyTreeCollisionService = ResolveService<IFamilyTreeCollisionService>();
            var collisionService = ResolveService<ICollisionService>();

            // Create new many-to-many relationship between Relationship and FamilyTree
            await familytreeRelationshipService.PutRelationshipToFamilyTreeAsync(new FamilyTreeRelationship
            {
                FamilyTreeId = familyTreeId,
                RelationshipId = relationshipId
            });

            _serviceDependencies._context.DetachAllEntities();

            var collisions = await collisionService.GetCollisionsByRelationshipIdAsync(relationshipId);

            // Add all collisions to new FamilyTree
            foreach (var col in collisions)
            {
                await familyTreeCollisionService.CreateAsync(new FamilyTreeCollision
                {
                    FamilyTreeId = familyTreeId,
                    CollisionId = col.Id,
                    IsSolved = false,
                    SolutionDate = null
                });
            }
        }

        /// <summary>
        /// Helper functions that decides if is necessary to delete relationship or just delete reference
        /// </summary>
        public async Task<Relationship> RemoveReferenceOrDeleteRelationship(Relationship relationship, int familytreeId)
        {
            var familytreeRelationshipService = ResolveService<IFamilyTreeRelationshipService>();
            var relationshipService = ResolveService<IRelationshipService>();
            var collisionService = ResolveService<ICollisionService>();

            var referenceOnThisFamilyTree = relationship.FamilyTreeRelationship.FirstOrDefault(rel => rel.FamilyTreeId == familytreeId);

            if (referenceOnThisFamilyTree != null && relationship.FamilyTreeRelationship.Count() == 1)
            {
                try
                {
                    await collisionService.RemoveCollisionsReferenceForSpecificFamilyTreeAndRelationship(relationship.Id, familytreeId);

                    await relationshipService.DeleteRelationshipAsync(relationship.Id);
                }
                // Relationship was already deleted by spouse
                catch (ObjectRelationshipNotFoundException) { }
                catch (ObjectNotFoundException) { }
            }
            else if (referenceOnThisFamilyTree != null)
            {
                try
                {
                    await collisionService.RemoveCollisionsReferenceForSpecificFamilyTreeAndRelationship(relationship.Id, familytreeId);

                    await familytreeRelationshipService.RemoveRelationshipfromFamilyTreeAsync(referenceOnThisFamilyTree);
                }
                // Reference might be already deleted by relationship
                catch (ObjectRelationshipNotFoundException) { }
            }

            return relationship;
        }

        /// <summary>
        /// Methods nulls ids of passed relationships and marriages, useful when they need to be created again 
        /// </summary>
        public ICollection<Relationship> NullIdsOfRelationshipsAndMarriages(ICollection<Relationship> relationships)
        {
            if (relationships == null)
                return null;

            foreach (var relationship in relationships)
            {
                relationship.Id = 0;
                relationship.AncestorOrHusbandPerson = null;
                relationship.DescendantOrWifePerson = null;

                if (relationship.Marriages != null)
                {
                    foreach (var marriage in relationship.Marriages)
                    {
                        marriage.RelationshipId = 0;
                        marriage.Id = 0;
                    }
                }
            }

            return relationships;
        }

        #region Validation methods

        /// <summary>
        /// Basic validation of received relationship
        /// </summary>
        private async Task GeneralRelationshipValidation(Relationship item)
        {
            // Relationship strictly requires two persons, so any id cannot be null
            if (item.AncestorOrHusbandPersonId == 0 || item.DescendantOrWifePersonId == 0)
                throw new InvalidObjectRelationshipException("Id of any person cannot be null.", nameof(RelationshipService), item.AncestorOrHusbandPersonId, item.DescendantOrWifePersonId);
            // Person Ids are the same
            else if (item.AncestorOrHusbandPersonId == item.DescendantOrWifePersonId)
                throw new InvalidObjectException("A Person cannot have relationship with himself/herself.", nameof(Relationship), item.AncestorOrHusbandPersonId);
            // FamilyTreeId is missing or have more records
            else if (item.FamilyTreeRelationship == null || item.FamilyTreeRelationship.Count() != 1)
                throw new InvalidObjectException("When creating relationship just one id of Familytree is needed.", nameof(Relationship), 0);

            // Check existence of Persons
            if (!ExistsInDb<Person>(item.AncestorOrHusbandPersonId))
                throw new ObjectNotFoundException(nameof(Person), item.AncestorOrHusbandPersonId);
            else if (!ExistsInDb<Person>(item.DescendantOrWifePersonId))
                throw new ObjectNotFoundException(nameof(Person), item.DescendantOrWifePersonId);

            var personService = ResolveService<IPersonService>();

            var ancestorOrHusband = await personService.GetPersonByIdAsync(item.AncestorOrHusbandPersonId);
            var descendantOrWife = await personService.GetPersonByIdAsync(item.DescendantOrWifePersonId);

            // Check gender of persons
            if (!(item.Type == RelationshipTypesEnum.isInMarriageWith && !ancestorOrHusband.IsFemale && descendantOrWife.IsFemale)
             && !(item.Type == RelationshipTypesEnum.isFatherOf && !ancestorOrHusband.IsFemale)
             && !(item.Type == RelationshipTypesEnum.isMotherOf && ancestorOrHusband.IsFemale))
            {
                throw new InvalidObjectException("Type does not correspond with gender of persons.", nameof(Relationship), 0);
            }

            // Check existence of FamilyTree
            if (!ExistsInDb<FamilyTree>(item.FamilyTreeRelationship.First().FamilyTreeId))
                throw new ObjectNotFoundException(nameof(FamilyTree), item.FamilyTreeRelationship.First().FamilyTreeId);
        }

        #endregion
    }
}
