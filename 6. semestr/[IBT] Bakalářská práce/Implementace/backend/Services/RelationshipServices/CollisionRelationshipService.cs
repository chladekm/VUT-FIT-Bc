/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    CollisionRelationshipService.cs                       */
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
    public class CollisionRelationshipService : BaseRelationshipService<CollisionRelationship>, ICollisionRelationshipService
    {

        public CollisionRelationshipService(FamilyTreeDbContext _context) : base(_context)
        {
        }

        public async Task<CollisionRelationship> CreateAsync(CollisionRelationship item)
        {
            return await Create<Collision, Relationship>(item);
        }

        public async Task<CollisionRelationship> DeleteAsync(CollisionRelationship item)
        {
            return await Delete<Collision, Relationship>(item);
        }

        protected override (CollisionRelationship, int, int) GetRecordAndIdsForRelationship(CollisionRelationship item)
        {
            var result = _context.CollisionRelationship.AsNoTracking().FirstOrDefault(fp => (fp.CollisionId == item.CollisionId) && (fp.RelationshipId == item.RelationshipId));

            return (result, item.CollisionId, item.RelationshipId);
        }

    }
}
