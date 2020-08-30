/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    IFamilyTreeCollisionService.cs                        */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity.Relationships;
using System.Threading.Tasks;

namespace Services.Interfaces.RelationshipInterfaces
{
    public interface IFamilyTreeCollisionService
    {
        public Task<FamilyTreeCollision> CreateAsync(FamilyTreeCollision item);
        public Task<FamilyTreeCollision> UpdateAsync(FamilyTreeCollision item);
        public Task<FamilyTreeCollision> DeleteAsync(FamilyTreeCollision item);
    }
}
