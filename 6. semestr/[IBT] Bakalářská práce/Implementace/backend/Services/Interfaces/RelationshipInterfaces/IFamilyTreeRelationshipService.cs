/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    IFamilyTreeRelationshipService.cs                     */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity.Relationships;
using System.Threading.Tasks;

namespace Services.Interfaces.RelationshipInterfaces
{
    public interface IFamilyTreeRelationshipService
    {
        public Task<FamilyTreeRelationship> PutRelationshipToFamilyTreeAsync(FamilyTreeRelationship item);
        public Task<FamilyTreeRelationship> RemoveRelationshipfromFamilyTreeAsync(FamilyTreeRelationship item);
    }
}
