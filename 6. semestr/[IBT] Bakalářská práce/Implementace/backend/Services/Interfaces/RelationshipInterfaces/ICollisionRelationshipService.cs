/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    ICollisionRelationshipService.cs                      */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity.Relationships;
using System.Threading.Tasks;

namespace Services.Interfaces.RelationshipInterfaces
{
    public interface ICollisionRelationshipService
    {
        public Task<CollisionRelationship> CreateAsync(CollisionRelationship item);
        public Task<CollisionRelationship> DeleteAsync(CollisionRelationship item);
    }
}
