/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    FamilyTreeRelationshipService.cs                      */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using Entity.Relationships;
using Microsoft.EntityFrameworkCore;
using Service;
using Services.Base;
using Services.Interfaces.RelationshipInterfaces;
using System.Linq;
using System.Threading.Tasks;

namespace Services.RelationshipServices
{
    public class FamilyTreeRelationshipService : BaseRelationshipService<FamilyTreeRelationship>, IFamilyTreeRelationshipService
    {

        public FamilyTreeRelationshipService(FamilyTreeDbContext _context) : base(_context)
        {
        }

        public async Task<FamilyTreeRelationship> PutRelationshipToFamilyTreeAsync(FamilyTreeRelationship item)
        {
            return await Create<FamilyTree, Relationship>(item);
        }

        public async Task<FamilyTreeRelationship> RemoveRelationshipfromFamilyTreeAsync(FamilyTreeRelationship item)
        {
            return await Delete<FamilyTree, Person>(item);
        }

        protected override (FamilyTreeRelationship, int, int) GetRecordAndIdsForRelationship(FamilyTreeRelationship item)
        {
            var result = _context.FamilyTreeRelationship.AsNoTracking().FirstOrDefault(fp => (fp.FamilyTreeId == item.FamilyTreeId) && (fp.RelationshipId == item.RelationshipId));

            return (result, item.FamilyTreeId, item.RelationshipId);
        }

    }
}
