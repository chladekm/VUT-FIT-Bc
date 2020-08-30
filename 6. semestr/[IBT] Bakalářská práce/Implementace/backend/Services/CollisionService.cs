/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    CollisionService.cs                                   */
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
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace Services
{
    public class CollisionService : BaseService<Collision>, ICollisionService
    {

        public CollisionService(IServiceDependencies _serviceDependencies) : base(_serviceDependencies)
        {
        }

        public async Task<int> GetCountOfNotSolvedCollisionsByFamilyTreeIdAsync(int id)
        {
            return await _serviceDependencies._context.FamilyTreeCollision.CountAsync(fc => fc.FamilyTreeId == id && fc.IsSolved == false);
        }

        #region Methods for getting specific collision(s)

        public async Task<IEnumerable<Collision>> GetCollisionsByFamilyTreeIdAsync(int id)
        {
            var references = await _serviceDependencies._context.FamilyTreeCollision.Where(fr => fr.FamilyTreeId == id)
                                                                                       .AsNoTracking().ToListAsync();

            var collisionIdList = references.Select(r => r.CollisionId).OfType<int>().ToList();

            var collisions = await _serviceDependencies._context.Collisions.Where(person => (collisionIdList.Contains(person.Id)))
                                                                            .AsNoTracking()
                                                                            .Include(collision => collision.FamilyTreeCollision)
                                                                            .Include(collision => collision.CollisionRelationship)
                                                                            .ThenInclude(cr => cr.Relationship)
                                                                            .ToListAsync();
            return collisions;
        }

        public async Task<Collision> GetCollisionByIdAsync(int id)
        {
            var result = await _serviceDependencies._context.Collisions.AsNoTracking()
                                                                        .Include(collision => collision.CollisionRelationship)
                                                                        .ThenInclude(cr => cr.Relationship)
                                                                        .Include(collision => collision.FamilyTreeCollision)
                                                                        .FirstOrDefaultAsync(col => col.Id == id);

            return result;
        }

        public async Task<IEnumerable<Collision>> GetCollisionsByRelationshipIdAsync(int relationshipId)
        {
            var result = await _serviceDependencies._context.CollisionRelationship.AsNoTracking()
                                                                                  .Where(cr => cr.RelationshipId == relationshipId)
                                                                                  .Include(cr => cr.Collision)
                                                                                  .ThenInclude(collision => collision.CollisionRelationship)
                                                                                  .Select(cr => cr.Collision)
                                                                                  .ToListAsync();
            return result;
        }

        #endregion

        #region CRUD operations

        public async Task<Collision> CreateCollisionAsync(Collision item)
        {
            return await Create(item);
        }

        public async Task<Collision> UpdateCollisionAsync(Collision item)
        {
            _serviceDependencies._context.Collisions.Update(item);
            await _serviceDependencies._context.SaveChangesAsync();

            return item;
        }

        public async Task<Collision> DeleteCollisionAsync(int id)
        {
            return await Delete(id);
        }

        #endregion

        /// <summary>
        /// Method marks collision as solved with current date
        /// </summary>
        public async Task<FamilyTreeCollision> ToggleCollisionSolutionAsync(FamilyTreeCollision item)
        {
            var familytreeCollisionService = ResolveService<IFamilyTreeCollisionService>();

            if (item.IsSolved)
                item.SolutionDate = DateTime.Now;
            else
                item.SolutionDate = null;

            return await familytreeCollisionService.UpdateAsync(item);
        }

        /// <summary>
        /// Method is called when deleting relationship, if collision has only two relationships 
        /// and one of them will be deleted, then collision will be deleted too
        /// </summary>
        public async Task CheckForCollisionDeleteAsync(int relationshipId)
        {
            var collisions = await GetCollisionsByRelationshipIdAsync(relationshipId);

            foreach (var collision in collisions)
            {
                //  Collision has two relationships -> one will be deleted -> will have 1 --> delete Collision 
                if (collision.CollisionRelationship.Count() == 2)
                {
                    await Delete(collision.Id);
                }
                // This type of collision may have more marriages in it
                else if (collision.Type == CollisionTypesEnum.marriageOrAncestor)
                {
                    var relationshipService = ResolveService<IRelationshipService>();
                    // Get relationship, that will be deleted
                    var relationship = await relationshipService.GetRelationshipByIdAsync(relationshipId);

                    var collisionWithRelationships = await GetCollisionByIdAsync(collision.Id);
                    var countMarriageRelationships = collisionWithRelationships.CollisionRelationship.Where(col => col.Relationship.Type == RelationshipTypesEnum.isInMarriageWith).Count();

                    // When collision has only marriages --> delete
                    if (countMarriageRelationships == (collisionWithRelationships.CollisionRelationship.Count() - 1) && relationship.Type != RelationshipTypesEnum.isInMarriageWith)
                        await Delete(collision.Id);
                }
            }
        }

        /// <summary>
        /// Function detects collisions for newly created relationship
        /// </summary>
        public async Task CollisionDetectionAsync(Relationship item)
        {
            var personService = ResolveService<IPersonService>();
            var relationshipService = ResolveService<IRelationshipService>();

            var ancestor = await personService.GetPersonByIdAsync(item.AncestorOrHusbandPersonId);
            var descendant = await personService.GetPersonByIdAsync(item.DescendantOrWifePersonId);

            // If one of persons is private, then there is no testing for collision needed
            if (ancestor.IsPrivate || descendant.IsPrivate)
                return;

            #region Generation conflict testing 

            // Testing for "generation conflict" type of collision
            if (item.Type == RelationshipTypesEnum.isInMarriageWith)
            {
                // Relationship where Wife is mother of Husband
                var relationshipToSearch = new Relationship
                {
                    AncestorOrHusbandPersonId = item.DescendantOrWifePersonId,
                    DescendantOrWifePersonId = item.AncestorOrHusbandPersonId,
                    Type = RelationshipTypesEnum.isMotherOf
                };

                // Find out if some exists
                var wifeIsAlsoMotherRelationship = await relationshipService.GetRelationshipByParticipatedPersonsAsync(relationshipToSearch);

                // Relationship where Husband is father of Wife
                relationshipToSearch = new Relationship
                {
                    AncestorOrHusbandPersonId = item.AncestorOrHusbandPersonId,
                    DescendantOrWifePersonId = item.DescendantOrWifePersonId,
                    Type = RelationshipTypesEnum.isFatherOf
                };

                // Find out if some exists
                var husbandIsAlsoFatherRelationships = await relationshipService.GetRelationshipByParticipatedPersonsAsync(relationshipToSearch);

                // Some conflict relationship exists
                if (wifeIsAlsoMotherRelationship != null || husbandIsAlsoFatherRelationships != null)
                {
                    int collisionId;

                    // Get collisionId if some relevant collision exists
                    if (wifeIsAlsoMotherRelationship != null)
                    {
                        collisionId = wifeIsAlsoMotherRelationship.CollisionRelationship
                                                                   .Where(cr => cr.Collision.Type == CollisionTypesEnum.marriageOrAncestor)
                                                                   .Select(cr => cr.CollisionId).FirstOrDefault();
                    }
                    else
                    {
                        collisionId = husbandIsAlsoFatherRelationships.CollisionRelationship
                                                                      .Where(cr => cr.Collision.Type == CollisionTypesEnum.marriageOrAncestor)
                                                                      .Select(cr => cr.CollisionId).FirstOrDefault();
                    }

                    // Collision already exists
                    if (collisionId != 0)
                    {
                        await AddReferencesToExistingCollisionAsync(item, collisionId);
                    }
                    // Create new collision
                    else
                    {
                        Collision collision = new Collision { };

                        collision.Type = CollisionTypesEnum.marriageOrAncestor;

                        // Create references
                        collision.CollisionRelationship = new List<CollisionRelationship>();
                        collision.FamilyTreeCollision = new List<FamilyTreeCollision>();


                        if (wifeIsAlsoMotherRelationship != null)
                        {
                            collision.CollisionRelationship.Add(new CollisionRelationship { RelationshipId = wifeIsAlsoMotherRelationship.Id });

                            // Add collision to all familytrees that has relationships
                            foreach (var reference in wifeIsAlsoMotherRelationship.FamilyTreeRelationship)
                            {
                                collision.FamilyTreeCollision.Add(new FamilyTreeCollision { FamilyTreeId = reference.FamilyTreeId, IsSolved = false });
                            }
                        }

                        if (husbandIsAlsoFatherRelationships != null)
                        {
                            collision.CollisionRelationship.Add(new CollisionRelationship { RelationshipId = husbandIsAlsoFatherRelationships.Id });

                            // Add collision to all familytrees that has relationships
                            foreach (var reference in husbandIsAlsoFatherRelationships.FamilyTreeRelationship)
                            {
                                collision.FamilyTreeCollision.Add(new FamilyTreeCollision { FamilyTreeId = reference.FamilyTreeId, IsSolved = false });
                            }
                        }

                        // There may be more relationships for same marriage, need to add all of them (item is included)
                        var allMarriagesForSamePersons = await relationshipService.GetRelationshipsListByParticipatedPersonsAsync(item);

                        foreach (var marriageRelationship in allMarriagesForSamePersons)
                        {
                            collision.CollisionRelationship.Add(new CollisionRelationship { RelationshipId = marriageRelationship.Id });

                            // Add collision to all familytrees that has the new one relationship
                            foreach (var reference in marriageRelationship.FamilyTreeRelationship)
                            {
                                collision.FamilyTreeCollision.Add(new FamilyTreeCollision { FamilyTreeId = reference.FamilyTreeId, IsSolved = false });
                            }
                        }

                        _serviceDependencies._context.DetachAllEntities();
                        var result = await CreateCollisionAsync(collision);
                    }

                }
            }
            else if (item.Type == RelationshipTypesEnum.isMotherOf || item.Type == RelationshipTypesEnum.isFatherOf)
            {
                // If are persons of the same gender -> skip (cannot exist marriage between them)
                if (ancestor.IsFemale != descendant.IsFemale)
                {
                    // Relationship where Mother is also wife of Descendant
                    var relationshipToSearch = new Relationship
                    {
                        AncestorOrHusbandPersonId = ((ancestor.IsFemale) ? item.DescendantOrWifePersonId : item.AncestorOrHusbandPersonId),
                        DescendantOrWifePersonId = ((ancestor.IsFemale) ? item.AncestorOrHusbandPersonId : item.DescendantOrWifePersonId),
                        Type = RelationshipTypesEnum.isInMarriageWith
                    };

                    // Find out if some exists
                    var AncestorAlsoSpouseRelationships = await relationshipService.GetRelationshipsListByParticipatedPersonsAsync(relationshipToSearch);

                    if (AncestorAlsoSpouseRelationships != null && AncestorAlsoSpouseRelationships.Count() != 0)
                    {
                        int collisionId = AncestorAlsoSpouseRelationships.First().CollisionRelationship
                                                                   .Where(cr => cr.Collision.Type == CollisionTypesEnum.marriageOrAncestor)
                                                                   .Select(cr => cr.CollisionId).FirstOrDefault();

                        // Collision already exists
                        if (collisionId != 0)
                        {
                            await AddReferencesToExistingCollisionAsync(item, collisionId);
                        }
                        else
                        {
                            Collision collision = new Collision { };

                            collision.Type = CollisionTypesEnum.marriageOrAncestor;

                            // Create references
                            collision.CollisionRelationship = new List<CollisionRelationship>();
                            collision.FamilyTreeCollision = new List<FamilyTreeCollision>();

                            foreach (var relationship in AncestorAlsoSpouseRelationships)
                            {
                                collision.CollisionRelationship.Add(new CollisionRelationship { RelationshipId = relationship.Id });

                                // Add collision to all familytrees that has relationships
                                foreach (var reference in relationship.FamilyTreeRelationship)
                                {
                                    collision.FamilyTreeCollision.Add(new FamilyTreeCollision { FamilyTreeId = reference.FamilyTreeId, IsSolved = false });
                                }
                            }

                            // Add current relationship
                            collision.CollisionRelationship.Add(new CollisionRelationship { RelationshipId = item.Id });

                            foreach (var reference in item.FamilyTreeRelationship)
                            {
                                collision.FamilyTreeCollision.Add(new FamilyTreeCollision { FamilyTreeId = reference.FamilyTreeId, IsSolved = false });
                            }

                            _serviceDependencies._context.DetachAllEntities();
                            var result = await CreateCollisionAsync(collision);

                        }

                    }
                }


            }

            #endregion

            #region Different ancestor conflict testing 

            // Testing for "different ancestor" type of collision
            if (item.Type == RelationshipTypesEnum.isMotherOf || item.Type == RelationshipTypesEnum.isFatherOf)
            {
                var AllPersonRelationships = await relationshipService.GetRelationshipsListByPersonIdAsync(item.DescendantOrWifePersonId);

                var sameRelationships = AllPersonRelationships.Where(rel => rel.Id != item.Id
                                                                  && rel.Type == item.Type
                                                                  && rel.DescendantOrWifePersonId == item.DescendantOrWifePersonId
                                                                  && rel.AncestorOrHusbandPerson.IsUndefined == false);

                // New Collision! (only 2 relationships in collision)
                if (sameRelationships.Count() == 1)
                {
                    Collision collision = new Collision { };

                    if (item.Type == RelationshipTypesEnum.isMotherOf)
                        collision.Type = CollisionTypesEnum.differentMother;
                    else
                        collision.Type = CollisionTypesEnum.differentFather;

                    // Create references
                    collision.CollisionRelationship = new CollisionRelationship[]
                    {
                        new CollisionRelationship { RelationshipId = item.Id },
                        new CollisionRelationship { RelationshipId = sameRelationships.First().Id }
                    };

                    collision.FamilyTreeCollision = new List<FamilyTreeCollision>();

                    // Add collision to all familytrees that has relationships
                    foreach (var reference in item.FamilyTreeRelationship)
                    {
                        collision.FamilyTreeCollision.Add(new FamilyTreeCollision { FamilyTreeId = reference.FamilyTreeId, IsSolved = false });
                    }

                    foreach (var reference in sameRelationships.First().FamilyTreeRelationship)
                    {
                        collision.FamilyTreeCollision.Add(new FamilyTreeCollision { FamilyTreeId = reference.FamilyTreeId, IsSolved = false });
                    }

                    _serviceDependencies._context.DetachAllEntities();
                    var result = await CreateCollisionAsync(collision);

                    return;
                }
                // Collision already exists! (there are more than one relationship of same type -- new relationship is not included)
                else if (sameRelationships.Count() > 1)
                {
                    _serviceDependencies._context.DetachAllEntities();

                    CollisionRelationship collisionRelationship;

                    // Get some relationship, that already is in collision of the same type
                    if (item.Type == RelationshipTypesEnum.isMotherOf)
                        collisionRelationship = sameRelationships.First().CollisionRelationship.FirstOrDefault((x => x.Collision.Type == CollisionTypesEnum.differentMother));
                    else
                        collisionRelationship = sameRelationships.First().CollisionRelationship.FirstOrDefault((x => x.Collision.Type == CollisionTypesEnum.differentFather));

                    // Extract collision Id
                    var collisionId = collisionRelationship.CollisionId;

                    // Add references
                    await AddReferencesToExistingCollisionAsync(item, collisionId);

                    return;
                }
            }

            #endregion

        }

        /// <summary>
        /// Methods removes references between specific FamilyTree and Collisions
        /// that are part of relationships we are removing from FamilyTree
        /// </summary>
        public async Task RemoveCollisionsReferenceForSpecificFamilyTreeAndRelationship(int relationshipId, int familytreeId)
        {
            var collisions = await GetCollisionsByRelationshipIdAsync(relationshipId);

            var familytreeCollisionService = ResolveService<IFamilyTreeCollisionService>();

            foreach (var collision in collisions)
            {
                try
                {
                    await familytreeCollisionService.DeleteAsync(new FamilyTreeCollision
                    {
                        FamilyTreeId = familytreeId,
                        CollisionId = collision.Id
                    });
                }
                // Other relationship may already delete whole collision
                catch (ObjectNotFoundException) { }
            }
        }

        /// <summary>
        /// Method adds FamilyTreeCollision references to existing collision
        /// </summary>
        private async Task AddReferencesToExistingCollisionAsync(Relationship item, int collisionId)
        {
            var collision = await GetCollisionByIdAsync(collisionId);

            // Add collision to all familytrees that has relationships
            foreach (var reference in item.FamilyTreeRelationship)
            {
                collision.FamilyTreeCollision.Add(new FamilyTreeCollision { FamilyTreeId = reference.FamilyTreeId, IsSolved = false });
            }

            // Ad new relationship to collision
            collision.CollisionRelationship.Add(new CollisionRelationship { RelationshipId = item.Id });

            await UpdateCollisionAsync(collision);

            return;
        }

    }

}
