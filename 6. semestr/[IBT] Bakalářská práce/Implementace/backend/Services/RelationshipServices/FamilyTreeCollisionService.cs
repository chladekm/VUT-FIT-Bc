/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    FamilyTreeCollisionService.cs                         */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using Entity.Relationships;
using Microsoft.EntityFrameworkCore;
using Service;
using Services.Base;
using Services.Exceptions;
using Services.Interfaces.RelationshipInterfaces;
using System.Linq;
using System.Threading.Tasks;

namespace Services.RelationshipServices
{
    public class FamilyTreeCollisionService : BaseRelationshipService<FamilyTreeCollision>, IFamilyTreeCollisionService
    {

        public FamilyTreeCollisionService(FamilyTreeDbContext _context) : base(_context)
        {
        }

        public async Task<FamilyTreeCollision> CreateAsync(FamilyTreeCollision item)
        {
            try
            {
                var result = await Create<FamilyTree, Collision>(item);
                return result;
            }
            // When replacing person in FamilyTree and relationship new relationship is still in collision
            // Catching "Already exists" error
            catch (InvalidObjectRelationshipException)
            {
                return item;
            }
        }

        public async Task<FamilyTreeCollision> UpdateAsync(FamilyTreeCollision item)
        {
            var result = await _context.FamilyTreeCollision.AsNoTracking().FirstOrDefaultAsync(fp => (fp.FamilyTreeId == item.FamilyTreeId) && (fp.CollisionId == item.CollisionId));

            if (result != null)
            {
                _context.Entry(result).CurrentValues.SetValues(item);
                _context.Entry(result).State = EntityState.Modified;

                await _context.SaveChangesAsync();
            }
            else
            {
                // Collision was solved and deleted, can ignore
                return item;
            }

            return result;
        }

        public async Task<FamilyTreeCollision> DeleteAsync(FamilyTreeCollision item)
        {
            return await Delete<FamilyTree, Collision>(item);
        }

        protected override (FamilyTreeCollision, int, int) GetRecordAndIdsForRelationship(FamilyTreeCollision item)
        {
            var result = _context.FamilyTreeCollision.AsNoTracking().FirstOrDefault(fp => (fp.FamilyTreeId == item.FamilyTreeId) && (fp.CollisionId == item.CollisionId));

            return (result, item.FamilyTreeId, item.CollisionId);
        }

    }
}
