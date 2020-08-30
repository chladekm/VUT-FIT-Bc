/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    ICollisionService.cs                                  */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using Entity.Relationships;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace Services.Interfaces
{
    public interface ICollisionService
    {
        // GET methods
        public Task<int> GetCountOfNotSolvedCollisionsByFamilyTreeIdAsync(int id);
        public Task<IEnumerable<Collision>> GetCollisionsByFamilyTreeIdAsync(int id);
        public Task<IEnumerable<Collision>> GetCollisionsByRelationshipIdAsync(int relationshipId);
        public Task<Collision> GetCollisionByIdAsync(int id);

        // CRUD methods
        public Task<Collision> CreateCollisionAsync(Collision item);
        public Task<Collision> DeleteCollisionAsync(int id);

        // Other methods
        public Task<FamilyTreeCollision> ToggleCollisionSolutionAsync(FamilyTreeCollision item);
        public Task CheckForCollisionDeleteAsync(int relationshipId);
        public Task CollisionDetectionAsync(Relationship item);
        public Task RemoveCollisionsReferenceForSpecificFamilyTreeAndRelationship(int relationshipId, int familytreeId);
    }
}
