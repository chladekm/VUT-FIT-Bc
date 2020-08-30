/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    MarriageService.cs                                    */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using Entity.Relationships;
using Microsoft.EntityFrameworkCore;
using Services.Base;
using Services.Exceptions;
using Services.Interfaces;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace Services
{
    public class MarriageService : BaseService<Marriage>, IMarriageService
    {
        public MarriageService(IServiceDependencies _serviceDependencies) : base(_serviceDependencies)
        {
        }

        #region CRUD operations

        public async Task<IEnumerable<Marriage>> GetMarriagesByRelationshipIdAsync(int Id)
        {
            if (!(ExistsInDb<Relationship>(Id)))
                throw new ObjectNotFoundException(nameof(Relationship), Id);

            return await _serviceDependencies._context.Marriages
                                                      .Where(Marriage => Marriage.RelationshipId == Id)
                                                      .OrderBy(Marriage => Marriage.MarriageDate)
                                                      .ToListAsync();
        }

        public async Task<Marriage> CreateMarriageAsync(Marriage item)
        {
            if (!(ExistsInDb<Relationship>(item.RelationshipId)))
                throw new ObjectNotFoundException(nameof(Relationship), item.RelationshipId);

            return await Create(item);
        }

        #endregion

        /// <summary>
        /// Method adds new information about marriage to Relationship
        /// Relationships are shared, so there is need to create new one or change references
        /// </summary>
        public async Task<Marriage> AddMarriageToRelationshipAsync(Marriage item, int familytreeId)
        {
            var relationshipService = ResolveService<IRelationshipService>();
            var relItem = await relationshipService.GetRelationshipByIdAsync(item.RelationshipId);

            // Temporarily add new marriage to current relationship 
            relItem.Marriages.Add(item);

            var allSameRelationships = await relationshipService.GetRelationshipsListByParticipatedPersonsAsync(relItem);
            var potencialSameRelationships = allSameRelationships.Where(rel => rel.Marriages.Count() == relItem.Marriages.Count());
            Relationship existingRelationship = null;

            relItem.Marriages = relItem.Marriages.OrderBy((m => m.MarriageDate)).OrderBy(m => m.MarriageAddress).ToList();


            foreach (var relationship in potencialSameRelationships)
            {
                bool isEqual = true;
                relationship.Marriages = relationship.Marriages.OrderBy((m => m.MarriageDate)).OrderBy(m => m.MarriageAddress).ToList();

                for (int i = 0; i < relationship.Marriages.Count(); i++)
                {
                    if (!((relationship.Marriages.ElementAt(i).MarriageDate == relItem.Marriages.ElementAt(i).MarriageDate)
                        && (relationship.Marriages.ElementAt(i).MarriageAddress == relItem.Marriages.ElementAt(i).MarriageAddress)))
                    {
                        isEqual = false;
                    }
                }

                if (isEqual)
                {
                    existingRelationship = relationship;
                    break;
                }
            }

            // Identic relationship as required already exists -> remove reference/delete old relationship and add reference to the exisiting one
            if (existingRelationship != null)
            {
                await relationshipService.AddExistingRelationshipToFamilyTreeAsync(existingRelationship.Id, familytreeId);
                await relationshipService.RemoveReferenceOrDeleteRelationship(relItem, familytreeId);
                item.RelationshipId = existingRelationship.Id;
                return item;
            }
            // No relationship as required does not exists
            else
            {
                // Current relationship is only in selected FamilyTree -> we can add new info about marriage
                if (relItem.FamilyTreeRelationship.Count() == 1 && relItem.FamilyTreeRelationship.First().FamilyTreeId == familytreeId)
                {
                    return await CreateMarriageAsync(item);
                }
                // Current relationship has more references --> remove reference from old relationship
                // --> create new one
                else
                {
                    // Remove old relationship marriage from familytree 
                    await relationshipService.RemoveReferenceOrDeleteRelationship(relItem, familytreeId);

                    // Null all ids, because new relationship will be created
                    relItem.Id = 0;
                    relItem.FamilyTreeRelationship = new List<FamilyTreeRelationship>() { new FamilyTreeRelationship
                    {
                        FamilyTreeId = familytreeId,
                        RelationshipId = 0 }
                    };

                    foreach (var marriage in relItem.Marriages)
                    {
                        marriage.Id = 0;
                        marriage.RelationshipId = 0;
                    }

                    var result = await relationshipService.CreateNewRelationshipInFamilyTreeAsync(relItem, familytreeId);
                    return result.Marriages.Last();
                }
            }
        }



    }
}
