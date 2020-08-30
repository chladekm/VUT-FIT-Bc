/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    IFamilyTreePersonService.cs                           */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity.Relationships;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace Services.Interfaces.RelationshipInterfaces
{
    public interface IFamilyTreePersonService
    {
        public Task<FamilyTreePerson> PutPersonToFamilyTreeAsync(FamilyTreePerson treePersonToCreate);
        public Task<FamilyTreePerson> DeletePersonfromFamilyTreeAsync(FamilyTreePerson familyTreePersonToDelete);
        public Task<IEnumerable<int>> GetIdsOfPublicFamilyTreesByPersonIdAsync(int personId);
    }
}
